# Mapa de Relacionamentos

Gerado automaticamente a partir de `schemas.sql`.

## Resumo

- Tabelas analisadas: `54`
- Relacionamentos explicitos: `5`
- Relacionamentos inferidos: `57`
- Pontos de revisao manual: `6`

## Relacionamentos Explicitos

- `assunto_denuncia.ass_tpa_cd` -> `assunto_tipo.tpa_cd` [explicit, explicit]: Constraint FOREIGN KEY declarada no SQL Server.
- `assunto_tipo.tpa_cla_cd` -> `assunto_classe.cla_cd` [explicit, explicit]: Constraint FOREIGN KEY declarada no SQL Server.
- `difusao_externa.dex_ext_cd` -> `orgaos_externos.ext_cd` [explicit, explicit]: Constraint FOREIGN KEY declarada no SQL Server.
- `orgaos_externos.ext_oet_cd` -> `orgaos_externos_tipos.oet_cd` [explicit, explicit]: Constraint FOREIGN KEY declarada no SQL Server.
- `vei_modelo.mar_cd` -> `vei_marca.mar_cd` [explicit, explicit]: Constraint FOREIGN KEY declarada no SQL Server.

## Relacionamentos Inferidos

- `acesso.ace_rot_cd` -> `rotinas.rot_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `acesso.ace_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `acesso_tempo.atd_cd` -> `atendimento.atd_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `acesso_tempo.den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `acesso_tempo.usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `assunto_denuncia.ass_cla_cd` -> `assunto_classe.cla_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `assunto_denuncia.ass_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `atendimento.atd_att_cd` -> `atendimento_tipo.att_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `atendimento.atd_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `atendimento.atd_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `buscas.bus_rel_cd` -> `relatorios.rel_cd` [manual, high]: Codigo do relatorio usado pela busca.
- `buscas.bus_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `correlatas.cor_orig_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `correlatas.cor_ref_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `denuncia.den_class` -> `classificacao.cld_cd` [manual, medium]: Campo de classificacao sugere referencia ao catalogo classificacao.
- `denuncia.den_xpto` -> `xpto.xpt_cd` [manual, medium]: Nome do campo sugere referencia ao catalogo xpto.
- `difusao_externa.dex_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `difusao_externa.dex_dit_cd` -> `difusao_tipo.dit_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `difusao_externa.dex_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `difusao_interna.din_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `difusao_interna.din_dit_cd` -> `difusao_tipo.dit_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `difusao_interna.din_int_cd` -> `orgaos_internos.int_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `difusao_interna.din_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `envolvidos.env_cabelo` -> `aux_cabelo.cab_cd` [manual, medium]: Codigo de cabelo sugere referencia ao catalogo auxiliar.
- `envolvidos.env_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `envolvidos.env_estatura` -> `aux_estatura.est_cd` [manual, medium]: Codigo de estatura sugere referencia ao catalogo auxiliar.
- `envolvidos.env_olhos` -> `aux_olhos.olh_cd` [manual, medium]: Codigo de olhos sugere referencia ao catalogo auxiliar.
- `envolvidos.env_pele` -> `aux_pele.pel_cd` [manual, medium]: Codigo de pele sugere referencia ao catalogo auxiliar.
- `envolvidos.env_porte` -> `aux_porte.prt_cd` [manual, medium]: Codigo de porte sugere referencia ao catalogo auxiliar.
- `envolvidos.env_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `item.itm_tpi_cd` -> `item_tipo.tpi_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `item.itm_umt_cd` -> `unidades_metricas.umt_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `item_tipo.tpi_cli_cd` -> `item_classe.cli_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `lock_registro.atd_cd` -> `atendimento.atd_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `lock_registro.den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `lock_registro.usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `log_alteracoes.den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `log_alteracoes.usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `quantifica_resultado.qtf_cli_cd` -> `item_classe.cli_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `quantifica_resultado.qtf_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `quantifica_resultado.qtf_itm_cd` -> `item.itm_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `quantifica_resultado.qtf_tpi_cd` -> `item_tipo.tpi_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `quantifica_resultado.qtf_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `resultado_direto.red_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `resultado_direto.red_ext_cd` -> `orgaos_externos.ext_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `resultado_direto.red_oet_cd` -> `orgaos_externos_tipos.oet_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `resultado_direto.red_rtp_cd` -> `resultado_tipo.rtp_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `resultado_direto.red_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `resultado_indireto.rei_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `resultado_indireto.rei_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `usuarios.usu_tipo` -> `usuarios_tipos.utp_cd` [manual, high]: Campo de tipo do usuario aponta semanticamente para usuarios_tipos.
- `veiculos.den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `veiculos.marca` -> `vei_marca.mar_cd` [manual, high]: Campo sem sufixo _cd, mas o nome coincide com a tabela de marcas.
- `veiculos.modelo` -> `vei_modelo.mod_cd` [manual, high]: Campo sem sufixo _cd, mas o nome coincide com a tabela de modelos.
- `xpto_denuncia.dxp_den_cd` -> `denuncia.den_cd` [manual, high]: Coluna usa den_cd; denuncia_com tambem compartilha esse campo, mas a referencia mais provavel e a tabela principal denuncia.
- `xpto_denuncia.dxp_usu_cd` -> `usuarios.usu_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.
- `xpto_denuncia.dxp_xpt_cd` -> `xpto.xpt_cd` [inferred, high]: Nome da coluna bate com a PK da tabela de destino.

## Revisao Manual

- `chaves.atendimento`: Parece contador/controle operacional, nao necessariamente FK para atendimento.
- `chaves.denuncia`: Parece contador/controle operacional, nao necessariamente FK para denuncia.
- `denuncia.den_corr_cd`: Pode apontar para correlatas.cor_cd, mas precisa validar pela aplicacao/dados.
- `denuncia.den_op_rec`: Pode apontar para usuarios.usu_cd, mas o nome nao prova a relacao sozinho.
- `envolvidos.env_end_tp`: Campo de tipo/endereco; pode ser dominio interno em vez de FK.
- `veiculos.com_tipo`: Campo numerico com cara de dominio interno; destino nao fica claro so pelo schema.

## Visao Por Tabela

### `acesso`

Saidas:
- `ace_rot_cd` -> `rotinas.rot_cd` [inferred, high]
- `ace_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `acesso_tempo`

Saidas:
- `atd_cd` -> `atendimento.atd_cd` [inferred, high]
- `den_cd` -> `denuncia.den_cd` [manual, high]
- `usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `assunto_classe`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `assunto_denuncia.ass_cla_cd` -> `cla_cd` [inferred, high]
- `assunto_tipo.tpa_cla_cd` -> `cla_cd` [explicit, explicit]

### `assunto_denuncia`

Saidas:
- `ass_cla_cd` -> `assunto_classe.cla_cd` [inferred, high]
- `ass_den_cd` -> `denuncia.den_cd` [manual, high]
- `ass_tpa_cd` -> `assunto_tipo.tpa_cd` [explicit, explicit]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `assunto_tipo`

Saidas:
- `tpa_cla_cd` -> `assunto_classe.cla_cd` [explicit, explicit]

Entradas:
- `assunto_denuncia.ass_tpa_cd` -> `tpa_cd` [explicit, explicit]

### `atendimento`

Saidas:
- `atd_att_cd` -> `atendimento_tipo.att_cd` [inferred, high]
- `atd_den_cd` -> `denuncia.den_cd` [manual, high]
- `atd_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- `acesso_tempo.atd_cd` -> `atd_cd` [inferred, high]
- `lock_registro.atd_cd` -> `atd_cd` [inferred, high]

### `atendimento_tipo`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `atendimento.atd_att_cd` -> `att_cd` [inferred, high]

### `aux_bairro`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `aux_cabelo`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `envolvidos.env_cabelo` -> `cab_cd` [manual, medium]

### `aux_estatura`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `envolvidos.env_estatura` -> `est_cd` [manual, medium]

### `aux_faixa_etaria`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `aux_logradouro`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `aux_municipio`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `aux_olhos`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `envolvidos.env_olhos` -> `olh_cd` [manual, medium]

### `aux_pele`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `envolvidos.env_pele` -> `pel_cd` [manual, medium]

### `aux_porte`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `envolvidos.env_porte` -> `prt_cd` [manual, medium]

### `aux_subbairro`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `aux_uf`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `buscas`

Saidas:
- `bus_rel_cd` -> `relatorios.rel_cd` [manual, high]
- `bus_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `chaves`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `classificacao`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `denuncia.den_class` -> `cld_cd` [manual, medium]

### `config`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `correlatas`

Saidas:
- `cor_orig_den_cd` -> `denuncia.den_cd` [manual, high]
- `cor_ref_den_cd` -> `denuncia.den_cd` [manual, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `dd_mulher`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `denuncia`

Saidas:
- `den_class` -> `classificacao.cld_cd` [manual, medium]
- `den_xpto` -> `xpto.xpt_cd` [manual, medium]

Entradas:
- `acesso_tempo.den_cd` -> `den_cd` [manual, high]
- `assunto_denuncia.ass_den_cd` -> `den_cd` [manual, high]
- `atendimento.atd_den_cd` -> `den_cd` [manual, high]
- `correlatas.cor_orig_den_cd` -> `den_cd` [manual, high]
- `correlatas.cor_ref_den_cd` -> `den_cd` [manual, high]
- `difusao_externa.dex_den_cd` -> `den_cd` [manual, high]
- `difusao_interna.din_den_cd` -> `den_cd` [manual, high]
- `envolvidos.env_den_cd` -> `den_cd` [manual, high]
- `lock_registro.den_cd` -> `den_cd` [manual, high]
- `log_alteracoes.den_cd` -> `den_cd` [manual, high]
- `quantifica_resultado.qtf_den_cd` -> `den_cd` [manual, high]
- `resultado_direto.red_den_cd` -> `den_cd` [manual, high]
- `resultado_indireto.rei_den_cd` -> `den_cd` [manual, high]
- `veiculos.den_cd` -> `den_cd` [manual, high]
- `xpto_denuncia.dxp_den_cd` -> `den_cd` [manual, high]

### `denuncia_com`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `difusao_externa`

Saidas:
- `dex_den_cd` -> `denuncia.den_cd` [manual, high]
- `dex_dit_cd` -> `difusao_tipo.dit_cd` [inferred, high]
- `dex_ext_cd` -> `orgaos_externos.ext_cd` [explicit, explicit]
- `dex_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `difusao_interna`

Saidas:
- `din_den_cd` -> `denuncia.den_cd` [manual, high]
- `din_dit_cd` -> `difusao_tipo.dit_cd` [inferred, high]
- `din_int_cd` -> `orgaos_internos.int_cd` [inferred, high]
- `din_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `difusao_tipo`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `difusao_externa.dex_dit_cd` -> `dit_cd` [inferred, high]
- `difusao_interna.din_dit_cd` -> `dit_cd` [inferred, high]

### `envolvidos`

Saidas:
- `env_cabelo` -> `aux_cabelo.cab_cd` [manual, medium]
- `env_den_cd` -> `denuncia.den_cd` [manual, high]
- `env_estatura` -> `aux_estatura.est_cd` [manual, medium]
- `env_olhos` -> `aux_olhos.olh_cd` [manual, medium]
- `env_pele` -> `aux_pele.pel_cd` [manual, medium]
- `env_porte` -> `aux_porte.prt_cd` [manual, medium]
- `env_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `graficos`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `item`

Saidas:
- `itm_tpi_cd` -> `item_tipo.tpi_cd` [inferred, high]
- `itm_umt_cd` -> `unidades_metricas.umt_cd` [inferred, high]

Entradas:
- `quantifica_resultado.qtf_itm_cd` -> `itm_cd` [inferred, high]

### `item_classe`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `item_tipo.tpi_cli_cd` -> `cli_cd` [inferred, high]
- `quantifica_resultado.qtf_cli_cd` -> `cli_cd` [inferred, high]

### `item_tipo`

Saidas:
- `tpi_cli_cd` -> `item_classe.cli_cd` [inferred, high]

Entradas:
- `item.itm_tpi_cd` -> `tpi_cd` [inferred, high]
- `quantifica_resultado.qtf_tpi_cd` -> `tpi_cd` [inferred, high]

### `lock_registro`

Saidas:
- `atd_cd` -> `atendimento.atd_cd` [inferred, high]
- `den_cd` -> `denuncia.den_cd` [manual, high]
- `usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `log_alteracoes`

Saidas:
- `den_cd` -> `denuncia.den_cd` [manual, high]
- `usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `numeracao`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `orgaos_externos`

Saidas:
- `ext_oet_cd` -> `orgaos_externos_tipos.oet_cd` [explicit, explicit]

Entradas:
- `difusao_externa.dex_ext_cd` -> `ext_cd` [explicit, explicit]
- `resultado_direto.red_ext_cd` -> `ext_cd` [inferred, high]

### `orgaos_externos_tipos`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `orgaos_externos.ext_oet_cd` -> `oet_cd` [explicit, explicit]
- `resultado_direto.red_oet_cd` -> `oet_cd` [inferred, high]

### `orgaos_internos`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `difusao_interna.din_int_cd` -> `int_cd` [inferred, high]

### `quantifica_resultado`

Saidas:
- `qtf_cli_cd` -> `item_classe.cli_cd` [inferred, high]
- `qtf_den_cd` -> `denuncia.den_cd` [manual, high]
- `qtf_itm_cd` -> `item.itm_cd` [inferred, high]
- `qtf_tpi_cd` -> `item_tipo.tpi_cd` [inferred, high]
- `qtf_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `relatorios`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `buscas.bus_rel_cd` -> `rel_cd` [manual, high]

### `resultado_direto`

Saidas:
- `red_den_cd` -> `denuncia.den_cd` [manual, high]
- `red_ext_cd` -> `orgaos_externos.ext_cd` [inferred, high]
- `red_oet_cd` -> `orgaos_externos_tipos.oet_cd` [inferred, high]
- `red_rtp_cd` -> `resultado_tipo.rtp_cd` [inferred, high]
- `red_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `resultado_indireto`

Saidas:
- `rei_den_cd` -> `denuncia.den_cd` [manual, high]
- `rei_usu_cd` -> `usuarios.usu_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `resultado_tipo`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `resultado_direto.red_rtp_cd` -> `rtp_cd` [inferred, high]

### `rotinas`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `acesso.ace_rot_cd` -> `rot_cd` [inferred, high]

### `unidades_metricas`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `item.itm_umt_cd` -> `umt_cd` [inferred, high]

### `usuarios`

Saidas:
- `usu_tipo` -> `usuarios_tipos.utp_cd` [manual, high]

Entradas:
- `acesso.ace_usu_cd` -> `usu_cd` [inferred, high]
- `acesso_tempo.usu_cd` -> `usu_cd` [inferred, high]
- `atendimento.atd_usu_cd` -> `usu_cd` [inferred, high]
- `buscas.bus_usu_cd` -> `usu_cd` [inferred, high]
- `difusao_externa.dex_usu_cd` -> `usu_cd` [inferred, high]
- `difusao_interna.din_usu_cd` -> `usu_cd` [inferred, high]
- `envolvidos.env_usu_cd` -> `usu_cd` [inferred, high]
- `lock_registro.usu_cd` -> `usu_cd` [inferred, high]
- `log_alteracoes.usu_cd` -> `usu_cd` [inferred, high]
- `quantifica_resultado.qtf_usu_cd` -> `usu_cd` [inferred, high]
- `resultado_direto.red_usu_cd` -> `usu_cd` [inferred, high]
- `resultado_indireto.rei_usu_cd` -> `usu_cd` [inferred, high]
- `xpto_denuncia.dxp_usu_cd` -> `usu_cd` [inferred, high]

### `usuarios_tipos`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `usuarios.usu_tipo` -> `utp_cd` [manual, high]

### `vei_marca`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `vei_modelo.mar_cd` -> `mar_cd` [explicit, explicit]
- `veiculos.marca` -> `mar_cd` [manual, high]

### `vei_modelo`

Saidas:
- `mar_cd` -> `vei_marca.mar_cd` [explicit, explicit]

Entradas:
- `veiculos.modelo` -> `mod_cd` [manual, high]

### `veiculos`

Saidas:
- `den_cd` -> `denuncia.den_cd` [manual, high]
- `marca` -> `vei_marca.mar_cd` [manual, high]
- `modelo` -> `vei_modelo.mod_cd` [manual, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.

### `xpto`

Saidas:
- Nenhuma relacao detectada saindo desta tabela.

Entradas:
- `denuncia.den_xpto` -> `xpt_cd` [manual, medium]
- `xpto_denuncia.dxp_xpt_cd` -> `xpt_cd` [inferred, high]

### `xpto_denuncia`

Saidas:
- `dxp_den_cd` -> `denuncia.den_cd` [manual, high]
- `dxp_usu_cd` -> `usuarios.usu_cd` [inferred, high]
- `dxp_xpt_cd` -> `xpto.xpt_cd` [inferred, high]

Entradas:
- Nenhuma relacao detectada chegando nesta tabela.
