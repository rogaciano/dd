from __future__ import annotations

import argparse
import logging
import os
import sys
from contextlib import closing
from dataclasses import dataclass, field
from typing import Any, Callable, Sequence

import psycopg
import pyodbc
from dotenv import load_dotenv
from psycopg import sql

from table_mappings import TABLE_MAPPINGS

PREFERRED_MSSQL_DRIVERS = [
    "ODBC Driver 18 for SQL Server",
    "ODBC Driver 17 for SQL Server",
    "ODBC Driver 13 for SQL Server",
    "ODBC Driver 11 for SQL Server",
    "SQL Server Native Client 11.0",
    "SQL Server",
]


TransformFn = Callable[[dict[str, Any]], Sequence[Any]]


class MigrationError(Exception):
    pass


@dataclass(slots=True)
class AppConfig:
    mssql_driver: str
    mssql_host: str
    mssql_port: str
    mssql_instance: str
    mssql_database: str
    mssql_user: str
    mssql_password: str
    mssql_encrypt: str
    mssql_trust_server_certificate: str
    mssql_timeout: int
    pg_host: str
    pg_port: int
    pg_database: str
    pg_user: str
    pg_password: str
    pg_connect_timeout: int
    batch_size: int


@dataclass(slots=True)
class TableMapping:
    name: str
    source_query: str
    target_table: str
    target_columns: list[str]
    conflict_columns: list[str] = field(default_factory=list)
    update_columns: list[str] = field(default_factory=list)
    truncate_before_load: bool = False
    transform: TransformFn | None = None

    @classmethod
    def from_dict(cls, raw: dict[str, Any]) -> "TableMapping":
        target_columns = list(raw["target_columns"])
        conflict_columns = list(raw.get("conflict_columns", []))
        update_columns = list(raw.get("update_columns", []))
        transform = raw.get("transform")

        if transform is not None and not callable(transform):
            raise MigrationError(
                f"Mapping '{raw.get('name', '<sem nome>')}' tem um transform invalido."
            )

        if not update_columns and conflict_columns:
            update_columns = [col for col in target_columns if col not in conflict_columns]

        invalid_conflicts = [col for col in conflict_columns if col not in target_columns]
        if invalid_conflicts:
            raise MigrationError(
                f"Mapping '{raw['name']}' possui conflict_columns fora de target_columns: "
                f"{', '.join(invalid_conflicts)}"
            )

        invalid_updates = [col for col in update_columns if col not in target_columns]
        if invalid_updates:
            raise MigrationError(
                f"Mapping '{raw['name']}' possui update_columns fora de target_columns: "
                f"{', '.join(invalid_updates)}"
            )

        return cls(
            name=raw["name"],
            source_query=raw["source_query"].strip().rstrip(";"),
            target_table=raw["target_table"],
            target_columns=target_columns,
            conflict_columns=conflict_columns,
            update_columns=update_columns,
            truncate_before_load=bool(raw.get("truncate_before_load", False)),
            transform=transform,
        )


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Migra dados do SQL Server para o PostgreSQL usando um mapa de tabelas."
    )
    parser.add_argument(
        "--table",
        action="append",
        dest="tables",
        help="Migra apenas a tabela logica informada. Pode ser usado mais de uma vez.",
    )
    parser.add_argument(
        "--list-tables",
        action="store_true",
        help="Lista os nomes configurados em table_mappings.py e encerra.",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Le as linhas do SQL Server, aplica o de/para e nao grava no PostgreSQL.",
    )
    parser.add_argument(
        "--validate-counts",
        action="store_true",
        help="Conta linhas na origem e no destino para comparacao ao final.",
    )
    parser.add_argument(
        "--batch-size",
        type=int,
        help="Sobrescreve o tamanho do lote definido em BATCH_SIZE.",
    )
    return parser.parse_args()


def setup_logging() -> None:
    logging.basicConfig(
        level=logging.INFO,
        format="%(asctime)s | %(levelname)s | %(message)s",
    )


def load_environment() -> None:
    if load_dotenv(".env"):
        return
    if load_dotenv(".env.example"):
        logging.warning("Arquivo .env nao encontrado. Usando .env.example como fallback.")


def load_config() -> AppConfig:
    load_environment()

    def get_env(name: str, default: str | None = None, required: bool = False) -> str:
        value = os.getenv(name, default)
        if required and (value is None or value == ""):
            raise MigrationError(f"Variavel obrigatoria nao definida: {name}")
        return value or ""

    return AppConfig(
        mssql_driver=get_env("MSSQL_DRIVER", "ODBC Driver 17 for SQL Server"),
        mssql_host=get_env("MSSQL_HOST", required=True),
        mssql_port=get_env("MSSQL_PORT", "1433"),
        mssql_instance=get_env("MSSQL_INSTANCE", ""),
        mssql_database=get_env("MSSQL_DATABASE", required=True),
        mssql_user=get_env("MSSQL_USER", required=True),
        mssql_password=get_env("MSSQL_PASSWORD", required=True),
        mssql_encrypt=get_env("MSSQL_ENCRYPT", "no"),
        mssql_trust_server_certificate=get_env(
            "MSSQL_TRUST_SERVER_CERTIFICATE", "yes"
        ),
        mssql_timeout=int(get_env("MSSQL_TIMEOUT", "30")),
        pg_host=get_env("PGHOST", "localhost"),
        pg_port=int(get_env("PGPORT", "5432")),
        pg_database=get_env("PGDATABASE", required=True),
        pg_user=get_env("PGUSER", required=True),
        pg_password=get_env("PGPASSWORD", required=True),
        pg_connect_timeout=int(get_env("PGCONNECT_TIMEOUT", "10")),
        batch_size=int(get_env("BATCH_SIZE", "1000")),
    )


def odbc_value(value: str) -> str:
    return "{" + value.replace("}", "}}") + "}"


def get_installed_odbc_drivers() -> list[str]:
    drivers: list[str] = []

    try:
        drivers.extend(driver for driver in pyodbc.drivers() if driver)
    except Exception:
        pass

    if os.name != "nt":
        return list(dict.fromkeys(drivers))

    try:
        import winreg

        with winreg.OpenKey(
            winreg.HKEY_LOCAL_MACHINE,
            r"SOFTWARE\ODBC\ODBCINST.INI\ODBC Drivers",
        ) as key:
            index = 0
            while True:
                try:
                    name, value, _ = winreg.EnumValue(key, index)
                except OSError:
                    break

                if str(value).lower() == "installed" and name not in drivers:
                    drivers.append(name)
                index += 1
    except OSError:
        pass

    return drivers


def resolve_mssql_driver(configured_driver: str) -> str:
    installed_drivers = get_installed_odbc_drivers()

    if configured_driver and configured_driver in installed_drivers:
        return configured_driver

    if configured_driver and installed_drivers:
        logging.warning(
            "Driver ODBC configurado '%s' nao encontrado. Instalados: %s",
            configured_driver,
            ", ".join(installed_drivers),
        )

    for driver in PREFERRED_MSSQL_DRIVERS:
        if driver in installed_drivers:
            if driver != configured_driver:
                logging.warning("Usando driver ODBC '%s' como fallback.", driver)
            return driver

    if configured_driver:
        return configured_driver

    raise MigrationError(
        "Nenhum driver ODBC para SQL Server foi encontrado. "
        "Instale um driver compativel ou defina MSSQL_DRIVER corretamente."
    )


def build_mssql_connection_string(config: AppConfig) -> str:
    driver = resolve_mssql_driver(config.mssql_driver)

    if config.mssql_port:
        server = f"{config.mssql_host},{config.mssql_port}"
    elif config.mssql_instance:
        server = f"{config.mssql_host}\\{config.mssql_instance}"
    else:
        server = config.mssql_host

    parts = [
        f"DRIVER={odbc_value(driver)}",
        f"SERVER={odbc_value(server)}",
        f"DATABASE={odbc_value(config.mssql_database)}",
        f"UID={odbc_value(config.mssql_user)}",
        f"PWD={odbc_value(config.mssql_password)}",
        f"Encrypt={config.mssql_encrypt}",
        f"TrustServerCertificate={config.mssql_trust_server_certificate}",
    ]
    return ";".join(parts) + ";"


def connect_mssql(config: AppConfig) -> pyodbc.Connection:
    conn_str = build_mssql_connection_string(config)
    logging.info(
        "Conectando no SQL Server %s (%s)...",
        config.mssql_database,
        config.mssql_host,
    )
    return pyodbc.connect(conn_str, timeout=config.mssql_timeout)


def connect_postgres(config: AppConfig) -> psycopg.Connection:
    logging.info(
        "Conectando no PostgreSQL %s:%s/%s...",
        config.pg_host,
        config.pg_port,
        config.pg_database,
    )
    return psycopg.connect(
        host=config.pg_host,
        port=config.pg_port,
        dbname=config.pg_database,
        user=config.pg_user,
        password=config.pg_password,
        connect_timeout=config.pg_connect_timeout,
    )


def normalize_mappings(selected_tables: list[str] | None) -> list[TableMapping]:
    mappings = [TableMapping.from_dict(item) for item in TABLE_MAPPINGS]
    if not selected_tables:
        return mappings

    wanted = {name.lower() for name in selected_tables}
    selected = [mapping for mapping in mappings if mapping.name.lower() in wanted]
    missing = wanted - {mapping.name.lower() for mapping in selected}
    if missing:
        raise MigrationError(
            "Tabela(s) nao encontrada(s) em table_mappings.py: "
            + ", ".join(sorted(missing))
        )
    return selected


def qualified_identifier(name: str) -> sql.Composable:
    parts = [part.strip() for part in name.split(".") if part.strip()]
    if not parts:
        raise MigrationError(f"Nome de tabela invalido: {name!r}")
    return sql.SQL(".").join(sql.Identifier(part) for part in parts)


def build_insert_sql(mapping: TableMapping) -> sql.Composable:
    table_sql = qualified_identifier(mapping.target_table)
    column_sql = sql.SQL(", ").join(sql.Identifier(column) for column in mapping.target_columns)
    placeholder_sql = sql.SQL(", ").join(sql.Placeholder() for _ in mapping.target_columns)

    statement = sql.SQL("INSERT INTO {} ({}) VALUES ({})").format(
        table_sql,
        column_sql,
        placeholder_sql,
    )

    if not mapping.conflict_columns:
        return statement

    conflict_sql = sql.SQL(", ").join(
        sql.Identifier(column) for column in mapping.conflict_columns
    )

    if not mapping.update_columns:
        return statement + sql.SQL(" ON CONFLICT ({}) DO NOTHING").format(conflict_sql)

    assignments = sql.SQL(", ").join(
        sql.SQL("{} = EXCLUDED.{}").format(
            sql.Identifier(column),
            sql.Identifier(column),
        )
        for column in mapping.update_columns
    )
    return statement + sql.SQL(" ON CONFLICT ({}) DO UPDATE SET {}").format(
        conflict_sql,
        assignments,
    )


def truncate_target(cursor: psycopg.Cursor, mapping: TableMapping) -> None:
    cursor.execute(
        sql.SQL("TRUNCATE TABLE {} RESTART IDENTITY CASCADE").format(
            qualified_identifier(mapping.target_table)
        )
    )


def get_source_columns(cursor: pyodbc.Cursor) -> list[str]:
    if not cursor.description:
        raise MigrationError("Consulta de origem nao retornou colunas.")
    return [column[0] for column in cursor.description]


def default_transform(mapping: TableMapping, row: dict[str, Any]) -> Sequence[Any]:
    try:
        return tuple(row[column] for column in mapping.target_columns)
    except KeyError as exc:
        raise MigrationError(
            f"Mapping '{mapping.name}' espera a coluna '{exc.args[0]}' "
            "na consulta de origem. Use alias no SELECT ou defina um transform."
        ) from exc


def transform_batch(
    mapping: TableMapping, columns: list[str], rows: Sequence[Sequence[Any]]
) -> list[Sequence[Any]]:
    payload: list[Sequence[Any]] = []
    for raw_row in rows:
        row = dict(zip(columns, raw_row))
        if mapping.transform is None:
            record = default_transform(mapping, row)
        else:
            record = mapping.transform(row)

        if len(record) != len(mapping.target_columns):
            raise MigrationError(
                f"Mapping '{mapping.name}' retornou {len(record)} valores, "
                f"mas target_columns possui {len(mapping.target_columns)} colunas."
            )
        payload.append(tuple(record))
    return payload


def fetch_count_mssql(cursor: pyodbc.Cursor, mapping: TableMapping) -> int:
    count_query = f"SELECT COUNT(1) FROM ({mapping.source_query}) AS src"
    cursor.execute(count_query)
    row = cursor.fetchone()
    return int(row[0]) if row else 0


def fetch_count_postgres(cursor: psycopg.Cursor, mapping: TableMapping) -> int:
    cursor.execute(
        sql.SQL("SELECT COUNT(1) FROM {}").format(qualified_identifier(mapping.target_table))
    )
    row = cursor.fetchone()
    return int(row[0]) if row else 0


def migrate_table(
    source_conn: pyodbc.Connection,
    target_conn: psycopg.Connection,
    mapping: TableMapping,
    batch_size: int,
    dry_run: bool,
    validate_counts: bool,
) -> None:
    logging.info("Iniciando tabela logica '%s' -> %s", mapping.name, mapping.target_table)

    source_expected_count: int | None = None
    if validate_counts:
        with closing(source_conn.cursor()) as count_cursor:
            source_expected_count = fetch_count_mssql(count_cursor, mapping)
        logging.info("Origem '%s' possui %s linhas.", mapping.name, source_expected_count)

    insert_sql = build_insert_sql(mapping)
    total_read = 0

    source_cursor = source_conn.cursor()
    target_cursor = target_conn.cursor()
    try:
        if mapping.truncate_before_load and not dry_run:
            logging.info("Limpando destino %s antes da carga.", mapping.target_table)
            truncate_target(target_cursor, mapping)
            target_conn.commit()

        source_cursor.execute(mapping.source_query)
        columns = get_source_columns(source_cursor)

        while True:
            rows = source_cursor.fetchmany(batch_size)
            if not rows:
                break

            payload = transform_batch(mapping, columns, rows)
            total_read += len(payload)

            if not dry_run:
                target_cursor.executemany(insert_sql, payload)
                target_conn.commit()

            logging.info("Tabela '%s': %s linhas processadas.", mapping.name, total_read)
    finally:
        source_cursor.close()
        target_cursor.close()

    if validate_counts:
        with target_conn.cursor() as cursor:
            target_count = fetch_count_postgres(cursor, mapping)
        logging.info(
            "Tabela '%s': origem=%s | destino=%s",
            mapping.name,
            source_expected_count,
            target_count,
        )
        if mapping.truncate_before_load and source_expected_count != target_count:
            logging.warning(
                "Contagem divergente na tabela '%s'. Revise filtros, transformacoes e chaves.",
                mapping.name,
            )

    if dry_run:
        logging.info("Dry-run finalizado para '%s' com %s linhas transformadas.", mapping.name, total_read)
    else:
        logging.info("Migracao finalizada para '%s' com %s linhas processadas.", mapping.name, total_read)


def main() -> int:
    setup_logging()
    args = parse_args()

    try:
        config = load_config()
        mappings = normalize_mappings(args.tables)
        if args.list_tables:
            for mapping in mappings:
                print(mapping.name)
            return 0

        batch_size = args.batch_size or config.batch_size
        if batch_size <= 0:
            raise MigrationError("BATCH_SIZE deve ser maior que zero.")

        source_conn = connect_mssql(config)
        target_conn = connect_postgres(config)
        try:
            for mapping in mappings:
                migrate_table(
                    source_conn=source_conn,
                    target_conn=target_conn,
                    mapping=mapping,
                    batch_size=batch_size,
                    dry_run=args.dry_run,
                    validate_counts=args.validate_counts,
                )
        finally:
            target_conn.close()
            source_conn.close()

        logging.info("Processo encerrado sem erros.")
        return 0
    except MigrationError as exc:
        logging.error(str(exc))
        return 1
    except (pyodbc.Error, psycopg.Error) as exc:
        logging.exception("Falha de banco de dados: %s", exc)
        return 2
    except Exception as exc:  # pragma: no cover - seguranca operacional
        logging.exception("Falha inesperada: %s", exc)
        return 99


if __name__ == "__main__":
    sys.exit(main())
