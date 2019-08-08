<!-- Page Content -->
<?php
session_start();
if ( isset( $_SESSION['auth'] ))
{
	// var_dump($_SESSION['auth']);die;
	$nome = $_SESSION['auth']['name'];
	$orgao = $_SESSION['auth']['cli'];
	$auth = $_SESSION['auth']['confirm'];
	if ( $auth )
	{
		$alert = 'success';
		$titleMsg = "{$orgao}<br />{$nome}<br />Seus dados foram confirmados com exito!<br />Abaixo está sua senha para realizar seu acesso!";
		$pass = "Sua senha(provisoria): ".$_SESSION['auth']['pass'];
	}else {
		$alert = 'danger';
		$titleMsg = "{$orgao}<br />{$nome}<br />ERROR! Seus dados não foram confirmados! verifique seus dados junto a seu Orgão/Unidade!";
		$pass = null;
	}
	// 	case 'ok':



	// switch ( $_SESSION['auth']['msg'] )
	// {
	// 	case 'ok':
	// 		$alert = 'success';
	// 		$titleMsg = '{$orgao}<br />{$nome}, seus dados foram confirmados com exito!<br />Abaixo está sua senha para realizar seu acesso!';
	// 		$pass = "<span class='label label-success'>{$_SESSION['auth']['msg']}</span>";
	// 		// $titleMsg = 'Enviamos para seu e-mail orientações para acesso de sua conta. Uma mensagem foi enviada!';
	// 		break;
	// 	case 'blank':
	// 		$alert = 'info';
	// 		$titleMsg = '{$orgao}<br />{$nome}, seus dados não foram confirmados, Verifique se não preencheu algum informação incorreta!';
	// 		$pass = null;
	// 		break;
	// 	case 'error':
	// 		$alert = 'danger';
	// 		$titleMsg = "{$orgao}<br />{$nome} - ERROR! As orientações não puderam ser enviadas, verifique seus dados junto a seu Orgão/Unidade!";
	// 		break;
	// }

	if ( !empty( $_SESSION['orgao'] ) )
	{
		unset($_SESSION['orgao']);
		unset($_SESSION['auth']);
		session_destroy();
	}	
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Gerasys - DRH Transparência">
    <meta name="author" content="HsNunes">

    <title>Gerasys - DRH Transparência - Confrmação de cadastro de Servidor</title>

    <!-- Bootstrap Core CSS -->
    <link href="../view/css/bootstrap.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"> -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js"></script> -->
    <!-- Custom CSS -->
    <!-- link href="../view/css/estiloDr.css" rel="stylesheet" -->
</head>
<body>

<?php if( empty($auth) ): ?>
	<!-- Navigation -->
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header" style="padding:0;min-height: 90px;">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-menu" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="?page=home">
					<img src="img/GERASYS_Logo.jpg" height="60" alt="DRH Transparencia logotipo">
				</a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse navbar-right" id="navbar-collapse-menu">&nbsp;</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container -->
	</nav>


	<div class="jumbotron" style="margin-top: 7%;background-color: #BDEB00;">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<h2>Instruções de Acesso</h2>
					<ul>
						<li>Preencha todas as informações do forumlário e clique em Confirmar Acesso;</li>
						<li>Você receberá uma mensagem (no seu E-mail Cadastrado), com todas as orientações de Acesso;</li>
					</ul>
				</div>

				<div class="col-md-6">
					<div class="panel panel-login">
						<div class="panel-heading">
							<div class="col-xs-12">
								<h3>PARA PRIMEIRO ACESSO:</h3>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12">
									<form id="confirmForm" class="form" action="../app/login.php" method="post" role="form" accept-charset="UTF-8">
										<!-- <div class="form-group">
											<input type="email" name="email" id="inputEmail" tabindex="1" class="form-control" placeholder="Seu Email" value="" required="">
										</div> -->
										<div class="form-group">
											<input type="text" name="idServ" id="inputId" tabindex="1" class="form-control" placeholder="Sua Matrícula" value="" required="">
										</div>
										<div class="form-group">
											<input type="text" name="admissao" id="inputAdmissao" tabindex="2" class="form-control maskDATE" placeholder="Data Admissão" required="">
										</div>
										<div class="form-group">
											<input type="text" name="cpf" id="inputCpf" tabindex="3" class="form-control maskCPF" placeholder="Seu CPF(apenas numero)" pattern="[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}" required="">
										</div>
										<div class="form-group">
											<input type="text" name="nascimento" id="inputNascimento" tabindex="4" class="form-control maskDATE" placeholder="Data Nascimento" pattern="[0-9]{2}\/[0-9]{2}\/[0-9]{4}" required="">
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-sm-6 col-sm-offset-3">
													<button id="sendConfirm" type="submit" name="confirmForm" class="btn btn-success btn-block">
														<span class="glyphicon glyphicon-ok"></span> 
														Confirmar Acesso
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php else: ?>

	<div class="jumbotron" style="padding:15px 40px;">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Confirmação de Cadastro</h1>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sint, explicabo dolores ipsam aliquam inventore corrupti eveniet quisquam.</p> -->
				<address style="margin-top:5%;">
                <h2>Contato</h2>
                <address>
                    <strong><a href="#">GERASYS.TI</a></strong>
                    <br>Rua Scilla Médice, 455
                    <br>Centro - Rondon do Pará - PA
                    <br>
                </address>
                <address>
                    <abbr title="Phone">Fone</abbr>(94) 3326 2160<br>
                    <abbr title="Phone">Fone</abbr>(94) 98119 4915<br>
                    <abbr title="Phone">Fone</abbr>(94) 99129 1158<br>
                    <abbr title="Email">Email</abbr> <a href="mailto:#">gerasys.ti@hotmail.com</a>
				</address>
			</div>
			<div class="col-md-5">
				<div class="row">
					<div class='alert alert-<?= $alert; ?> alert-dismissible' role='alert'>
						<h4><?= $titleMsg; ?></h4>
						<p style="font-weight:bolder;font-size: 1.5em;"><?= $pass; ?></p>
					</div>
				</div>
				<div class="row">
					<a class="btn btn-warning" href="../index.php?un=clean">Retornar a página incial para efetuar seu acesso.</a>
				</div>
			</div>
		</div>
	</div>
<!-- /.container -->

<?php endif; ?>

	<script src="../view/js/jquery.js" type="text/javascript"></script>
	<script src="../view/js/jquery-maskedinput.js" type="text/javascript"></script>
	<script src="../view/js/appScript.js" type="text/javascript"></script>

</body>
</html>