# Escopo V1

## Objetivo

Este documento separa o que parece ser:

- `core` do produto
- modulo satelite
- mecanismo legado que nao vale levar para a arquitetura nova

Base de leitura utilizada:

- `Manual DD.pdf`
- `schemas.sql`
- `relationship_map.md`

O objetivo nao e reescrever o sistema antigo tela por tela. O objetivo e preservar o valor de negocio e descartar a forma antiga de implementacao.

---

## Leitura do Sistema Legado

Pelo manual e pelo schema, o fluxo principal do sistema antigo e:

1. operador faz login
2. registra uma denuncia
3. descreve o relato
4. informa local de ocorrencia
5. cadastra envolvidos
6. classifica por assunto
7. marca urgencia quando necessario
8. encaminha a denuncia para orgaos internos ou externos
9. registra resultados e desdobramentos
10. faz atendimento ou complemento quando a informacao nao nasce como nova denuncia

Esse e o fluxo que realmente importa.

O restante do legado mistura:

- tabelas auxiliares de catalogo
- travas operacionais do sistema antigo
- contadores manuais
- relatorios e buscas salvos no banco
- modulos muito especificos do contexto operacional da epoca

---

## Core do Produto Novo

O `core` de negocio do projeto novo, na minha leitura, e este:

### 1. Denuncia

Entidade central do sistema.

Deve representar:

- relato
- data e hora de recebimento
- canal de entrada
- situacao atual
- prioridade
- contexto de anonimato
- protocolo de acompanhamento

Base legada principal:

- `denuncia`

### 2. Local da Ocorrencia

Faz parte da denuncia, mas deve ser modelado como bloco proprio.

Deve suportar:

- UF
- municipio
- bairro
- logradouro
- numero
- complemento
- referencia
- endereco manual quando necessario

Base legada principal:

- campos de local em `denuncia`
- catalogos `aux_uf`, `aux_municipio`, `aux_bairro`, `aux_subbairro`, `aux_logradouro`

### 3. Envolvidos

Representa pessoas ligadas a denuncia.

Deve suportar:

- nome ou identificacao parcial
- apelido
- sexo
- idade aproximada
- caracteristicas fisicas
- endereco quando informado
- observacoes

Base legada principal:

- `envolvidos`
- catalogos fisicos auxiliares

### 4. Classificacao Tematica

Toda denuncia precisa ser classificada para triagem e operacao.

Deve suportar:

- categoria principal
- categoria secundaria
- marcadores de urgencia
- etiquetas operacionais quando realmente fizer sentido

Base legada principal:

- `assunto_classe`
- `assunto_tipo`
- `assunto_denuncia`
- `classificacao`

### 5. Encaminhamento

Parte essencial do produto, porque a denuncia so gera valor operacional quando vira encaminhamento rastreavel.

Deve suportar:

- encaminhamento interno
- encaminhamento externo
- data e hora
- responsavel
- tipo de encaminhamento
- status do encaminhamento

Base legada principal:

- `difusao_interna`
- `difusao_externa`
- `difusao_tipo`
- `orgaos_internos`
- `orgaos_externos`
- `orgaos_externos_tipos`

### 6. Evolucao e Resultado

A denuncia precisa ter historico e fechamento operacional.

Deve suportar:

- movimentacoes
- mudanca de status
- resultado registrado
- quantificacao quando aplicavel
- data, usuario e observacao

Base legada principal:

- `resultado_direto`
- `resultado_indireto`
- `resultado_tipo`
- `quantifica_resultado`
- `log_alteracoes`

### 7. Atendimento Interno

No legado, `atendimento` representa informacao que nao virou denuncia ou uma interacao complementar.

No sistema novo, isso nao precisa existir com o mesmo desenho antigo, mas a capacidade de registrar interacoes internas continua importante.

Base legada principal:

- `atendimento`
- `atendimento_tipo`
- `denuncia_com`
- `correlatas`

### 8. Seguranca, Auditoria e Backoffice

Nao e o `core` de negocio, mas e obrigatorio para o produto existir com seguranca.

Deve suportar:

- autenticacao do painel interno
- autorizacao por papel e permissao
- trilha de auditoria
- logs tecnicos com contexto
- rate limiting
- rastreabilidade de alteracoes

Base legada principal:

- `usuarios`
- `usuarios_tipos`
- `acesso`
- `rotinas`
- `log_alteracoes`

---

## O Que Entra na V1

### Obrigatorio

- portal publico para registrar denuncia anonima
- protocolo seguro para acompanhamento
- painel interno autenticado
- fila de triagem
- classificacao por assunto
- local de ocorrencia
- cadastro de envolvidos
- encaminhamento interno e externo
- historico da denuncia
- registro de resultado
- busca e filtro basicos
- auditoria minima
- logs tecnicos e monitoramento

### Fortemente recomendado ja na V1

- anexos
- mascaramento de dados sensiveis no painel
- fila assincrona para notificacoes e processamento
- controle de acesso por perfil
- status padronizados da denuncia

### Pode ficar para V1.1

- dashboards gerenciais
- relatorios avancados
- buscas salvas por usuario
- correlacao rica entre casos
- modulos especializados por tema

---

## Modulos Satelite

Esses itens fazem sentido no dominio, mas nao parecem ser o nucleo universal do produto.

### `dd_mulher`

Faz sentido como modulo tematico ou formulario especializado, nao como centro da arquitetura.

Se houver exigencia de negocio real, entra como extensao da denuncia, nao como base do modelo inteiro.

### `veiculos`

Roubo e furto de veiculo e um subtipo de ocorrencia.

Faz mais sentido como modulo complementar da denuncia do que como parte obrigatoria da V1, a menos que esse uso seja muito frequente no negocio.

### `xpto`

No legado, parece funcionar como marcador de temas em evidencia ou campanhas operacionais.

No sistema novo, a melhor leitura e tratar isso como `etiqueta`.

Modelo sugerido:

- uma denuncia pode ter `N` etiquetas
- uma etiqueta pode estar vinculada a `N` denuncias
- a relacao deve ser `N:N`

Uso sugerido:

- temas em evidencia
- campanhas operacionais
- agrupamentos transversais que nao substituem a classificacao principal

Observacao:

- `etiqueta` nao substitui `assunto`, `classe` ou `tipo`
- `etiqueta` complementa a classificacao
- se o negocio realmente usa esse conceito, vale entrar ja na primeira versao

### `correlatas` e `complemento`

A necessidade de relacionar casos e registrar complemento e real.

Mas o desenho antigo nao precisa ser preservado. O sistema novo pode resolver isso melhor com:

- historico de interacoes
- vinculos entre casos
- merge ou referencia cruzada

---

## O Que e Legado e Nao Deve Ser Levado Como Esta

### Controle tecnico antigo

- `numeracao`
- `chaves`
- `lock_registro`
- `acesso_tempo`

Essas tabelas refletem limitações de implementacao antiga.

No sistema novo:

- numeracao deve ser tratada pelo banco e pela aplicacao
- protocolo deve ser um identificador de negocio seguro
- travas de edicao devem ser revistas
- se houver concorrencia, preferir abordagem moderna de auditoria ou bloqueio curto e explicito

### Permissoes modeladas por rotinas antigas

- `rotinas`
- `acesso`
- parte de `usuarios_tipos`

Isso deve virar um modelo moderno de:

- papeis
- permissoes
- policies

### Relatorios e buscas persistidos como SQL no banco

- `relatorios`
- `buscas`
- `graficos`

Isso e forte sinal de legado tecnico.

No sistema novo:

- consultas devem morar no codigo
- relatorios devem usar filtros tipados
- dashboards devem ser calculados pela aplicacao
- nao vale armazenar SQL livre no banco

### Catalogos auxiliares que podem ser reavaliados

- `aux_*`
- `vei_marca`
- `vei_modelo`
- `item_*`
- `unidades_metricas`

Parte desses catalogos continua util.

Mas nao vale copiar tudo automaticamente. O certo e validar:

- quais realmente entram na V1
- quais podem ser substituidos por tabelas mais limpas
- quais podem ser integrados com fontes externas ou seeds

---

## Redesenho Moderno Sugerido

### Entidades provaveis do novo dominio

- `usuarios`
- `papeis`
- `denuncias`
- `denuncia_locais`
- `denuncia_envolvidos`
- `grupos_assunto`
- `assuntos`
- `encaminhamentos`
- `orgaos`
- `denuncia_movimentacoes`
- `resultados`
- `anexos`
- `logs_auditoria`

### Observacao importante

Essas entidades nao precisam ser 1:1 com as tabelas antigas.

Exemplos:

- `denuncia_com` pode virar parte de `denuncia_movimentacoes`
- `log_alteracoes` pode virar `logs_auditoria`
- `correlatas` pode virar `denuncia_vinculos`
- `etiquetas` ou `denuncia_etiqueta`

---

## Mapa de Decisao

### Manter como conceito de negocio

- denuncia
- local da ocorrencia
- envolvidos
- assunto e classificacao
- encaminhamento
- resultado
- atendimento interno

### Manter, mas redesenhar

- usuarios e permissoes
- historico
- complemento
- correlacao entre casos
- questionarios especializados
- catalogos auxiliares

### Nao levar como implementacao

- contadores manuais
- bloqueios tecnicos antigos
- SQL de relatorio salvo no banco
- grafico salvo como imagem no banco
- permissao baseada em rotina legada

---

## Proposta de Recorte Funcional da V1

### Fluxo publico

- abrir denuncia anonima
- preencher relato
- informar local
- informar envolvidos
- anexar evidencias, se habilitado
- receber protocolo

### Fluxo interno

- autenticar no painel
- localizar denuncia
- classificar
- priorizar
- encaminhar
- registrar andamento
- registrar resultado

### Fluxo de governanca

- auditar acessos e alteracoes
- filtrar casos por status, categoria e periodo
- operar com seguranca e rastreabilidade

---

## Riscos de Copiar o Legado Sem Redesenho

- repetir acoplamentos desnecessarios
- manter regras escondidas em campos pouco claros
- perpetuar modulos de baixo valor
- carregar nomenclatura confusa
- comprometer seguranca e manutenibilidade

---

## Decisao Recomendada

O produto novo deve nascer com foco em:

- `denuncia`
- `triagem`
- `encaminhamento`
- `resultado`
- `auditoria`

Todo o resto deve ser avaliado por valor real de negocio, nao por existencia no legado.

---

## Proximos Passos

1. transformar este recorte em entidades e regras da V1
2. desenhar os papeis do painel interno
3. definir o fluxo do usuario anonimo
4. decidir o que entra como modulo opcional
5. modelar o banco novo sem compromisso com o schema legado
