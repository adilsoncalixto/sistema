
function apenasNumeros(string)
{
    var numsStr = string.replace(/[^0-9]/g,'');
    //return parseInt(numsStr);
    return numsStr;
}

function somenteNumeros(num) {
    var string = num.value;
    var numsStr = string.replace(/[^0-9]/g,'');
    //return parseInt(numsStr);
    num.value = numsStr;
}

function validaCPF(cpf)
{
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11)
        return false;
    for (i = 0; i < cpf.length - 1; i++)
        if (cpf.charAt(i) != cpf.charAt(i + 1))
        {
            digitos_iguais = 0;
            break;
        }
    if (!digitos_iguais)
    {
        numeros = cpf.substring(0,9);
        digitos = cpf.substring(9);
        soma = 0;
        for (i = 10; i > 1; i--)
            soma += numeros.charAt(10 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
            return false;
        numeros = cpf.substring(0,10);
        soma = 0;
        for (i = 11; i > 1; i--)
            soma += numeros.charAt(11 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1))
            return false;
        return true;
    }
    else
        return false;
}

function valData(data) { //dd/mm/aaaa

    day = data.substring(0,2);
    month = data.substring(3,5);
    year = data.substring(6,10);

    if( (month==01) || (month==03) || (month==05) || (month==07) || (month==08) || (month==10) || (month==12) )    {//mes com 31 dias
        if( (day < 01) || (day > 31) ){
            alert('Data Inválida');
        }
    } else

    if( (month==04) || (month==06) || (month==09) || (month==11) ){//mes com 30 dias
        if( (day < 01) || (day > 30) ){
            alert('Data Inválida');
        }
    } else

    if( (month==02) ){//February and leap year
        if( (year % 4 == 0) && ( (year % 100 != 0) || (year % 400 == 0) ) ){
            if( (day < 01) || (day > 29) ){
                alert('Data Inválida');
            }
        } else {
            if( (day < 01) || (day > 28) ){
                alert('Data Inválida');
            }
        }
    }

}