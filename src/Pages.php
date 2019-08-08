<?php

class Pages
{
	public $conn;

	public function __construct( Config $config )
	{
		$this->conn = $config->conn();
	}

	public function create($dados) {
		$insert = $this->conn->prepare('INSERT INTO pages (title, body, slug) VALUES (:title, :body, :slug);');
		$insert->bindValue(':title', $dados['title'], PDO::PARAM_STR);
		$insert->bindValue(':body', $dados['body'], PDO::PARAM_STR);
		$insert->bindValue(':slug', $this->slug($dados['title']), PDO::PARAM_STR);
		return $insert->execute();
	}

	public function read($slug=null,  $id =null) {
		if (!empty($slug)){
			$select = $this->conn->prepare('SELECT * FROM pages WHERE slug=:slug;');
			$select->bindValue(':slug', $slug, PDO::PARAM_STR);
			$select->execute();
			return $select->fetch();
		} else if (!empty($id)) {
			$select = $this->conn->prepare('SELECT * FROM pages WHERE id=:id;');
			$select->bindValue(':id', $id, PDO::PARAM_INT);
			$select->execute();
			return $select->fetch();
		} else {
			$select = $this->conn->prepare('SELECT * FROM pages');
			$select->execute();
			return $select->fetchAll();
		}
	}

	public function update($dados, $id) {
		$update = $this->conn->prepare('UPDATE pages SET title=:title, body=:body, slug=:slug WHERE id=:id;');
		$update->bindValue(':title', $dados['title'], PDO::PARAM_STR);
		$update->bindValue(':body', $dados['body'], PDO::PARAM_STR);
		$update->bindValue(':slug', $dados['slug'], PDO::PARAM_STR);
		$update->bindValue(':id', $id, PDO::PARAM_INT);
		return $update->execute();
	}

	public function delete($id) {
		$delete = $this->conn->prepare('DELETE FROM pages WHERE id=:id;');
		$delete->bindValue(':id', $id, PDO::PARAM_INT);
		return $delete->execute();
	}

	public function slug($title) {
		$slug = strtolower($title);
		$slug = preg_replace('/[^a-z0-9]\ -/', '', $slug);
		$slug = preg_replace('/[ ]/', '-', $slug);
		return $slug;
	}

}