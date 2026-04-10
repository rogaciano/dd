# Importacao e Evolucao

## Objetivo

Este documento define a estrategia recomendada para trabalhar com o legado enquanto o sistema novo ainda esta em desenvolvimento.

O problema aqui nao e apenas importar dados. O problema real e permitir:

- reimportar o legado sempre que necessario
- evoluir o banco novo com migrations proprias
- preencher dados novos do sistema de forma separada
- evitar que a importacao destrua informacoes complementares criadas no sistema novo

---

## Principio Central

Nao importar o SQL Server direto para as tabelas finais da aplicacao.

Separar o fluxo em `3 camadas`:

1. `espelho legado`
2. `modelo novo da aplicacao`
3. `dados novos e complementares`

Essa separacao e o que vai permitir reimportar varias vezes sem baguncar o dominio novo.

---

## Camada 1: Espelho Legado

### Finalidade

Receber uma copia quase 1:1 do SQL Server antigo.

Essa camada existe para:

- preservar a origem
- permitir recarga completa
- servir como base de transformacao
- evitar acoplamento direto entre SQL Server e tabelas novas

### Recomendacao tecnica

Usar um banco PostgreSQL separado para o espelho do legado:

- `disque_denuncia_legado`

Esse banco recebe a copia quase 1:1 do SQL Server antigo.

### Como carregar

Usar os scripts ja criados para copiar o SQL Server para o PostgreSQL, apontando o destino para:

- banco `disque_denuncia_legado`
- schema `public`

Objetivo dessa camada:

- ser descartavel
- poder ser recarregada do zero
- nao receber ajustes manuais

### Regra

Tudo que estiver dentro do banco `disque_denuncia_legado` e dado de origem.

Nao editar manualmente.

---

## Camada 2: Modelo Novo da Aplicacao

### Finalidade

Representar o dominio novo do sistema.

Exemplos:

- `denuncias`
- `denuncia_locais`
- `denuncia_envolvidos`
- `assuntos`
- `etiquetas`
- `encaminhamentos`
- `resultados`

### Como evolui

Essa camada deve evoluir somente por:

- `Laravel migrations`
- `seeders`
- comandos de importacao/projecao controlados

### Regra

O banco novo da aplicacao nao deve depender do banco legado para existir.

Ele deve nascer e evoluir com:

- `php artisan migrate`
- `php artisan db:seed`

---

## Camada 3: Dados Novos e Complementares

### Finalidade

Armazenar tudo aquilo que:

- nao existe no legado
- existe de forma insuficiente no legado
- foi criado no sistema novo
- nao deve ser sobrescrito quando a importacao rodar de novo

Exemplos:

- novas etiquetas
- novos assuntos
- novos orgaos
- configuracoes de triagem
- auditoria nova
- campos internos criados na V1
- classificacoes novas
- anexos enviados no sistema novo

### Regra

Dados complementares nao devem ficar misturados com colunas inteiramente controladas pela importacao.

Se uma informacao pertence ao sistema novo, ela precisa ser tratada como dado do sistema novo.

---

## Fluxo Recomendado

### Passo 1: recarregar o espelho legado

Fluxo:

- SQL Server antigo -> banco `disque_denuncia_legado`

Comportamento esperado:

- pode truncar e recarregar
- pode ser executado varias vezes
- nao afeta diretamente as tabelas finais do sistema novo

### Passo 2: projetar o legado no modelo novo

Fluxo:

- `disque_denuncia_legado` -> tabelas novas da aplicacao em `disque_denuncia_novo`

Esse passo deve ser implementado por comandos controlados, por exemplo:

- `php artisan legado:importar-denuncias`
- `php artisan legado:importar-envolvidos`
- `php artisan legado:importar-encaminhamentos`

### Passo 3: preencher dados novos da aplicacao

Fluxo:

- seeders
- cadastros internos
- configuracoes de negocio
- enriquecimentos manuais ou automatizados

Esse passo nao deve depender de nova carga do legado para existir.

---

## Separacao de Responsabilidades

### A importacao controla

- campos copiados do legado
- vinculos originados do legado
- identificadores da origem
- atualizacao de snapshots importados

### A aplicacao nova controla

- status novos da V1
- etiquetas novas
- regras novas de triagem
- anexos novos
- auditoria nova
- configuracoes de acesso
- dados operacionais que nao existiam no sistema antigo

### O que nunca deve acontecer

- uma reimportacao apagar melhoria funcional da V1
- uma carga do legado sobrescrever dado produzido no sistema novo sem regra explicita

---

## Estrategia de Persistencia

## Opcao recomendada

### 1. Guardar referencia da origem

Nas tabelas novas que recebem dados importados, incluir campos como:

- `origem_legado_id`
- `origem_legado_tabela`
- `importado_em`
- `atualizado_em_importacao`

Opcional:

- `hash_origem`
- `dados_origem jsonb`

### 2. Fazer upsert por chave de origem

Nao usar o `id` interno da tabela nova como criterio de reconciliacao com o legado.

Usar:

- `origem_legado_tabela + origem_legado_id`

Ou, se a tabela for derivada de uma unica origem:

- `origem_legado_id` unico

### 3. Separar campos importados de campos novos

Se uma tabela nova misturar:

- informacao vinda do legado
- informacao criada apenas na nova aplicacao

entao a importacao precisa respeitar quais campos ela pode atualizar.

Exemplo:

- importacao atualiza `relato`, `recebida_em`, `protocolo_original`
- importacao nao atualiza `status_interno_v1`, `observacao_triagem`, `responsavel_usuario_id`, `encerrada_em_manual`

---

## Padrao Recomendado Para Evolucao

### Tipo A: tabela inteiramente importada

Exemplo:

- uma tabela de espelho ou snapshot tecnico

Comportamento:

- pode ser recriada
- pode ser recarregada integralmente

### Tipo B: tabela de dominio com base importada

Exemplo:

- `denuncias`

Comportamento:

- nasce de importacao
- depois passa a receber dados novos do sistema
- reimportacao faz apenas `upsert` controlado

### Tipo C: tabela nativa da nova aplicacao

Exemplos:

- `papeis`
- `logs_auditoria`
- `anexos`
- `etiquetas` novas

Comportamento:

- nunca deve depender do legado
- deve ser gerida apenas por migrations, seeders e operacao da aplicacao

---

## Seeds e Preenchimentos Separados

Essa parte e importante para o seu ponto sobre evoluir tabelas novas e preencher informacoes complementares de maneira separada.

### Categoria 1: seeds estruturais

Servem para subir a aplicacao.

Exemplos:

- `papeis`
- `tipos_resultado`
- `grupos_assunto`
- `assuntos` iniciais

Rodam com:

- `php artisan db:seed`

### Categoria 2: importadores de legado

Servem para projetar o banco `disque_denuncia_legado` no dominio novo.

Exemplos:

- importar denuncias
- importar envolvidos
- importar encaminhamentos

Rodam com comandos proprios:

- `php artisan legado:importar-*`

### Categoria 3: preenchimentos complementares

Servem para adicionar dados novos que nao vieram do legado.

Exemplos:

- novas etiquetas operacionais
- configuracoes de negocio
- orgaos criados depois
- classificacoes novas
- enriquecimento interno

Rodam separados dos importadores.

Sugestao:

- `php artisan sistema:popular-iniciais`
- `php artisan sistema:complementar-*`

---

## Como Evitar Sobrescrita Indevida

### Regra 1

Toda tabela importada precisa ter clareza sobre:

- quais colunas a importacao pode alterar
- quais colunas sao exclusivas do sistema novo

### Regra 2

Se uma informacao complementar for importante e instavel, prefira armazenar em tabela separada.

Exemplo:

- em vez de colocar toda triagem nova dentro de `denuncias`, criar `denuncia_triagens`

### Regra 3

Se houver risco de conflito, a importacao deve ser conservadora.

Melhor:

- deixar um campo sem atualizar

do que:

- sobrescrever dado novo da operacao atual

---

## Desenho Recomendado Para a V1

### Banco `disque_denuncia_legado`

Recebe:

- copia do SQL Server

Atualizado por:

- scripts Python atuais

### Banco `disque_denuncia_novo`

Recebe:

- migrations do Laravel
- tabelas novas da aplicacao
- dados importados ja transformados
- dados novos complementares

Atualizado por:

- migrations
- seeders
- comandos de importacao
- operacao normal do sistema

---

## Processo de Trabalho Recomendado

### Quando o legado mudar ou precisar ser recarregado

1. atualizar `disque_denuncia_legado`
2. rodar importadores da aplicacao
3. validar contagens e consistencia

### Quando o dominio novo evoluir

1. criar nova migration
2. ajustar importadores se necessario
3. ajustar seeders complementares se necessario
4. rerodar apenas o que mudou

### Quando surgirem dados novos do sistema

1. criar tabela nova ou seeder novo
2. nao acoplar isso ao espelho legado
3. manter comando separado de preenchimento

---

## Comandos Recomendados no Futuro

### Espelho legado

- `python migrate_copy.py --truncate --validate-counts`

Observacao:

- esse fluxo deve apontar para o banco `disque_denuncia_legado`

### Aplicacao nova

- `php artisan migrate`
- `php artisan db:seed`

### Projecao do legado no dominio novo

- `php artisan legado:importar-denuncias`
- `php artisan legado:importar-envolvidos`
- `php artisan legado:importar-encaminhamentos`

### Complementos do sistema novo

- `php artisan sistema:popular-iniciais`
- `php artisan sistema:popular-etiquetas`
- `php artisan sistema:popular-orgaos`

---

## Decisao Recomendada

O caminho mais seguro e escalavel para este projeto e:

1. usar o PostgreSQL como ponto de convergencia
2. manter `disque_denuncia_legado` como espelho descartavel e recarregavel
3. manter o dominio novo separado e evoluindo por migrations
4. tratar importacao e preenchimento complementar como processos diferentes

Assim voce consegue:

- continuar importando enquanto desenvolve
- mudar o modelo novo sem depender do desenho antigo
- rerodar cargas sem destruir o que a nova aplicacao ja produziu

---

## Proximo Passo Recomendado

Depois deste documento, os proximos passos mais uteis sao:

1. ajustar a estrategia atual para carregar em `disque_denuncia_legado`
2. definir quais tabelas novas precisam de `origem_legado_id`
3. criar o primeiro importador do dominio novo, por exemplo `denuncias`
