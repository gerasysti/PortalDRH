$(document).ready(function()
{
	// alert('JS APP');
	// $('#modalAg').click( preparaAgenda );
	// $('#modalAgendamento #submitModal').click( sendModal );
	$('#menuUnidade').find('li').each( function()
	{
		if ( $(this).attr('class') == 'active' )
		{
			selectUnidade( $(this) );
		}
	});
	$('.maskCPF').mask("999.999.999-99");
	$('.maskDATE').mask("99/99/9999");
	$('#confirmForm').validate();
	$('#loginForm').validate();

	$(".dataTable").dataTable({
		"oLanguage":{
			"sLengthMenu": "Display _MENU_ records per page",
			"sZeroRecords": "Nenhum dado encontrado para exibição",
			"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
			"sInfoEmpy": "Nenhum registro para ser exibido",
			"sInfoFiltered": "(filtrado de _MAX_ registros)",
			"sSearch": "Pesquisar:",
			"sLengthMenu": "Mostrar _MENU_ registros por pagina",
			 "oPaginate": {
				 "sFirst":    "Primeiro",
				 "sPrevious": "Anterior",
				 "sNext":     "Próximo",
				 "sLast":     "Último"
			 }
		},
		"sScrollY": "auto",
		"bPaginate": true,
		"aaSorting":[[0, "desc"]],
		"aLengthMenu": [[50, 80], [50, 80]],
    	"bLengthChange" : true,
    	"iDisplayLength" : 50
	});
	$('#DataTables_Table_0_filter input').attr('placeholder', 'Use para filtrar os resultados!');
	setTimeout( "$('#alertMsg').fadeOut('slow')", 7000 );
	$('#mesConsulta').change( liberaBusca );
	$('#anoConsulta').change( liberaBusca );
	// $('#buscaDados').on('click', pesquisaDados );
	// $('#buscaDados')on( 'click', 'button', pesquisaDados );
	// $('#confirmForm, #sendConfirm').submit( sendConfirm );
	// $('#menuUnidade').change( selectUnidade );
});

var liberaBusca = function()
{
	if( $('#mesConsulta').val() != 'Selecione...' )
	{
		$('#buscaDados').attr('type', 'submit').removeAttr('disabled');
	}
}

var pesquisaDados = function()
{
	// console.log('pesquisaDados: ');
	var self = $(this);
	var form = self.closest('form');
	var dados = form.serialize();
	buscaAjax( dados );
}
var buscaAjax = function( dados )
{
	$.ajax({
		type: "POST",
		data: dados,
		url: "app/unidade.php",
		beforeSend: function(){  },
		success: function( res )
		{
			$('#showGrid table tbody').html( res );
		},
		error: function(){ alert('ERRO FILE!') }
	});
	return false;
}

var selectUnidade = function( self )
{
	var element = self.parent().prev();
	var strTxt = self.text();
	var contElement = '<i class="fa fa-shield"></i>';
	var caret = ' <b class="caret"></b>';
	var legend = contElement+strTxt+caret;
	element.html( legend ).focus();
	// console.log( legend );
}

// function validateForm()
// {
//     var x = document.forms["myForm"]["fname"].value;
//     if (x == null || x == "") {
//         alert("Name must be filled out");
//         return false;
//     }
// }



/**
* Funções de Agendamento
--------------------------------------------------------------
var preparaAgenda = function()
{
	var base = $(this).closest('section');
	var service = base.attr('id');
	var typeService = base.find('.headerModal h2 span').text();
	var modal = $('#modalAgendamento .modal-body');
	modal.find('h4').text( typeService );
	modal.find('#servico').val( service );
	modal.find('#tipoServico').val( typeService );
	// console.log(service+' - '+typeService);
}
var sendModal = function()
{
	var dados = $('#modalAgendamento .modal-body form').serialize();
	$.ajax({
		type: "POST",
		data: "modalSends=ok&"+dados,
		url: "src/mailer.php",
		beforeSend: function()
		{
			var txtSpend = '<div class="alert alert-warning alertMsg" role="alert">';
			txtSpend += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
			txtSpend += '<span aria-hidden="true">&times;</span></button>';
			txtSpend += '<strong>Aguarde!</strong> Enviando Informações.</div>';
			$('#modalAgendamento .modal-body').prepend(txtSpend);
		},
		success: function(res)
		{
			$('#modalAgendamento .modal-body .alertMsg').remove();
			var txtSuccess = '<div class="alert alert-warning alertMsg" role="alert">';
			txtSuccess += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
			txtSuccess += '<span aria-hidden="true">&times;</span></button>';
			txtSuccess += '<strong>SUCESSO!</strong> '+res+'</div>';
      		$('#modalAgendamento .modal-body').prepend(txtSuccess);
      		$('#modalAgendamento .modal-body form')[0].reset();
			console.log(res);
		},
		error: function()
		{
			$('#modalAgendamento .modal-body .alertMsg').remove();
			var txtError = '<div class="alert alert-warning alertMsg" role="alert">';
			txtError += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
			txtError += '<span aria-hidden="true">&times;</span></button>';
			txtError += '<strong>ERROR!</strong> Erro ao Enviar seus Dados!</div>';
      		$('#modalAgendamento .modal-body').prepend(txtError);
			// alert('Erro na Fun&ccedil;&atilde;o');
			// setTimeout( $('#modalAgendamento').modal('hide'), 5000 );
		}
	});
}
**/