from __future__ import annotations

import argparse
import json
from collections import defaultdict
from dataclasses import asdict, dataclass
from pathlib import Path

from convert_schema import ForeignKey, Table, parse_foreign_keys, parse_tables


@dataclass(slots=True)
class Relationship:
    source_table: str
    source_column: str
    target_table: str
    target_column: str
    category: str
    confidence: str
    reason: str


@dataclass(slots=True)
class ReviewItem:
    table: str
    column: str
    note: str


MANUAL_RELATIONSHIPS: dict[tuple[str, str], tuple[str, str, str, str]] = {
    ("denuncia_com", "den_cd"): (
        "denuncia",
        "den_cd",
        "high",
        "Tabela auxiliar da denuncia; PK em den_cd sugere relacao 1:1.",
    ),
    ("dd_mulher", "mul_den_cd"): (
        "denuncia",
        "den_cd",
        "high",
        "Tabela especializada por denuncia; PK usa o codigo da denuncia.",
    ),
    ("usuarios", "usu_tipo"): (
        "usuarios_tipos",
        "utp_cd",
        "high",
        "Campo de tipo do usuario aponta semanticamente para usuarios_tipos.",
    ),
    ("veiculos", "marca"): (
        "vei_marca",
        "mar_cd",
        "high",
        "Campo sem sufixo _cd, mas o nome coincide com a tabela de marcas.",
    ),
    ("veiculos", "modelo"): (
        "vei_modelo",
        "mod_cd",
        "high",
        "Campo sem sufixo _cd, mas o nome coincide com a tabela de modelos.",
    ),
    ("buscas", "bus_rel_cd"): (
        "relatorios",
        "rel_cd",
        "high",
        "Codigo do relatorio usado pela busca.",
    ),
    ("denuncia", "den_xpto"): (
        "xpto",
        "xpt_cd",
        "medium",
        "Nome do campo sugere referencia ao catalogo xpto.",
    ),
    ("denuncia", "den_class"): (
        "classificacao",
        "cld_cd",
        "medium",
        "Campo de classificacao sugere referencia ao catalogo classificacao.",
    ),
    ("envolvidos", "env_pele"): (
        "aux_pele",
        "pel_cd",
        "medium",
        "Codigo de pele sugere referencia ao catalogo auxiliar.",
    ),
    ("envolvidos", "env_estatura"): (
        "aux_estatura",
        "est_cd",
        "medium",
        "Codigo de estatura sugere referencia ao catalogo auxiliar.",
    ),
    ("envolvidos", "env_olhos"): (
        "aux_olhos",
        "olh_cd",
        "medium",
        "Codigo de olhos sugere referencia ao catalogo auxiliar.",
    ),
    ("envolvidos", "env_cabelo"): (
        "aux_cabelo",
        "cab_cd",
        "medium",
        "Codigo de cabelo sugere referencia ao catalogo auxiliar.",
    ),
    ("envolvidos", "env_porte"): (
        "aux_porte",
        "prt_cd",
        "medium",
        "Codigo de porte sugere referencia ao catalogo auxiliar.",
    ),
}


REVIEW_CANDIDATES: dict[tuple[str, str], str] = {
    ("denuncia", "den_op_rec"): "Pode apontar para usuarios.usu_cd, mas o nome nao prova a relacao sozinho.",
    ("denuncia", "den_corr_cd"): "Pode apontar para correlatas.cor_cd, mas precisa validar pela aplicacao/dados.",
    ("veiculos", "com_tipo"): "Campo numerico com cara de dominio interno; destino nao fica claro so pelo schema.",
    ("envolvidos", "env_end_tp"): "Campo de tipo/endereco; pode ser dominio interno em vez de FK.",
    ("chaves", "denuncia"): "Parece contador/controle operacional, nao necessariamente FK para denuncia.",
    ("chaves", "atendimento"): "Parece contador/controle operacional, nao necessariamente FK para atendimento.",
}


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Gera um mapa de relacionamentos explicitos e inferidos a partir do schemas.sql."
    )
    parser.add_argument(
        "--input",
        default="schemas.sql",
        help="Arquivo de entrada com o schema do SQL Server.",
    )
    parser.add_argument(
        "--output-md",
        default="relationship_map.md",
        help="Arquivo Markdown de saida.",
    )
    parser.add_argument(
        "--output-json",
        default="relationship_map.json",
        help="Arquivo JSON de saida.",
    )
    return parser.parse_args()


def get_single_column_pk(table: Table) -> str | None:
    for constraint in table.constraints:
        if constraint.kind == "PRIMARY KEY" and len(constraint.columns) == 1:
            return constraint.columns[0]
    return None


def build_explicit_relationships(foreign_keys: list[ForeignKey]) -> list[Relationship]:
    return [
        Relationship(
            source_table=fk.table,
            source_column=fk.column,
            target_table=fk.ref_table,
            target_column=fk.ref_column,
            category="explicit",
            confidence="explicit",
            reason="Constraint FOREIGN KEY declarada no SQL Server.",
        )
        for fk in foreign_keys
    ]


def build_inferred_relationships(
    tables: list[Table], explicit_relationships: list[Relationship]
) -> tuple[list[Relationship], list[ReviewItem]]:
    explicit_keys = {
        (rel.source_table, rel.source_column, rel.target_table, rel.target_column)
        for rel in explicit_relationships
    }
    explicit_columns = {(rel.source_table, rel.source_column) for rel in explicit_relationships}
    pks = {table.name: get_single_column_pk(table) for table in tables}

    inferred: list[Relationship] = []
    review: list[ReviewItem] = []

    for table in tables:
        pk = pks[table.name]
        for column in table.columns:
            key = (table.name, column.name)
            if column.name == pk or key in explicit_columns:
                continue

            manual = MANUAL_RELATIONSHIPS.get(key)
            if manual:
                target_table, target_column, confidence, reason = manual
                inferred.append(
                    Relationship(
                        source_table=table.name,
                        source_column=column.name,
                        target_table=target_table,
                        target_column=target_column,
                        category="manual",
                        confidence=confidence,
                        reason=reason,
                    )
                )
                continue

            matches: list[tuple[str, str]] = []
            for ref_table, ref_pk in pks.items():
                if not ref_pk or ref_table == table.name:
                    continue
                if column.name == ref_pk or column.name.endswith("_" + ref_pk):
                    matches.append((ref_table, ref_pk))

            if len(matches) == 1:
                ref_table, ref_pk = matches[0]
                relation_key = (table.name, column.name, ref_table, ref_pk)
                if relation_key not in explicit_keys:
                    inferred.append(
                        Relationship(
                            source_table=table.name,
                            source_column=column.name,
                            target_table=ref_table,
                            target_column=ref_pk,
                            category="inferred",
                            confidence="high",
                            reason="Nome da coluna bate com a PK da tabela de destino.",
                        )
                    )
                continue

            if len(matches) > 1:
                match_set = {(ref_table, ref_pk) for ref_table, ref_pk in matches}
                if match_set == {("denuncia", "den_cd"), ("denuncia_com", "den_cd")}:
                    inferred.append(
                        Relationship(
                            source_table=table.name,
                            source_column=column.name,
                            target_table="denuncia",
                            target_column="den_cd",
                            category="manual",
                            confidence="high",
                            reason=(
                                "Coluna usa den_cd; denuncia_com tambem compartilha esse campo, "
                                "mas a referencia mais provavel e a tabela principal denuncia."
                            ),
                        )
                    )
                    continue

                options = ", ".join(f"{ref_table}.{ref_pk}" for ref_table, ref_pk in matches)
                review.append(
                    ReviewItem(
                        table=table.name,
                        column=column.name,
                        note=(
                            "Coluna combina com mais de uma PK e precisa de validacao humana: "
                            + options
                        ),
                    )
                )
                continue

            if key in REVIEW_CANDIDATES:
                review.append(
                    ReviewItem(
                        table=table.name,
                        column=column.name,
                        note=REVIEW_CANDIDATES[key],
                    )
                )

    return inferred, review


def sort_relationships(relationships: list[Relationship]) -> list[Relationship]:
    return sorted(
        relationships,
        key=lambda rel: (
            rel.source_table,
            rel.source_column,
            rel.target_table,
            rel.target_column,
            rel.category,
        ),
    )


def sort_review_items(items: list[ReviewItem]) -> list[ReviewItem]:
    return sorted(items, key=lambda item: (item.table, item.column))


def group_outgoing(relationships: list[Relationship]) -> dict[str, list[Relationship]]:
    grouped: dict[str, list[Relationship]] = defaultdict(list)
    for relationship in relationships:
        grouped[relationship.source_table].append(relationship)
    return {key: sort_relationships(value) for key, value in grouped.items()}


def group_incoming(relationships: list[Relationship]) -> dict[str, list[Relationship]]:
    grouped: dict[str, list[Relationship]] = defaultdict(list)
    for relationship in relationships:
        grouped[relationship.target_table].append(relationship)
    return {key: sort_relationships(value) for key, value in grouped.items()}


def render_relationship_line(relationship: Relationship) -> str:
    return (
        f"- `{relationship.source_table}.{relationship.source_column}` -> "
        f"`{relationship.target_table}.{relationship.target_column}` "
        f"[{relationship.category}, {relationship.confidence}]"
        f": {relationship.reason}"
    )


def build_markdown(
    tables: list[Table],
    explicit_relationships: list[Relationship],
    inferred_relationships: list[Relationship],
    review_items: list[ReviewItem],
) -> str:
    all_relationships = sort_relationships(explicit_relationships + inferred_relationships)
    outgoing = group_outgoing(all_relationships)
    incoming = group_incoming(all_relationships)

    lines: list[str] = [
        "# Mapa de Relacionamentos",
        "",
        "Gerado automaticamente a partir de `schemas.sql`.",
        "",
        "## Resumo",
        "",
        f"- Tabelas analisadas: `{len(tables)}`",
        f"- Relacionamentos explicitos: `{len(explicit_relationships)}`",
        f"- Relacionamentos inferidos: `{len(inferred_relationships)}`",
        f"- Pontos de revisao manual: `{len(review_items)}`",
        "",
        "## Relacionamentos Explicitos",
        "",
    ]

    if explicit_relationships:
        for relationship in sort_relationships(explicit_relationships):
            lines.append(render_relationship_line(relationship))
    else:
        lines.append("- Nenhum relacionamento explicito encontrado.")

    lines.extend(["", "## Relacionamentos Inferidos", ""])
    if inferred_relationships:
        for relationship in sort_relationships(inferred_relationships):
            lines.append(render_relationship_line(relationship))
    else:
        lines.append("- Nenhum relacionamento inferido.")

    lines.extend(["", "## Revisao Manual", ""])
    if review_items:
        for item in sort_review_items(review_items):
            lines.append(f"- `{item.table}.{item.column}`: {item.note}")
    else:
        lines.append("- Nenhum ponto pendente de revisao.")

    lines.extend(["", "## Visao Por Tabela", ""])
    for table in sorted(table.name for table in tables):
        lines.append(f"### `{table}`")
        lines.append("")

        if table in outgoing:
            lines.append("Saidas:")
            for relationship in outgoing[table]:
                lines.append(
                    f"- `{relationship.source_column}` -> `{relationship.target_table}.{relationship.target_column}` "
                    f"[{relationship.category}, {relationship.confidence}]"
                )
        else:
            lines.append("Saidas:")
            lines.append("- Nenhuma relacao detectada saindo desta tabela.")

        lines.append("")

        if table in incoming:
            lines.append("Entradas:")
            for relationship in incoming[table]:
                lines.append(
                    f"- `{relationship.source_table}.{relationship.source_column}` -> `{relationship.target_column}` "
                    f"[{relationship.category}, {relationship.confidence}]"
                )
        else:
            lines.append("Entradas:")
            lines.append("- Nenhuma relacao detectada chegando nesta tabela.")

        lines.append("")

    return "\n".join(lines).rstrip() + "\n"


def build_json_payload(
    tables: list[Table],
    explicit_relationships: list[Relationship],
    inferred_relationships: list[Relationship],
    review_items: list[ReviewItem],
) -> dict[str, object]:
    return {
        "summary": {
            "table_count": len(tables),
            "explicit_relationship_count": len(explicit_relationships),
            "inferred_relationship_count": len(inferred_relationships),
            "review_item_count": len(review_items),
        },
        "explicit_relationships": [asdict(item) for item in sort_relationships(explicit_relationships)],
        "inferred_relationships": [asdict(item) for item in sort_relationships(inferred_relationships)],
        "review_items": [asdict(item) for item in sort_review_items(review_items)],
    }


def main() -> int:
    args = parse_args()
    text = Path(args.input).read_text(encoding="utf-8")
    tables = parse_tables(text)
    foreign_keys = parse_foreign_keys(text)

    explicit_relationships = build_explicit_relationships(foreign_keys)
    inferred_relationships, review_items = build_inferred_relationships(
        tables, explicit_relationships
    )

    markdown = build_markdown(
        tables=tables,
        explicit_relationships=explicit_relationships,
        inferred_relationships=inferred_relationships,
        review_items=review_items,
    )
    Path(args.output_md).write_text(markdown, encoding="utf-8")

    payload = build_json_payload(
        tables=tables,
        explicit_relationships=explicit_relationships,
        inferred_relationships=inferred_relationships,
        review_items=review_items,
    )
    Path(args.output_json).write_text(
        json.dumps(payload, ensure_ascii=False, indent=2),
        encoding="utf-8",
    )

    print(
        f"Gerados {args.output_md} e {args.output_json} com "
        f"{len(explicit_relationships)} relacoes explicitas, "
        f"{len(inferred_relationships)} inferidas e {len(review_items)} itens de revisao."
    )
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
