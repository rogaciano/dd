# Status e Proximos Passos

Atualizado em: 2026-04-23

## Situacao atual

Este arquivo foi recriado porque `windsurf/docs/STATUS_E_PROXIMOS_PASSOS.md` nao existia no workspace atual.

O projeto esta estruturado como um monolito Laravel moderno para a V1 do sistema de denuncias, com separacao entre:

- `disque_denuncia_legado`: espelho recarregavel do SQL Server 2008
- `disque_denuncia_novo`: banco da aplicacao nova
- comandos de projecao do legado para o modelo novo

## Validado nesta rodada

- `php artisan about` executou com sucesso.
- `php artisan route:list` executou com sucesso e listou 59 rotas.
- `cmd /c npm run build` executou com sucesso, incluindo build client e SSR.
- `php artisan test` executou sem falha fatal no PHP local.
- `php artisan route:list --except-vendor` executou com sucesso e listou as rotas da aplicacao.
- `php -l` executou sem erro nos arquivos PHP alterados nesta rodada.

Observacao sobre testes:

- Resultado local: 2 testes passaram e 28 foram ignorados.
- Motivo dos skips: o PHP local nao possui `pdo_sqlite`.
- A imagem Docker da aplicacao foi ajustada para instalar `pdo_sqlite`, entao a validacao completa deve ser feita dentro do container.

## Atualizacao desta rodada

O fluxo de criacao interna de denuncia foi alinhado com o modelo de dominio atual:

- `canal` do formulario passou a enviar valores normalizados: `interno`, `telefone`, `web`.
- `assunto_id` passou a ser obrigatorio no cadastro interno.
- A denuncia criada internamente agora grava o assunto principal em `denuncia_assuntos`.
- `difusaoImediata` agora alimenta `urgente` e eleva `prioridade` para `alta`.
- Etiquetas gravam `criado_por_usuario_id` na tabela pivo.
- Veiculos informados com marca/modelo passam a criar ou reutilizar registros em `veiculo_marcas` e `veiculo_modelos`.
- Foi criado teste de feature para o fluxo interno: `tests/Feature/DenunciaControllerTest.php`.

## Atualizacao de modernizacao do legado

Foi iniciada uma segunda rodada de modernizacao da projecao legado -> banco novo:

- catalogos fisicos de envolvidos deixaram de depender apenas de `varchar`
- foram adicionadas FKs em `denuncia_envolvidos` para cor de pele, estatura, olhos, cabelo e porte fisico
- `legado:importar-carga` agora importa esses catalogos e preenche tanto o texto legivel quanto os IDs estruturados
- `correlatas` passou a ter destino proprio em `denuncia_vinculos`
- `difusao_tipo` passou a ter destino proprio em `tipos_encaminhamento`
- `resultado_quantificacoes` passou a aceitar catalogos estruturados para classe, tipo, item e unidade de medida
- a decisao arquitetural foi documentada em `windsurf/docs/MODERNIZACAO_LEGADO_PARA_DISQUE_DENUNCIA_NOVO.md`

## Validacao bloqueada neste terminal

Nao foi possivel validar banco e Docker a partir deste terminal porque:

- `docker ps` falhou por permissao no Docker API do Windows.
- `php artisan migrate:status` falhou porque este terminal nao consegue conectar em `127.0.0.1:5432`.

Se o ambiente do usuario ja esta funcionando no navegador, essa limitacao parece ser do contexto do terminal usado pelo agente, nao necessariamente do projeto.

## Entregas tecnicas ja aplicadas

### Ambiente

- `.env` da aplicacao ajustado para rodar localmente com `php artisan serve`.
- Redis deixou de ser obrigatorio no host local:
  - `SESSION_DRIVER=file`
  - `CACHE_STORE=file`
  - `QUEUE_CONNECTION=sync`
- `.env.example` e documentacao ajustados para `disque_denuncia_novo` e `disque_denuncia_legado`.

### Dominio de denuncia

- Criados valores centralizados para `status` e `canal`.
- Criado gerador de protocolo sequencial mensal.
- Criada tabela de sequencias de protocolo.
- Criados indices para dashboard e conciliacao com legado.
- Fluxo publico e fluxo interno passaram a usar valores normalizados.

### Importacao legado

- Importacao de denuncias usa `origem_legado_tabela` e `origem_legado_id`.
- Importacao de veiculos foi corrigida para vincular por `origem_legado_id`, nao pelo `id` interno novo.
- Mapeamento de marca/modelo de veiculo foi ajustado para o schema legado.

### Dashboard

- Dashboard deixou de carregar todas as denuncias com `get()`.
- Agora usa paginação no backend e paginator no Vue/Inertia.
- Metricas foram separadas da lista paginada.

### Models

- Models com tabelas em portugues fora da convencao padrao do Eloquent receberam `protected $table`.
- Cast de senha do `User` foi corrigido para `password`.

## Proximo passo recomendado

Antes de criar novas funcionalidades, fechar a base operacional:

1. Rodar migrations no banco novo.
2. Rodar seeders.
3. Rodar testes completos dentro do container.
4. Confirmar que o portal publico, login, dashboard e cadastro interno abrem sem erro.
5. Confirmar que a importacao legado ainda roda contra `disque_denuncia_legado`.

Comandos recomendados no ambiente do usuario:

```powershell
cd E:\projetos\disque
docker compose up -d --build
```

```powershell
docker exec -it php_apache_sistema bash
cd /var/www/html/sistema
composer install
npm install
npm run build
php artisan migrate --seed
php artisan test
```

Se estiver rodando com `php artisan serve` fora do container:

```powershell
cd E:\projetos\disque\sistema
php artisan optimize:clear
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

## Proximo bloco de produto

Depois da base operacional, o foco deve ser continuar pelo core da V1:

1. Formulario publico de denuncia anonima.
2. Cadastro interno de denuncia por operador.
3. Triagem e classificacao por assunto.
4. Etiquetas operacionais.
5. Encaminhamentos.
6. Movimentacoes/auditoria.
7. Resultados/desdobramentos.

## Decisao recomendada agora

Seguir pelo modulo de criacao de denuncia/cadastro interno antes de relatorios ou paineis avancados.

Motivo:

- e o centro do dominio
- valida protocolo, anonimato, local, envolvidos, assuntos e etiquetas
- prepara a triagem
- revela cedo inconsistencias de banco, permissao e UX

## Checklist imediato

- Confirmar que migrations rodam no `disque_denuncia_novo`.
- Confirmar que `php artisan test` roda dentro do container sem skips por `pdo_sqlite`.
- Revisar telas atuais de `Denuncias/Create.vue`.
- Comparar campos do formulario atual com `V1_ESCOPO.md`, `MODELO_DOMINIO.md` e `BANCO_V1.md`.
- Definir o primeiro fluxo completo: criar denuncia interna com local, envolvidos, veiculos, assuntos e etiquetas.
