import os
import glob
import re

MIGRATIONS_DIR = 'database/migrations'
MODELS_DIR = 'app/Models'

# Mapping defined in MODELO_DOMINIO.md
models_data = {
    'Papel': {
        'table': 'papeis',
        'fields': [
            "$table->id();",
            "$table->string('nome');",
            "$table->string('slug')->unique();",
            "$table->timestamps();"
        ],
        'migration_file': 'create_papels_table.php'
    },
    'Denuncia': {
        'table': 'denuncias',
        'fields': [
            "$table->id();",
            "$table->string('protocolo')->unique();",
            "$table->string('token_acompanhamento_hash')->nullable();",
            "$table->string('canal')->nullable();",
            "$table->string('status')->default('recebida');",
            "$table->string('prioridade')->default('normal');",
            "$table->boolean('urgente')->default(false);",
            "$table->text('resumo')->nullable();",
            "$table->text('relato');",
            "$table->timestamp('recebida_em')->nullable();",
            "$table->timestamp('enviada_em')->nullable();",
            "$table->foreignId('criada_por_usuario_id')->nullable()->constrained('users');",
            "$table->foreignId('responsavel_usuario_id')->nullable()->constrained('users');",
            "$table->string('ip_hash')->nullable();",
            "$table->string('user_agent_hash')->nullable();",
            "$table->timestamp('triada_em')->nullable();",
            "$table->timestamp('encerrada_em')->nullable();",
            "$table->timestamps();"
        ],
        'migration_file': 'create_denuncias_table.php'
    },
    'DenunciaLocal': {
        'table': 'denuncia_locais',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->string('pais_codigo')->nullable();",
            "$table->string('uf')->nullable();",
            "$table->string('municipio')->nullable();",
            "$table->string('bairro')->nullable();",
            "$table->string('subbairro')->nullable();",
            "$table->string('logradouro_tipo')->nullable();",
            "$table->string('logradouro_nome')->nullable();",
            "$table->string('numero')->nullable();",
            "$table->string('complemento')->nullable();",
            "$table->string('cep')->nullable();",
            "$table->string('referencia')->nullable();",
            "$table->text('endereco_manual')->nullable();",
            "$table->string('latitude')->nullable();",
            "$table->string('longitude')->nullable();",
            "$table->timestamps();"
        ],
        'migration_file': 'create_denuncia_locals_table.php'
    },
    'DenunciaEnvolvido': {
        'table': 'denuncia_envolvidos',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->string('papel_no_caso')->nullable();",
            "$table->string('nome')->nullable();",
            "$table->string('apelido')->nullable();",
            "$table->string('sexo')->nullable();",
            "$table->string('idade_estimada')->nullable();",
            "$table->string('cor_pele')->nullable();",
            "$table->string('estatura')->nullable();",
            "$table->string('olhos')->nullable();",
            "$table->string('cabelo')->nullable();",
            "$table->string('porte_fisico')->nullable();",
            "$table->text('sinais_particulares')->nullable();",
            "$table->text('observacoes')->nullable();",
            "$table->text('descricao_endereco')->nullable();",
            "$table->timestamps();"
        ],
        'migration_file': 'create_denuncia_envolvidos_table.php'
    },
    'GrupoAssunto': {
        'table': 'grupos_assunto',
        'fields': [
            "$table->id();",
            "$table->string('nome');",
            "$table->string('slug')->unique();",
            "$table->boolean('ativo')->default(true);",
            "$table->integer('ordem_exibicao')->default(0);",
            "$table->timestamps();"
        ],
        'migration_file': 'create_grupo_assuntos_table.php'
    },
    'Assunto': {
        'table': 'assuntos',
        'fields': [
            "$table->id();",
            "$table->foreignId('grupo_assunto_id')->constrained('grupos_assunto')->onDelete('cascade');",
            "$table->string('nome');",
            "$table->string('slug')->unique();",
            "$table->boolean('ativo')->default(true);",
            "$table->integer('ordem_exibicao')->default(0);",
            "$table->timestamps();"
        ],
        'migration_file': 'create_assuntos_table.php'
    },
    'Etiqueta': {
        'table': 'etiquetas',
        'fields': [
            "$table->id();",
            "$table->string('nome');",
            "$table->string('slug')->unique();",
            "$table->string('cor')->nullable();",
            "$table->boolean('ativo')->default(true);",
            "$table->timestamps();"
        ],
        'migration_file': 'create_etiquetas_table.php'
    },
    'Orgao': {
        'table': 'orgaos',
        'fields': [
            "$table->id();",
            "$table->string('nome');",
            "$table->string('tipo')->nullable();",
            "$table->string('categoria')->nullable();",
            "$table->string('email_destino')->nullable();",
            "$table->string('contato_destino')->nullable();",
            "$table->text('endereco')->nullable();",
            "$table->string('municipio')->nullable();",
            "$table->string('uf')->nullable();",
            "$table->string('cep')->nullable();",
            "$table->boolean('ativo')->default(true);",
            "$table->timestamps();"
        ],
        'migration_file': 'create_orgaos_table.php'
    },
    'Encaminhamento': {
        'table': 'encaminhamentos',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->foreignId('orgao_id')->constrained('orgaos')->onDelete('cascade');",
            "$table->string('tipo')->nullable();",
            "$table->string('status')->nullable();",
            "$table->timestamp('enviado_em')->nullable();",
            "$table->timestamp('prazo_em')->nullable();",
            "$table->text('observacoes')->nullable();",
            "$table->foreignId('criado_por_usuario_id')->nullable()->constrained('users');",
            "$table->timestamps();"
        ],
        'migration_file': 'create_encaminhamentos_table.php'
    },
    'DenunciaMovimentacao': {
        'table': 'denuncia_movimentacoes',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->string('tipo')->nullable();",
            "$table->string('titulo')->nullable();",
            "$table->text('conteudo')->nullable();",
            "$table->string('visibilidade')->default('interna');",
            "$table->foreignId('criado_por_usuario_id')->nullable()->constrained('users');",
            "$table->timestamps();"
        ],
        'migration_file': 'create_denuncia_movimentacaos_table.php'
    },
    'TipoResultado': {
        'table': 'tipos_resultado',
        'fields': [
            "$table->id();",
            "$table->string('nome');",
            "$table->string('slug')->unique();",
            "$table->boolean('ativo')->default(true);",
            "$table->integer('ordem_exibicao')->default(0);",
            "$table->timestamps();"
        ],
        'migration_file': 'create_tipo_resultados_table.php'
    },
    'Resultado': {
        'table': 'resultados',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->foreignId('tipo_resultado_id')->constrained('tipos_resultado')->onDelete('cascade');",
            "$table->foreignId('orgao_id')->nullable()->constrained('orgaos');",
            "$table->timestamp('registrado_em')->nullable();",
            "$table->timestamp('efetivado_em')->nullable();",
            "$table->text('descricao')->nullable();",
            "$table->foreignId('criado_por_usuario_id')->nullable()->constrained('users');",
            "$table->timestamps();"
        ],
        'migration_file': 'create_resultados_table.php'
    },
    'ResultadoQuantificacao': {
        'table': 'resultado_quantificacoes',
        'fields': [
            "$table->id();",
            "$table->foreignId('resultado_id')->constrained('resultados')->onDelete('cascade');",
            "$table->string('rotulo')->nullable();",
            "$table->decimal('quantidade', 10, 2)->nullable();",
            "$table->string('unidade')->nullable();",
            "$table->text('observacoes')->nullable();",
            "$table->timestamps();"
        ],
        'migration_file': 'create_resultado_quantificacaos_table.php'
    },
    'Anexo': {
        'table': 'anexos',
        'fields': [
            "$table->id();",
            "$table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');",
            "$table->string('disco')->nullable();",
            "$table->string('caminho');",
            "$table->string('nome_original')->nullable();",
            "$table->string('mime_type')->nullable();",
            "$table->integer('tamanho')->nullable();",
            "$table->string('checksum')->nullable();",
            "$table->foreignId('enviado_por_usuario_id')->nullable()->constrained('users');",
            "$table->timestamp('enviado_em')->nullable();",
            "$table->timestamps();"
        ],
        'migration_file': 'create_anexos_table.php'
    },
    'LogAuditoria': {
        'table': 'logs_auditoria',
        'fields': [
            "$table->id();",
            "$table->foreignId('usuario_id')->nullable()->constrained('users');",
            "$table->string('evento');",
            "$table->string('entidade_tipo')->nullable();",
            "$table->unsignedBigInteger('entidade_id')->nullable();",
            "$table->text('descricao')->nullable();",
            "$table->string('ip_hash')->nullable();",
            "$table->json('metadados')->nullable();",
            "$table->timestamps();"
        ],
        'migration_file': 'create_log_auditorias_table.php'
    }
}

for model_name, info in models_data.items():
    table_name = info['table']
    fields = info['fields']
    # 1. Update migration
    migration_files = glob.glob(f"{MIGRATIONS_DIR}/*{info['migration_file']}")
    if migration_files:
        migration_file = migration_files[0]
        with open(migration_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace the table name in Schema::create
        content = re.sub(r"Schema::create\('([^']+)'", f"Schema::create('{table_name}'", content)
        content = re.sub(r"Schema::dropIfExists\('([^']+)'", f"Schema::dropIfExists('{table_name}'", content)
        
        # Replace the blueprint
        blueprint_fields = "\n            ".join(fields)
        content = re.sub(
            r"Schema::create\('([^']+)', function \(Blueprint \$table\) \{(.*?)\}\);",
            f"Schema::create('{table_name}', function (Blueprint $table) {{\n            {blueprint_fields}\n        }});",
            content,
            flags=re.DOTALL
        )
        with open(migration_file, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated migration {os.path.basename(migration_file)}")

    # 2. Update Model
    model_file = f"{MODELS_DIR}/{model_name}.php"
    if os.path.exists(model_file):
        with open(model_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        if 'protected $table' not in content:
            table_declaration = f"    protected $table = '{table_name}';\n    protected $guarded = ['id'];\n"
            content = re.sub(
                r"class " + model_name + r" extends Model\n\{(.*?)(use HasFactory;)",
                r"class " + model_name + r" extends Model\n{\n    \2\n" + table_declaration,
                content,
                flags=re.DOTALL
            )
            with open(model_file, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Updated model {model_name}")

# Also need pivot tables: papel_usuario, denuncia_assuntos, denuncia_etiqueta
print("All done.")
