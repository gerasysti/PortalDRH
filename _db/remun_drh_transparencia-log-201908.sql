


/*------ GERASYS.TI 06/08/2019 20:57:06 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure GET_GUID_UUID_HEX
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

COMMENT ON PROCEDURE GET_GUID_UUID_HEX IS 'Procedure Gerar ID GUID

    Autor   :   Isaque M. Ribeiro
    Data    :   06/08/2019

Stored procedure por gerar IDs unicos para registro que utilizarao com chaves
primarios campos do tipo TGUID.';




/*------ GERASYS.TI 07/08/2019 20:19:55 --------*/

COMMENT ON COLUMN REMUN_CARGO_FUNCAO.TIPO_SAL IS
'Tipo Salario:
1 - Normal
2 - Hora/aula';



/*------ GERASYS.TI 07/08/2019 20:32:02 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Token unknown - line 10, column 1.
from.

*/

/*------ GERASYS.TI 07/08/2019 20:32:06 --------*/

/*!!! Error occured !!!
Column does not belong to referenced table.
Dynamic SQL Error.
SQL error code = -206.
Column unknown.
S.TIPO_SAL.
At line 12, column 10.

*/


/*------ GERASYS.TI 08/08/2019 20:48:18 --------*/

CREATE DOMAIN DMN_BIGINT AS
BIGINT;CREATE DOMAIN DMN_BIGINT_NN AS
BIGINT
NOT NULL;


/*------ GERASYS.TI 08/08/2019 20:53:43 --------*/

CREATE TABLE REMUN_LANCTO_CH (
    ID DMN_GUID_NN NOT NULL,
    CONTROLE DMN_BIGINT_NN,
    ID_CLIENTE DMN_INTEGER_NN,
    ID_UNID_LOTACAO DMN_INTEGER_NN,
    ANO_MES DMN_CHAR06_NN,
    DATA DMN_DATE,
    HORA DMN_TIME,
    USUARIO DMN_INTEGER,
    SITUACAO DMN_SMALLINT_NN,
    IMPORTADO DMN_BOOLEAN DEFAULT 0);

ALTER TABLE REMUN_LANCTO_CH
ADD CONSTRAINT PK_REMUN_LANCTO_CH
PRIMARY KEY (ID);

COMMENT ON COLUMN REMUN_LANCTO_CH.ID IS
'ID';

COMMENT ON COLUMN REMUN_LANCTO_CH.CONTROLE IS
'Controle Numerico';

COMMENT ON COLUMN REMUN_LANCTO_CH.ID_CLIENTE IS
'Cliente';

COMMENT ON COLUMN REMUN_LANCTO_CH.ID_UNID_LOTACAO IS
'Unidade de Lotacao';

COMMENT ON COLUMN REMUN_LANCTO_CH.ANO_MES IS
'Ano/Mes';

COMMENT ON COLUMN REMUN_LANCTO_CH.DATA IS
'Data de lancamento';

COMMENT ON COLUMN REMUN_LANCTO_CH.HORA IS
'Hora de lancamento';

COMMENT ON COLUMN REMUN_LANCTO_CH.USUARIO IS
'Usuario do lancamento';

COMMENT ON COLUMN REMUN_LANCTO_CH.SITUACAO IS
'Situacao:
0 - Aberto
1 - Finalizado
2 - Cancelado';

COMMENT ON COLUMN REMUN_LANCTO_CH.IMPORTADO IS
'Importando pelo Remuneratus:
0 - Nao
1 - Sim';

GRANT ALL ON REMUN_LANCTO_CH TO "PUBLIC";



/*------ GERASYS.TI 08/08/2019 20:54:07 --------*/

ALTER TABLE REMUN_LANCTO_CH
ADD CONSTRAINT UNQ_REMUN_LANCTO_CH
UNIQUE (CONTROLE);




/*------ GERASYS.TI 08/08/2019 20:55:15 --------*/

ALTER TABLE REMUN_LANCTO_CH
ADD CONSTRAINT FK_REMUN_LANCTO_CH_CLI
FOREIGN KEY (ID_CLIENTE)
REFERENCES ADM_CLIENTE(ID);

ALTER TABLE REMUN_LANCTO_CH
ADD CONSTRAINT FK_REMUN_LANCTO_CH_LOT
FOREIGN KEY (ID_CLIENTE,ID_UNID_LOTACAO)
REFERENCES REMUN_UNID_LOTACAO(ID_CLIENTE,ID_LOTACAO);




/*------ GERASYS.TI 08/08/2019 20:55:38 --------*/

COMMENT ON COLUMN REMUN_LANCTO_CH.SITUACAO IS
'Situacao:
0 - Aberto
1 - Finalizado
2 - Cancelado';

ALTER TABLE REMUN_LANCTO_CH ADD IBE$$TEMP_COLUMN
 SMALLINT DEFAULT 0
;

UPDATE RDB$RELATION_FIELDS F1
SET
F1.RDB$DEFAULT_VALUE  = (SELECT F2.RDB$DEFAULT_VALUE
                         FROM RDB$RELATION_FIELDS F2
                         WHERE (F2.RDB$RELATION_NAME = 'REMUN_LANCTO_CH') AND
                               (F2.RDB$FIELD_NAME = 'IBE$$TEMP_COLUMN')),
F1.RDB$DEFAULT_SOURCE = (SELECT F3.RDB$DEFAULT_SOURCE FROM RDB$RELATION_FIELDS F3
                         WHERE (F3.RDB$RELATION_NAME = 'REMUN_LANCTO_CH') AND
                               (F3.RDB$FIELD_NAME = 'IBE$$TEMP_COLUMN'))
WHERE (F1.RDB$RELATION_NAME = 'REMUN_LANCTO_CH') AND
      (F1.RDB$FIELD_NAME = 'SITUACAO');

ALTER TABLE REMUN_LANCTO_CH DROP IBE$$TEMP_COLUMN;




/*------ GERASYS.TI 08/08/2019 20:56:44 --------*/

CREATE SEQUENCE GEN_LANCTO_CH;

COMMENT ON SEQUENCE GEN_LANCTO_CH IS 'Sequencial para lanamento de carga horaria';




/*------ GERASYS.TI 08/08/2019 20:59:31 --------*/

SET TERM ^ ;

CREATE trigger tg_remun_lancto_ch_id for remun_lancto_ch
active before insert position 0
AS
begin
  if (new.id is null) then
    Select
      g.hex_uuid_format
    from GET_GUID_UUID_HEX g
    Into
      new.id;

  if (new.controle is null) then
    new.controle = gen_id(GEN_LANCTO_CH, 1);
end
^

SET TERM ; ^




/*------ GERASYS.TI 08/08/2019 21:01:07 --------*/

SET TERM ^ ;

CREATE OR ALTER trigger tg_remun_lancto_ch_id for remun_lancto_ch
active before insert position 0
AS
begin
  if (new.id is null) then
    Select
      g.hex_uuid_format
    from GET_GUID_UUID_HEX g
    Into
      new.id;

  if (new.controle is null) then
    new.controle = gen_id(GEN_LANCTO_CH, 1);
end
^

SET TERM ; ^

COMMENT ON TRIGGER TG_REMUN_LANCTO_CH_ID IS 'Trigger Gerar ID Lancamento CH.

    Autor   :   Isaque M. Ribeiro
    Data    :   08/08/2019

Trigger responsavel por gerar os codigos necessarios para controle caso este nao
tenham sido informados.';




/*------ GERASYS.TI 08/08/2019 21:01:40 --------*/

ALTER TABLE REMUN_LANCTO_CH
ADD CONSTRAINT FK_REMUN_LANCTO_CH_USR
FOREIGN KEY (USUARIO)
REFERENCES ADM_USUARIO(ID);




/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column ID position 1;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column ID_CLIENTE position 2;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column ID_UNID_LOTACAO position 3;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column CONTROLE position 4;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column ANO_MES position 5;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column DATA position 6;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column HORA position 7;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column USUARIO position 8;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column SITUACAO position 9;


/*------ GERASYS.TI 08/08/2019 21:02:00 --------*/

alter table REMUN_LANCTO_CH
alter column IMPORTADO position 10;


/*------ GERASYS.TI 08/08/2019 21:08:06 --------*/

SET TERM ^ ;

CREATE OR ALTER trigger tg_remun_lancto_ch_id for remun_lancto_ch
active before insert position 0
AS
begin /*
  if (new.id is null) then
    Select
      g.hex_uuid_format
    from GET_GUID_UUID_HEX g
    Into
      new.id;*/

  if (new.controle is null) then
    new.controle = gen_id(GEN_LANCTO_CH, 1);
end
^

SET TERM ; ^




/*------ GERASYS.TI 08/08/2019 21:08:13 --------*/

ALTER TABLE REMUN_LANCTO_CH DROP CONSTRAINT PK_REMUN_LANCTO_CH;




/*------ GERASYS.TI 08/08/2019 21:08:24 --------*/

ALTER TABLE REMUN_LANCTO_CH ALTER ID TO ID_LANCTO;

COMMENT ON COLUMN REMUN_LANCTO_CH.ID_LANCTO IS
'ID';




/*------ GERASYS.TI 08/08/2019 21:08:33 --------*/

ALTER TABLE REMUN_LANCTO_CH
ADD CONSTRAINT PK_REMUN_LANCTO_CH
PRIMARY KEY (ID_LANCTO);




/*------ GERASYS.TI 08/08/2019 21:08:56 --------*/

SET TERM ^ ;

CREATE OR ALTER trigger tg_remun_lancto_ch_id for remun_lancto_ch
active before insert position 0
AS
begin
  if (new.id_lancto is null) then
    Select
      g.hex_uuid_format
    from GET_GUID_UUID_HEX g
    Into
      new.id_lancto;

  if (new.controle is null) then
    new.controle = gen_id(GEN_LANCTO_CH, 1);
end
^

SET TERM ; ^




/*------ GERASYS.TI 08/08/2019 21:14:08 --------*/

CREATE DOMAIN DMN_SIM_NAO AS
CHAR(1)
DEFAULT 'N'
NOT NULL
CHECK ((value = 'S') or (value = 'N'));


/*------ GERASYS.TI 08/08/2019 21:24:11 --------*/

CREATE TABLE REMUN_LANCTO_CH_PROF (
    ID_LANCTO_PROF DMN_GUID_NN NOT NULL,
    ID_LANCTO DMN_GUID_NN NOT NULL,
    ID_CLIENTE DMN_INTEGER_NN,
    ID_SERVIDOR DMN_INTEGER_NN,
    ID_UNID_LOTACAO DMN_INTEGER_NN,
    ANO_MES DMN_CHAR06_NN,
    QTD_H_AULA_NORMAL DMN_INTEGER,
    QTD_H_AULA_SUBSTITUICAO DMN_INTEGER,
    QTD_H_AULA_OUTRA DMN_INTEGER,
    QTD_FALTA DMN_INTEGER,
    OBSERVACAO DMN_VARCHAR40,
    CALC_GRAT_SERIES_INICIAIS DMN_SIM_NAO,
    CALC_GRAT_DIFICIL_ACESSO DMN_SIM_NAO,
    CALC_GRAT_ENSINO_ESPEC DMN_SIM_NAO,
    CALC_GRAT_MULTI_SERIE DMN_SIM_NAO);

ALTER TABLE REMUN_LANCTO_CH_PROF
ADD CONSTRAINT PK_REMUN_LANCTO_CH_PROF
PRIMARY KEY (ID_LANCTO_PROF);

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.ID_LANCTO_PROF IS
'ID Lancamento Professor';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.ID_LANCTO IS
'ID Lancamento (Cabecalho)';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.ID_CLIENTE IS
'Cliente';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.ID_SERVIDOR IS
'Servidor';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.ID_UNID_LOTACAO IS
'Ulidade de Lotacao';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.ANO_MES IS
'Ano/Mes';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.QTD_H_AULA_NORMAL IS
'Quantidade Hora/Aula Normal';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.QTD_H_AULA_SUBSTITUICAO IS
'Quantidade Hora/Aula Substituicao';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.QTD_H_AULA_OUTRA IS
'Quantidade Hora/Aula Outra';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.QTD_FALTA IS
'Quantidade de faltas';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.OBSERVACAO IS
'Observacoes';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.CALC_GRAT_SERIES_INICIAIS IS
'Calcular gratificacao Series Iniciais:
N - Nao
S - Sim';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.CALC_GRAT_DIFICIL_ACESSO IS
'Calcular gratificacao de Dificil Acesso:
N - Nao
S - Sim';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.CALC_GRAT_ENSINO_ESPEC IS
'Calcular gratificacao de Ensino Especial:
N - Nao
S - Sim';

COMMENT ON COLUMN REMUN_LANCTO_CH_PROF.CALC_GRAT_MULTI_SERIE IS
'Calcular gratificacao Multi-Serie:
N - Nao
S - Sim';

GRANT ALL ON REMUN_LANCTO_CH_PROF TO "PUBLIC";



/*------ GERASYS.TI 08/08/2019 21:24:53 --------*/

ALTER TABLE REMUN_LANCTO_CH_PROF
ADD CONSTRAINT FK_REMUN_LANCTO_CH_PROF
FOREIGN KEY (ID_LANCTO)
REFERENCES REMUN_LANCTO_CH(ID_LANCTO)
ON DELETE CASCADE
ON UPDATE CASCADE;




/*------ GERASYS.TI 08/08/2019 21:25:36 --------*/

ALTER TABLE REMUN_LANCTO_CH_PROF
ADD CONSTRAINT FK_REMUN_LANCTO_CH_PROF_PROF
FOREIGN KEY (ID_CLIENTE,ID_SERVIDOR)
REFERENCES REMUN_SERVIDOR(ID_CLIENTE,ID_SERVIDOR);

ALTER TABLE REMUN_LANCTO_CH_PROF
ADD CONSTRAINT FK_REMUN_LANCTO_CH_PROF_LOT
FOREIGN KEY (ID_CLIENTE,ID_UNID_LOTACAO)
REFERENCES REMUN_UNID_LOTACAO(ID_CLIENTE,ID_LOTACAO);




/*------ GERASYS.TI 08/08/2019 21:26:20 --------*/

ALTER TABLE REMUN_LANCTO_CH_PROF
ADD CONSTRAINT UNQ_REMUN_LANCTO_CH_PROF
UNIQUE (ID_CLIENTE,ID_SERVIDOR,ID_UNID_LOTACAO,ANO_MES);



/*------ GERASYS.TI 09/08/2019 17:00:15 --------*/

/*!!! Error occured !!!
Column does not belong to referenced table.
Dynamic SQL Error.
SQL error code = -206.
Column unknown.
EV.DESCRICAO.
At line 41, column 8.

*/