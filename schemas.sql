USE [disque_denuncia]
GO
/****** Object:  Table [dbo].[assunto_tipo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[assunto_tipo](
	[tpa_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[tpa_cla_cd] [smallint] NOT NULL,
	[tpa_ds] [char](40) NOT NULL,
	[tpa_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_assunto_tipo] PRIMARY KEY CLUSTERED 
(
	[tpa_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[assunto_denuncia]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[assunto_denuncia](
	[ass_cd] [int] IDENTITY(1,1) NOT NULL,
	[ass_den_cd] [int] NOT NULL,
	[ass_cla_cd] [smallint] NOT NULL,
	[ass_tpa_cd] [smallint] NOT NULL,
	[ass_principal] [tinyint] NOT NULL,
 CONSTRAINT [PK_denuncia_assuntos] PRIMARY KEY NONCLUSTERED 
(
	[ass_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[assunto_classe]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[assunto_classe](
	[cla_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[cla_ds] [char](40) NOT NULL,
	[cla_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_assunto_classe] PRIMARY KEY CLUSTERED 
(
	[cla_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_assunto_classe] UNIQUE NONCLUSTERED 
(
	[cla_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[xpto_denuncia]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[xpto_denuncia](
	[dxp_cd] [int] IDENTITY(1,1) NOT NULL,
	[dxp_xpt_cd] [smallint] NOT NULL,
	[dxp_den_cd] [int] NOT NULL,
	[dxp_data] [datetime] NOT NULL,
	[dxp_usu_cd] [smallint] NOT NULL,
 CONSTRAINT [PK_xpto_denuncia] PRIMARY KEY NONCLUSTERED 
(
	[dxp_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[veiculos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[veiculos](
	[vei_cd] [int] IDENTITY(1,1) NOT NULL,
	[den_cd] [int] NOT NULL,
	[marca] [smallint] NOT NULL,
	[modelo] [smallint] NOT NULL,
	[cor] [char](15) NOT NULL,
	[ano_mod] [smallint] NOT NULL,
	[ano_fab] [smallint] NOT NULL,
	[placa] [char](7) NOT NULL,
	[municipio] [char](30) NOT NULL,
	[uf] [char](2) NOT NULL,
	[chassis] [char](20) NOT NULL,
	[proprietario] [char](50) NOT NULL,
	[com_nome] [char](50) NOT NULL,
	[com_endereco] [char](60) NOT NULL,
	[com_bairro] [char](30) NOT NULL,
	[com_municipio] [char](30) NOT NULL,
	[com_uf] [char](2) NOT NULL,
	[com_telefone] [char](40) NOT NULL,
	[com_doc] [char](30) NOT NULL,
	[com_tipo] [tinyint] NOT NULL,
	[ro_dp] [char](20) NOT NULL,
	[seguradora] [char](30) NOT NULL,
	[detalhes] [text] NOT NULL,
 CONSTRAINT [PK_veiculos] PRIMARY KEY NONCLUSTERED 
(
	[vei_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[numeracao]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[numeracao](
	[numero] [int] NOT NULL,
	[dataalt] [datetime] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[log_alteracoes]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[log_alteracoes](
	[log_cd] [int] IDENTITY(1,1) NOT NULL,
	[den_cd] [int] NULL,
	[operacao] [char](10) NULL,
	[data] [datetime] NOT NULL,
	[usu_cd] [smallint] NULL,
	[versao] [smallint] NULL,
	[original] [text] NULL,
	[novo] [text] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[vei_marca]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[vei_marca](
	[mar_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[mar_ds] [char](30) NOT NULL,
 CONSTRAINT [PK_vei_marca] PRIMARY KEY NONCLUSTERED 
(
	[mar_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_vei_marca] UNIQUE NONCLUSTERED 
(
	[mar_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[usuarios_tipos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[usuarios_tipos](
	[utp_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[utp_ds] [char](20) NOT NULL,
 CONSTRAINT [PK_usuarios_tipos] PRIMARY KEY CLUSTERED 
(
	[utp_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_usuarios_tipos] UNIQUE NONCLUSTERED 
(
	[utp_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[usuarios]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[usuarios](
	[usu_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[usu_nome] [char](50) NOT NULL,
	[usu_login] [char](12) NOT NULL,
	[usu_turno] [char](10) NOT NULL,
	[usu_tipo] [tinyint] NOT NULL,
	[usu_senha] [char](8) NOT NULL,
	[usu_status] [tinyint] NOT NULL,
 CONSTRAINT [PK_usuarios] PRIMARY KEY CLUSTERED 
(
	[usu_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_usuarios] UNIQUE NONCLUSTERED 
(
	[usu_login] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[unidades_metricas]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[unidades_metricas](
	[umt_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[umt_ds] [char](40) NOT NULL,
 CONSTRAINT [PK_unidades_metricas] PRIMARY KEY CLUSTERED 
(
	[umt_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_unidades_metricas] UNIQUE NONCLUSTERED 
(
	[umt_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[rotinas]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[rotinas](
	[rot_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[rot_ds] [char](30) NOT NULL,
	[rot_nomesis] [char](30) NOT NULL,
	[rot_st] [tinyint] NOT NULL,
	[rot_default_adm] [tinyint] NOT NULL,
	[rot_default_sup] [tinyint] NOT NULL,
	[rot_default_ana] [tinyint] NOT NULL,
	[rot_default_ate] [tinyint] NOT NULL,
	[ordem] [tinyint] NOT NULL,
 CONSTRAINT [PK_rotinas] PRIMARY KEY CLUSTERED 
(
	[rot_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[resultado_tipo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[resultado_tipo](
	[rtp_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[rtp_ds] [char](50) NOT NULL,
	[rtp_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_resultado_tipo] PRIMARY KEY NONCLUSTERED 
(
	[rtp_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_resultado_tipo] UNIQUE NONCLUSTERED 
(
	[rtp_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[resultado_indireto]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[resultado_indireto](
	[rei_cd] [int] IDENTITY(1,1) NOT NULL,
	[rei_den_cd] [int] NOT NULL,
	[rei_cad_data] [smalldatetime] NOT NULL,
	[rei_usu_cd] [smallint] NOT NULL,
 CONSTRAINT [PK_resultado_indireto] PRIMARY KEY NONCLUSTERED 
(
	[rei_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[resultado_direto]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[resultado_direto](
	[red_cd] [int] IDENTITY(1,1) NOT NULL,
	[red_den_cd] [int] NOT NULL,
	[red_rtp_cd] [smallint] NOT NULL,
	[red_resp_data] [datetime] NULL,
	[red_oper_data] [datetime] NULL,
	[red_cad_data] [datetime] NOT NULL,
	[red_usu_cd] [smallint] NOT NULL,
	[red_relato] [text] NOT NULL,
	[red_ext_cd] [int] NOT NULL,
	[red_oet_cd] [int] NOT NULL,
 CONSTRAINT [PK_resultado_direto] PRIMARY KEY NONCLUSTERED 
(
	[red_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[relatorios]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[relatorios](
	[rel_cd] [tinyint] NOT NULL,
	[rel_descricao] [nvarchar](50) NOT NULL,
	[rel_file] [nvarchar](30) NOT NULL,
	[rel_form] [nvarchar](20) NOT NULL,
	[rel_sql] [ntext] NOT NULL,
	[rel_filtros] [nvarchar](50) NOT NULL,
	[rel_ordem] [smallint] NOT NULL,
	[rel_grafico] [image] NULL,
	[rel_tipo] [nvarchar](1) NOT NULL,
	[rel_graf_itens] [tinyint] NOT NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[quantifica_resultado]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[quantifica_resultado](
	[qtf_cd] [int] IDENTITY(1,1) NOT NULL,
	[qtf_den_cd] [int] NOT NULL,
	[qtf_cli_cd] [tinyint] NOT NULL,
	[qtf_tpi_cd] [tinyint] NOT NULL,
	[qtf_itm_cd] [smallint] NOT NULL,
	[qtf_qtd] [int] NOT NULL,
	[qtf_data] [datetime] NOT NULL,
	[qtf_usu_cd] [smallint] NOT NULL,
 CONSTRAINT [PK_quantifica_resultado] PRIMARY KEY NONCLUSTERED 
(
	[qtf_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[orgaos_internos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[orgaos_internos](
	[int_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[int_ds] [char](50) NOT NULL,
	[int_st] [tinyint] NOT NULL,
	[int_dest] [char](80) NOT NULL,
 CONSTRAINT [PK_orgaos_internos] PRIMARY KEY CLUSTERED 
(
	[int_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_orgaos_internos] UNIQUE NONCLUSTERED 
(
	[int_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[orgaos_externos_tipos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[orgaos_externos_tipos](
	[oet_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[oet_ds] [char](40) NOT NULL,
	[oet_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_orgaos_externos_tipos] PRIMARY KEY CLUSTERED 
(
	[oet_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_orgaos_externos_tipos] UNIQUE NONCLUSTERED 
(
	[oet_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[chaves]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[chaves](
	[denuncia] [int] NOT NULL,
	[atendimento] [int] NOT NULL
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[buscas]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[buscas](
	[bus_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[bus_ds] [char](100) NOT NULL,
	[bus_buscas] [text] NOT NULL,
	[bus_usu_cd] [int] NOT NULL,
	[bus_rel_cd] [int] NOT NULL,
 CONSTRAINT [PK_buscas] PRIMARY KEY NONCLUSTERED 
(
	[bus_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_uf]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_uf](
	[udf_cd] [char](2) NOT NULL,
	[udf_ds] [char](20) NOT NULL,
 CONSTRAINT [PK_aux_uf] PRIMARY KEY NONCLUSTERED 
(
	[udf_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_subbairro]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_subbairro](
	[sba_cd] [int] IDENTITY(1,1) NOT NULL,
	[sba_bai_ds] [char](40) NOT NULL,
	[sba_ds] [char](40) NOT NULL,
 CONSTRAINT [PK_aux_subbairro] PRIMARY KEY NONCLUSTERED 
(
	[sba_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_porte]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_porte](
	[prt_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[prt_ds] [char](10) NOT NULL,
 CONSTRAINT [PK_aux_porte] PRIMARY KEY NONCLUSTERED 
(
	[prt_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_aux_porte] UNIQUE NONCLUSTERED 
(
	[prt_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_pele]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_pele](
	[pel_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[pel_ds] [char](10) NOT NULL,
 CONSTRAINT [PK_aux_pele] PRIMARY KEY NONCLUSTERED 
(
	[pel_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_aux_pele] UNIQUE NONCLUSTERED 
(
	[pel_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_olhos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_olhos](
	[olh_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[olh_ds] [char](10) NOT NULL,
 CONSTRAINT [PK_aux_olhos] PRIMARY KEY NONCLUSTERED 
(
	[olh_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_aux_olhos] UNIQUE NONCLUSTERED 
(
	[olh_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_municipio]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_municipio](
	[mun_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[mun_ds] [char](40) NOT NULL,
	[mun_uf_cd] [char](2) NOT NULL,
 CONSTRAINT [PK_aux_municipios] PRIMARY KEY NONCLUSTERED 
(
	[mun_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_logradouro]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_logradouro](
	[lgr_cd] [int] IDENTITY(1,1) NOT NULL,
	[lgr_tp] [char](10) NOT NULL,
	[lgr_ds] [char](60) NOT NULL,
	[lgr_bai_ds] [char](40) NOT NULL,
	[lgr_sba_ds] [char](40) NOT NULL,
	[lgr_mun_ds] [char](40) NOT NULL,
	[lgr_uf_cd] [char](2) NOT NULL,
	[lgr_cep] [int] NOT NULL,
 CONSTRAINT [PK_aux_logradouro] PRIMARY KEY NONCLUSTERED 
(
	[lgr_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_faixa_etaria]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_faixa_etaria](
	[fet_cd] [char](10) NOT NULL,
	[fet_ds] [char](15) NOT NULL,
 CONSTRAINT [PK_aux_faixa_etaria] PRIMARY KEY NONCLUSTERED 
(
	[fet_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_aux_faixa_etaria] UNIQUE NONCLUSTERED 
(
	[fet_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_estatura]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_estatura](
	[est_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[est_ds] [char](10) NOT NULL,
 CONSTRAINT [PK_aux_estatura] PRIMARY KEY NONCLUSTERED 
(
	[est_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_aux_estatura] UNIQUE NONCLUSTERED 
(
	[est_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_cabelo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_cabelo](
	[cab_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[cab_ds] [char](10) NOT NULL,
 CONSTRAINT [PK_aux_cabelo] PRIMARY KEY NONCLUSTERED 
(
	[cab_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_aux_cabelo] UNIQUE NONCLUSTERED 
(
	[cab_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[aux_bairro]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[aux_bairro](
	[bai_cd] [int] IDENTITY(1,1) NOT NULL,
	[bai_ds] [char](40) NOT NULL,
	[bai_mun_ds] [char](40) NOT NULL,
	[bai_uf_cd] [char](2) NOT NULL,
 CONSTRAINT [PK_aux_bairro] PRIMARY KEY NONCLUSTERED 
(
	[bai_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[atendimento_tipo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[atendimento_tipo](
	[att_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[att_ds] [char](40) NULL,
	[att_st] [tinyint] NULL,
 CONSTRAINT [PK_atendimento_tipo] PRIMARY KEY NONCLUSTERED 
(
	[att_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_atendimento_tipo] UNIQUE NONCLUSTERED 
(
	[att_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[atendimento]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[atendimento](
	[atd_cd] [int] NOT NULL,
	[atd_dt_rec] [smalldatetime] NULL,
	[atd_usu_cd] [smallint] NULL,
	[atd_att_cd] [smallint] NULL,
	[atd_texto] [text] NULL,
	[atd_den_cd] [int] NULL,
	[atd_den_num] [char](15) NULL,
 CONSTRAINT [PK_atendimento] PRIMARY KEY NONCLUSTERED 
(
	[atd_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[acesso_tempo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[acesso_tempo](
	[usu_cd] [smallint] NOT NULL,
	[den_cd] [int] NOT NULL,
	[atd_cd] [int] NOT NULL,
	[locktime] [datetime] NOT NULL,
	[unlocktime] [datetime] NOT NULL,
	[l_status] [char](1) NOT NULL,
	[u_status] [char](1) NOT NULL
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[acesso]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[acesso](
	[ace_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[ace_usu_cd] [smallint] NOT NULL,
	[ace_rot_cd] [smallint] NOT NULL,
 CONSTRAINT [PK_acesso] PRIMARY KEY NONCLUSTERED 
(
	[ace_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[item_tipo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[item_tipo](
	[tpi_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[tpi_cli_cd] [smallint] NOT NULL,
	[tpi_ds] [char](40) NOT NULL,
	[tpi_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_item_tipo] PRIMARY KEY CLUSTERED 
(
	[tpi_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_item_tipo] UNIQUE NONCLUSTERED 
(
	[tpi_cli_cd] ASC,
	[tpi_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[item_classe]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[item_classe](
	[cli_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[cli_ds] [char](40) NOT NULL,
	[cli_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_item_classe] PRIMARY KEY CLUSTERED 
(
	[cli_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[item]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[item](
	[itm_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[itm_ds] [char](40) NOT NULL,
	[itm_tpi_cd] [smallint] NOT NULL,
	[itm_umt_cd] [smallint] NOT NULL,
	[itm_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_item] PRIMARY KEY CLUSTERED 
(
	[itm_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[graficos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[graficos](
	[grf_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[descricao] [char](50) NOT NULL,
	[codigo] [char](30) NOT NULL,
	[grafico] [image] NOT NULL,
 CONSTRAINT [PK_graficos] PRIMARY KEY NONCLUSTERED 
(
	[grf_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[envolvidos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[envolvidos](
	[env_cd] [int] IDENTITY(1,1) NOT NULL,
	[env_den_cd] [int] NOT NULL,
	[env_usu_cd] [smallint] NOT NULL,
	[env_nome] [char](40) NOT NULL,
	[env_vulgo] [char](30) NOT NULL,
	[env_end_tp] [tinyint] NOT NULL,
	[env_logr_tp] [char](10) NOT NULL,
	[env_logr_ds] [char](50) NOT NULL,
	[env_logr_num] [char](10) NOT NULL,
	[env_logr_cmpl] [char](20) NOT NULL,
	[env_logr_bairro] [char](40) NOT NULL,
	[env_logr_subbairro] [char](40) NOT NULL,
	[env_logr_mun] [char](40) NOT NULL,
	[env_logr_uf] [char](2) NOT NULL,
	[env_loc_ref] [text] NOT NULL,
	[env_sexo] [char](1) NOT NULL,
	[env_idade] [tinyint] NOT NULL,
	[env_pele] [tinyint] NOT NULL,
	[env_estatura] [tinyint] NOT NULL,
	[env_olhos] [tinyint] NOT NULL,
	[env_cabelo] [tinyint] NOT NULL,
	[env_porte] [tinyint] NOT NULL,
	[env_caract] [text] NOT NULL,
 CONSTRAINT [PK_envolvidos] PRIMARY KEY CLUSTERED 
(
	[env_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[difusao_tipo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[difusao_tipo](
	[dit_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[dit_ds] [char](30) NOT NULL,
	[dit_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_difusao_tipo] PRIMARY KEY NONCLUSTERED 
(
	[dit_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_difusao_tipo] UNIQUE NONCLUSTERED 
(
	[dit_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[difusao_interna]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[difusao_interna](
	[din_cd] [int] IDENTITY(1,1) NOT NULL,
	[din_den_cd] [int] NOT NULL,
	[din_int_cd] [smallint] NOT NULL,
	[din_data] [datetime] NOT NULL,
	[din_dit_cd] [smallint] NOT NULL,
	[din_usu_cd] [smallint] NOT NULL,
 CONSTRAINT [PK_difusao_interna] PRIMARY KEY NONCLUSTERED 
(
	[din_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[dd_mulher]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[dd_mulher](
	[mul_cd] [int] IDENTITY(1,1) NOT NULL,
	[mul_den_cd] [int] NOT NULL,
	[mul_lugar] [char](50) NOT NULL,
	[mul_vive] [char](50) NOT NULL,
	[mul_filhos] [char](20) NOT NULL,
	[mul_filhos_qtd] [tinyint] NOT NULL,
	[mul_filhos_agd] [char](20) NOT NULL,
	[mul_agr_alcool] [char](20) NOT NULL,
	[mul_quando] [char](40) NOT NULL,
	[mul_freq] [char](40) NOT NULL,
	[mul_hora] [char](20) NOT NULL,
	[mul_test] [char](20) NOT NULL,
	[mul_t_pai_vit] [bit] NOT NULL,
	[mul_t_mae_vit] [bit] NOT NULL,
	[mul_t_pai_agr] [bit] NOT NULL,
	[mul_t_mae_agr] [bit] NOT NULL,
	[mul_t_filhos] [bit] NOT NULL,
	[mul_t_outros] [bit] NOT NULL,
	[mul_t_ods] [char](50) NOT NULL,
	[mul_data] [datetime] NOT NULL,
	[mul_tpag_fisica] [bit] NOT NULL,
	[mul_tpag_verbal] [bit] NOT NULL,
	[mul_tpag_sexual] [bit] NOT NULL,
	[mul_tpag_carcere] [bit] NOT NULL,
	[mul_tpag_ameaca] [bit] NOT NULL,
	[mul_tpag_ninform] [bit] NOT NULL,
	[mul_tpag_outros] [bit] NOT NULL,
	[mul_tpag_ods] [char](50) NOT NULL,
	[mul_agr_ab] [bit] NOT NULL,
	[mul_agr_af] [bit] NOT NULL,
	[mul_agr_pau] [bit] NOT NULL,
	[mul_agr_fio] [bit] NOT NULL,
	[mul_agr_cig] [bit] NOT NULL,
	[mul_agr_mao] [bit] NOT NULL,
	[mul_agr_est] [bit] NOT NULL,
	[mul_agr_soc] [bit] NOT NULL,
	[mul_agr_emp] [bit] NOT NULL,
	[mul_agr_chu] [bit] NOT NULL,
	[mul_agr_out] [bit] NOT NULL,
	[mul_agr_ods] [char](50) NOT NULL,
 CONSTRAINT [PK_dd_muher] PRIMARY KEY CLUSTERED 
(
	[mul_den_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[correlatas]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[correlatas](
	[cor_cd] [int] IDENTITY(1,1) NOT NULL,
	[cor_orig_den_cd] [int] NOT NULL,
	[cor_ref_den_cd] [int] NOT NULL,
 CONSTRAINT [PK_correlatas] PRIMARY KEY NONCLUSTERED 
(
	[cor_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[config]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[config](
	[cfg_cd] [tinyint] IDENTITY(1,1) NOT NULL,
	[cfg_uf] [char](2) NOT NULL,
	[cfg_mun] [char](40) NOT NULL,
	[cfg_capital] [char](40) NOT NULL,
	[cfg_titulo] [char](100) NOT NULL,
 CONSTRAINT [PK_config] PRIMARY KEY NONCLUSTERED 
(
	[cfg_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[denuncia_com]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[denuncia_com](
	[com_cd] [int] IDENTITY(1,1) NOT NULL,
	[den_cd] [int] NOT NULL,
	[den_com] [text] NOT NULL,
 CONSTRAINT [PK_denuncia_com] PRIMARY KEY NONCLUSTERED 
(
	[den_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[denuncia]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[denuncia](
	[den_cd] [int] NOT NULL,
	[den_numero] [int] NOT NULL,
	[den_dt_rec] [smalldatetime] NOT NULL,
	[den_dt_alt] [smalldatetime] NOT NULL,
	[den_op_rec] [smallint] NOT NULL,
	[den_logr_manual] [tinyint] NOT NULL,
	[den_logr_tp] [char](10) NOT NULL,
	[den_logr_ds] [char](50) NOT NULL,
	[den_logr_num] [char](10) NOT NULL,
	[den_logr_cmpl] [char](20) NOT NULL,
	[den_logr_bairro] [char](40) NOT NULL,
	[den_logr_subbairro] [char](40) NOT NULL,
	[den_logr_mun] [char](40) NOT NULL,
	[den_logr_uf] [char](2) NOT NULL,
	[den_logr_cep] [int] NOT NULL,
	[den_loc_ref] [text] NOT NULL,
	[den_xpto] [smallint] NOT NULL,
	[den_class] [smallint] NOT NULL,
	[den_texto] [text] NOT NULL,
	[den_versao] [smallint] NOT NULL,
	[den_corr_cd] [int] NOT NULL,
	[den_imediata] [tinyint] NOT NULL,
	[den_usu_lock] [char](12) NOT NULL,
	[den_redifundir] [bit] NOT NULL,
 CONSTRAINT [PK_denuncia] PRIMARY KEY CLUSTERED 
(
	[den_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[orgaos_externos]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[orgaos_externos](
	[ext_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[ext_ds] [char](50) NOT NULL,
	[ext_oet_cd] [tinyint] NOT NULL,
	[ext_st] [tinyint] NOT NULL,
	[ext_dest] [char](80) NOT NULL,
	[ext_endereco] [char](50) NOT NULL,
	[ext_bairro] [char](40) NOT NULL,
	[ext_municipio] [char](40) NOT NULL,
	[ext_uf] [char](2) NOT NULL,
	[ext_cep] [int] NOT NULL,
	[ext_telefones] [char](50) NOT NULL,
 CONSTRAINT [PK_orgaos_externos] PRIMARY KEY CLUSTERED 
(
	[ext_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_orgaos_externos] UNIQUE NONCLUSTERED 
(
	[ext_oet_cd] ASC,
	[ext_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[lock_registro]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[lock_registro](
	[usu_cd] [int] NOT NULL,
	[den_cd] [int] NOT NULL,
	[atd_cd] [int] NOT NULL,
	[locktime] [datetime] NOT NULL,
	[l_status] [char](1) NOT NULL,
	[u_status] [char](1) NOT NULL,
 CONSTRAINT [IX_lock_registro] UNIQUE NONCLUSTERED 
(
	[den_cd] ASC,
	[atd_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[vei_modelo]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[vei_modelo](
	[mod_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[mar_cd] [smallint] NOT NULL,
	[mod_ds] [char](30) NOT NULL,
 CONSTRAINT [PK_vei_modelo] PRIMARY KEY NONCLUSTERED 
(
	[mod_cd] ASC,
	[mar_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[xpto]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[xpto](
	[xpt_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[xpt_ds] [char](40) NOT NULL,
	[xpt_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_xpto] PRIMARY KEY CLUSTERED 
(
	[xpt_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_xpto] UNIQUE NONCLUSTERED 
(
	[xpt_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[difusao_externa]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[difusao_externa](
	[dex_cd] [int] IDENTITY(1,1) NOT NULL,
	[dex_den_cd] [int] NOT NULL,
	[dex_ext_cd] [smallint] NOT NULL,
	[dex_data] [datetime] NOT NULL,
	[dex_dit_cd] [smallint] NOT NULL,
	[dex_usu_cd] [smallint] NOT NULL,
 CONSTRAINT [PK_difusao_externa] PRIMARY KEY NONCLUSTERED 
(
	[dex_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[classificacao]    Script Date: 04/09/2026 10:40:52 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[classificacao](
	[cld_cd] [smallint] IDENTITY(1,1) NOT NULL,
	[cld_ds] [char](20) NOT NULL,
	[cld_st] [tinyint] NOT NULL,
 CONSTRAINT [PK_classificacao] PRIMARY KEY CLUSTERED 
(
	[cld_cd] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY],
 CONSTRAINT [IX_classificacao] UNIQUE NONCLUSTERED 
(
	[cld_ds] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
/****** Object:  Default [DF_acesso_tempo_unlocktime]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[acesso_tempo] ADD  CONSTRAINT [DF_acesso_tempo_unlocktime]  DEFAULT (getdate()) FOR [unlocktime]
GO
/****** Object:  Default [DF_assunto_classe_cla_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[assunto_classe] ADD  CONSTRAINT [DF_assunto_classe_cla_st]  DEFAULT (0) FOR [cla_st]
GO
/****** Object:  Default [DF_assunto_denuncia_ass_principal]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[assunto_denuncia] ADD  CONSTRAINT [DF_assunto_denuncia_ass_principal]  DEFAULT (0) FOR [ass_principal]
GO
/****** Object:  Default [DF_assunto_tipo_tpa_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[assunto_tipo] ADD  CONSTRAINT [DF_assunto_tipo_tpa_st]  DEFAULT (0) FOR [tpa_st]
GO
/****** Object:  Default [DF_atendimento_atd_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento] ADD  CONSTRAINT [DF_atendimento_atd_cd]  DEFAULT (0) FOR [atd_cd]
GO
/****** Object:  Default [DF_atendimento_atd_dt_rec]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento] ADD  CONSTRAINT [DF_atendimento_atd_dt_rec]  DEFAULT (getdate()) FOR [atd_dt_rec]
GO
/****** Object:  Default [DF_atendimento_atd_usu_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento] ADD  CONSTRAINT [DF_atendimento_atd_usu_cd]  DEFAULT (0) FOR [atd_usu_cd]
GO
/****** Object:  Default [DF_atendimento_atd_att_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento] ADD  CONSTRAINT [DF_atendimento_atd_att_cd]  DEFAULT (0) FOR [atd_att_cd]
GO
/****** Object:  Default [DF_atendimento_atd_texto]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento] ADD  CONSTRAINT [DF_atendimento_atd_texto]  DEFAULT ('') FOR [atd_texto]
GO
/****** Object:  Default [DF_atendimento_atd_den_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento] ADD  CONSTRAINT [DF_atendimento_atd_den_cd]  DEFAULT (0) FOR [atd_den_cd]
GO
/****** Object:  Default [DF_atendimento_atd_den_num]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento] ADD  CONSTRAINT [DF_atendimento_atd_den_num]  DEFAULT ('') FOR [atd_den_num]
GO
/****** Object:  Default [DF_atendimento_tipo_atd_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[atendimento_tipo] ADD  CONSTRAINT [DF_atendimento_tipo_atd_st]  DEFAULT (0) FOR [att_st]
GO
/****** Object:  Default [DF_aux_bairro_bai_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_bairro] ADD  CONSTRAINT [DF_aux_bairro_bai_ds]  DEFAULT ('') FOR [bai_ds]
GO
/****** Object:  Default [DF_aux_bairro_bai_mun_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_bairro] ADD  CONSTRAINT [DF_aux_bairro_bai_mun_ds]  DEFAULT ('') FOR [bai_mun_ds]
GO
/****** Object:  Default [DF_aux_bairro_bai_uf_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_bairro] ADD  CONSTRAINT [DF_aux_bairro_bai_uf_cd]  DEFAULT ('') FOR [bai_uf_cd]
GO
/****** Object:  Default [DF_aux_logradouro_lgr_tp]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_logradouro] ADD  CONSTRAINT [DF_aux_logradouro_lgr_tp]  DEFAULT ('') FOR [lgr_tp]
GO
/****** Object:  Default [DF_aux_logradouro_lgr_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_logradouro] ADD  CONSTRAINT [DF_aux_logradouro_lgr_ds]  DEFAULT ('') FOR [lgr_ds]
GO
/****** Object:  Default [DF_aux_logradouro_lgr_bai_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_logradouro] ADD  CONSTRAINT [DF_aux_logradouro_lgr_bai_ds]  DEFAULT ('') FOR [lgr_bai_ds]
GO
/****** Object:  Default [DF_aux_logradouro_lgr_sba_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_logradouro] ADD  CONSTRAINT [DF_aux_logradouro_lgr_sba_ds]  DEFAULT ('') FOR [lgr_sba_ds]
GO
/****** Object:  Default [DF_aux_logradouro_lgr_mun_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_logradouro] ADD  CONSTRAINT [DF_aux_logradouro_lgr_mun_ds]  DEFAULT ('') FOR [lgr_mun_ds]
GO
/****** Object:  Default [DF_aux_logradouro_lgr_uf_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_logradouro] ADD  CONSTRAINT [DF_aux_logradouro_lgr_uf_cd]  DEFAULT ('') FOR [lgr_uf_cd]
GO
/****** Object:  Default [DF_aux_logradouro_lgr_cep]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[aux_logradouro] ADD  CONSTRAINT [DF_aux_logradouro_lgr_cep]  DEFAULT (0) FOR [lgr_cep]
GO
/****** Object:  Default [DF_buscas_bus_usu_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[buscas] ADD  CONSTRAINT [DF_buscas_bus_usu_cd]  DEFAULT (0) FOR [bus_usu_cd]
GO
/****** Object:  Default [DF_buscas_bus_rel_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[buscas] ADD  CONSTRAINT [DF_buscas_bus_rel_cd]  DEFAULT (0) FOR [bus_rel_cd]
GO
/****** Object:  Default [DF_classificacao_cld_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[classificacao] ADD  CONSTRAINT [DF_classificacao_cld_st]  DEFAULT (0) FOR [cld_st]
GO
/****** Object:  Default [DF_config_cfg_titulo]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[config] ADD  CONSTRAINT [DF_config_cfg_titulo]  DEFAULT ('') FOR [cfg_titulo]
GO
/****** Object:  Default [DF_dd_mulher_mul_lugar]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_lugar]  DEFAULT ('') FOR [mul_lugar]
GO
/****** Object:  Default [DF_dd_mulher_mul_vive]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_vive]  DEFAULT ('') FOR [mul_vive]
GO
/****** Object:  Default [DF_dd_mulher_mul_filhos]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_filhos]  DEFAULT ('') FOR [mul_filhos]
GO
/****** Object:  Default [DF_dd_mulher_mul_filhos_qtd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_filhos_qtd]  DEFAULT (0) FOR [mul_filhos_qtd]
GO
/****** Object:  Default [DF_dd_mulher_mul_filhos_agd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_filhos_agd]  DEFAULT ('') FOR [mul_filhos_agd]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_alcool]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_alcool]  DEFAULT ('') FOR [mul_agr_alcool]
GO
/****** Object:  Default [DF_dd_mulher_mul_quando]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_quando]  DEFAULT ('') FOR [mul_quando]
GO
/****** Object:  Default [DF_dd_mulher_mul_freq]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_freq]  DEFAULT ('') FOR [mul_freq]
GO
/****** Object:  Default [DF_dd_mulher_mul_hora]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_hora]  DEFAULT ('') FOR [mul_hora]
GO
/****** Object:  Default [DF_dd_mulher_mul_test]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_test]  DEFAULT ('') FOR [mul_test]
GO
/****** Object:  Default [DF_dd_mulher_mul_t_pai_vit]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_t_pai_vit]  DEFAULT (0) FOR [mul_t_pai_vit]
GO
/****** Object:  Default [DF_dd_mulher_mul_t_mae_vit]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_t_mae_vit]  DEFAULT (0) FOR [mul_t_mae_vit]
GO
/****** Object:  Default [DF_dd_mulher_mul_t_pai_agr]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_t_pai_agr]  DEFAULT (0) FOR [mul_t_pai_agr]
GO
/****** Object:  Default [DF_dd_mulher_mul_t_mae_agr]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_t_mae_agr]  DEFAULT (0) FOR [mul_t_mae_agr]
GO
/****** Object:  Default [DF_dd_mulher_mul_t_filhos]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_t_filhos]  DEFAULT (0) FOR [mul_t_filhos]
GO
/****** Object:  Default [DF_dd_mulher_mul_t_outros]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_t_outros]  DEFAULT (0) FOR [mul_t_outros]
GO
/****** Object:  Default [DF_dd_mulher_mul_t_ods]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_t_ods]  DEFAULT ('') FOR [mul_t_ods]
GO
/****** Object:  Default [DF_dd_mulher_mul_data]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_data]  DEFAULT (getdate()) FOR [mul_data]
GO
/****** Object:  Default [DF_dd_mulher_mul_tpag_fisica]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tpag_fisica]  DEFAULT (0) FOR [mul_tpag_fisica]
GO
/****** Object:  Default [DF_dd_mulher_mul_tpag_verbal]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tpag_verbal]  DEFAULT (0) FOR [mul_tpag_verbal]
GO
/****** Object:  Default [DF_dd_mulher_mul_tpag_sexual]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tpag_sexual]  DEFAULT (0) FOR [mul_tpag_sexual]
GO
/****** Object:  Default [DF_dd_mulher_mul_tpag_carcere]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tpag_carcere]  DEFAULT (0) FOR [mul_tpag_carcere]
GO
/****** Object:  Default [DF_dd_mulher_mul_tpag_ameaca]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tpag_ameaca]  DEFAULT (0) FOR [mul_tpag_ameaca]
GO
/****** Object:  Default [DF_dd_mulher_mul_tpag_ninform]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tpag_ninform]  DEFAULT (0) FOR [mul_tpag_ninform]
GO
/****** Object:  Default [DF_dd_mulher_mul_tpag_outros]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tpag_outros]  DEFAULT (0) FOR [mul_tpag_outros]
GO
/****** Object:  Default [DF_dd_mulher_mul_tipo_agr]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_tipo_agr]  DEFAULT ('') FOR [mul_tpag_ods]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_ab]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_ab]  DEFAULT (0) FOR [mul_agr_ab]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_af]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_af]  DEFAULT (0) FOR [mul_agr_af]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_pau]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_pau]  DEFAULT (0) FOR [mul_agr_pau]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_fio]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_fio]  DEFAULT (0) FOR [mul_agr_fio]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_cig]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_cig]  DEFAULT (0) FOR [mul_agr_cig]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_mao]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_mao]  DEFAULT (0) FOR [mul_agr_mao]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_est]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_est]  DEFAULT (0) FOR [mul_agr_est]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_soc]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_soc]  DEFAULT (0) FOR [mul_agr_soc]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_emp]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_emp]  DEFAULT (0) FOR [mul_agr_emp]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_chu]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_chu]  DEFAULT (0) FOR [mul_agr_chu]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_out]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_out]  DEFAULT (0) FOR [mul_agr_out]
GO
/****** Object:  Default [DF_dd_mulher_mul_agr_ods]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[dd_mulher] ADD  CONSTRAINT [DF_dd_mulher_mul_agr_ods]  DEFAULT ('') FOR [mul_agr_ods]
GO
/****** Object:  Default [DF_denuncia_den_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_cd]  DEFAULT (0) FOR [den_cd]
GO
/****** Object:  Default [DF_denuncia_den_numero]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_numero]  DEFAULT (0) FOR [den_numero]
GO
/****** Object:  Default [DF_denuncia_den_dt_rec]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_dt_rec]  DEFAULT (getdate()) FOR [den_dt_rec]
GO
/****** Object:  Default [DF_denuncia_den_dt_alt]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_dt_alt]  DEFAULT (getdate()) FOR [den_dt_alt]
GO
/****** Object:  Default [DF_denuncia_den_logr_manual]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_manual]  DEFAULT (0) FOR [den_logr_manual]
GO
/****** Object:  Default [DF_denuncia_den_logr_tp]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_tp]  DEFAULT ('') FOR [den_logr_tp]
GO
/****** Object:  Default [DF_denuncia_den_logr_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_ds]  DEFAULT ('') FOR [den_logr_ds]
GO
/****** Object:  Default [DF_denuncia_den_logr_num]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_num]  DEFAULT ('') FOR [den_logr_num]
GO
/****** Object:  Default [DF_denuncia_den_logr_cmpl]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_cmpl]  DEFAULT ('') FOR [den_logr_cmpl]
GO
/****** Object:  Default [DF_denuncia_den_logr_bairro]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_bairro]  DEFAULT ('') FOR [den_logr_bairro]
GO
/****** Object:  Default [DF_denuncia_den_logr_subbairro]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_subbairro]  DEFAULT ('') FOR [den_logr_subbairro]
GO
/****** Object:  Default [DF_denuncia_den_logr_mun]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_mun]  DEFAULT ('') FOR [den_logr_mun]
GO
/****** Object:  Default [DF_denuncia_den_logr_uf]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_uf]  DEFAULT ('') FOR [den_logr_uf]
GO
/****** Object:  Default [DF_denuncia_den_logr_cep]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_logr_cep]  DEFAULT (0) FOR [den_logr_cep]
GO
/****** Object:  Default [DF_denuncia_den_loc_ref]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_loc_ref]  DEFAULT ('') FOR [den_loc_ref]
GO
/****** Object:  Default [DF_denuncia_den_xpto]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_xpto]  DEFAULT (0) FOR [den_xpto]
GO
/****** Object:  Default [DF_denuncia_den_class]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_class]  DEFAULT (0) FOR [den_class]
GO
/****** Object:  Default [DF_denuncia_den_texto]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_texto]  DEFAULT ('') FOR [den_texto]
GO
/****** Object:  Default [DF_denuncia_den_versao]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_versao]  DEFAULT (0) FOR [den_versao]
GO
/****** Object:  Default [DF_denuncia_den_corr_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_corr_cd]  DEFAULT (0) FOR [den_corr_cd]
GO
/****** Object:  Default [DF_denuncia_den_imediata]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_imediata]  DEFAULT (0) FOR [den_imediata]
GO
/****** Object:  Default [DF_denuncia_den_usu_lock]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_usu_lock]  DEFAULT ('') FOR [den_usu_lock]
GO
/****** Object:  Default [DF_denuncia_den_redifundir]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[denuncia] ADD  CONSTRAINT [DF_denuncia_den_redifundir]  DEFAULT (0) FOR [den_redifundir]
GO
/****** Object:  Default [DF_difusao_externa_dex_data]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[difusao_externa] ADD  CONSTRAINT [DF_difusao_externa_dex_data]  DEFAULT (getdate()) FOR [dex_data]
GO
/****** Object:  Default [DF_difusao_interna_din_data]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[difusao_interna] ADD  CONSTRAINT [DF_difusao_interna_din_data]  DEFAULT (getdate()) FOR [din_data]
GO
/****** Object:  Default [DF_difusao_tipo_dit_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[difusao_tipo] ADD  CONSTRAINT [DF_difusao_tipo_dit_st]  DEFAULT (0) FOR [dit_st]
GO
/****** Object:  Default [DF_envolvidos_env_nome]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_nome]  DEFAULT ('') FOR [env_nome]
GO
/****** Object:  Default [DF_envolvidos_env_vulgo]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_vulgo]  DEFAULT ('') FOR [env_vulgo]
GO
/****** Object:  Default [DF_envolvidos_env_end_tp]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_end_tp]  DEFAULT (0) FOR [env_end_tp]
GO
/****** Object:  Default [DF_envolvidos_env_logr_tp]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_tp]  DEFAULT ('') FOR [env_logr_tp]
GO
/****** Object:  Default [DF_envolvidos_env_logr_ds]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_ds]  DEFAULT ('') FOR [env_logr_ds]
GO
/****** Object:  Default [DF_envolvidos_env_logr_num]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_num]  DEFAULT ('') FOR [env_logr_num]
GO
/****** Object:  Default [DF_envolvidos_env_logr_cmpl]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_cmpl]  DEFAULT ('') FOR [env_logr_cmpl]
GO
/****** Object:  Default [DF_envolvidos_env_logr_bairro]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_bairro]  DEFAULT ('') FOR [env_logr_bairro]
GO
/****** Object:  Default [DF_envolvidos_env_logr_subbairro]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_subbairro]  DEFAULT ('') FOR [env_logr_subbairro]
GO
/****** Object:  Default [DF_envolvidos_env_logr_mun]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_mun]  DEFAULT ('') FOR [env_logr_mun]
GO
/****** Object:  Default [DF_envolvidos_env_logr_uf]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_logr_uf]  DEFAULT ('') FOR [env_logr_uf]
GO
/****** Object:  Default [DF_envolvidos_env_loc_ref]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_loc_ref]  DEFAULT ('') FOR [env_loc_ref]
GO
/****** Object:  Default [DF_envolvidos_env_sexo]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_sexo]  DEFAULT ('') FOR [env_sexo]
GO
/****** Object:  Default [DF_envolvidos_env_idade]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_idade]  DEFAULT (0) FOR [env_idade]
GO
/****** Object:  Default [DF_envolvidos_env_pele]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_pele]  DEFAULT (0) FOR [env_pele]
GO
/****** Object:  Default [DF_envolvidos_env_estatura]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_estatura]  DEFAULT (0) FOR [env_estatura]
GO
/****** Object:  Default [DF_envolvidos_env_olhos]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_olhos]  DEFAULT (0) FOR [env_olhos]
GO
/****** Object:  Default [DF_envolvidos_env_cabelo]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_cabelo]  DEFAULT (0) FOR [env_cabelo]
GO
/****** Object:  Default [DF_envolvidos_env_porte]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_porte]  DEFAULT (0) FOR [env_porte]
GO
/****** Object:  Default [DF_envolvidos_env_caract]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[envolvidos] ADD  CONSTRAINT [DF_envolvidos_env_caract]  DEFAULT ('') FOR [env_caract]
GO
/****** Object:  Default [DF_item_itm_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[item] ADD  CONSTRAINT [DF_item_itm_st]  DEFAULT (0) FOR [itm_st]
GO
/****** Object:  Default [DF_item_classe_cli_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[item_classe] ADD  CONSTRAINT [DF_item_classe_cli_st]  DEFAULT (0) FOR [cli_st]
GO
/****** Object:  Default [DF_item_tipo_tpi_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[item_tipo] ADD  CONSTRAINT [DF_item_tipo_tpi_st]  DEFAULT (0) FOR [tpi_st]
GO
/****** Object:  Default [DF_lock_registro_locktime]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[lock_registro] ADD  CONSTRAINT [DF_lock_registro_locktime]  DEFAULT (getdate()) FOR [locktime]
GO
/****** Object:  Default [DF_lock_registro_u_status]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[lock_registro] ADD  CONSTRAINT [DF_lock_registro_u_status]  DEFAULT ('F') FOR [u_status]
GO
/****** Object:  Default [DF_log_alteracoes_data]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[log_alteracoes] ADD  CONSTRAINT [DF_log_alteracoes_data]  DEFAULT (getdate()) FOR [data]
GO
/****** Object:  Default [DF_orgaos_externos_ext_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_st]  DEFAULT (0) FOR [ext_st]
GO
/****** Object:  Default [DF_orgaos_externos_ext_dest]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_dest]  DEFAULT ('') FOR [ext_dest]
GO
/****** Object:  Default [DF_orgaos_externos_ext_endereco]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_endereco]  DEFAULT ('') FOR [ext_endereco]
GO
/****** Object:  Default [DF_orgaos_externos_ext_bairro]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_bairro]  DEFAULT ('') FOR [ext_bairro]
GO
/****** Object:  Default [DF_orgaos_externos_ext_municipio]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_municipio]  DEFAULT ('') FOR [ext_municipio]
GO
/****** Object:  Default [DF_orgaos_externos_ext_uf]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_uf]  DEFAULT ('') FOR [ext_uf]
GO
/****** Object:  Default [DF_orgaos_externos_ext_cep]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_cep]  DEFAULT (0) FOR [ext_cep]
GO
/****** Object:  Default [DF_orgaos_externos_ext_telefones]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos] ADD  CONSTRAINT [DF_orgaos_externos_ext_telefones]  DEFAULT ('') FOR [ext_telefones]
GO
/****** Object:  Default [DF_orgaos_externos_tipos_oet_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos_tipos] ADD  CONSTRAINT [DF_orgaos_externos_tipos_oet_st]  DEFAULT (0) FOR [oet_st]
GO
/****** Object:  Default [DF_orgaos_internos_int_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_internos] ADD  CONSTRAINT [DF_orgaos_internos_int_st]  DEFAULT (0) FOR [int_st]
GO
/****** Object:  Default [DF_orgaos_internos_int_dest]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_internos] ADD  CONSTRAINT [DF_orgaos_internos_int_dest]  DEFAULT ('') FOR [int_dest]
GO
/****** Object:  Default [DF_quantifica_resultado_qtf_data]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[quantifica_resultado] ADD  CONSTRAINT [DF_quantifica_resultado_qtf_data]  DEFAULT (getdate()) FOR [qtf_data]
GO
/****** Object:  Default [DF_resultado_direto_red_cad_data]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[resultado_direto] ADD  CONSTRAINT [DF_resultado_direto_red_cad_data]  DEFAULT (getdate()) FOR [red_cad_data]
GO
/****** Object:  Default [DF_resultado_direto_red_ext_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[resultado_direto] ADD  CONSTRAINT [DF_resultado_direto_red_ext_cd]  DEFAULT (0) FOR [red_ext_cd]
GO
/****** Object:  Default [DF_resultado_direto_red_oet_cd]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[resultado_direto] ADD  CONSTRAINT [DF_resultado_direto_red_oet_cd]  DEFAULT (0) FOR [red_oet_cd]
GO
/****** Object:  Default [DF_resultado_tipo_rtp_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[resultado_tipo] ADD  CONSTRAINT [DF_resultado_tipo_rtp_st]  DEFAULT (0) FOR [rtp_st]
GO
/****** Object:  Default [DF_rotinas_rot_st]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[rotinas] ADD  CONSTRAINT [DF_rotinas_rot_st]  DEFAULT (0) FOR [rot_st]
GO
/****** Object:  Default [DF_rotinas_rot_default_adm]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[rotinas] ADD  CONSTRAINT [DF_rotinas_rot_default_adm]  DEFAULT (0) FOR [rot_default_adm]
GO
/****** Object:  Default [DF_rotinas_rot_default_sup]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[rotinas] ADD  CONSTRAINT [DF_rotinas_rot_default_sup]  DEFAULT (0) FOR [rot_default_sup]
GO
/****** Object:  Default [DF_rotinas_rot_default_ana]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[rotinas] ADD  CONSTRAINT [DF_rotinas_rot_default_ana]  DEFAULT (0) FOR [rot_default_ana]
GO
/****** Object:  Default [DF_rotinas_rot_default_ate]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[rotinas] ADD  CONSTRAINT [DF_rotinas_rot_default_ate]  DEFAULT (0) FOR [rot_default_ate]
GO
/****** Object:  Default [DF_rotinas_ordem]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[rotinas] ADD  CONSTRAINT [DF_rotinas_ordem]  DEFAULT (0) FOR [ordem]
GO
/****** Object:  Default [DF_usuarios_usu_status]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[usuarios] ADD  CONSTRAINT [DF_usuarios_usu_status]  DEFAULT (0) FOR [usu_status]
GO
/****** Object:  Default [DF_veiculos_marca]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_marca]  DEFAULT (0) FOR [marca]
GO
/****** Object:  Default [DF_veiculos_modelo]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_modelo]  DEFAULT (0) FOR [modelo]
GO
/****** Object:  Default [DF_veiculos_cor]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_cor]  DEFAULT ('') FOR [cor]
GO
/****** Object:  Default [DF_veiculos_ano_mod]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_ano_mod]  DEFAULT (0) FOR [ano_mod]
GO
/****** Object:  Default [DF_veiculos_ano_fab]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_ano_fab]  DEFAULT (0) FOR [ano_fab]
GO
/****** Object:  Default [DF_veiculos_placa]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_placa]  DEFAULT ('') FOR [placa]
GO
/****** Object:  Default [DF_veiculos_municipio]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_municipio]  DEFAULT ('') FOR [municipio]
GO
/****** Object:  Default [DF_veiculos_uf]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_uf]  DEFAULT ('') FOR [uf]
GO
/****** Object:  Default [DF_veiculos_chassis]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_chassis]  DEFAULT (0) FOR [chassis]
GO
/****** Object:  Default [DF_veiculos_proprietario]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_proprietario]  DEFAULT ('') FOR [proprietario]
GO
/****** Object:  Default [DF_veiculos_com_nome]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_nome]  DEFAULT ('') FOR [com_nome]
GO
/****** Object:  Default [DF_veiculos_com_endereco]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_endereco]  DEFAULT ('') FOR [com_endereco]
GO
/****** Object:  Default [DF_veiculos_com_bairro]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_bairro]  DEFAULT ('') FOR [com_bairro]
GO
/****** Object:  Default [DF_veiculos_com_municipio]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_municipio]  DEFAULT ('') FOR [com_municipio]
GO
/****** Object:  Default [DF_veiculos_com_uf]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_uf]  DEFAULT ('') FOR [com_uf]
GO
/****** Object:  Default [DF_veiculos_com_telefone]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_telefone]  DEFAULT ('') FOR [com_telefone]
GO
/****** Object:  Default [DF_veiculos_com_doc]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_doc]  DEFAULT ('') FOR [com_doc]
GO
/****** Object:  Default [DF_veiculos_com_tipo]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_com_tipo]  DEFAULT (0) FOR [com_tipo]
GO
/****** Object:  Default [DF_veiculos_ro_dp]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_ro_dp]  DEFAULT ('') FOR [ro_dp]
GO
/****** Object:  Default [DF_veiculos_seguradora]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_seguradora]  DEFAULT ('') FOR [seguradora]
GO
/****** Object:  Default [DF_veiculos_caract]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[veiculos] ADD  CONSTRAINT [DF_veiculos_caract]  DEFAULT ('') FOR [detalhes]
GO
/****** Object:  Default [DF_xpto_xpt_status]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[xpto] ADD  CONSTRAINT [DF_xpto_xpt_status]  DEFAULT (0) FOR [xpt_st]
GO
/****** Object:  Default [DF_xpto_denuncia_dxp_data]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[xpto_denuncia] ADD  CONSTRAINT [DF_xpto_denuncia_dxp_data]  DEFAULT (getdate()) FOR [dxp_data]
GO
/****** Object:  Check [CK_usuarios]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[usuarios]  WITH NOCHECK ADD  CONSTRAINT [CK_usuarios] CHECK  ((len([usu_login]) >= 3 and len([usu_senha]) >= 0))
GO
ALTER TABLE [dbo].[usuarios] CHECK CONSTRAINT [CK_usuarios]
GO
/****** Object:  ForeignKey [FK_assunto_denuncia_assunto_tipo]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[assunto_denuncia]  WITH NOCHECK ADD  CONSTRAINT [FK_assunto_denuncia_assunto_tipo] FOREIGN KEY([ass_tpa_cd])
REFERENCES [dbo].[assunto_tipo] ([tpa_cd])
NOT FOR REPLICATION
GO
ALTER TABLE [dbo].[assunto_denuncia] CHECK CONSTRAINT [FK_assunto_denuncia_assunto_tipo]
GO
/****** Object:  ForeignKey [FK_assunto_tipo_assunto_classe]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[assunto_tipo]  WITH NOCHECK ADD  CONSTRAINT [FK_assunto_tipo_assunto_classe] FOREIGN KEY([tpa_cla_cd])
REFERENCES [dbo].[assunto_classe] ([cla_cd])
NOT FOR REPLICATION
GO
ALTER TABLE [dbo].[assunto_tipo] CHECK CONSTRAINT [FK_assunto_tipo_assunto_classe]
GO
/****** Object:  ForeignKey [FK_difusao_externa_orgaos_externos]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[difusao_externa]  WITH CHECK ADD  CONSTRAINT [FK_difusao_externa_orgaos_externos] FOREIGN KEY([dex_ext_cd])
REFERENCES [dbo].[orgaos_externos] ([ext_cd])
GO
ALTER TABLE [dbo].[difusao_externa] CHECK CONSTRAINT [FK_difusao_externa_orgaos_externos]
GO
/****** Object:  ForeignKey [FK_orgaos_externos_orgaos_externos_tipos]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[orgaos_externos]  WITH CHECK ADD  CONSTRAINT [FK_orgaos_externos_orgaos_externos_tipos] FOREIGN KEY([ext_oet_cd])
REFERENCES [dbo].[orgaos_externos_tipos] ([oet_cd])
GO
ALTER TABLE [dbo].[orgaos_externos] CHECK CONSTRAINT [FK_orgaos_externos_orgaos_externos_tipos]
GO
/****** Object:  ForeignKey [FK_vei_modelo_vei_marca]    Script Date: 04/09/2026 10:40:52 ******/
ALTER TABLE [dbo].[vei_modelo]  WITH CHECK ADD  CONSTRAINT [FK_vei_modelo_vei_marca] FOREIGN KEY([mar_cd])
REFERENCES [dbo].[vei_marca] ([mar_cd])
GO
ALTER TABLE [dbo].[vei_modelo] CHECK CONSTRAINT [FK_vei_modelo_vei_marca]
GO
