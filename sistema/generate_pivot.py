import os
import glob
import re

MIGRATIONS_DIR = 'database/migrations'

pivot_data = {
    'create_papel_usuario_table.php': {
        'table': 'papel_usuario',
        'fields': [
            "$table->id();",
            "$table->foreignId('papel_id')->constrained('papeis')->onDelete('cascade');",
            "$table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');",
            "$table->timestamps();"
        ]
    },
    'create_denuncia_assunto_table.php': {
        'table': 'denuncia_assuntos',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->foreignId('assunto_id')->constrained('assuntos')->onDelete('cascade');",
            "$table->boolean('principal')->default(false);",
            "$table->foreignId('criado_por_usuario_id')->nullable()->constrained('users');",
            "$table->timestamps();"
        ]
    },
    'create_denuncia_etiqueta_table.php': {
        'table': 'denuncia_etiqueta',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->foreignId('etiqueta_id')->constrained('etiquetas')->onDelete('cascade');",
            "$table->foreignId('criado_por_usuario_id')->nullable()->constrained('users');",
            "$table->timestamps();"
        ]
    }
}

for migration_file, info in pivot_data.items():
    table_name = info['table']
    fields = info['fields']
    files = glob.glob(f"{MIGRATIONS_DIR}/*{migration_file}")
    if files:
        fpath = files[0]
        with open(fpath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        blueprint_fields = "\n            ".join(fields)
        content = re.sub(
            r"Schema::create\('([^']+)', function \(Blueprint \$table\) \{(.*?)\}\);",
            f"Schema::create('{table_name}', function (Blueprint $table) {{\n            {blueprint_fields}\n        }});",
            content,
            flags=re.DOTALL
        )
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {migration_file}")
