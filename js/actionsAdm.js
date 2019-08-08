$(document).ready(function()
{
	// alert('carregou');
	$('#selConsultas, #selExames option').on( 'change', mudaEspecialidade );
	// $("p").click(function(){
	//     $(this).hide();
	// });
});

var mudaEspecialidade = function()
{
	// alert('achow');
	var self = $(this);
	var idGet = self.val();
	// var id = idGet.substr(1, 1);
	var pg = idGet.substr(0, 1) == 'C' ? 'consultas' : 'exames';
	// console.log( "id: "+id+' pg='+pg );
	location.href="?padm="+pg+"&cod="+idGet;
}