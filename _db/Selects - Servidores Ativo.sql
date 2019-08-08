Select
    s.id_cliente
  , s.id_servidor
  , s.matricula
  , s.nome
  , s.dt_admissao
  , s.id_cargo_atual
  , c.descricao as ds_cargo_atual
  , s.situacao
from REMUN_SERVIDOR s
  inner join REMUN_CARGO_FUNCAO c on (c.id_cliente = s.id_cliente and c.id_cargo = s.id_cargo_atual)
where (s.id_cliente = 15019)
  and (c.tipo_sal   = '2')
  and (s.situacao   = 1)
