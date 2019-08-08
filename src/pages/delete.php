<?php
if (empty($_GET['id']))
{
	echo '<h1>Nao e possivel fazer isso</h1>';
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// var_dump($modulo);
	// var_dump($_POST);
	$modulo->delete( $_POST['id'] );
	header("Location: index.php?page={$getPages}");
}