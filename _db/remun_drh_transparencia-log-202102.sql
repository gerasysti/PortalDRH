


/*------ GERASYS.TI 22/01/2021 15:24:29 --------*/

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
      , id_cargo
   );

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

