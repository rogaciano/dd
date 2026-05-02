# Modernizacao do Legado para o Banco Novo

Atualizado em: 2026-05-01

## Objetivo

Melhorar a projecao do legado no `disque_denuncia_novo` sem copiar cegamente o desenho do SQL Server 2008.

O criterio usado aqui e:

- nao espelhar tabela legada so porque ela existe
- preservar o significado do dado quando o legado usava codigo auxiliar
- evitar substituir dominio por `varchar(255)` quando a informacao e controlada
- manter flexibilidade onde o dado e naturalmente textual ou incompleto

## Diagnostico do estado atual

O modelo novo ja fez boas simplificacoes, mas ainda havia pontos em que o legado estava sendo achatado demais:

- `envolvidos.env_pele`, `env_estatura`, `env_olhos`, `env_cabelo`, `env_porte`
  - no importador estavam virando textos como `pele 3`, `cabelo 2`
  - isso preservava o codigo, mas perdia o significado do catalogo
- `quantifica_resultado`
  - antes virava `resultado_quantificacoes` com `rotulo`, `unidade` e `observacoes`
  - agora tambem recebe catalogos e FKs estruturadas
- `difusao_tipo`
  - antes existia no legado, mas nao estava representada explicitamente no novo
- `correlatas`
  - antes nao tinha equivalente proprio no banco novo
- `atendimento` e `atendimento_tipo`
  - ainda nao viraram uma entidade moderna separada

Por outro lado, alguns catalogos `aux_` do legado nao devem virar FK no core novo:

- `aux_uf`
- `aux_municipio`
- `aux_bairro`
- `aux_subbairro`
- `aux_logradouro`

Essas tabelas do legado sao snapshots operacionais antigos. Para o sistema novo, o local da denuncia continua melhor representado por texto estruturado, com liberdade de preenchimento e futura evolucao para codigos IBGE/georreferenciamento se necessario.

## Matriz de decisao

| Legado | Situacao no novo | Decisao |
|---|---|---|
| `aux_pele`, `aux_estatura`, `aux_olhos`, `aux_cabelo`, `aux_porte` | Estavam achatados em texto | Normalizar em catalogos proprios com FK em `denuncia_envolvidos` |
| `aux_uf`, `aux_municipio`, `aux_bairro`, `aux_subbairro`, `aux_logradouro` | Ja simplificados em `denuncia_locais` | Manter como texto estruturado; nao espelhar 1:1 |
| `xpto`, `xpto_denuncia` | Ja reinterpretados | Continuar tratando como `etiquetas` |
| `classificacao` | Parcialmente absorvida por assuntos/status | Nao criar espelho 1:1 agora; revisar depois como classificacao operacional |
| `difusao_tipo` | Ja representado | Absorver em `tipos_encaminhamento` e FK em `encaminhamentos` |
| `correlatas` | Ja representado | Absorver em `denuncia_vinculos` |
| `atendimento`, `atendimento_tipo` | Sem entidade nova | Criar depois `interacoes` ou `manifestacoes` |
| `item_classe`, `item_tipo`, `item`, `unidades_metricas`, `quantifica_resultado` | Ja representados | Absorver em catalogos e FKs de `resultado_quantificacoes` |

## Executado nesta rodada

### 1. Catalogos fisicos de envolvidos

Foram criadas tabelas novas:

- `cores_pele`
- `faixas_estatura`
- `cores_olhos`
- `tipos_cabelo`
- `portes_fisicos`

Cada uma delas guarda:

- `nome`
- `slug`
- `ativo`
- `ordem_exibicao`
- `origem_legado_id`

Isso permite:

- importar do legado sem perder o significado
- usar selects controlados no sistema novo
- manter rastreabilidade com o codigo original

### 2. FKs em `denuncia_envolvidos`

`denuncia_envolvidos` passou a aceitar:

- `cor_pele_id`
- `faixa_estatura_id`
- `cor_olhos_id`
- `tipo_cabelo_id`
- `porte_fisico_id`

Os campos textuais antigos foram mantidos por compatibilidade e por fallback manual:

- `cor_pele`
- `estatura`
- `olhos`
- `cabelo`
- `porte_fisico`

Regra pratica:

- FK guarda o dado estruturado
- texto continua servindo como snapshot legivel e fallback para digitacao livre

### 3. Importador legado melhorado

O comando `legado:importar-carga` agora:

- importa os catalogos fisicos do legado antes dos envolvidos
- cria/atualiza os catalogos no banco novo
- preenche tanto o texto legivel quanto a FK correspondente

Exemplo:

- antes: `cor_pele = "pele 3"`
- agora: `cor_pele = "Parda"` e `cor_pele_id = ...` quando o catalogo existir

### 4. `correlatas` virou `denuncia_vinculos`

Foi criada a tabela:

- `denuncia_vinculos`

Com colunas centrais:

- `denuncia_origem_id`
- `denuncia_relacionada_id`
- `tipo`
- `observacoes`
- `origem_legado_id`
- `origem_legado_tabela`

Isso permite:

- relacionar denuncias sem depender do desenho antigo
- manter rastreabilidade da linha original da tabela `correlatas`
- evoluir depois para tipos mais ricos de vinculo

### 5. `difusao_tipo` virou catalogo proprio

Foi criada a tabela:

- `tipos_encaminhamento`

E `encaminhamentos` passou a aceitar:

- `tipo_encaminhamento_id`
- `origem_legado_id`
- `origem_legado_tabela`

Regra adotada:

- `orgao.tipo` define se o encaminhamento e interno ou externo
- `tipo_encaminhamento_id` define a natureza do encaminhamento herdada de `difusao_tipo`
- `tipo` textual fica apenas como compatibilidade temporaria

### 6. Quantificacao de resultado ficou estruturada

Foram criadas tabelas novas:

- `classes_item_resultado`
- `tipos_item_resultado`
- `itens_resultado`
- `unidades_medida`

E `resultado_quantificacoes` passou a aceitar:

- `classe_item_resultado_id`
- `tipo_item_resultado_id`
- `item_resultado_id`
- `unidade_medida_id`
- `origem_legado_id`
- `origem_legado_tabela`

Regra adotada:

- os campos textuais `rotulo` e `unidade` permanecem como snapshot legivel
- as FKs preservam a estrutura de `item_classe`, `item_tipo`, `item` e `unidades_metricas`

## O que ainda recomendo melhorar em seguida

### Prioridade restante: atendimento legado

`atendimento` no legado mistura:

- informacao que nao virou denuncia
- complemento operacional
- interacao humana

No novo sistema isso merece uma entidade propria, em vez de ficar escondido em movimentacao generica.

## Regra de arquitetura adotada

Nem todo catalogo legado deve sobreviver como tabela nova.

Usar tabela propria quando:

- o valor e controlado
- o valor aparece em filtros, relatorios ou selects
- ha risco real de divergencia sem normalizacao

Usar texto estruturado quando:

- o dado e incompleto
- o dado varia demais
- o sistema novo nao deve ficar preso ao snapshot legado

## Arquivos impactados nesta rodada

- `sistema/app/Console/Commands/ImportarCargaLegado.php`
- `sistema/app/Models/DenunciaEnvolvido.php`
- `sistema/app/Models/CorPele.php`
- `sistema/app/Models/FaixaEstatura.php`
- `sistema/app/Models/CorOlhos.php`
- `sistema/app/Models/TipoCabelo.php`
- `sistema/app/Models/PorteFisico.php`
- `sistema/app/Models/DenunciaVinculo.php`
- `sistema/app/Models/TipoEncaminhamento.php`
- `sistema/app/Models/ClasseItemResultado.php`
- `sistema/app/Models/TipoItemResultado.php`
- `sistema/app/Models/ItemResultado.php`
- `sistema/app/Models/UnidadeMedida.php`
- `sistema/database/migrations/2026_05_01_223000_create_catalogos_fisicos_envolvidos_table.php`
- `sistema/database/migrations/2026_05_01_223100_add_catalogo_fks_to_denuncia_envolvidos_table.php`
- `sistema/database/migrations/2026_05_01_223200_create_denuncia_vinculos_table.php`
- `sistema/database/migrations/2026_05_01_223300_create_tipos_encaminhamento_and_update_encaminhamentos_table.php`
- `sistema/database/migrations/2026_05_01_223400_create_catalogos_resultado_quantificacao_table.php`
