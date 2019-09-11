


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

