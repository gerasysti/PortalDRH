<?php if ( isset( $_SESSION['logout'] )): ?>
	<div class="row" id="alertMsg" style="padding:10px;">
		<div class='alert alert-danger alert-dismissible' role='alert'>
			<h4>ERROR!</h4>
			<p><strong>Id/Matrícula ou Senha, Incorretos!</strong> verifique seus dados junto a seu Orgão/Unidade!</p>
		</div>
	</div>
<?php unset($_SESSION['logout']); endif; ?>
	<!-- Half Page Image Background Carousel Header -->
	<header id="drCarousel" class="carousel slide">
		<!-- Indicators -->
		<ol class="carousel-indicators hide">
			<li data-target="#drCarousel" data-slide-to="0" class="active"></li>
<!-- 			<li data-target="#drCarousel" data-slide-to="1"></li>
			<li data-target="#drCarousel" data-slide-to="2"></li> -->
		</ol>

		<!-- Wrapper for Slides -->
		<div class="carousel-inner">
			<div class="item active">
				<!-- Set the first background image using inline CSS below. -->
				<!-- <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=Slide One');"></div> -->
				<div class="fill" style="background-image:url('view/img/bgPaginasl.jpg');"></div>
				<div class="carousel-caption">
					<h1 display:block;top:-50;>DRH Transparência</h1>
					<p></p>
					<!-- <button type="button" class="btn btn-info sld">Veja mais</button> -->
					<h2></h2>
				</div>
			</div>
		</div>

		<!-- Controls -->
		<!-- <a class="left carousel-control" href="#drCarousel" data-slide="prev">
			<span class="icon-prev"></span>
		</a>
		<a class="right carousel-control" href="#drCarousel" data-slide="next">
			<span class="icon-next"></span>
		</a> -->

	</header>

	<!-- Page Content -->
	<div class="container">
        <div class="row" style="margin-bottom:80px;">
            <div class="col-sm-8">
                <h2>Sobre a Transparência</h2>
                <p>
                	Disponibilizar informações sobre a LEI Nº 12.527, DE 18 DE NOVEMBRO DE 2011.<br />
					<strong>(Lei de acesso à informação).</strong>
				</p>
				<p>Disponibilizar o Contra-Cheque on-line do Servidor Municipal.</p>
            </div>
            <div class="col-sm-4">
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
        </div>
        <!-- /.row -->
	</div>
	<!-- /.container -->
