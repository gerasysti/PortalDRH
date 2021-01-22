


/*------ GERASYS.TI 10/09/2019 11:03:12 --------*/

SET TERM ^ ;

create or alter procedure SET_CARGA_HORARIA_PROFESSOR (
    ID_LANCTO DMN_GUID,
    ID_CLIENTE DMN_INTEGER,
    ID_SERVIDOR DMN_INTEGER,
    ID_ESCOLA DMN_INTEGER,
    ANO_MES DMN_CHAR06,
    QTDE_CH_NORMAL DMN_INTEGER,
    QTDE_CH_SUBSTITUICAO DMN_INTEGER,
    QTDE_CH_OUTRAS DMN_INTEGER,
    QTDE__FALTAS DMN_INTEGER,
    OBSERVACAO DMN_VARCHAR40,
    CALC_GRAT_SERIES_INICIAIS DMN_SIM_NAO = 'N',
    CALC_GRAT_DIFICIL_ACESSO DMN_SIM_NAO = 'N',
    CALC_GRAT_ENSINO_ESPEC DMN_SIM_NAO = 'N',
    CALC_GRAT_MULTI_SERIE DMN_SIM_NAO = 'N')
returns (
    ID_LANCTO_PROF DMN_GUID)
as
begin
  /* Buscar lancamento da Carga Horaria do Professor */
  Select
    lnc.id_lancto_prof
  from REMUN_LANCTO_CH_PROF lnc
  where (lnc.id_cliente = :id_cliente)
    and (lnc.id_servidor = :id_servidor)
    and (lnc.id_unid_lotacao = :id_escola)
    and (lnc.ano_mes = :ano_mes)
  Into
    id_lancto_prof;

  if (trim(coalesce(:id_lancto_prof, '')) = '') then
  begin
    /* Gerar GUID para novo lancamento */
    Select
      x.hex_uuid_format
    from GET_GUID_UUID_HEX x
    Into
      id_lancto_prof;

    /* Inserir lancamento de Carga Horaria */
    Insert Into REMUN_LANCTO_CH_PROF (
        id_lancto_prof
      , id_lancto
      , id_cliente
      , id_servidor
      , id_unid_lotacao
      , ano_mes
      , qtd_h_aula_normal
      , qtd_h_aula_substituicao
      , qtd_h_aula_outra
      , qtd_falta
      , observacao
      , calc_grat_series_iniciais
      , calc_grat_dificil_acesso
      , calc_grat_ensino_espec
      , calc_grat_multi_serie
    ) values (
        :id_lancto_prof
      , :id_lancto
      , :id_cliente
      , :id_servidor
      , :id_escola
      , :ano_mes
      , :qtde_ch_normal
      , :qtde_ch_substituicao
      , :qtde_ch_outras
      , :qtde__faltas
      , :observacao
      , :calc_grat_series_iniciais
      , :calc_grat_dificil_acesso
      , :calc_grat_ensino_espec
      , :calc_grat_multi_serie
    );
  end 
  else
  begin
    /* Atualizar lancamento de Carga Horaria */
    Update REMUN_LANCTO_CH_PROF lnc Set
        id_unid_lotacao         = :id_escola
      , ano_mes                 = :ano_mes
      , qtd_h_aula_normal       = :qtde_ch_normal
      , qtd_h_aula_substituicao = :qtde_ch_substituicao
      , qtd_h_aula_outra        = :qtde_ch_outras
      , qtd_falta               = :qtde__faltas
      , observacao              = :observacao
      , calc_grat_series_iniciais = :calc_grat_series_iniciais
      , calc_grat_dificil_acesso  = :calc_grat_dificil_acesso
      , calc_grat_ensino_espec    = :calc_grat_ensino_espec
      , calc_grat_multi_serie     = :calc_grat_multi_serie
    where (lnc.id_lancto_prof = :id_lancto_prof);
  end 

  suspend;
end
^

SET TERM ; ^

GRANT EXECUTE ON PROCEDURE SET_CARGA_HORARIA_PROFESSOR TO "PUBLIC";



/*------ GERASYS.TI 10/09/2019 11:08:59 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SET_CARGA_HORARIA_PROFESSOR (
    ID_LANCTO DMN_GUID,
    ID_CLIENTE DMN_INTEGER,
    ID_SERVIDOR DMN_INTEGER,
    ID_ESCOLA DMN_INTEGER,
    ANO_MES DMN_CHAR06,
    QTDE_CH_NORMAL DMN_INTEGER,
    QTDE_CH_SUBSTITUICAO DMN_INTEGER,
    QTDE_CH_OUTRAS DMN_INTEGER,
    QTDE__FALTAS DMN_INTEGER,
    OBSERVACAO DMN_VARCHAR40,
    CALC_GRAT_SERIES_INICIAIS DMN_SIM_NAO = 'n',
    CALC_GRAT_DIFICIL_ACESSO DMN_SIM_NAO = 'n',
    CALC_GRAT_ENSINO_ESPEC DMN_SIM_NAO = 'n',
    CALC_GRAT_MULTI_SERIE DMN_SIM_NAO = 'n')
returns (
    ID_LANCTO_PROF DMN_GUID)
as
begin
  /* Buscar lancamento da Carga Horaria do Professor */
  Select
    lnc.id_lancto_prof
  from REMUN_LANCTO_CH_PROF lnc
  where (lnc.id_cliente = :id_cliente)
    and (lnc.id_servidor = :id_servidor)
    and (lnc.id_unid_lotacao = :id_escola)
    and (lnc.ano_mes = :ano_mes)
  Into
    id_lancto_prof;

  if (trim(coalesce(:id_lancto_prof, '')) = '') then
  begin
    /* Gerar GUID para novo lancamento */
    Select
      x.hex_uuid_format
    from GET_GUID_UUID_HEX x
    Into
      id_lancto_prof;

    /* Inserir lancamento de Carga Horaria */
    Insert Into REMUN_LANCTO_CH_PROF (
        id_lancto_prof
      , id_lancto
      , id_cliente
      , id_servidor
      , id_unid_lotacao
      , ano_mes
      , qtd_h_aula_normal
      , qtd_h_aula_substituicao
      , qtd_h_aula_outra
      , qtd_falta
      , observacao
      , calc_grat_series_iniciais
      , calc_grat_dificil_acesso
      , calc_grat_ensino_espec
      , calc_grat_multi_serie
    ) values (
        :id_lancto_prof
      , :id_lancto
      , :id_cliente
      , :id_servidor
      , :id_escola
      , :ano_mes
      , :qtde_ch_normal
      , :qtde_ch_substituicao
      , :qtde_ch_outras
      , :qtde__faltas
      , :observacao
      , :calc_grat_series_iniciais
      , :calc_grat_dificil_acesso
      , :calc_grat_ensino_espec
      , :calc_grat_multi_serie
    );
  end 
  else
  begin
    /* Atualizar lancamento de Carga Horaria */
    Update REMUN_LANCTO_CH_PROF lnc Set
        id_unid_lotacao         = :id_escola
      , ano_mes                 = :ano_mes
      , qtd_h_aula_normal       = :qtde_ch_normal
      , qtd_h_aula_substituicao = :qtde_ch_substituicao
      , qtd_h_aula_outra        = :qtde_ch_outras
      , qtd_falta               = :qtde__faltas
      , observacao              = :observacao
      , calc_grat_series_iniciais = :calc_grat_series_iniciais
      , calc_grat_dificil_acesso  = :calc_grat_dificil_acesso
      , calc_grat_ensino_espec    = :calc_grat_ensino_espec
      , calc_grat_multi_serie     = :calc_grat_multi_serie
    where (lnc.id_lancto_prof = :id_lancto_prof);
  end 

  suspend;
end
^

SET TERM ; ^




/*------ GERASYS.TI 10/09/2019 11:09:24 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SET_CARGA_HORARIA_PROFESSOR (
    ID_LANCTO DMN_GUID,
    ID_CLIENTE DMN_INTEGER,
    ID_SERVIDOR DMN_INTEGER,
    ID_ESCOLA DMN_INTEGER,
    ANO_MES DMN_CHAR06,
    QTDE_CH_NORMAL DMN_INTEGER,
    QTDE_CH_SUBSTITUICAO DMN_INTEGER,
    QTDE_CH_OUTRAS DMN_INTEGER,
    QTDE__FALTAS DMN_INTEGER,
    OBSERVACAO DMN_VARCHAR40,
    CALC_GRAT_SERIES_INICIAIS DMN_SIM_NAO = 'N',
    CALC_GRAT_DIFICIL_ACESSO DMN_SIM_NAO = 'N',
    CALC_GRAT_ENSINO_ESPEC DMN_SIM_NAO = 'N',
    CALC_GRAT_MULTI_SERIE DMN_SIM_NAO = 'N')
returns (
    ID_LANCTO_PROF DMN_GUID)
as
begin
  /* Buscar lancamento da Carga Horaria do Professor */
  Select
    lnc.id_lancto_prof
  from REMUN_LANCTO_CH_PROF lnc
  where (lnc.id_cliente = :id_cliente)
    and (lnc.id_servidor = :id_servidor)
    and (lnc.id_unid_lotacao = :id_escola)
    and (lnc.ano_mes = :ano_mes)
  Into
    id_lancto_prof;

  if (trim(coalesce(:id_lancto_prof, '')) = '') then
  begin
    /* Gerar GUID para novo lancamento */
    Select
      x.hex_uuid_format
    from GET_GUID_UUID_HEX x
    Into
      id_lancto_prof;

    /* Inserir lancamento de Carga Horaria */
    Insert Into REMUN_LANCTO_CH_PROF (
        id_lancto_prof
      , id_lancto
      , id_cliente
      , id_servidor
      , id_unid_lotacao
      , ano_mes
      , qtd_h_aula_normal
      , qtd_h_aula_substituicao
      , qtd_h_aula_outra
      , qtd_falta
      , observacao
      , calc_grat_series_iniciais
      , calc_grat_dificil_acesso
      , calc_grat_ensino_espec
      , calc_grat_multi_serie
    ) values (
        :id_lancto_prof
      , :id_lancto
      , :id_cliente
      , :id_servidor
      , :id_escola
      , :ano_mes
      , :qtde_ch_normal
      , :qtde_ch_substituicao
      , :qtde_ch_outras
      , :qtde__faltas
      , :observacao
      , :calc_grat_series_iniciais
      , :calc_grat_dificil_acesso
      , :calc_grat_ensino_espec
      , :calc_grat_multi_serie
    );
  end 
  else
  begin
    /* Atualizar lancamento de Carga Horaria */
    Update REMUN_LANCTO_CH_PROF lnc Set
        id_unid_lotacao         = :id_escola
      , ano_mes                 = :ano_mes
      , qtd_h_aula_normal       = :qtde_ch_normal
      , qtd_h_aula_substituicao = :qtde_ch_substituicao
      , qtd_h_aula_outra        = :qtde_ch_outras
      , qtd_falta               = :qtde__faltas
      , observacao              = :observacao
      , calc_grat_series_iniciais = :calc_grat_series_iniciais
      , calc_grat_dificil_acesso  = :calc_grat_dificil_acesso
      , calc_grat_ensino_espec    = :calc_grat_ensino_espec
      , calc_grat_multi_serie     = :calc_grat_multi_serie
    where (lnc.id_lancto_prof = :id_lancto_prof);
  end 

  suspend;
end
^

SET TERM ; ^




/*------ GERASYS.TI 10/09/2019 11:13:50 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SET_CARGA_HORARIA_PROFESSOR (
    ID_LANCTO DMN_GUID,
    ID_CLIENTE DMN_INTEGER,
    ID_SERVIDOR DMN_INTEGER,
    ID_ESCOLA DMN_INTEGER,
    ANO_MES DMN_CHAR06,
    QTDE_CH_NORMAL DMN_INTEGER,
    QTDE_CH_SUBSTITUICAO DMN_INTEGER,
    QTDE_CH_OUTRAS DMN_INTEGER,
    QTDE_FALTAS DMN_INTEGER,
    OBSERVACAO DMN_VARCHAR40,
    CALC_GRAT_SERIES_INICIAIS DMN_SIM_NAO = 'N',
    CALC_GRAT_DIFICIL_ACESSO DMN_SIM_NAO = 'N',
    CALC_GRAT_ENSINO_ESPEC DMN_SIM_NAO = 'N',
    CALC_GRAT_MULTI_SERIE DMN_SIM_NAO = 'N')
returns (
    ID_LANCTO_PROF DMN_GUID)
as
begin
  /* Buscar lancamento da Carga Horaria do Professor */
  Select
    lnc.id_lancto_prof
  from REMUN_LANCTO_CH_PROF lnc
  where (lnc.id_cliente = :id_cliente)
    and (lnc.id_servidor = :id_servidor)
    and (lnc.id_unid_lotacao = :id_escola)
    and (lnc.ano_mes = :ano_mes)
  Into
    id_lancto_prof;

  if (trim(coalesce(:id_lancto_prof, '')) = '') then
  begin
    /* Gerar GUID para novo lancamento */
    Select
      x.hex_uuid_format
    from GET_GUID_UUID_HEX x
    Into
      id_lancto_prof;

    /* Inserir lancamento de Carga Horaria */
    Insert Into REMUN_LANCTO_CH_PROF (
        id_lancto_prof
      , id_lancto
      , id_cliente
      , id_servidor
      , id_unid_lotacao
      , ano_mes
      , qtd_h_aula_normal
      , qtd_h_aula_substituicao
      , qtd_h_aula_outra
      , qtd_falta
      , observacao
      , calc_grat_series_iniciais
      , calc_grat_dificil_acesso
      , calc_grat_ensino_espec
      , calc_grat_multi_serie
    ) values (
        :id_lancto_prof
      , :id_lancto
      , :id_cliente
      , :id_servidor
      , :id_escola
      , :ano_mes
      , :qtde_ch_normal
      , :qtde_ch_substituicao
      , :qtde_ch_outras
      , :qtde_faltas
      , :observacao
      , :calc_grat_series_iniciais
      , :calc_grat_dificil_acesso
      , :calc_grat_ensino_espec
      , :calc_grat_multi_serie
    );
  end 
  else
  begin
    /* Atualizar lancamento de Carga Horaria */
    Update REMUN_LANCTO_CH_PROF lnc Set
        id_unid_lotacao         = :id_escola
      , ano_mes                 = :ano_mes
      , qtd_h_aula_normal       = :qtde_ch_normal
      , qtd_h_aula_substituicao = :qtde_ch_substituicao
      , qtd_h_aula_outra        = :qtde_ch_outras
      , qtd_falta               = :qtde_faltas
      , observacao              = :observacao
      , calc_grat_series_iniciais = :calc_grat_series_iniciais
      , calc_grat_dificil_acesso  = :calc_grat_dificil_acesso
      , calc_grat_ensino_espec    = :calc_grat_ensino_espec
      , calc_grat_multi_serie     = :calc_grat_multi_serie
    where (lnc.id_lancto_prof = :id_lancto_prof);
  end 

  suspend;
end
^

SET TERM ; ^




/*------ GERASYS.TI 10/09/2019 16:30:12 --------*/

ALTER TABLE REMUN_LANCTO_CH
ADD CONSTRAINT UNQ_REMUN_LANCTO_CH_ANOMES
UNIQUE (ID_CLIENTE,ID_UNID_LOTACAO,ANO_MES,SITUACAO);



/*------ GERASYS.TI 11/10/2019 11:57:29 --------*/

/*!!! Error occured !!!
Column does not belong to referenced table.
Dynamic SQL Error.
SQL error code = -206.
Column unknown.
E.NR_EXERCICIO.
At line 2, column 7.

*/

/*------ GERASYS.TI 11/10/2019 12:00:50 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Invalid expression in the select list (not contained in either an aggregate function or the GROUP BY clause).

*/

/*------ GERASYS.TI 11/10/2019 12:01:15 --------*/

/*!!! Error occured !!!
Column does not belong to referenced table.
Dynamic SQL Error.
SQL error code = -206.
Column unknown.
X.NR_EXERCICIO.
At line 13, column 12.

*/

/*------ GERASYS.TI 15/10/2019 20:45:07 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Token unknown - line 24, column 5.
Union.

*/

/*------ GERASYS.TI 15/10/2019 20:45:18 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Invalid command.
count of column list and variable list do not match.

*/

/*------ GERASYS.TI 15/10/2019 20:45:38 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Invalid command.
invalid ORDER BY clause.

*/

/*------ GERASYS.TI 15/10/2019 20:45:39 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Invalid command.
invalid ORDER BY clause.

*/

/*------ GERASYS.TI 15/10/2019 20:48:11 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Token unknown - line 1, column 10.
first.

*/


/*------ GERASYS.TI 08/06/2020 20:07:46 --------*/

ALTER TABLE REMUN_LANCTO_CH_PROF
    ADD TIPO_FALTA DMN_SMALLINT_NN DEFAULT 0;

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.TIPO_FALTA IS
'Tipo da falta:
0 - Hora/Aula
1 - Dia';

alter table REMUN_LANCTO_CH_PROF
alter ID_LANCTO_PROF position 1;

alter table REMUN_LANCTO_CH_PROF
alter ID_LANCTO position 2;

alter table REMUN_LANCTO_CH_PROF
alter ID_CLIENTE position 3;

alter table REMUN_LANCTO_CH_PROF
alter ID_SERVIDOR position 4;

alter table REMUN_LANCTO_CH_PROF
alter ID_UNID_LOTACAO position 5;

alter table REMUN_LANCTO_CH_PROF
alter ANO_MES position 6;

alter table REMUN_LANCTO_CH_PROF
alter QTD_H_AULA_NORMAL position 7;

alter table REMUN_LANCTO_CH_PROF
alter QTD_H_AULA_SUBSTITUICAO position 8;

alter table REMUN_LANCTO_CH_PROF
alter QTD_H_AULA_OUTRA position 9;

alter table REMUN_LANCTO_CH_PROF
alter QTD_FALTA position 10;

alter table REMUN_LANCTO_CH_PROF
alter TIPO_FALTA position 11;

alter table REMUN_LANCTO_CH_PROF
alter OBSERVACAO position 12;

alter table REMUN_LANCTO_CH_PROF
alter CALC_GRAT_SERIES_INICIAIS position 13;

alter table REMUN_LANCTO_CH_PROF
alter CALC_GRAT_DIFICIL_ACESSO position 14;

alter table REMUN_LANCTO_CH_PROF
alter CALC_GRAT_ENSINO_ESPEC position 15;

alter table REMUN_LANCTO_CH_PROF
alter CALC_GRAT_MULTI_SERIE position 16;




/*------ GERASYS.TI 09/07/2020 16:22:00 --------*/

SET TERM ^ ;

create or alter procedure SP_DUPLICAR_LACTO_CH (
    ID_LANCTO_ORIGEM DMN_GUID,
    ID_LOTACAO_DESTINO DMN_INTEGER,
    ID_COMPETENCIA_DESTINO DMN_CHAR06,
    DATA DMN_DATE,
    HORA DMN_TIME,
    USUARIO DMN_INTEGER)
as
declare variable ID_CONTROLE DMN_BIGINT;
declare variable ID_LANCTO DMN_GUID;
begin
  Select
      g.hex_uuid_format
    , gen_id(GEN_LANCTO_CH, 1)
  from GET_GUID_UUID_HEX g
  Into
      id_lancto
    , id_controle;

  -- Gerar cabecalho
  Insert Into REMUN_LANCTO_CH (
      id_lancto
    , id_cliente
    , id_unid_lotacao
    , controle
    , ano_mes
    , data
    , hora
    , usuario
    , situacao
    , importado
  ) Select
        :id_lancto
      , a.id_cliente
      , :id_lotacao_destino
      , :id_controle
      , :id_competencia_destino
      , :data
      , :hora
      , :usuario
      , 0
      , 0
    from REMUN_LANCTO_CH a
    where (a.id_lancto = :id_lancto_origem);

  -- Gerar lancamentos detalhes
  Insert Into REMUN_LANCTO_CH_PROF (
      id_lancto_prof
    , id_lancto
    , id_cliente
    , id_servidor
    , id_unid_lotacao
    , ano_mes
    , qtd_h_aula_normal
    , qtd_h_aula_substituicao
    , qtd_h_aula_outra
    , qtd_falta
    , tipo_falta
    , observacao
    , calc_grat_series_iniciais
    , calc_grat_dificil_acesso
    , calc_grat_ensino_espec
    , calc_grat_multi_serie
  ) Select
        (Select first 1 x.hex_uuid_format from GET_GUID_UUID_HEX x) as id_lancto_prof
      , :id_lancto
      , b.id_cliente
      , b.id_servidor
      , :id_lotacao_destino
      , :id_competencia_destino
      , b.qtd_h_aula_normal
      , b.qtd_h_aula_substituicao
      , b.qtd_h_aula_outra
      , null
      , 0
      , b.observacao
      , b.calc_grat_series_iniciais
      , b.calc_grat_dificil_acesso
      , b.calc_grat_ensino_espec
      , b.calc_grat_multi_serie
    from REMUN_LANCTO_CH_PROF b
    where (b.id_lancto = :id_lancto_origem);
end

^

SET TERM ; ^

GRANT EXECUTE ON PROCEDURE SP_DUPLICAR_LACTO_CH TO "PUBLIC";



/*------ GERASYS.TI 09/07/2020 16:24:21 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_DUPLICAR_LACTO_CH (
    ID_LANCTO_ORIGEM DMN_GUID,
    ID_LOTACAO_DESTINO DMN_INTEGER,
    ID_COMPETENCIA_DESTINO DMN_CHAR06,
    DATA DMN_DATE,
    HORA DMN_TIME,
    USUARIO DMN_INTEGER)
as
declare variable ID_CONTROLE DMN_BIGINT;
declare variable ID_LANCTO DMN_GUID;
begin
  Select
      g.hex_uuid_format
    , gen_id(GEN_LANCTO_CH, 1)
  from GET_GUID_UUID_HEX g
  Into
      id_lancto
    , id_controle;

  -- Gerar cabecalho
  Insert Into REMUN_LANCTO_CH (
      id_lancto
    , id_cliente
    , id_unid_lotacao
    , controle
    , ano_mes
    , data
    , hora
    , usuario
    , situacao
    , importado
  ) Select
        :id_lancto
      , a.id_cliente
      , :id_lotacao_destino
      , :id_controle
      , :id_competencia_destino
      , :data
      , :hora
      , :usuario
      , 0
      , 0
    from REMUN_LANCTO_CH a
    where (a.id_lancto = :id_lancto_origem);

  -- Gerar lancamentos detalhes
  Insert Into REMUN_LANCTO_CH_PROF (
      id_lancto_prof
    , id_lancto
    , id_cliente
    , id_servidor
    , id_unid_lotacao
    , ano_mes
    , qtd_h_aula_normal
    , qtd_h_aula_substituicao
    , qtd_h_aula_outra
    , qtd_falta
    , tipo_falta
    , observacao
    , calc_grat_series_iniciais
    , calc_grat_dificil_acesso
    , calc_grat_ensino_espec
    , calc_grat_multi_serie
  ) Select
        (Select first 1 x.hex_uuid_format from GET_GUID_UUID_HEX x) as id_lancto_prof
      , :id_lancto
      , b.id_cliente
      , b.id_servidor
      , :id_lotacao_destino
      , :id_competencia_destino
      , b.qtd_h_aula_normal
      , b.qtd_h_aula_substituicao
      , b.qtd_h_aula_outra
      , null
      , 0
      , b.observacao
      , b.calc_grat_series_iniciais
      , b.calc_grat_dificil_acesso
      , b.calc_grat_ensino_espec
      , b.calc_grat_multi_serie
    from REMUN_LANCTO_CH_PROF b
    where (b.id_lancto = :id_lancto_origem);
end

^

SET TERM ; ^

COMMENT ON PROCEDURE SP_DUPLICAR_LACTO_CH IS 'Procedure DUPLICAR LANCAMENTOS DE CARGA HORARIA.

    Autor   :   Isaque M. Ribeiro
    Data    :   09/07/2020

Stored procedure responsavel por gerar novos lancamentos de cargas horarios para
professores com base em outro lancamento previamente existente.';



/*------ GERASYS.TI 09/07/2020 16:55:37 --------*/

/*!!! Error occured !!!
Arithmetic overflow or division by zero has occurred.
arithmetic exception, numeric overflow, or string truncation.
string right truncation.

*/

/*------ GERASYS.TI 09/07/2020 16:55:59 --------*/

/*!!! Error occured !!!
Column does not belong to referenced table.
Dynamic SQL Error.
SQL error code = -206.
Column unknown.
U.NOME.
At line 6, column 16.

*/


/*------ GERASYS.TI 13/01/2021 20:22:51 --------*/

create view view_clientes (
    id
  , nome
  , cnpj
  , municipio_nome
  , municipio_uf
  , titulo_portal
)
as
Select
    u.id
  , u.nome
  , u.cnpj
  , u.municipio_nome
  , u.municipio_uf
  , trim(coalesce(nullif(trim(u.titulo_portal), ''), u.nome)) as titulo_portal
from ADM_CLIENTE u
order by
    6

;

GRANT ALL ON VIEW_CLIENTES TO "PUBLIC";



/*------ GERASYS.TI 13/01/2021 20:26:15 --------*/

CREATE VIEW VW_CLIENTES(
    ID,
    NOME,
    CNPJ,
    MUNICIPIO_NOME,
    MUNICIPIO_UF,
    TITULO_PORTAL)
AS
Select
    u.id
  , u.nome
  , u.cnpj
  , u.municipio_nome
  , u.municipio_uf
  , trim(coalesce(nullif(trim(u.titulo_portal), ''), u.nome)) as titulo_portal
from ADM_CLIENTE u
order by
    6
;

GRANT ALL ON VW_CLIENTES TO "PUBLIC";



/*------ GERASYS.TI 13/01/2021 20:26:26 --------*/

DROP VIEW VIEW_CLIENTES;




/*------ GERASYS.TI 22/01/2021 15:10:58 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZA_TAB_CARGO (
    PE_ANO_MES char(6),
    PE_ID_CLIENTE integer,
    PE_ID_CARGO integer,
    PE_DESCRICAO varchar(50),
    PE_TIPO_TCM char(2),
    PE_QTD_VAGAS integer,
    PE_NUM_ATO_CRIACAO varchar(11),
    PE_DT_ATO_CRIACAO date,
    PE_VENCTO_BASE numeric(15,4),
    PE_TIPO_SAL char(1),
    PE_BASE_CALC integer,
    PE_FORMA_CALC_SAL char(1),
    PE_DESCR_TIPO_TCM varchar(80),
    PE_QTD_REF smallint,
    PE_VAL_REF00 numeric(15,4),
    PE_VAL_REF01 numeric(15,4),
    PE_VAL_REF02 numeric(15,4),
    PE_VAL_REF03 numeric(15,4),
    PE_VAL_REF04 numeric(15,4),
    PE_VAL_REF05 numeric(15,4),
    PE_VAL_REF06 numeric(15,4),
    PE_VAL_REF07 numeric(15,4),
    PE_VAL_REF08 numeric(15,4),
    PE_VAL_REF09 numeric(15,4),
    PE_VAL_REF10 numeric(15,4),
    PE_VAL_REF11 numeric(15,4),
    PE_VAL_REF12 numeric(15,4),
    PE_VAL_REF13 numeric(15,4),
    PE_VAL_REF14 numeric(15,4),
    PE_VAL_REF15 numeric(15,4))
as
begin
    UPDATE OR INSERT INTO REMUN_CARGO_FUNCAO (
        id_cliente
      , id_cargo
      , descricao
      , tipo_tcm
      , qtd_vagas
      , num_ato_criacao
      , dt_ato_criacao
      , vencto_base
      , tipo_sal
      , base_calc
      , forma_calc_sal
      , descr_tipo_tcm
      , qtd_ref
    ) values (
        :pe_id_cliente
      , :pe_id_cargo
      , :pe_descricao
      , :pe_tipo_tcm
      , :pe_qtd_vagas
      , :pe_num_ato_criacao
      , :pe_dt_ato_criacao
      , :pe_vencto_base
      , :pe_tipo_sal
      , :pe_base_calc
      , :pe_forma_calc_sal
      , :pe_descr_tipo_tcm
      , :pe_qtd_ref
    ) MATCHING (
        id_cliente
      , id_cargo);

/*
   update remun_cargo_funcao set
      DESCRICAO        = :pe_descricao,
      TIPO_TCM         = :pe_tipo_tcm,
      QTD_VAGAS        = :pe_qtd_vagas,
      NUM_ATO_CRIACAO  = :pe_num_ato_criacao,
      DT_ATO_CRIACAO   = :pe_dt_ato_criacao,
      VENCTO_BASE      = :pe_vencto_base,
      TIPO_SAL         = :pe_tipo_sal,
      BASE_CALC        = :pe_base_calc,
      FORMA_CALC_SAL   = :pe_forma_calc_sal,
      DESCR_TIPO_TCM   = :pe_descr_tipo_tcm,
      QTD_REF          = :pe_qtd_ref
   where
      (id_cliente = :pe_id_cliente) and
      (id_cargo   = :pe_id_cargo);
   if (row_count = 0) then
   begin
      insert into remun_cargo_funcao(ID_CLIENTE, ID_CARGO, DESCRICAO,
         TIPO_TCM, QTD_VAGAS, NUM_ATO_CRIACAO, DT_ATO_CRIACAO,
         VENCTO_BASE, TIPO_SAL, BASE_CALC, FORMA_CALC_SAL,
         DESCR_TIPO_TCM, QTD_REF)
      values(:pe_id_cliente, :pe_id_cargo, :pe_descricao, :pe_tipo_tcm,
         :pe_qtd_vagas, :pe_num_ato_criacao, :pe_dt_ato_criacao,
         :pe_vencto_base, :pe_tipo_sal, :pe_base_calc, :pe_forma_calc_sal,
         :pe_descr_tipo_tcm, :pe_qtd_ref);
   end
*/
   if (pe_val_ref00 = '0') then
      pe_val_ref00 = Null;
   if (pe_val_ref01 = '0') then
      pe_val_ref01 = Null;
   if (pe_val_ref02 = '0') then
      pe_val_ref02 = Null;
   if (pe_val_ref03 = '0') then
      pe_val_ref03 = Null;
   if (pe_val_ref04 = '0') then
      pe_val_ref04 = Null;
   if (pe_val_ref05 = '0') then
      pe_val_ref05 = Null;
   if (pe_val_ref06 = '0') then
      pe_val_ref06 = Null;
   if (pe_val_ref07 = '0') then
      pe_val_ref07 = Null;
   if (pe_val_ref08 = '0') then
      pe_val_ref08 = Null;
   if (pe_val_ref09 = '0') then
      pe_val_ref09 = Null;
   if (pe_val_ref10 = '0') then
      pe_val_ref10 = Null;
   if (pe_val_ref11 = '0') then
      pe_val_ref11 = Null;
   if (pe_val_ref12 = '0') then
      pe_val_ref12 = Null;
   if (pe_val_ref13 = '0') then
      pe_val_ref13 = Null;
   if (pe_val_ref14 = '0') then
      pe_val_ref14 = Null;
   if (pe_val_ref15 = '0') then
      pe_val_ref15 = Null;

   update remun_cargo_ref set
      VAL_REF00 = :pe_val_ref00, VAL_REF01 = :pe_val_ref01,
      VAL_REF02 = :pe_val_ref02, VAL_REF03 = :pe_val_ref03,
      VAL_REF04 = :pe_val_ref04, VAL_REF05 = :pe_val_ref05,
      VAL_REF06 = :pe_val_ref06, VAL_REF07 = :pe_val_ref07,
      VAL_REF08 = :pe_val_ref08, VAL_REF09 = :pe_val_ref09,
      VAL_REF10 = :pe_val_ref10, VAL_REF11 = :pe_val_ref11,
      VAL_REF12 = :pe_val_ref12, VAL_REF13 = :pe_val_ref13,
      VAL_REF14 = :pe_val_ref14, VAL_REF15 = :pe_val_ref15
   where
      (id_cliente = :pe_id_cliente) and
      (ano_mes = :pe_ano_mes) and
      (id_cargo = :pe_id_cargo);
   if (row_count = 0) then
   begin
      insert into remun_cargo_ref(ID_CLIENTE, ANO_MES, ID_CARGO,
         VAL_REF00, VAL_REF01, VAL_REF02, VAL_REF03, VAL_REF04,
         VAL_REF05, VAL_REF06, VAL_REF07, VAL_REF08, VAL_REF09,
         VAL_REF10, VAL_REF11, VAL_REF12, VAL_REF13, VAL_REF14,
         VAL_REF15)
      values(:pe_ID_CLIENTE, :pe_ANO_MES, :pe_ID_CARGO,
         :pe_VAL_REF00, :pe_VAL_REF01, :pe_VAL_REF02, :pe_VAL_REF03, :pe_VAL_REF04,
         :pe_VAL_REF05, :pe_VAL_REF06, :pe_VAL_REF07, :pe_VAL_REF08, :pe_VAL_REF09,
         :pe_VAL_REF10, :pe_VAL_REF11, :pe_VAL_REF12, :pe_VAL_REF13, :pe_VAL_REF14,
         :pe_VAL_REF15);
   end

end
^

SET TERM ; ^




/*------ GERASYS.TI 22/01/2021 15:15:06 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZA_TAB_CARGO (
    PE_ANO_MES char(6),
    PE_ID_CLIENTE integer,
    PE_ID_CARGO integer,
    PE_DESCRICAO varchar(50),
    PE_TIPO_TCM char(2),
    PE_QTD_VAGAS integer,
    PE_NUM_ATO_CRIACAO varchar(11),
    PE_DT_ATO_CRIACAO date,
    PE_VENCTO_BASE numeric(15,4),
    PE_TIPO_SAL char(1),
    PE_BASE_CALC integer,
    PE_FORMA_CALC_SAL char(1),
    PE_DESCR_TIPO_TCM varchar(80),
    PE_QTD_REF smallint,
    PE_VAL_REF00 numeric(15,4),
    PE_VAL_REF01 numeric(15,4),
    PE_VAL_REF02 numeric(15,4),
    PE_VAL_REF03 numeric(15,4),
    PE_VAL_REF04 numeric(15,4),
    PE_VAL_REF05 numeric(15,4),
    PE_VAL_REF06 numeric(15,4),
    PE_VAL_REF07 numeric(15,4),
    PE_VAL_REF08 numeric(15,4),
    PE_VAL_REF09 numeric(15,4),
    PE_VAL_REF10 numeric(15,4),
    PE_VAL_REF11 numeric(15,4),
    PE_VAL_REF12 numeric(15,4),
    PE_VAL_REF13 numeric(15,4),
    PE_VAL_REF14 numeric(15,4),
    PE_VAL_REF15 numeric(15,4))
as
begin
    UPDATE OR INSERT INTO REMUN_CARGO_FUNCAO (
        id_cliente
      , id_cargo
      , descricao
      , tipo_tcm
      , qtd_vagas
      , num_ato_criacao
      , dt_ato_criacao
      , vencto_base
      , tipo_sal
      , base_calc
      , forma_calc_sal
      , descr_tipo_tcm
      , qtd_ref
    ) values (
        :pe_id_cliente
      , :pe_id_cargo
      , :pe_descricao
      , :pe_tipo_tcm
      , :pe_qtd_vagas
      , :pe_num_ato_criacao
      , :pe_dt_ato_criacao
      , :pe_vencto_base
      , :pe_tipo_sal
      , :pe_base_calc
      , :pe_forma_calc_sal
      , :pe_descr_tipo_tcm
      , :pe_qtd_ref
    ) MATCHING (
        id_cliente
      , id_cargo);

   if (pe_val_ref00 = '0') then
      pe_val_ref00 = Null;
   if (pe_val_ref01 = '0') then
      pe_val_ref01 = Null;
   if (pe_val_ref02 = '0') then
      pe_val_ref02 = Null;
   if (pe_val_ref03 = '0') then
      pe_val_ref03 = Null;
   if (pe_val_ref04 = '0') then
      pe_val_ref04 = Null;
   if (pe_val_ref05 = '0') then
      pe_val_ref05 = Null;
   if (pe_val_ref06 = '0') then
      pe_val_ref06 = Null;
   if (pe_val_ref07 = '0') then
      pe_val_ref07 = Null;
   if (pe_val_ref08 = '0') then
      pe_val_ref08 = Null;
   if (pe_val_ref09 = '0') then
      pe_val_ref09 = Null;
   if (pe_val_ref10 = '0') then
      pe_val_ref10 = Null;
   if (pe_val_ref11 = '0') then
      pe_val_ref11 = Null;
   if (pe_val_ref12 = '0') then
      pe_val_ref12 = Null;
   if (pe_val_ref13 = '0') then
      pe_val_ref13 = Null;
   if (pe_val_ref14 = '0') then
      pe_val_ref14 = Null;
   if (pe_val_ref15 = '0') then
      pe_val_ref15 = Null;

    UPDATE OR INSERT INTO REMUN_CARGO_REF (
        id_cliente
      , ano_mes
      , id_cargo
      , val_ref00
      , val_ref01
      , val_ref02
      , val_ref03
      , val_ref04
      , val_ref05
      , val_ref06
      , val_ref07
      , val_ref08
      , val_ref09
      , val_ref10
      , val_ref11
      , val_ref12
      , val_ref13
      , val_ref14
      , val_ref15
    ) values (
        :pe_id_cliente
      , :pe_ano_mes
      , :pe_id_cargo
      , :pe_val_ref00
      , :pe_val_ref01
      , :pe_val_ref02
      , :pe_val_ref03
      , :pe_val_ref04
      , :pe_val_ref05
      , :pe_val_ref06
      , :pe_val_ref07
      , :pe_val_ref08
      , :pe_val_ref09
      , :pe_val_ref10
      , :pe_val_ref11
      , :pe_val_ref12
      , :pe_val_ref13
      , :pe_val_ref14
      , :pe_val_ref15
    ) MATCHING (
        id_cliente
      , ano_mes
      , id_cargo
    );
end
^

SET TERM ; ^

