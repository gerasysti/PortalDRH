/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function SomenteNumero(e){
    var tecla = (window.event)?event.keyCode:e.which;
    if( (tecla > 47 && tecla < 58) ) return true;
    else {
        if ( tecla === 8 || tecla === 0) return true;
        else  return false;
    }
}

function TextoMaiusculo(o) {
    return o.value.toUpperCase();
}

function TextoMinusculo(o) {
    return o.value.toLowerCase();
}

function strToInt(value) {
    var str = "0" + value;
    return parseInt(str);
} 

function formatar(mascara, documento){
    var i = documento.value.length;
    var saida = mascara.substring(0, 1);
    var texto = mascara.substring(i);

    if ( texto.substring(0, 1) !== saida ){
        documento.value += texto.substring(0, 1);
    }
//    <td height="24">Hora:</td>
//    <td><input type="text" name="hora" maxlength="5" OnKeyPress="formatar('##:##', this)" ></td>
}

function formatar_numero(mascara, documento, e){
    var retorno = false;
    
    var tecla = (window.event)?event.keyCode:e.which;
    if( (tecla > 47 && tecla < 58) ) retorno = true;
    else {
        if ( tecla === 8 || tecla === 0) retorno = true;
        else  retorno = false;
    }
    
    if ( retorno === true ) {
        var i = documento.value.length;
        var saida = mascara.substring(0, 1);
        var texto = mascara.substring(i);

        if ( texto.substring(0, 1) !== saida ){
            documento.value += texto.substring(0, 1);
        }
    }
    
    return retorno;
//    <td height="24">Hora:</td>
//    <td><input type="text" name="hora" maxlength="5" OnKeyPress="return formatar_numero('##:##', this, event)" ></td>
}

function somente_numero(e){
    var tecla = (window.event)?event.keyCode:e.which;
    if( (tecla > 47 && tecla < 58) ) return true;
    else {
        if ( tecla === 8 || tecla === 0) return true;
        else  return false;
    }
}

function somente_numero_decimal(e){
    var tecla = (window.event)?event.keyCode:e.which;
    if( (tecla > 47 && tecla < 58) ) return true;
    else{
        if ( tecla === 44 || tecla === 8 || tecla === 0) return true;
        else  return false;
    }
}

function formatNumber( value ) {
    // Documentação :
    // http://www.emidioleite.com.br/2013/03/30/numberformat-com-javascript-formatando-numeros-com-javascript/
    var tmp = value + "";
    var num = new NumberFormat();
    num.setInputDecimal(".");
    num.setSeparators(true, '.', ',');
    num.setNumber(tmp);

    return num.toFormatted();
}

function zeroEsquerda(str, qtde) {
//    var foo = "";
//    var tam = zeros - valor.length;
//    
//    while (foo.length < tam) {
//        foo = "0" + foo;
//    }
//    
//    var str = foo.concat(valor);
//    
//    return str;
    var foo = "";
    var qte = qtde * (-1);
    while (foo.length < qtde) {
        foo = "0" + foo;
    }
    
    return (foo + str).slice(qte);
} 

function validarData(data) { 
    // DD/MM/AAAA
    // 0123456789
    // 1234567890
    var dia = data.substring(0,2);
    var mes = data.substring(3,5);
    var ano = data.substring(6,10);
 
    //Criando um objeto Date usando os valores ano, mes e dia.
    var novaData = new Date(ano, (mes-1), dia); 
    
    var mesmoDia = parseInt(dia, 10) === parseInt(novaData.getDate());
    var mesmoMes = parseInt(mes, 10) === parseInt(novaData.getMonth()) + 1;
    var mesmoAno = parseInt(ano) === parseInt(novaData.getFullYear());
 
    if (!((mesmoDia) && (mesmoMes) && (mesmoAno))) {
        return false;
    } else {  
        return true;
    }
}

function validarHora(hora) { 
    // HH:MM
    // 01234
    // 12345
    var hr = hora.substring(0,2);
    var mm = hora.substring(3,5);
 
    if ( (parseInt(hr, 10) < 0) || (parseInt(hr, 10) > 23) || (parseInt(mm, 10) < 0) || (parseInt(mm, 10) > 59) )  {
        return false;
    } else {  
        return true;
    }
}

