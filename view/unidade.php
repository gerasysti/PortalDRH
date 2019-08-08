<?php if ( isset( $_SESSION['logout'] )): ?>
	<div class="row" id="alertMsg" style="padding:10px;">
		<div class='alert alert-danger alert-dismissible' role='alert'>
			<h4>ERROR!</h4>
			<p><strong>Id/Matrícula ou Senha, Incorretos!</strong> verifique seus dados junto a seu Orgão/Unidade!</p>
		</div>
	</div>
<?php unset($_SESSION['logout']); endif; ?>
	<!-- Main jumbotron for a primary marketing message or call to action -->

<?php if ( !empty($exibeLista) ): ?>
<div class="selectConsulta" style="background:#00D6D4;padding:1%;margin:0 0 1% 0;">
	<div class="container">
		<div class="row">
			<div class='alert alert-info alert-dismissible' role='alert'>
				<h3>CONTRA-CHEQUE</h3>
				<strong>Utilize o menu acima para fazer seu acesso ao Contra Cheque!</strong>
			</div>
		</div>
	</div>
</div>
<?php else: ?>
<div class="selectConsulta" style="background:#00D6D4;padding:1%;margin:0 0 1% 0;">
	<div class="container">
		<div class="row">
			<h4>Selecione um mês e ano e clique em Consultar...</h4>
			<form class="form-inline" name="listaConsultas" action="" method="POST">
				<div class="form-group col-md-12">

					<label for="mesConsulta">MÊS:</label>
					<select name="mes" id="mesConsulta" class="form-control" style="margin-right:2%;">
						<?=  $optionsMes; ?>
					</select>

					<label for="anoConsulta">ANO:</label>
					<select name="ano" id="anoConsulta" class="form-control" style="margin-right:2%;">
						<?= $optionsAno; ?>
					</select>

					<div id="vinculoBox" class="hide">
					<label for="vinculoConsulta">VÍNCULO:</label>
					<select name="vinculo" id="vinculoConsulta" class="form-control" style="margin-right:2%;">
						<option selected>TODOS</option>
						<option value="1">EFETIVO/CONCURSADO</option>
						<option value="2">ESTÁVEIS</option>
						<option value="3">COMISSIONADOS/EFETIVOS</option>
						<option value="4">COMISSIONADOS/TEMPORÁRIOS</option>
						<option value="5">AGENTES POLÍTICOS</option>
						<option value="6">MEMBRO DE CONSELHOS MUNICIPAIS</option>
						<option value="7">INATIVOS</option>
						<option value="8">PENSIONISTAS</option>
						<option value="9">TEMPORÁRIOS/CONTRATADOS</option>
						<option value="10">OUTROS</option>
					</select>
					</div>

					<button type="button" class="btn btn-default" id="buscaDados" disabled="">Consultar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="showGrid" style="margin-bottom:5%;">
	<div class="container-fluid">
		<div class="panel panel-default">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Listagem de Servidores de acordo com a Lei de Acesso à Informação - 
						<strong>Lei Nº 12.527, de 18 de Novembro de 2011</strong>.
					</h3>
				</div>
				<div class="panel-body">

				<?php if ( $msglinesTable==true ): ?>
					<div id='alertMsg' class='alert alert-danger alert-dismissible' role='alert'>
						<button type='button' class='close' data-dismiss='alert' aria-label='close'>
							<span aria-hidden='true'>&times;</span>
						</button>
						<strong>Nenhum registro encontrado para exibição neste periodo <?= "{$mes}/{$ano}"; ?>!</strong>
					</div>
				<?php endif; ?>

					<table class="table table-bordered table-hover table-condensed table-striped dataTable" style="font-size:9pt;width:100%;margin:0 auto;padding:5px;">
						<thead>
							<tr style="font-weight:bold;">
								<td class='lnRight'>Matríc</td>
								<td>Nome do Servidor</td>
								<td>Cargo/Função</td>
								<td class='lnRight'>Dias Trab</td>
								<td>Vínculo</td>
								<td class='lnCenter'>Dt. Admissão</td>
								<td class='lnRight'>Vencto Base</td>
								<td class='lnRight'>Tot. Venctos</td>
								<td class='lnRight'>Tot. Descontos</td>
								<td class='lnRight'>Sal. Líquido</td>
								<td>Estado Funcional</td>
							</tr>
						</thead>
						<tbody>
							<?= @$linesTable; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>