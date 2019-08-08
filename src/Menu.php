<?php

class Menu
{
	public $conn;

	public function __construct( Config $config )
	{
		$this->conn = $config->conn();
	}

	public function create( $dados )
	{
		$insert = $this->conn->prepare('INSERT INTO dr_menu ( nome, link, conteudo ) VALUES ( :nome, :link, :conteudo )');
		$insert->bindValue(':nome', $dados['title'], PDO::PARAM_STR);
		$insert->bindValue(':link', '#', PDO::PARAM_STR);
		$insert->bindValue(':conteudo', $dados['body'], PDO::PARAM_STR);
		return $insert->execute();
	}

	public function read( $id=null )
	{
		if ( !empty($id) )
		{
			$select = $this->conn->prepare('SELECT * FROM dr_menu WHERE id=:id;');
			$select->bindValue(':id', $id, PDO::PARAM_INT);
			$select->execute();
			$res = $select->fetch();
		} else {
			$select = $this->conn->prepare('SELECT * FROM dr_menu');
			$select->execute();
			$res = $select->fetchAll();
		}
		return $res;
	}

	public function update( $dados, $id )
	{
		$update = $this->conn->prepare('UPDATE dr_menu SET nome=:nome, conteudo=:conteudo WHERE id=:id;');
		$update->bindValue(':nome', $dados['title'], PDO::PARAM_STR);
		$update->bindValue(':conteudo', $dados['body'], PDO::PARAM_STR);
		$update->bindValue(':id', $id, PDO::PARAM_INT);
		return $update->execute();
	}

	public function delete($id)
	{
		$delete = $this->conn->prepare('DELETE FROM dr_menu WHERE id=:id;');
		$delete->bindValue(':id', $id, PDO::PARAM_INT);
		return $delete->execute();
	}
}