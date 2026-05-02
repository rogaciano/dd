from __future__ import annotations

import argparse
import json
import logging
import os
import sys
from collections import defaultdict, deque
from contextlib import closing
from dataclasses import dataclass
from datetime import datetime
from pathlib import Path
from typing import Any

from dotenv import load_dotenv

from convert_schema import ForeignKey, Table, parse_foreign_keys, parse_tables

PREFERRED_MSSQL_DRIVERS = [
    "ODBC Driver 18 for SQL Server",
    "ODBC Driver 17 for SQL Server",
    "ODBC Driver 13 for SQL Server",
    "ODBC Driver 11 for SQL Server",
    "SQL Server Native Client 11.0",
    "SQL Server",
]


class CopyError(Exception):
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
    pg_schema: str
    pg_connect_timeout: int
    batch_size: int
    reject_log_path: str


@dataclass(slots=True)
class RowPayload:
    values: tuple[Any, ...]
    source_row_number: int
    sanitized_columns: tuple[str, ...]


@dataclass(slots=True)
class TableCopyStats:
    table_name: str
    processed_count: int = 0
    inserted_count: int = 0
    rejected_count: int = 0
    sanitized_row_count: int = 0
    sanitized_value_count: int = 0


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Copia tabelas do SQL Server para o PostgreSQL quando o schema destino espelha a origem."
    )
    parser.add_argument(
        "--schema-file",
        default="schemas.sql",
        help="Arquivo com o schema do SQL Server usado para descobrir tabelas, colunas e dependencias.",
    )
    parser.add_argument(
        "--table",
        action="append",
        dest="tables",
        help="Migra apenas a tabela informada. Pode ser usado mais de uma vez.",
    )
    parser.add_argument(
        "--list-tables",
        action="store_true",
        help="Lista as tabelas na ordem de carga e encerra.",
    )
    parser.add_argument(
        "--audit",
        action="store_true",
        help="Compara as tabelas do schema-file com as tabelas existentes no PostgreSQL e imprime as ausentes.",
    )
    parser.add_argument(
        "--audit-columns",
        action="store_true",
        help="Quando usado com --audit, tambem compara colunas por tabela.",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Le e processa as linhas da origem, mas nao grava no PostgreSQL.",
    )
    parser.add_argument(
        "--validate-counts",
        action="store_true",
        help="Compara a quantidade de linhas da origem com o destino no final de cada tabela.",
    )
    parser.add_argument(
        "--truncate",
        action="store_true",
        help="Trunca as tabelas selecionadas no PostgreSQL antes da carga. Para carga parcial, use com cuidado.",
    )
    parser.add_argument(
        "--batch-size",
        type=int,
        help="Sobrescreve o tamanho do lote definido em BATCH_SIZE.",
    )
    return parser.parse_args()


def setup_logging() -> None:
    logging.basicConfig(level=logging.INFO, format="%(asctime)s | %(levelname)s | %(message)s")


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
            raise CopyError(f"Variavel obrigatoria nao definida: {name}")
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
        mssql_trust_server_certificate=get_env("MSSQL_TRUST_SERVER_CERTIFICATE", "yes"),
        mssql_timeout=int(get_env("MSSQL_TIMEOUT", "30")),
        pg_host=get_env("PGHOST", "localhost"),
        pg_port=int(get_env("PGPORT", "5432")),
        pg_database=get_env("PGDATABASE", required=True),
        pg_user=get_env("PGUSER", required=True),
        pg_password=get_env("PGPASSWORD", required=True),
        pg_schema=get_env("PGSCHEMA", "public"),
        pg_connect_timeout=int(get_env("PGCONNECT_TIMEOUT", "10")),
        batch_size=int(get_env("BATCH_SIZE", "1000")),
        reject_log_path=get_env("REJECT_LOG_PATH", "migration_rejects.jsonl"),
    )


def odbc_value(value: str) -> str:
    return "{" + value.replace("}", "}}") + "}"


def get_installed_odbc_drivers() -> list[str]:
    drivers: list[str] = []

    try:
        import pyodbc

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

    raise CopyError(
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


def connect_mssql(config: AppConfig) -> Any:
    import pyodbc

    return pyodbc.connect(build_mssql_connection_string(config), timeout=config.mssql_timeout)


def connect_postgres(config: AppConfig) -> Any:
    import psycopg

    return psycopg.connect(
        host=config.pg_host,
        port=config.pg_port,
        dbname=config.pg_database,
        user=config.pg_user,
        password=config.pg_password,
        connect_timeout=config.pg_connect_timeout,
    )


def load_schema(schema_file: str) -> tuple[list[Table], list[ForeignKey]]:
    text = Path(schema_file).read_text(encoding="utf-8")
    return parse_tables(text), parse_foreign_keys(text)


def sort_tables(
    tables: list[Table], foreign_keys: list[ForeignKey], selected_names: list[str] | None
) -> list[Table]:
    if selected_names:
        selected_lookup = {name.lower() for name in selected_names}
        selected_tables = [table for table in tables if table.name.lower() in selected_lookup]
        missing = selected_lookup - {table.name.lower() for table in selected_tables}
        if missing:
            raise CopyError(
                "Tabela(s) nao encontrada(s) no schema: " + ", ".join(sorted(missing))
            )
    else:
        selected_tables = list(tables)

    table_map = {table.name: table for table in selected_tables}
    graph: dict[str, set[str]] = defaultdict(set)
    indegree: dict[str, int] = {table.name: 0 for table in selected_tables}

    for fk in foreign_keys:
        if fk.table not in table_map or fk.ref_table not in table_map:
            continue
        if fk.table in graph[fk.ref_table]:
            continue
        graph[fk.ref_table].add(fk.table)
        indegree[fk.table] += 1

    queue = deque(sorted(name for name, degree in indegree.items() if degree == 0))
    ordered_names: list[str] = []

    while queue:
        current = queue.popleft()
        ordered_names.append(current)
        for child in sorted(graph[current]):
            indegree[child] -= 1
            if indegree[child] == 0:
                queue.append(child)

    if len(ordered_names) != len(selected_tables):
        unresolved = sorted(name for name, degree in indegree.items() if degree > 0)
        raise CopyError("Dependencias circulares detectadas: " + ", ".join(unresolved))

    return [table_map[name] for name in ordered_names]


def build_source_query(table: Table) -> str:
    select_parts: list[str] = []
    for column in table.columns:
        quoted = f"[{column.name}]"
        if column.sqlserver_type.lower() == "char":
            select_parts.append(f"RTRIM({quoted}) AS {quoted}")
        else:
            select_parts.append(quoted)

    columns_sql = ",\n    ".join(select_parts)
    return f"SELECT\n    {columns_sql}\nFROM [dbo].[{table.name}]"


def get_primary_key_columns(table: Table) -> list[str]:
    for constraint in table.constraints:
        if constraint.kind == "PRIMARY KEY":
            return list(constraint.columns)
    return []


def sanitize_value(value: Any) -> Any:
    if isinstance(value, str) and "\x00" in value:
        return value.replace("\x00", "")
    return value


def sanitize_row(table: Table, row: tuple[Any, ...]) -> RowPayload:
    sanitized_values: list[Any] = []
    sanitized_columns: list[str] = []

    for column, value in zip(table.columns, row):
        clean_value = sanitize_value(value)
        if clean_value != value:
            sanitized_columns.append(column.name)
        sanitized_values.append(clean_value)

    return RowPayload(
        values=tuple(sanitized_values),
        source_row_number=0,
        sanitized_columns=tuple(sanitized_columns),
    )


def serialize_value(value: Any) -> Any:
    if isinstance(value, bytes):
        return f"<bytes:{len(value)}>"
    if isinstance(value, datetime):
        return value.isoformat()
    if isinstance(value, str) and len(value) > 500:
        return value[:500] + "...<truncated>"
    return value


def build_row_snapshot(table: Table, row_payload: RowPayload) -> dict[str, Any]:
    row_dict = {
        column.name: serialize_value(value)
        for column, value in zip(table.columns, row_payload.values)
    }

    primary_key_columns = get_primary_key_columns(table)
    primary_key = {
        column_name: row_dict.get(column_name)
        for column_name in primary_key_columns
        if column_name in row_dict
    }

    snapshot: dict[str, Any] = {
        "source_row_number": row_payload.source_row_number,
        "sanitized_columns": list(row_payload.sanitized_columns),
    }

    if primary_key:
        snapshot["primary_key"] = primary_key
    else:
        preview_columns = list(row_dict)[:5]
        snapshot["row_preview"] = {name: row_dict[name] for name in preview_columns}

    return snapshot


def log_rejected_row(
    config: AppConfig,
    table: Table,
    row_payload: RowPayload,
    error: Exception,
) -> None:
    log_path = Path(config.reject_log_path)
    log_path.parent.mkdir(parents=True, exist_ok=True)

    payload = {
        "timestamp": datetime.now().isoformat(timespec="seconds"),
        "table": table.name,
        "error_type": error.__class__.__name__,
        "error": str(error),
        **build_row_snapshot(table, row_payload),
    }

    with log_path.open("a", encoding="utf-8") as handle:
        handle.write(json.dumps(payload, ensure_ascii=False) + "\n")


def qualified_identifier(schema: str, table_name: str) -> Any:
    from psycopg import sql

    return sql.SQL(".").join((sql.Identifier(schema), sql.Identifier(table_name)))


def build_insert_sql(config: AppConfig, table: Table) -> Any:
    from psycopg import sql

    target_table = qualified_identifier(config.pg_schema, table.name)
    columns_sql = sql.SQL(", ").join(sql.Identifier(column.name) for column in table.columns)
    placeholders = sql.SQL(", ").join(sql.Placeholder() for _ in table.columns)
    return sql.SQL("INSERT INTO {} ({}) VALUES ({})").format(
        target_table,
        columns_sql,
        placeholders,
    )


def truncate_tables(cursor: Any, config: AppConfig, tables: list[Table]) -> None:
    from psycopg import sql

    table_list = sql.SQL(", ").join(
        qualified_identifier(config.pg_schema, table.name) for table in reversed(tables)
    )
    cursor.execute(
        sql.SQL("TRUNCATE TABLE {} RESTART IDENTITY CASCADE").format(table_list)
    )


def check_target_tables_exist(cursor: Any, config: AppConfig, tables: list[Table]) -> None:
    expected = {table.name for table in tables}
    cursor.execute(
        """
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = %s
        """,
        (config.pg_schema,),
    )
    existing = {row[0] for row in cursor.fetchall()}
    missing = sorted(expected - existing)

    if missing:
        preview = ", ".join(missing[:10])
        if len(missing) > 10:
            preview += f", ... (+{len(missing) - 10})"
        raise CopyError(
            "Tabelas ausentes no PostgreSQL schema "
            f"'{config.pg_schema}': {preview}. "
            "Aplique primeiro o arquivo schema_pg.sql no banco de destino "
            "ou ajuste PGSCHEMA no .env."
        )


def fetch_existing_tables(cursor: Any, schema: str) -> set[str]:
    cursor.execute(
        """
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = %s
        """,
        (schema,),
    )
    return {row[0] for row in cursor.fetchall()}


def fetch_existing_columns(cursor: Any, schema: str) -> dict[str, set[str]]:
    cursor.execute(
        """
        SELECT table_name, column_name
        FROM information_schema.columns
        WHERE table_schema = %s
        """,
        (schema,),
    )
    result: dict[str, set[str]] = defaultdict(set)
    for table_name, column_name in cursor.fetchall():
        result[str(table_name)].add(str(column_name))
    return result


def audit_postgres_schema(
    target_conn: Any, config: AppConfig, tables: list[Table], audit_columns: bool
) -> int:
    expected_tables = {table.name for table in tables}
    expected_columns = {table.name: {col.name for col in table.columns} for table in tables}

    with target_conn.cursor() as cursor:
        existing_tables = fetch_existing_tables(cursor, config.pg_schema)
        existing_columns = fetch_existing_columns(cursor, config.pg_schema) if audit_columns else {}

    missing_tables = sorted(expected_tables - existing_tables)
    extra_tables = sorted(existing_tables - expected_tables)

    print(f"Schema PostgreSQL: {config.pg_schema}")
    print(f"Tabelas esperadas (schema-file): {len(expected_tables)}")
    print(f"Tabelas existentes (PostgreSQL): {len(existing_tables)}")
    print(f"Tabelas ausentes (PostgreSQL): {len(missing_tables)}")

    if missing_tables:
        for name in missing_tables:
            print(f"- {name}")

    if extra_tables:
        print(f"Tabelas extras (PostgreSQL, nao estao no schema-file): {len(extra_tables)}")
        for name in extra_tables[:30]:
            print(f"+ {name}")
        if len(extra_tables) > 30:
            print(f"+ ... (+{len(extra_tables) - 30})")

    if audit_columns and not missing_tables:
        column_problems = 0
        for table in tables:
            expected = expected_columns.get(table.name, set())
            existing = existing_columns.get(table.name, set())
            missing_cols = sorted(expected - existing)
            extra_cols = sorted(existing - expected)
            if not missing_cols and not extra_cols:
                continue
            column_problems += 1
            print(f"Tabela {table.name}:")
            if missing_cols:
                print(f"  Colunas ausentes: {', '.join(missing_cols)}")
            if extra_cols:
                preview = ", ".join(extra_cols[:30])
                suffix = f", ... (+{len(extra_cols) - 30})" if len(extra_cols) > 30 else ""
                print(f"  Colunas extras: {preview}{suffix}")

        if column_problems:
            print(f"Tabelas com divergencia de colunas: {column_problems}")

    return 4 if missing_tables else 0


def fetch_count_mssql(cursor: Any, table: Table) -> int:
    cursor.execute(f"SELECT COUNT(1) FROM [dbo].[{table.name}]")
    row = cursor.fetchone()
    return int(row[0]) if row else 0


def fetch_count_postgres(cursor: Any, config: AppConfig, table: Table) -> int:
    from psycopg import sql

    cursor.execute(
        sql.SQL("SELECT COUNT(1) FROM {}").format(
            qualified_identifier(config.pg_schema, table.name)
        )
    )
    row = cursor.fetchone()
    return int(row[0]) if row else 0


def reset_identity_sequences(cursor: Any, config: AppConfig, table: Table) -> None:
    from psycopg import sql

    for column in table.columns:
        if not column.identity:
            continue

        cursor.execute(
            sql.SQL(
                """
                SELECT setval(
                    pg_get_serial_sequence(%s, %s),
                    COALESCE((SELECT MAX({column}) FROM {table}), 1),
                    (SELECT COUNT(1) > 0 FROM {table})
                )
                """
            ).format(
                column=sql.Identifier(column.name),
                table=qualified_identifier(config.pg_schema, table.name),
            ),
            (f"{config.pg_schema}.{table.name}", column.name),
        )


def insert_batch_with_recovery(
    target_conn: Any,
    target_cursor: Any,
    insert_sql: Any,
    config: AppConfig,
    table: Table,
    payload: list[RowPayload],
) -> tuple[int, int]:
    values = [item.values for item in payload]

    try:
        target_cursor.executemany(insert_sql, values)
        target_conn.commit()
        return len(payload), 0
    except Exception as batch_error:
        target_conn.rollback()
        logging.warning(
            "Falha no lote da tabela %s. Tentando isolar linhas individualmente. Erro: %s",
            table.name,
            batch_error,
        )

    inserted_count = 0
    rejected_count = 0

    for row_payload in payload:
        try:
            target_cursor.execute(insert_sql, row_payload.values)
            target_conn.commit()
            inserted_count += 1
        except Exception as row_error:
            target_conn.rollback()
            rejected_count += 1
            log_rejected_row(config, table, row_payload, row_error)

    return inserted_count, rejected_count


def copy_table(
    source_conn: Any,
    target_conn: Any,
    config: AppConfig,
    table: Table,
    batch_size: int,
    dry_run: bool,
    validate_counts: bool,
) -> TableCopyStats:
    logging.info("Copiando tabela %s...", table.name)
    source_query = build_source_query(table)
    insert_sql = build_insert_sql(config, table)
    stats = TableCopyStats(table_name=table.name)

    expected_count: int | None = None
    if validate_counts:
        with closing(source_conn.cursor()) as cursor:
            expected_count = fetch_count_mssql(cursor, table)
        logging.info("Origem %s possui %s linhas.", table.name, expected_count)

    total = 0
    source_cursor = source_conn.cursor()
    target_cursor = target_conn.cursor()
    try:
        source_cursor.execute(source_query)

        while True:
            rows = source_cursor.fetchmany(batch_size)
            if not rows:
                break

            payload: list[RowPayload] = []
            for index, row in enumerate(rows, start=1):
                row_payload = sanitize_row(table, tuple(row))
                row_payload.source_row_number = total + index
                payload.append(row_payload)
                if row_payload.sanitized_columns:
                    stats.sanitized_row_count += 1
                    stats.sanitized_value_count += len(row_payload.sanitized_columns)

            total += len(payload)
            stats.processed_count = total

            if not dry_run:
                inserted_count, rejected_count = insert_batch_with_recovery(
                    target_conn=target_conn,
                    target_cursor=target_cursor,
                    insert_sql=insert_sql,
                    config=config,
                    table=table,
                    payload=payload,
                )
                stats.inserted_count += inserted_count
                stats.rejected_count += rejected_count

            logging.info("Tabela %s: %s linhas processadas.", table.name, total)

        if not dry_run:
            reset_identity_sequences(target_cursor, config, table)
            target_conn.commit()
    finally:
        source_cursor.close()
        target_cursor.close()

    if validate_counts:
        with target_conn.cursor() as cursor:
            target_count = fetch_count_postgres(cursor, config, table)
        logging.info("Tabela %s: origem=%s | destino=%s", table.name, expected_count, target_count)

    if dry_run:
        logging.info("Dry-run finalizado em %s.", table.name)
    else:
        logging.info("Carga finalizada em %s.", table.name)

    if stats.sanitized_row_count:
        logging.warning(
            "Tabela %s: %s linhas tiveram bytes NUL removidos em %s campo(s).",
            table.name,
            stats.sanitized_row_count,
            stats.sanitized_value_count,
        )

    if stats.rejected_count:
        logging.warning(
            "Tabela %s: %s linha(s) rejeitada(s). Consulte %s.",
            table.name,
            stats.rejected_count,
            config.reject_log_path,
        )

    return stats


def main() -> int:
    setup_logging()
    args = parse_args()

    try:
        tables, foreign_keys = load_schema(args.schema_file)
        ordered_tables = sort_tables(tables, foreign_keys, args.tables)

        if args.list_tables:
            for table in ordered_tables:
                print(table.name)
            return 0

        config = load_config()
        batch_size = args.batch_size or config.batch_size
        if batch_size <= 0:
            raise CopyError("BATCH_SIZE deve ser maior que zero.")

        target_conn = connect_postgres(config)

        if args.audit:
            try:
                return audit_postgres_schema(
                    target_conn=target_conn,
                    config=config,
                    tables=ordered_tables,
                    audit_columns=args.audit_columns,
                )
            finally:
                target_conn.close()

        source_conn = connect_mssql(config)
        total_rejected = 0
        try:
            with target_conn.cursor() as cursor:
                check_target_tables_exist(cursor, config, ordered_tables)

            if args.truncate and not args.dry_run:
                with target_conn.cursor() as cursor:
                    truncate_tables(cursor, config, ordered_tables)
                target_conn.commit()

            for table in ordered_tables:
                stats = copy_table(
                    source_conn=source_conn,
                    target_conn=target_conn,
                    config=config,
                    table=table,
                    batch_size=batch_size,
                    dry_run=args.dry_run,
                    validate_counts=args.validate_counts,
                )
                total_rejected += stats.rejected_count
        finally:
            target_conn.close()
            source_conn.close()

        if total_rejected:
            logging.warning(
                "Processo encerrado com %s linha(s) rejeitada(s). Consulte %s.",
                total_rejected,
                config.reject_log_path,
            )
            return 3

        logging.info("Processo encerrado sem erros.")
        return 0
    except CopyError as exc:
        logging.error(str(exc))
        return 1
    except Exception as exc:
        if exc.__class__.__module__.startswith(("pyodbc", "psycopg")):
            logging.exception("Falha de banco de dados: %s", exc)
            return 2
        logging.exception("Falha inesperada: %s", exc)
        return 99


if __name__ == "__main__":
    sys.exit(main())
