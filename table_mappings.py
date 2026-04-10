from __future__ import annotations

from typing import Any


def clean_text(value: Any) -> str | None:
    if value is None:
        return None
    text = str(value).strip()
    return text or None


def as_bool(value: Any) -> bool | None:
    if value is None:
        return None
    if isinstance(value, bool):
        return value
    if isinstance(value, str):
        normalized = value.strip().lower()
        if normalized in {"1", "true", "t", "yes", "y", "sim", "s"}:
            return True
        if normalized in {"0", "false", "f", "no", "n", "nao"}:
            return False
    return bool(value)


TABLE_MAPPINGS = [
    {
        "name": "usuario",
        "source_query": """
            SELECT
                Id AS id,
                Nome AS nome,
                Email AS email,
                Ativo AS ativo
            FROM dbo.Usuario
        """,
        "target_table": "public.usuario",
        "target_columns": ["id", "nome", "email", "ativo"],
        "conflict_columns": ["id"],
        "update_columns": ["nome", "email", "ativo"],
        "truncate_before_load": False,
        "transform": lambda row: (
            row["id"],
            clean_text(row["nome"]),
            clean_text(row["email"]),
            as_bool(row["ativo"]),
        ),
    },
]
