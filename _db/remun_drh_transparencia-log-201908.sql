


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