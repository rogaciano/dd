# Modelo de Dominio

## Objetivo

Este documento traduz o [V1_ESCOPO.md](e:\projetos\disque\V1_ESCOPO.md) em um modelo de dominio inicial para o sistema novo.

O foco aqui e definir:

- entidades principais
- relacionamentos
- agregado central
- nomenclatura recomendada
- ordem de implementacao da V1

---

## Convencao Recomendada

### Linguagem dos nomes tecnicos

Recomendacao:

- tabelas e campos de dominio em portugues
- nomes sem acentos
- `snake_case`

Exemplos:

- tabela: `denuncias`
- model: `Denuncia`
- campo: `responsavel_usuario_id`

### Excecoes praticas

Para reduzir atrito com Laravel e com convencoes tecnicas amplamente conhecidas, vale manter:

- `id`
- `created_at`
- `updated_at`
- `deleted_at` se algum dia entrar
- termos tecnicos como `checksum`, `mime_type`, `jsonb`

Essa combinacao deixa o dominio legivel para a equipe e evita customizacao desnecessaria do framework.

---

## Agregado Principal

O agregado raiz da V1 deve ser `Denuncia`.

Tudo o que representa a vida util do caso orbita essa entidade:

- local
- envolvidos
- assuntos
- etiquetas
- anexos
- encaminhamentos
- movimentacoes
- resultados

---

## Entidades Principais

### 1. `usuarios`

Usuarios internos do backoffice.

Responsabilidades:

- autenticacao
- autoria de acoes
- atribuicao operacional

Campos provaveis:

- `id`
- `nome`
- `login`
- `email`
- `senha`
- `status`
- `ultimo_login_em`
- `created_at`
- `updated_at`

### 2. `papeis`

Papeis de acesso do sistema.

Exemplos:

- `administrador`
- `supervisor`
- `analista`
- `atendente`
- `visualizador`

Relacionamento:

- `usuarios` N:N `papeis`

Tabela de apoio:

- `papel_usuario`

### 3. `denuncias`

Entidade central do dominio.

Responsabilidades:

- representar a denuncia
- concentrar protocolo, relato e estado operacional
- servir como centro de todos os desdobramentos

Campos provaveis:

- `id`
- `protocolo`
- `token_acompanhamento_hash`
- `canal`
- `status`
- `prioridade`
- `urgente`
- `resumo`
- `relato`
- `recebida_em`
- `enviada_em`
- `criada_por_usuario_id`
- `responsavel_usuario_id`
- `ip_hash`
- `user_agent_hash`
- `triada_em`
- `encerrada_em`
- `created_at`
- `updated_at`

### 4. `denuncia_locais`

Local principal da denuncia.

Relacionamento:

- `denuncias` 1:1 `denuncia_locais`

Campos provaveis:

- `id`
- `denuncia_id`
- `pais_codigo`
- `uf`
- `municipio`
- `bairro`
- `subbairro`
- `logradouro_tipo`
- `logradouro_nome`
- `numero`
- `complemento`
- `cep`
- `referencia`
- `endereco_manual`
- `latitude`
- `longitude`
- `created_at`
- `updated_at`

### 5. `denuncia_envolvidos`

Pessoas ligadas a denuncia.

Relacionamento:

- `denuncias` 1:N `denuncia_envolvidos`

Campos provaveis:

- `id`
- `denuncia_id`
- `papel_no_caso`
- `nome`
- `apelido`
- `sexo`
- `idade_estimada`
- `cor_pele`
- `cor_pele_id`
- `estatura`
- `faixa_estatura_id`
- `olhos`
- `cor_olhos_id`
- `cabelo`
- `tipo_cabelo_id`
- `porte_fisico`
- `porte_fisico_id`
- `sinais_particulares`
- `observacoes`
- `descricao_endereco`
- `created_at`
- `updated_at`

Observacao:

- os textos continuam como snapshot legivel e fallback manual
- os campos `*_id` apontam para catalogos fisicos normalizados

### 6. `grupos_assunto`

Agrupador tematico principal.

Equivale aproximadamente a `classe de assunto` do legado.

Campos provaveis:

- `id`
- `nome`
- `slug`
- `ativo`
- `ordem_exibicao`
- `created_at`
- `updated_at`

### 7. `assuntos`

Assunto operacional.

Equivale aproximadamente a `tipo de assunto` do legado.

Relacionamento:

- `grupos_assunto` 1:N `assuntos`

Campos provaveis:

- `id`
- `grupo_assunto_id`
- `nome`
- `slug`
- `ativo`
- `ordem_exibicao`
- `created_at`
- `updated_at`

### 8. `denuncia_assuntos`

Vinculo entre denuncia e assunto.

Relacionamento:

- `denuncias` N:N `assuntos`

Campos provaveis:

- `id`
- `denuncia_id`
- `assunto_id`
- `principal`
- `criado_por_usuario_id`
- `created_at`

Observacao:

- uma denuncia pode ter varios assuntos
- apenas um deve ser principal

### 9. `etiquetas`

Etiqueta operacional transversal.

Origem conceitual:

- substitui o papel do `xpto`

Relacionamento:

- `denuncias` N:N `etiquetas`

Campos provaveis:

- `id`
- `nome`
- `slug`
- `cor`
- `ativo`
- `created_at`
- `updated_at`

### 10. `denuncia_etiqueta`

Pivot entre denuncia e etiqueta.

Campos provaveis:

- `denuncia_id`
- `etiqueta_id`
- `criado_por_usuario_id`
- `created_at`

### 11. `orgaos`

Destinos de encaminhamento.

Modelagem sugerida:

- uma tabela unica para orgaos internos e externos

Campos provaveis:

- `id`
- `nome`
- `tipo`
- `categoria`
- `email_destino`
- `contato_destino`
- `endereco`
- `municipio`
- `uf`
- `cep`
- `ativo`
- `created_at`
- `updated_at`

### 12. `encaminhamentos`

Encaminhamentos de uma denuncia.

Relacionamento:

- `denuncias` 1:N `encaminhamentos`
- `orgaos` 1:N `encaminhamentos`

Campos provaveis:

- `id`
- `denuncia_id`
- `orgao_id`
- `tipo_encaminhamento_id`
- `tipo`
- `status`
- `enviado_em`
- `prazo_em`
- `observacoes`
- `criado_por_usuario_id`
- `origem_legado_id`
- `origem_legado_tabela`
- `created_at`
- `updated_at`

Observacao:

- `interno` e `externo` devem vir do `orgao`
- `tipo` textual fica como compatibilidade temporaria
- `difusao_tipo` deve ser absorvido por `tipos_encaminhamento`

### 12A. `tipos_encaminhamento`

Catalogo estruturado de tipos de encaminhamento.

Campos provaveis:

- `id`
- `nome`
- `slug`
- `ativo`
- `ordem_exibicao`
- `origem_legado_id`
- `created_at`
- `updated_at`

### 12B. `denuncia_vinculos`

Vinculos entre denuncias relacionadas.

Campos provaveis:

- `id`
- `denuncia_origem_id`
- `denuncia_relacionada_id`
- `tipo`
- `observacoes`
- `origem_legado_id`
- `origem_legado_tabela`
- `created_at`
- `updated_at`

### 13. `denuncia_movimentacoes`

Historico operacional da denuncia.

Relacionamento:

- `denuncias` 1:N `denuncia_movimentacoes`

Campos provaveis:

- `id`
- `denuncia_id`
- `tipo`
- `titulo`
- `conteudo`
- `visibilidade`
- `criado_por_usuario_id`
- `created_at`

Uso:

- complemento
- observacao interna
- mudanca de status
- registro de triagem

### 14. `tipos_resultado`

Catalogo de tipos de resultado.

Campos provaveis:

- `id`
- `nome`
- `slug`
- `ativo`
- `ordem_exibicao`
- `created_at`
- `updated_at`

### 15. `resultados`

Resultados vinculados a uma denuncia.

Relacionamento:

- `denuncias` 1:N `resultados`
- `tipos_resultado` 1:N `resultados`

Campos provaveis:

- `id`
- `denuncia_id`
- `tipo_resultado_id`
- `orgao_id`
- `registrado_em`
- `efetivado_em`
- `descricao`
- `criado_por_usuario_id`
- `created_at`
- `updated_at`

### 16. `resultado_quantificacoes`

Quantificacao simples do resultado.

Relacionamento:

- `resultados` 1:N `resultado_quantificacoes`

Campos provaveis:

- `id`
- `resultado_id`
- `classe_item_resultado_id`
- `tipo_item_resultado_id`
- `item_resultado_id`
- `rotulo`
- `quantidade`
- `unidade_medida_id`
- `unidade`
- `observacoes`
- `origem_legado_id`
- `origem_legado_tabela`
- `created_at`
- `updated_at`

Observacao:

- os textos `rotulo` e `unidade` continuam uteis como snapshot legivel
- as FKs estruturam a origem de `item_classe`, `item_tipo`, `item` e `unidades_metricas`

### 16A. `classes_item_resultado`

Catalogo das classes de itens quantificados.

### 16B. `tipos_item_resultado`

Catalogo dos tipos de itens quantificados.

### 16C. `itens_resultado`

Catalogo dos itens quantificados.

### 16D. `unidades_medida`

Catalogo das unidades de medida de resultados.

### 17. `anexos`

Arquivos ligados a denuncia.

Relacionamento:

- `denuncias` 1:N `anexos`

Campos provaveis:

- `id`
- `denuncia_id`
- `disco`
- `caminho`
- `nome_original`
- `mime_type`
- `tamanho`
- `checksum`
- `enviado_por_usuario_id`
- `enviado_em`
- `created_at`
- `updated_at`

### 18. `logs_auditoria`

Trilha de auditoria de negocio.

Campos provaveis:

- `id`
- `usuario_id`
- `evento`
- `entidade_tipo`
- `entidade_id`
- `descricao`
- `ip_hash`
- `metadados`
- `created_at`

Observacao:

- auditoria de negocio nao substitui log tecnico da aplicacao

---

## Relacionamentos Principais

- `usuarios` N:N `papeis`
- `denuncias` 1:1 `denuncia_locais`
- `denuncias` 1:N `denuncia_envolvidos`
- `denuncias` N:N `assuntos`
- `denuncias` N:N `etiquetas`
- `denuncias` 1:N `encaminhamentos`
- `denuncias` 1:N `denuncia_movimentacoes`
- `denuncias` 1:N `resultados`
- `denuncias` 1:N `anexos`
- `resultados` 1:N `resultado_quantificacoes`

---

## Estados e Tipos Recomendados

### `denuncias.status`

Sugestao inicial:

- `rascunho`
- `recebida`
- `triagem`
- `classificada`
- `encaminhada`
- `em_andamento`
- `resolvida`
- `encerrada`
- `arquivada`

### `denuncias.prioridade`

Sugestao inicial:

- `baixa`
- `normal`
- `alta`
- `critica`

### `denuncia_movimentacoes.tipo`

Sugestao inicial:

- `nota`
- `mudanca_status`
- `complemento`
- `triagem`
- `sistema`

### `denuncia_movimentacoes.visibilidade`

Sugestao inicial:

- `interna`
- `restrita`

### `encaminhamentos.tipo`

Sugestao inicial:

- `operacional`
- `informativo`

### `encaminhamentos.status`

Sugestao inicial:

- `pendente`
- `enviado`
- `confirmado`
- `concluido`
- `cancelado`

---

## Regras de Modelagem

### 1. Nao duplicar assunto e etiqueta

- `assunto` classifica o tema principal da denuncia
- `etiqueta` agrupa casos por recorte operacional transversal

### 2. Nao copiar relatorios do legado para o banco novo

Nada equivalente a:

- `relatorios`
- `buscas`
- `graficos`

Esses recursos devem morar na aplicacao.

### 3. Nao usar contadores manuais

- IDs tecnicos ficam com o banco
- protocolo publico e uma regra de negocio

### 4. Nao misturar historico do caso com auditoria

- `denuncia_movimentacoes` contam a historia operacional
- `logs_auditoria` contam quem fez o que

### 5. Comecar simples nos catalogos auxiliares

So promover para tabela propria quando houver valor real para:

- consistencia
- filtro
- governanca

---

## Itens Fora da V1

- `denuncia_vinculos`
- modulo especifico de violencia domestica
- modulo especifico de veiculos
- dashboards complexos
- buscas salvas
- regras automaticas de distribuicao

---

## Ordem Sugerida de Implementacao

### Bloco 1

- `usuarios`
- `papeis`
- `papel_usuario`

### Bloco 2

- `denuncias`
- `denuncia_locais`
- `denuncia_envolvidos`

### Bloco 3

- `grupos_assunto`
- `assuntos`
- `denuncia_assuntos`
- `etiquetas`
- `denuncia_etiqueta`

### Bloco 4

- `orgaos`
- `encaminhamentos`
- `denuncia_movimentacoes`
- `anexos`

### Bloco 5

- `tipos_resultado`
- `resultados`
- `resultado_quantificacoes`
- `logs_auditoria`

---

## Mapa Rapido do Legado Para o Novo Modelo

- `denuncia` -> `denuncias`
- dados de local em `denuncia` -> `denuncia_locais`
- `envolvidos` -> `denuncia_envolvidos`
- `assunto_classe` -> `grupos_assunto`
- `assunto_tipo` -> `assuntos`
- `assunto_denuncia` -> `denuncia_assuntos`
- `xpto` e `xpto_denuncia` -> `etiquetas` e `denuncia_etiqueta`
- `difusao_*` -> `encaminhamentos`
- `orgaos_*` -> `orgaos`
- `resultado_*` -> `resultados`
- `quantifica_resultado` -> `resultado_quantificacoes`
- `denuncia_com` -> `denuncia_movimentacoes`
- `log_alteracoes` -> `logs_auditoria`

---

## Proximo Passo Recomendado

Depois deste documento, o proximo passo mais util e gerar as primeiras migrations da V1 com essa nomenclatura:

1. acesso e papeis
2. denuncia e local
3. assuntos e etiquetas
4. envolvidos e movimentacoes
5. encaminhamentos e resultados
