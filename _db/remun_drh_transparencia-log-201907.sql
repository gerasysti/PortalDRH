


/*------ GERASYS.TI 03/07/2019 13:13:12 --------*/

CREATE TABLE REMUN_EVENTO_AVULSO (
    ID_CLIENTE DMN_INTEGER_NN NOT NULL,
    ID_UNID_GESTORA DMN_INTEGER_NN NOT NULL,
    ID_UNID_LOTACAO DMN_INTEGER_NN NOT NULL,
    ID_EVENTO DMN_INTEGER_NN NOT NULL,
    ANO_MES DMN_CHAR06_NN NOT NULL,
    DATA DMN_DATE,
    HORA DMN_TIME,
    USUARIO DMN_INTEGER,
    SITUACAO DMN_SMALLINT_NN DEFAULT 0,
    IMPORTADO DMN_BOOLEAN DEFAULT 0);

ALTER TABLE REMUN_EVENTO_AVULSO
ADD CONSTRAINT PK_REMUN_EVENTO_AVULSO
PRIMARY KEY (ID_CLIENTE,ID_EVENTO,ID_UNID_GESTORA,ID_UNID_LOTACAO,ANO_MES);

COMMENT ON COLUMN REMUN_EVENTO_AVULSO.DATA IS
'Data de lancamento';

COMMENT ON COLUMN REMUN_EVENTO_AVULSO.HORA IS
'Hora de lancamento';

COMMENT ON COLUMN REMUN_EVENTO_AVULSO.USUARIO IS
'Usuario do lancamento';

COMMENT ON COLUMN REMUN_EVENTO_AVULSO.SITUACAO IS
'Situacao:
0 - Aberto
1 - Finalizado
2 - Cancelado';

COMMENT ON COLUMN REMUN_EVENTO_AVULSO.IMPORTADO IS
'Importando pelo Remuneratus:
0 - Nao
1 - Sim';

GRANT ALL ON REMUN_EVENTO_AVULSO TO "PUBLIC";



/*------ GERASYS.TI 03/07/2019 13:15:43 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_UGT
FOREIGN KEY (ID_CLIENTE,ID_UNID_GESTORA)
REFERENCES REMUN_UNID_GESTORA(ID_CLIENTE,ID);

ALTER TABLE REMUN_EVENTO_AVULSO
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_ULO
FOREIGN KEY (ID_CLIENTE,ID_UNID_LOTACAO)
REFERENCES REMUN_UNID_LOTACAO(ID_CLIENTE,ID_LOTACAO);

ALTER TABLE REMUN_EVENTO_AVULSO
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_EVENTO
FOREIGN KEY (ID_CLIENTE,ID_EVENTO)
REFERENCES REMUN_EVENTO(ID_CLIENTE,ID_EVENTO);

ALTER TABLE REMUN_EVENTO_AVULSO
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_USER
FOREIGN KEY (USUARIO)
REFERENCES ADM_USUARIO(ID);




/*------ GERASYS.TI 03/07/2019 13:19:39 --------*/

CREATE DOMAIN DMN_NUMERO AS
NUMERIC(18,2);


/*------ GERASYS.TI 03/07/2019 13:20:55 --------*/

CREATE TABLE REMUN_EVENTO_AVULSO_ITEM (
    ID_CLIENTE DMN_INTEGER_NN NOT NULL,
    ID_UNID_GESTORA DMN_INTEGER_NN NOT NULL,
    ID_UNID_LOTACAO DMN_INTEGER_NN NOT NULL,
    ID_EVENTO DMN_INTEGER_NN NOT NULL,
    ANO_MES DMN_CHAR06_NN NOT NULL,
    ID_SERVIDOR DMN_INTEGER_NN NOT NULL,
    QUANT DMN_NUMERO,
    VALOR DMN_NUMERO,
    OBS DMN_VARCHAR40);

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM
ADD CONSTRAINT PK_REMUN_EVENTO_AVULSO_ITEM
PRIMARY KEY (ID_CLIENTE,ID_UNID_GESTORA,ID_UNID_LOTACAO,ID_EVENTO,ANO_MES,ID_SERVIDOR);

GRANT ALL ON REMUN_EVENTO_AVULSO_ITEM TO "PUBLIC";



/*------ GERASYS.TI 03/07/2019 13:21:50 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_ITEM
FOREIGN KEY (ID_CLIENTE,ID_UNID_GESTORA,ID_UNID_LOTACAO,ID_EVENTO,ANO_MES)
REFERENCES REMUN_EVENTO_AVULSO(ID_CLIENTE,ID_EVENTO,ID_UNID_GESTORA,ID_UNID_LOTACAO,ANO_MES)
ON DELETE CASCADE
ON UPDATE CASCADE;

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_ITEM_SRV
FOREIGN KEY (ID_CLIENTE,ID_SERVIDOR)
REFERENCES REMUN_SERVIDOR(ID_CLIENTE,ID_SERVIDOR);




/*------ GERASYS.TI 03/07/2019 13:22:42 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM
    ADD SEQUENCIA DMN_INTEGER_NN;

alter table REMUN_EVENTO_AVULSO_ITEM
alter ID_CLIENTE position 1;

alter table REMUN_EVENTO_AVULSO_ITEM
alter ID_UNID_GESTORA position 2;

alter table REMUN_EVENTO_AVULSO_ITEM
alter ID_UNID_LOTACAO position 3;

alter table REMUN_EVENTO_AVULSO_ITEM
alter ID_EVENTO position 4;

alter table REMUN_EVENTO_AVULSO_ITEM
alter ANO_MES position 5;

alter table REMUN_EVENTO_AVULSO_ITEM
alter ID_SERVIDOR position 6;

alter table REMUN_EVENTO_AVULSO_ITEM
alter SEQUENCIA position 7;

alter table REMUN_EVENTO_AVULSO_ITEM
alter QUANT position 8;

alter table REMUN_EVENTO_AVULSO_ITEM
alter VALOR position 9;

alter table REMUN_EVENTO_AVULSO_ITEM
alter OBS position 10;




/*------ GERASYS.TI 03/07/2019 13:34:41 --------*/

SET TERM ^ ;

create or alter procedure SET_REMUN_EVENTO_AVULSO_ITEM (
    ID_CLIENTE DMN_INTEGER,
    ID_UNID_GESTORA DMN_INTEGER,
    ID_UNID_LOTACAO DMN_INTEGER,
    ID_EVENTO DMN_INTEGER,
    ANO_MES DMN_CHAR06)
as
begin
  if (not exists(
    Select
      e.data
    from REMUN_EVENTO_AVULSO e
    where (e.id_cliente      = :id_cliente)
      and (e.id_unid_gestora = :id_unid_gestora)
      and (e.id_unid_lotacao = :id_unid_lotacao)
      and (e.id_evento       = :id_evento)
      and (e.ano_mes         = :ano_mes)
  )) then
  begin
      Insert Into REMUN_EVENTO_AVULSO_ITEM (
          id_cliente
        , id_unid_gestora
        , id_unid_lotacao
        , id_evento
        , ano_mes
        , id_servidor
        , quant
        , valor
      ) Select
            sv.id_cliente
          , sv.id_unid_gestora
          , sv.id_unid_lotacao
          , :id_evento as id_evento
          , :ano_mes   as ano_mes
          , sv.id_servidor
          , null
          , null
        from REMUN_SERVIDOR sv
          left join REMUN_EVENTO_AVULSO_ITEM lc on (lc.id_cliente = sv.id_cliente and lc.id_servidor = sv.id_servidor and lc.id_evento = :id_evento and lc.ano_mes = :ano_mes)
        where (sv.id_cliente      = :id_cliente)
          and (sv.id_unid_gestora = :id_unid_gestora)
          and (sv.id_unid_lotacao = :id_unid_lotacao)
          and (lc.sequencia is null);
  end
end
^

SET TERM ; ^

GRANT EXECUTE ON PROCEDURE SET_REMUN_EVENTO_AVULSO_ITEM TO "PUBLIC";



/*------ GERASYS.TI 03/07/2019 13:37:02 --------*/

SET TERM ^ ;

CREATE trigger tg_remun_evento_avulso_item_seq for remun_evento_avulso_item
active before insert position 0
AS
begin
  Select
    max(e.sequencia)
  from REMUN_EVENTO_AVULSO_ITEM e
  where (e.id_cliente      = new.id_cliente)
    and (e.id_unid_gestora = new.id_unid_gestora)
    and (e.id_unid_lotacao = new.id_unid_lotacao)
    and (e.id_evento       = new.id_evento)
    and (e.ano_mes         = new.ano_mes)
    and (e.id_servidor     = new.id_servidor)
  Into
    new.sequencia;

  new.sequencia = coalesce(new.sequencia, 0) + 1;
end
^

SET TERM ; ^




/*------ GERASYS.TI 03/07/2019 13:37:43 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SET_REMUN_EVENTO_AVULSO_ITEM (
    ID_CLIENTE DMN_INTEGER,
    ID_UNID_GESTORA DMN_INTEGER,
    ID_UNID_LOTACAO DMN_INTEGER,
    ID_EVENTO DMN_INTEGER,
    ANO_MES DMN_CHAR06)
as
begin
  if (not exists(
    Select
      e.data
    from REMUN_EVENTO_AVULSO e
    where (e.id_cliente      = :id_cliente)
      and (e.id_unid_gestora = :id_unid_gestora)
      and (e.id_unid_lotacao = :id_unid_lotacao)
      and (e.id_evento       = :id_evento)
      and (e.ano_mes         = :ano_mes)
  )) then
  begin
      Insert Into REMUN_EVENTO_AVULSO_ITEM (
          id_cliente
        , id_unid_gestora
        , id_unid_lotacao
        , id_evento
        , ano_mes
        , id_servidor
        , quant
        , valor
      ) Select
            sv.id_cliente
          , sv.id_unid_gestora
          , sv.id_unid_lotacao
          , :id_evento as id_evento
          , :ano_mes   as ano_mes
          , sv.id_servidor
          , null
          , null
        from REMUN_SERVIDOR sv
          left join REMUN_EVENTO_AVULSO_ITEM lc on (lc.id_cliente = sv.id_cliente and lc.id_servidor = sv.id_servidor and lc.id_evento = :id_evento and lc.ano_mes = :ano_mes)
        where (sv.id_cliente      = :id_cliente)
          and (sv.id_unid_gestora = :id_unid_gestora)
          and (sv.id_unid_lotacao = :id_unid_lotacao)
          and (lc.sequencia is null)
        order by
          sv.nome;
  end
end
^

SET TERM ; ^




/*------ GERASYS.TI 03/07/2019 13:38:42 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SET_REMUN_EVENTO_AVULSO_ITEM (
    ID_CLIENTE DMN_INTEGER,
    ID_UNID_GESTORA DMN_INTEGER,
    ID_UNID_LOTACAO DMN_INTEGER,
    ID_EVENTO DMN_INTEGER,
    ANO_MES DMN_CHAR06)
as
begin
  if (not exists(
    Select
      e.data
    from REMUN_EVENTO_AVULSO e
    where (e.id_cliente      = :id_cliente)
      and (e.id_unid_gestora = :id_unid_gestora)
      and (e.id_unid_lotacao = :id_unid_lotacao)
      and (e.id_evento       = :id_evento)
      and (e.ano_mes         = :ano_mes)
  )) then
  begin
      Insert Into REMUN_EVENTO_AVULSO_ITEM (
          id_cliente
        , id_unid_gestora
        , id_unid_lotacao
        , id_evento
        , ano_mes
        , id_servidor
        , quant
        , valor
      ) Select
            sv.id_cliente
          , sv.id_unid_gestora
          , sv.id_unid_lotacao
          , :id_evento as id_evento
          , :ano_mes   as ano_mes
          , sv.id_servidor
          , null
          , null
        from REMUN_SERVIDOR sv
          left join REMUN_EVENTO_AVULSO_ITEM lc on (lc.id_cliente = sv.id_cliente and lc.id_servidor = sv.id_servidor and lc.id_evento = :id_evento and lc.ano_mes = :ano_mes)
        where (sv.id_cliente      = :id_cliente)
          and (sv.id_unid_gestora = :id_unid_gestora)
          and (sv.id_unid_lotacao = :id_unid_lotacao)
          and (sv.situacao        = 1)
          and (lc.sequencia is null)
        order by
          sv.nome;
  end
end
^

SET TERM ; ^



/*------ GERASYS.TI 03/07/2019 14:47:33 --------*/

/*!!! Error occured !!!
Column does not belong to referenced table.
Dynamic SQL Error.
SQL error code = -206.
Column unknown.
LC.ID_LOTACAO.
At line 39, column 15.

*/


/*------ GERASYS.TI 03/07/2019 15:55:01 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO
    ADD CONTROLE DMN_INTEGER_NN;

alter table REMUN_EVENTO_AVULSO
alter ID_CLIENTE position 1;

alter table REMUN_EVENTO_AVULSO
alter ID_UNID_GESTORA position 2;

alter table REMUN_EVENTO_AVULSO
alter ID_UNID_LOTACAO position 3;

alter table REMUN_EVENTO_AVULSO
alter ID_EVENTO position 4;

alter table REMUN_EVENTO_AVULSO
alter ANO_MES position 5;

alter table REMUN_EVENTO_AVULSO
alter CONTROLE position 6;

alter table REMUN_EVENTO_AVULSO
alter DATA position 7;

alter table REMUN_EVENTO_AVULSO
alter HORA position 8;

alter table REMUN_EVENTO_AVULSO
alter USUARIO position 9;

alter table REMUN_EVENTO_AVULSO
alter SITUACAO position 10;

alter table REMUN_EVENTO_AVULSO
alter IMPORTADO position 11;




/*------ GERASYS.TI 03/07/2019 21:27:28 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO
ADD CONSTRAINT UNQ_REMUN_EVENTO_AVULSO
UNIQUE (CONTROLE);




/*------ GERASYS.TI 03/07/2019 21:28:11 --------*/

CREATE SEQUENCE GEN_EVENTO_AVULSO;




/*------ GERASYS.TI 04/07/2019 21:12:45 --------*/

COMMENT ON SEQUENCE GEN_EVENTO_AVULSO IS 'Sequencial para o lancamento mensal dos eventos avulsos';



/*------ GERASYS.TI 04/07/2019 21:51:43 --------*/

CREATE INDEX IDX_REMUN_EVENTO_AVULSO_IMP
ON REMUN_EVENTO_AVULSO (IMPORTADO);

CREATE INDEX IDX_REMUN_EVENTO_AVULSO_SIT
ON REMUN_EVENTO_AVULSO (SITUACAO);






/*------ GERASYS.TI 04/07/2019 22:26:44 --------*/

SET TERM ^ ;

CREATE OR ALTER trigger tg_remun_evento_avulso_item_seq for remun_evento_avulso_item
active before insert position 0
AS
begin
  Select
    max(e.sequencia)
  from REMUN_EVENTO_AVULSO_ITEM e
  where (e.id_cliente      = new.id_cliente)
    and (e.id_unid_gestora = new.id_unid_gestora)
    and (e.id_unid_lotacao = new.id_unid_lotacao)
    and (e.id_evento       = new.id_evento)
    and (e.ano_mes         = new.ano_mes)
  Into
    new.sequencia;

  new.sequencia = coalesce(new.sequencia, 0) + 1;
end
^

SET TERM ; ^




/*------ GERASYS.TI 05/07/2019 11:00:32 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SET_REMUN_EVENTO_AVULSO_ITEM (
    ID_CLIENTE DMN_INTEGER,
    ID_UNID_GESTORA DMN_INTEGER,
    ID_UNID_LOTACAO DMN_INTEGER,
    ID_EVENTO DMN_INTEGER,
    ANO_MES DMN_CHAR06)
as
begin
  if (exists(
    Select
      e.data
    from REMUN_EVENTO_AVULSO e
    where (e.id_cliente      = :id_cliente)
      and (e.id_unid_gestora = :id_unid_gestora)
      and (e.id_unid_lotacao = :id_unid_lotacao)
      and (e.id_evento       = :id_evento)
      and (e.ano_mes         = :ano_mes)
  )) then
  begin
      Insert Into REMUN_EVENTO_AVULSO_ITEM (
          id_cliente
        , id_unid_gestora
        , id_unid_lotacao
        , id_evento
        , ano_mes
        , id_servidor
        , quant
        , valor
      ) Select
            sv.id_cliente
          , sv.id_unid_gestora
          , sv.id_unid_lotacao
          , :id_evento as id_evento
          , :ano_mes   as ano_mes
          , sv.id_servidor
          , null
          , null
        from REMUN_SERVIDOR sv
          left join REMUN_EVENTO_AVULSO_ITEM lc on (lc.id_cliente = sv.id_cliente and lc.id_servidor = sv.id_servidor and lc.id_evento = :id_evento and lc.ano_mes = :ano_mes)
        where (sv.id_cliente      = :id_cliente)
          and (sv.id_unid_gestora = :id_unid_gestora)
          and (sv.id_unid_lotacao = :id_unid_lotacao)
          and (sv.situacao        = 1)
          and (lc.sequencia is null)
        order by
          sv.nome;
  end
end
^

SET TERM ; ^




/*------ GERASYS.TI 05/07/2019 11:02:57 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM DROP CONSTRAINT FK_REMUN_EVENTO_AVULSO_ITEM;




/*------ GERASYS.TI 05/07/2019 11:03:29 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_ITEM_1
FOREIGN KEY (ID_CLIENTE,ID_EVENTO,ID_UNID_GESTORA,ID_UNID_LOTACAO,ANO_MES)
REFERENCES REMUN_EVENTO_AVULSO(ID_CLIENTE,ID_EVENTO,ID_UNID_GESTORA,ID_UNID_LOTACAO,ANO_MES);



/*------ 05/07/2019 11:31:38 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM DROP CONSTRAINT FK_REMUN_EVENTO_AVULSO_ITEM_1;

/*------ 05/07/2019 11:31:38 --------*/

ALTER TABLE REMUN_EVENTO_AVULSO_ITEM
ADD CONSTRAINT FK_REMUN_EVENTO_AVULSO_ITEM
FOREIGN KEY (ID_CLIENTE,ID_EVENTO,ID_UNID_GESTORA,ID_UNID_LOTACAO,ANO_MES)
REFERENCES REMUN_EVENTO_AVULSO(ID_CLIENTE,ID_EVENTO,ID_UNID_GESTORA,ID_UNID_LOTACAO,ANO_MES)
ON DELETE CASCADE
ON UPDATE CASCADE
USING INDEX FK_REMUN_EVENTO_AVULSO_ITEM_1;


/*------ GERASYS.TI 06/07/2019 11:42:04 --------*/

SET TERM ^ ;

create or alter procedure SET_REMUN_EVENTO_SERVIDOR (
    ANO_MES DMN_CHAR06,
    ID_CLIENTE DMN_INTEGER,
    ID_UNID_GESTORA DMN_INTEGER,
    ID_UNID_LOTACAO DMN_INTEGER,
    ID_EVENTO DMN_INTEGER,
    ID_SERVIDOR DMN_INTEGER,
    TIPO_LANC DMN_SMALLINT,
    QUANT DMN_NUMERO,
    VALOR DMN_NUMERO)
as
declare variable V_SEQUENCIA DMN_INTEGER;
declare variable V_QUANT DMN_NUMERO;
declare variable V_VALOR DMN_NUMERO;
begin
  Select
      e.sequencia
    , e.quant
    , e.valor
  from REMUN_EVENTO_AVULSO_ITEM e
  where (e.id_cliente      = :id_cliente)
    and (e.id_unid_gestora = :id_unid_gestora)
    and (e.id_unid_lotacao = :id_unid_lotacao)
    and (e.id_evento       = :id_evento)
    and (e.ano_mes         = :ano_mes)
    and (e.id_servidor     = :id_servidor)
  Into
      v_sequencia
    , v_quant
    , v_valor;

  if (coalesce(:v_sequencia, 0) = 0) then
  begin
    Insert Into REMUN_EVENTO_AVULSO_ITEM (
        id_cliente
      , id_unid_gestora
      , id_unid_lotacao
      , id_evento
      , ano_mes
      , id_servidor
      , quant
      , valor
    ) values (
        :id_cliente
      , :id_unid_gestora
      , :id_unid_lotacao
      , :id_evento
      , :ano_mes
      , :id_servidor
      , :quant
      , :valor
    );
  end
  else
  begin
    Update REMUN_EVENTO_AVULSO_ITEM e Set
        e.quant = (case when (coalesce(:tipo_lanc, 0) = 0) then :quant else :v_quant end)
      , e.valor = (case when (coalesce(:tipo_lanc, 0) = 1) then :valor else :v_valor end)
    where (e.id_cliente      = :id_cliente)
      and (e.id_unid_gestora = :id_unid_gestora)
      and (e.id_unid_lotacao = :id_unid_lotacao)
      and (e.id_evento       = :id_evento)
      and (e.ano_mes         = :ano_mes)
      and (e.id_servidor     = :id_servidor);
  end
end
^

SET TERM ; ^

GRANT EXECUTE ON PROCEDURE SET_REMUN_EVENTO_SERVIDOR TO "PUBLIC";



/*------ GERASYS.TI 06/07/2019 11:43:16 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SET_REMUN_EVENTO_SERVIDOR (
    ANO_MES DMN_CHAR06,
    ID_CLIENTE DMN_INTEGER,
    ID_UNID_GESTORA DMN_INTEGER,
    ID_UNID_LOTACAO DMN_INTEGER,
    ID_EVENTO DMN_INTEGER,
    ID_SERVIDOR DMN_INTEGER,
    TIPO_LANC DMN_SMALLINT,
    QUANT DMN_NUMERO,
    VALOR DMN_NUMERO,
    OBS DMN_VARCHAR40)
as
declare variable V_SEQUENCIA DMN_INTEGER;
declare variable V_QUANT DMN_NUMERO;
declare variable V_VALOR DMN_NUMERO;
begin
  Select
      e.sequencia
    , e.quant
    , e.valor
  from REMUN_EVENTO_AVULSO_ITEM e
  where (e.id_cliente      = :id_cliente)
    and (e.id_unid_gestora = :id_unid_gestora)
    and (e.id_unid_lotacao = :id_unid_lotacao)
    and (e.id_evento       = :id_evento)
    and (e.ano_mes         = :ano_mes)
    and (e.id_servidor     = :id_servidor)
  Into
      v_sequencia
    , v_quant
    , v_valor;

  if (coalesce(:v_sequencia, 0) = 0) then
  begin
    Insert Into REMUN_EVENTO_AVULSO_ITEM (
        id_cliente
      , id_unid_gestora
      , id_unid_lotacao
      , id_evento
      , ano_mes
      , id_servidor
      , quant
      , valor
      , obs
    ) values (
        :id_cliente
      , :id_unid_gestora
      , :id_unid_lotacao
      , :id_evento
      , :ano_mes
      , :id_servidor
      , :quant
      , :valor
      , :obs
    );
  end
  else
  begin
    Update REMUN_EVENTO_AVULSO_ITEM e Set
        e.quant = (case when (coalesce(:tipo_lanc, 0) = 0) then :quant else :v_quant end)
      , e.valor = (case when (coalesce(:tipo_lanc, 0) = 1) then :valor else :v_valor end)
      , e.obs   = :obs
    where (e.id_cliente      = :id_cliente)
      and (e.id_unid_gestora = :id_unid_gestora)
      and (e.id_unid_lotacao = :id_unid_lotacao)
      and (e.id_evento       = :id_evento)
      and (e.ano_mes         = :ano_mes)
      and (e.id_servidor     = :id_servidor);
  end
end
^

SET TERM ; ^



/*------ GERASYS.TI 31/07/2019 21:55:59 --------*/

ALTER TABLE ADM_USUARIO
    ADD LANCAR_CH_PROFESSORES DMN_SMALLINT_NN;

COMMENT ON COLUMN ADM_USUARIO.LANCAR_CH_PROFESSORES IS
'Lancar Cargar Horaria de Professores pelo Portal:
0 - Nao
1 - Sim';




/*------ GERASYS.TI 31/07/2019 21:56:03 --------*/

UPDATE ADM_USUARIO
SET LANCAR_CH_PROFESSORES = 0;




/*------ GERASYS.TI 31/07/2019 21:56:52 --------*/

ALTER TABLE ADM_USUARIO_UNID_GESTORA
    ADD LANCAR_CH_PROFESSORES DMN_BOOLEAN;

COMMENT ON COLUMN ADM_USUARIO_UNID_GESTORA.LANCAR_EVENTOS IS
'Permitir lancamento de eventos avulsos no portal:
0 - Nao
1 - Sim';

COMMENT ON COLUMN ADM_USUARIO_UNID_GESTORA.LANCAR_CH_PROFESSORES IS
'Permitir lancamento de carga horaria de professores no portal:
0 - Nao
1 - Sim';




/*------ GERASYS.TI 31/07/2019 21:56:56 --------*/

UPDATE ADM_USUARIO_UNID_GESTORA
SET LANCAR_CH_PROFESSORES = 0;




/*------ GERASYS.TI 31/07/2019 21:57:27 --------*/

ALTER TABLE ADM_USUARIO_UNID_LOTACAO
    ADD LANCAR_CH_PROFESSORES DMN_BOOLEAN;

COMMENT ON COLUMN ADM_USUARIO_UNID_LOTACAO.LANCAR_EVENTOS IS
'Permitir lancamento de eventos avulsos no portal:
0 - Nao
1 - Sim';

COMMENT ON COLUMN ADM_USUARIO_UNID_LOTACAO.LANCAR_CH_PROFESSORES IS
'Permitir lancamento de carga horaria de professores no portal:
0 - Nao
1 - Sim';




/*------ GERASYS.TI 31/07/2019 21:57:31 --------*/

UPDATE ADM_USUARIO_UNID_LOTACAO
SET LANCAR_CH_PROFESSORES = 0;




/*------ GERASYS.TI 31/07/2019 22:33:05 --------*/

CREATE DOMAIN DMN_GUID_UUID AS
VARCHAR(32);CREATE DOMAIN DMN_GUID_UUID_NN AS
VARCHAR(32)
NOT NULL;CREATE DOMAIN DMN_GUID AS
VARCHAR(38);CREATE DOMAIN DMN_GUID_NN AS
VARCHAR(38)
NOT NULL;


/*------ GERASYS.TI 31/07/2019 22:33:28 --------*/

SET TERM ^ ;

create or alter procedure GET_GUID_UUID_HEX
returns (
    REAL_UUID char(16) character set OCTETS,
    HEX_UUID DMN_GUID_UUID,
    HEX_UUID_FORMAT DMN_GUID)
as
declare variable I integer;
declare variable C integer;
begin

  real_uuid = gen_uuid();
  hex_uuid  = '';

  i = 0;

  while (:i < 16) do
  begin
    c = ascii_val(substring(real_uuid from i + 1 for 1));

    if (:c < 0) then
      c = 256 + :c;

    hex_uuid = :hex_uuid ||
      substring('0123456789abcdef' from bin_shr(:c,  4) + 1 for 1) ||
      substring('0123456789abcdef' from bin_and(:c, 15) + 1 for 1);

    i = :i + 1;
  end

  /*                        8   -  4 -  4 - 4  -      12       */
  /* Formato exemplo: '{5B86B088-F14F-4872-B876-977FBEF9CB91}' */
  hex_uuid_format = '{' ||
    substring(:hex_uuid from  1 for  8) || '-' || -- 8
    substring(:hex_uuid from  9 for  4) || '-' || -- 4
    substring(:hex_uuid from 13 for  4) || '-' || -- 4
    substring(:hex_uuid from 17 for  4) || '-' || -- 4
    substring(:hex_uuid from 21 for 12) || '}';   -- 12

  hex_uuid        = upper(:hex_uuid);
  hex_uuid_format = upper(:hex_uuid_format);

  suspend;

end
^

SET TERM ; ^

GRANT EXECUTE ON PROCEDURE GET_GUID_UUID_HEX TO "PUBLIC";
