<?php
class Login
{
	public $conn;

	public function __construct( Config $config )
	{
		$this->conn = $config->conn();
	}

	public function login( $dados=null, $user )
	{
		@session_start();
		// if ( empty($dados) ):
		// 	if ( !empty($_COOKIE['webbase_resu']) and !empty($_COOKIE['webbase_ssap']) ):
		// 		$dados['usuario'] = base64_decode($_COOKIE['webbase_resu']);
		// 		$dados['senha'] = base64_decode($_COOKIE['webbase_resu']);
		// 	endif;
		// else:
		// 	$this->lembrar($dados);
		// endif;

		if ( isset($dados['usuario']) and isset($dados['senha']) )
		{
			if ( password_verify( $dados['senha'], $user['senha'] ) ):
				$_SESSION['user'] = $user;
				header('Location: index.php');
			endif;
		}

	}

	public function logout()
	{
		@session_start();
		session_unset();
		session_destroy();
		setcookie('webbase_resu');
		setcookie('webbase_ssap');
		header('Location: login.php');
	}

	public function protege()
	{
		@session_start();
		if ( empty($_SESSION['user']) )
		{
			header('Location: login.php');
		}
	}

	public function getUser( $dados )
	{
		if (file_exists('../src/Usuarios.php'))
		{
			echo '../src/Usuarios.php';
			require '../config/Config.php';
			require '../src/Usuarios.php';
		}
	}

	public function lembrar( $dados )
	{
		$cookie = [
			'usuario'=>base64_encode($dados['usuario']),
			// 'senha'=>base64_encode($dados['senha'])
		];
		setcookie('webbase_resu', $cookie['usuario'], (time() + (1*24*3600)), $_SERVER['SERVER_NAME'] );
		// setcookie('webbase_ssap', $cookie['senha'], (time() + (1*24*3600)), $_SERVER['SERVER_NAME'] );
	}

	public function read($usuario)
	{
		$sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
		$user = $this->mysql->prepare($sql);
		$user->bindValue( ':usuario', $usuario, PDO::PARAM_STR );
		$user->execute();
		return $user->fetch();
	}
}