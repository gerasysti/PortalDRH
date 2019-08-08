<!-- Page Content -->
<?php
if ( isset($_GET['msg']))
{
	switch ($_GET['msg'])
	{
		case 'ok':
			$alert = 'success';
			$titleMsg = 'Sua Mensagem foi enviada!';
			break;
		case 'blank':
			$alert = 'info';
			$titleMsg = 'Você não preencheu algum campo!';
			break;
		case 'error':
			$alert = 'danger';
			$titleMsg = 'Sua mensagem não pode ser enviada! - Error!';
			break;
	}
	echo "<div class='alert alert-{$alert} alert-dismissible fade in' role='alert'>
      <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>
      <h4>{$titleMsg}</h4>
    </div>";
}
?>

<div class="container">
	<section in="contato">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Contato</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-7">
				<!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sint, explicabo dolores ipsam aliquam inventore corrupti eveniet quisquam.</p> -->
				<address style="margin-top:5%;">
					<strong>GeraSys - DRH Trasnparencia,</strong><br />
					<abbr title="Phone">Fone:</abbr> (91) 00000-0000<br />
					<abbr title="Phone">Fone:</abbr> (91) 00000-0000
				</address>
			</div>
			<div class="col-md-5">
				<div class="row">
					<div class="form-area">
						<form role="form" method="post" action='src/mailer.php'>
							<h3 style="margin-bottom:5px;text-align:center;">Contato</h3>
							<div class="form-group">
								<input type="text" class="form-control" id="name" name="name" placeholder="Nome" required>
							</div>
							<div class="form-group">
								<input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
							</div>
			<!--                   <div class="form-group">
			<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Fone/Celular">
			</div> -->
							<div class="form-group">
								<input type="text" class="form-control" id="subject" name="subject" placeholder="Assunto" required>
							</div>
							<div class="form-group">
								<textarea name="mensagem" class="form-control" type="textarea" id="message" placeholder="Mensagem" maxlength="140" rows="5" required></textarea>
								<span class="help-block"><p id="characterLeft" class="help-block ">Limite de palavras</p></span>
							</div>
							<button type="submit" id="submit" name="contatoSends" class="btn btn-primary pull-right">Enviar Mensagem</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<!-- /.container -->
