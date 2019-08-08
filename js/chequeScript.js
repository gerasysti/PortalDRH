$(document).ready(function()
{
	// alert('JS CHEWQUE');
	// $('#anoCheque').click( changeAno );
	$('#mesCheque').change( viewCheque );
});


var viewCheque = function()
{
	console.log( 'viewCheque: ' );
	var self = $(this);
	var ano = $('#anoCheque').val();
	var mes = self.val();
	var dados = { 'ACTION':'viewCheque','ANO':ano,'MES':mes };
	var rs = getDadosAjax( dados );
	console.log( rs );
}

var changeAno = function()
{
	console.log( 'ChangeAno: ' );
	var self = $(this);
	var ano = self.val();
	var dados = { 'ACTION':'getMeses','ANO':ano };
	var rs = getDadosAjax( dados );
	console.log( dados);
}


var getDadosAjax = function( dados )
{
	console.log(dados);
	$.ajax({
		type: "POST",
		data: dados,
		url: "../src/contracheque/cheque.php",
		beforeSend: function(){
			$('#viewCcheque').html('<i class="fa fa-spinner fa-pulse"></i>');
		},
		success: function(res)
		{
			$('#viewCcheque').html(res);
			console.log(res);
		},
		error: function(){}
	});
	return false;
}