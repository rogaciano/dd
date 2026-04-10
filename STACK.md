# Planejamento de Stack

## Objetivo

Este documento registra a stack-base recomendada para o novo sistema de denuncias anonimas.

O foco e construir uma aplicacao:

- segura por padrao
- moderna e simples de operar
- agradavel de programar
- agradavel para o usuario final
- facil de evoluir sem carregar os problemas do legado

Este projeto nao deve copiar o modelo legado "como esta". O banco antigo em [schemas.sql](e:\projetos\disque\schemas.sql) e o mapa em [relationship_map.md](e:\projetos\disque\relationship_map.md) servem como referencia de dominio e migracao, nao como definicao final da nova arquitetura.

---

## Stack Recomendada

### Backend

- `Laravel 13`
- `PHP 8.4`
- `PostgreSQL`
- `Redis`
- `Laravel Horizon`
- `Laravel Pulse`
- `Laravel Sanctum`
- `Telescope` apenas em ambiente local/dev

### Frontend

- `Inertia.js v3`
- `Vue 3`
- `Tailwind CSS 4`
- `Headless UI`
- `Vite`

### Infra e Operacao

- `Docker` para ambiente local
- `Nginx` ou `Caddy` na borda
- `HTTPS` obrigatorio
- filas assincronas com `Redis + Horizon`
- logs estruturados por ambiente

---

## Decisao Arquitetural

### Abordagem recomendada

Comecar com um `modern monolith`:

- `Laravel` controla regras de negocio, autenticacao, autorizacao, filas e observabilidade
- `Inertia + Vue` entrega a interface sem separar frontend e backend em dois projetos
- `PostgreSQL` como banco principal

### Motivo

Essa abordagem reduz:

- complexidade de deploy
- problemas de autenticacao entre apps separados
- necessidade de CORS para a interface principal
- duplicacao de validacoes entre frontend e backend

Tambem melhora:

- velocidade de desenvolvimento
- coesao do codigo
- manutencao por equipe pequena ou media

### O que evitar no inicio

- microservicos
- `Nuxt` separado consumindo API propria para a aplicacao principal
- importar o schema legado diretamente para o dominio novo
- excesso de bibliotecas visuais pesadas

---

## Principios Tecnicos

- dominio novo, modelado para o negocio atual
- regras explicitas, sem logica espalhada em controllers
- migrations limpas e reversiveis
- codigo orientado a casos de uso
- componentes Vue pequenos e reutilizaveis
- validacao no backend como fonte de verdade
- observabilidade desde a V1
- privacidade e anonimato tratados como requisito de produto

---

## Estrutura Recomendada

### Backend Laravel

Organizacao sugerida:

- `app/Actions`
- `app/DTOs`
- `app/Enums`
- `app/Http/Controllers`
- `app/Http/Requests`
- `app/Models`
- `app/Policies`
- `app/Services`
- `app/Support`
- `app/Jobs`
- `app/Events`
- `app/Listeners`

Uso sugerido:

- `Controllers`: finos, apenas orquestracao HTTP
- `Form Requests`: validacao e autorizacao de entrada
- `Actions` ou `Services`: casos de uso do sistema
- `Policies`: autorizacao por recurso
- `DTOs`: transporte de dados entre camadas
- `Jobs`: anexos, notificacoes, tarefas demoradas

### Frontend Vue

Organizacao sugerida:

- `resources/js/Pages`
- `resources/js/Layouts`
- `resources/js/Components`
- `resources/js/Forms`
- `resources/js/Composables`
- `resources/js/Types`
- `resources/css`

Uso sugerido:

- `Pages`: telas ligadas a rotas
- `Layouts`: estruturas de pagina
- `Components`: componentes reutilizaveis
- `Forms`: formularios complexos
- `Composables`: estado e comportamento reutilizavel

---

## Seguranca

Seguranca e anonimato sao requisitos centrais deste sistema.

### Regras obrigatorias

- `HTTPS` em todos os ambientes publicados
- protecao CSRF nas rotas web
- validacao server-side em toda entrada
- `Policies` para cada area administrativa
- rate limiting forte em login, criacao de denuncia, anexos e consultas sensiveis
- sanitizacao e validacao de upload
- armazenamento seguro de anexos
- segredos fora do repositorio
- backups testados
- principio do menor privilegio em banco e infraestrutura

### Decisoes de privacidade a definir

- armazenar IP integral, parcial, com hash ou nao armazenar
- armazenar `user-agent` ou nao
- permitir acompanhamento por protocolo sem identificar denunciante
- politica de retencao de logs tecnicos
- politica de retencao de anexos
- politica de mascaramento de dados no backoffice

### Recomendacoes

- nao registrar em log generico o texto integral da denuncia
- nao registrar payloads sensiveis sem necessidade operacional real
- separar log tecnico de trilha de auditoria
- tratar anexos como superficie critica

---

## Logs, Auditoria e Observabilidade

### Logs tecnicos

Devem conter:

- `request_id`
- ambiente
- rota
- usuario autenticado quando existir
- tempo de resposta
- severidade
- contexto minimo para diagnostico

Nao devem conter:

- conteudo integral da denuncia
- dados pessoais sem necessidade
- anexos ou blobs

### Auditoria de negocio

Criar trilha propria para:

- login
- logout
- falhas de autenticacao relevantes
- alteracao de status da denuncia
- alteracao de atribuicao
- visualizacao de dados sensiveis em contexto administrativo
- exportacoes

### Ferramentas

- `Pulse` para metricas e visao operacional
- `Horizon` para filas
- `Telescope` so em local/dev

---

## Banco de Dados

### Banco principal

- `PostgreSQL`

### Diretrizes

- nomes consistentes e previsiveis
- `foreign keys` reais no novo modelo
- colunas de auditoria padrao quando fizer sentido:
  - `created_at`
  - `updated_at`
  - `deleted_at` quando houver soft delete
- indices pensados para busca operacional
- evitar tabela generica demais

### Estrategia em relacao ao legado

- usar o legado para entender entidades e fluxo
- redesenhar o schema para o produto novo
- fazer importador separado quando a migracao for realmente necessaria

---

## UX e Interface

### Direcao

A interface deve transmitir:

- confianca
- clareza
- sobriedade
- acolhimento sem parecer informal demais

### Principios

- fluxo de denuncia simples e progressivo
- poucos campos por etapa
- linguagem direta
- acessibilidade real
- bom funcionamento em celular
- componentes consistentes
- estados vazios, erros e confirmacoes bem tratados

### Stack de UI

- `Tailwind CSS 4`
- `Headless UI`
- componentes internos do projeto

Evitar depender cedo de um kit visual pesado que dite toda a interface.

---

## Modulos Provaveis da V1

- portal publico de denuncia anonima
- protocolo ou codigo de acompanhamento
- painel interno autenticado
- triagem inicial
- classificacao da denuncia
- registro de atendimento
- anexos
- historico de movimentacoes
- relatorios basicos

---

## Roadmap Tecnico Inicial

### Fase 1

- criar o projeto Laravel
- configurar PostgreSQL, Redis e ambiente local com Docker
- instalar `Inertia`, `Vue`, `Tailwind` e stack de autenticacao interna
- definir padroes de logs e auditoria
- modelar o nucleo do dominio

### Fase 2

- construir fluxo publico de denuncia
- construir painel interno minimo
- adicionar anexos e filas
- adicionar observabilidade operacional

### Fase 3

- relatorios
- filtros avancados
- importacao do legado, se ainda fizer sentido
- endurecimento de seguranca para producao

---

## Convencoes de Codigo

### Backend

- controllers curtos
- regra de negocio fora de controller
- validacao em `Form Requests`
- testes para casos criticos
- enums para estados e tipos controlados
- evitar helpers espalhados sem dono claro

### Frontend

- componentes pequenos
- formularios desacoplados da pagina quando crescerem
- evitar logica pesada dentro de template
- tipagem clara nas props e contratos
- reutilizacao por composables e componentes base

---

## Decisoes Ja Recomendadas

- `Laravel + Inertia + Vue` em vez de frontend separado no inicio
- `PostgreSQL` como banco principal
- `Redis` para fila, cache e rate limiting
- `Sanctum` para autenticacao do painel
- `Pulse` e `Horizon` como base operacional
- `Telescope` somente em ambiente nao produtivo

---

## Itens Para Preencher Depois

### Produto

- nome do sistema:
- escopo da V1:
- perfis de usuario:
- canais de entrada da denuncia:
- necessidade de protocolo de acompanhamento:
- necessidade de anexos:
- necessidade de geolocalizacao:

### Seguranca e compliance

- politica de retencao:
- politica de LGPD:
- quem pode ver o que:
- quais eventos devem gerar auditoria:
- o que pode ou nao entrar em log:

### Operacao

- ambiente de hospedagem:
- estrategia de backup:
- estrategia de monitoramento:
- volume esperado de denuncias:
- necessidade de multiunidade ou multiorgao:

### Integracoes

- email:
- SMS ou WhatsApp:
- storage de anexos:
- autenticacao corporativa:
- integracoes externas:

---

## Proximo Passo Recomendado

Depois de revisar este documento, o proximo passo e criar um segundo arquivo com o escopo funcional da V1, por exemplo:

- fluxo da denuncia
- fluxo interno de triagem
- papeis e permissoes
- entidades principais
- regras de negocio obrigatorias

Nome sugerido:

- `V1_ESCOPO.md`
