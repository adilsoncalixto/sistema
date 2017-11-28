var d = new Date();
dateP = (d.toLocaleString());

$(document).ready(function(){
    $('.caixa_alta').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    });
});

$(document).ready(function(){
    $('.caixa_baixa').keyup(function(){
        $(this).val($(this).val().toLowerCase());
    });
});

//datepicker default contrato
$('.calendario .date').datepicker({
  format: 'dd/mm/yyyy',
  language: 'pt-BR',
  orientation: 'top auto',
  autoclose: true,
  //startDate: dateP
});

//modal novo contrato
$('#novo').on('click', function() {
    //limpa campos
    $('#identificacao').val('');

    //chrome
    $('#cliente option[value=0]').attr('selected','selected');
    $('#tipo_contrato option[value=0]').attr('selected','selected');
    $('#status_contrato option[value=0]').attr('selected','selected');

    //safari
    $('#cliente option[value=0]').attr('selected', true);
    $('#tipo_contrato option[value=0]').attr('selected', true);
    $('#status_contrato option[value=0]').attr('selected', true);

    $('#cliente option').attr('selected', false);
    $('#tipo_contrato option').attr('selected', false);
    $('#status_contrato option').attr('selected', false);

    //firefox
    $('#cliente option[value=0]').prop('selected','selected');
    $('#tipo_contrato option[value=0]').prop('selected','selected');
    $('#status_contrato option[value=0]').prop('selected','selected');
    
    //atualizar
    updates();

    //abre modal
    $('#ModalNovoContrato').modal('show');
});

//click botão salvar dados modal
$('#salvar_dados').on('click', function() {
    //dados
    var cliente = $('#cliente').val();
    var identificacao = $('#identificacao').val();
    var tipo_contrato = $('#tipo_contrato').val();
    var status_contrato = $('#status_contrato').val();
    var vigencia = $('#vigencia').val();
    var check = $('#vigencia_check').is(':checked');
    
    //validar campo não selecionado cliente
    var cl = validarCampoIgualZero(cliente, 'for_cliente_message', 'Cliente');
        
    //validar campo vazio Identificação
    if(cl == true) var iden = campoObrigatorio(identificacao, 'for_identificacao_message', 'for_identificacao', 'Identificação');
    
    //validar campo não selecionado tipo contrato
    if(iden == true) var Tip = validarCampoIgualZero(tipo_contrato, 'for_tipo_contrato_message', 'Tipo de contrato');
    
    //validar campo não selecionado status contrato
    if(Tip == true) var Sta = validarCampoIgualZero(status_contrato, 'for_status_contrato_message', 'Status');
    
    //valida se foi feito o check ou se não adicionou nenhuma data
    if(Sta == true)
        var vig = validarVigencia(vigencia.length, check, 'for_vigencia_message', 'for_vigencia', 'Data de vigência'); 
    
    if(vig == true){
    
        var formDados = $('#novo_contrato').serializeArray(); //dados inputs
        var URL = $('#novo_contrato').attr('action'); //url action
        $.post(URL,
            formDados,
            function(data){
                    
                $('#ModalNovoContrato').modal('hide'); //fecha modal
                location.reload(); //atualiza pagina

            }).fail(function() { });
    }
});

//quando click no check da vigencia
$('#vigencia_check').on('click', function() {
    var check = $('#vigencia_check').is(':checked'); //verifica se foi feio o check
    //se foi feito o check
    if(check ==  true){
        
        $('#vigencia').val(''); //limpa campos data
        $('.calendario .date').datepicker('remove'); //remove datepicker
        
        //monta vazioo datepicker
        $('.calendario .date').datepicker({
          format: 'dd/mm/yyyy',
          language: 'pt-BR',
          orientation: 'top auto',
          autoclose: true,
          //startDate: dateP
        });
        
        //desabilita o campo
        $('#vigencia').attr('disabled', true);
    
    } else{
        //habilita o campo
        $('#vigencia').attr('disabled', false);
    }
});

//quando click no check da vigencia
$('#vigencia_check_editar').on('click', function() {
    var check = $('#vigencia_check_editar').is(':checked');//verifica se foi feio o check
    //se foi feito o check
    if(check ==  true){
        
        $('#vigencia_editar').val('');//limpa campos data
        $('.calendario .date').datepicker('remove');//remove datepicker
        //monta vazioo datepicker
        $('.calendario .date').datepicker({
          format: 'dd/mm/yyyy',
          language: 'pt-BR',
          orientation: 'top auto',
          autoclose: true,
          //startDate: dateP
        });
        
        //desabilita o campo
        $('#vigencia_editar').attr('disabled', true);
    
    } else{
         //habilita o campo
        $('#vigencia_editar').attr('disabled', false);
    }
});

$('.date').on('change', function() //change para atualiza data para formato pt-br
{
    var data = $('.date');

    $.each(data, function() {
      $(this).datepicker('remove');
      $(this).datepicker({
         format: 'dd/mm/yyyy',
         language: 'pt-BR',
         autoclose: true
     });            
   });
});

//click botão atualizar dados modal editar
$('#salvar_dados_editar').on('click', function() {
    //dados modal
    var id = $('#id_contrato_editar').val();
    var cliente = $('#cliente_editar').val();
    var identificacao = $('#identificacao_editar').val();
    var tipo_contrato = $('#tipo_contrato_editar').val();
    var status_contrato = $('#status_contrato_editar').val();
    var vigencia = $('#vigencia_editar').val();
    var check = $('#vigencia_check_editar').is(':checked');

    var contrato = liberadoEncerramento(id); //verificar se contrato esta liberado para encerrar
    if(contrato == false && status_contrato == 1){

        $('#mensage_parametro').empty(); //limpa div message
        //aviso
        $('#mensage_parametro').append(
            '<div class="alert alert-warning">'+
                '<button data-dismiss="alert" class="close">×</button>'+
                    '<i class="fa fa-exclamation-triangle"></i> '+
                        ' <strong>Aviso!</strong> Encerramento não autorizado, há projetos não encerrado no Rosie.'+
            '</div>');
        $('#ModalEditarContrato').modal('hide'); //fecha modal
        return false;
    }

    //valida campo igual 0 cliente editar
    var cl = validarCampoIgualZero(cliente, 'for_cliente_message_editar', 'Cliente');
    
    //valida campo obrigatio ou vazio identificação editar
    if(cl == true) var iden = campoObrigatorio(identificacao, 'for_identificacao_message_editar', 'for_identificacao_editar', 'Identificação');
    
    //valida campo igual 0 tipo contrato modal editar
    if(iden == true) var Tip = validarCampoIgualZero(tipo_contrato, 'for_tipo_contrato_message_editar', 'Tipo de contrato');
    
    //valida campo igual 0 status contrato modal editar
    if(Tip == true) var Sta = validarCampoIgualZero(status_contrato, 'for_status_contrato_message_editar', 'Status');

    //validar vigencia / data
    if(Sta == true)
        var vig = validarVigencia(vigencia.length, check, 'for_vigencia_message_editar', 'for_vigencia_editar', 'Data de vigência'); 
    
    if(vig == true){
    
        var formDados = $('#editar_contrato').serializeArray(); //dados inputs
        var URL = $('#editar_contrato').attr('action'); //url action
        $.post(URL,
            formDados,
            function(data) {
                    
                $('#ModalEditarContrato').modal('hide'); //fecha modal
                location.reload(); //atualiza pagina

            }).fail(function() { });
    }
});

$('#remover_dados').on('click', function(){

    var formDados = $('#remover_contrato').serializeArray(); //dados inputs
    var URL = $('#remover_contrato').attr('action'); //url action
    $.post(URL,
        formDados,
        function(data)
        {
            var r = $.isPlainObject(data);

            if(r == true){
                    
                $('#ModalRemoverContrato').modal('hide'); //fecha modal
                location.reload(); //atualiza pagina

            } else {

                $('#mensage_parametro').empty(); //limpa campo, message identificação
                $('#mensage_parametro').append(
                    '<div class="alert alert-warning"><button data-dismiss="alert" class="close">×</button><i class="fa fa-exclamation-triangle"></i> <strong>Aviso!</strong> Ação não autorizado, há projetos vinculado ao contrato.</div>'
                );
                $('#ModalRemoverContrato').modal('hide'); //fecha modal
                return false;
            }

        }).fail(function(jqXHR, textStatus, errorThrown){ });
});

//click botão atualizar dados modal editar
$('#salvar_dados_encerrar').on('click', function() {
    var id = $('#id_contrato_encerrar').val();
    var contrato = liberadoEncerramento(id); //verificar se contrato esta liberado para encerrar
    if(contrato == false ){

        $('#mensage_parametro').empty(); //limpa div message
        //aviso
        $('#mensage_parametro').append(
            '<div class="alert alert-warning">'+
                '<button data-dismiss="alert" class="close">×</button>'+
                    '<i class="fa fa-exclamation-triangle"></i> '+
                        ' <strong>Aviso!</strong> Encerramento não autorizado, há projeto vinculado ao contrato não encerrado no Rosie.'+
            '</div>');
        $('#ModalEncerrarContrato').modal('hide'); //fecha modal
        return false;
    }

    
    var formDados = $('#encerrar_contrato').serializeArray(); //dados inputs
    var URL = $('#encerrar_contrato').attr('action'); //url action
    $.post(URL,
        formDados,
        function(data) {
                    
            $('#ModalEncerrarContrato').modal('hide'); //fecha modal
             location.reload(); //atualiza pagina

    }).fail(function() { });

});

function validarCampoIgualZero(id, id_message, titulo) //função valida campo igual 0
{
  if(id == 0){

        $('#'+id_message).empty(); //limpa campo
        //message campo
        $('#'+id_message).append(
            '<div class="alert alert-danger">'+
               '<button data-dismiss="alert" class="close">×</button>'+
                    '<i class="fa fa-info-circle"></i> '+
                       'Nenhum <strong>'+titulo+'</strong> selecionado!'+
            '</div>');
        return false;
    } else{
        $('#'+id_message).empty(); //limpa campo
        return true;
    }  
}

function campoObrigatorio(id, id_message, id_for_name, titulo) //função campo obrigatorio ou vazio
{
    if(id == ''){

        $('#'+id_message).empty(); //limpa campo
        $('#'+id_for_name).addClass('has-error'); //erro label
        //message erro
        $('#'+id_message).append(
            '<div class="alert alert-danger">'+
               '<button data-dismiss="alert" class="close">×</button>'+
                    '<i class="fa fa-info-circle"></i> '+
                       '<strong>'+titulo+'</strong> é obrigatório!'+
            '</div>');
        return false;
    } else{
        $('#'+id_for_name).removeClass('has-error'); //remove erro label
        $('#'+id_message).empty(); //remove message
        return true;
    }  
}

function validarVigencia(id, check, id_message, id_for_name, titulo ) //função valida data vigencia
{
    //se foi feito o check
    if(check == true){

        $('#'+id_message).empty(); //limpa o campo message
        $('#'+id_for_name).removeClass('has-error'); //remove o erro label
        return true;
    }
    //se não fez o check e não adicionou data no calendario
    else if(id == 0) {

        $('#'+id_message).empty(); //limpa campo message
        $('#'+id_for_name).addClass('has-error'); //erro label
        //message 
        $('#'+id_message).append(
            '<div class="alert alert-danger">'+
               '<button data-dismiss="alert" class="close">×</button>'+
                    '<i class="fa fa-info-circle"></i> '+
                       '<strong>'+titulo+'</strong> é obrigatório!'+
            '</div>');
        return false;
    } else {
        return true;
    }
}

function MontarModalEditar(id) //função monta modal editar
{
    var formDados = $('#editar_contrato').serializeArray(); //dados modal
    $.get('/comercial/contrato/edit/'+id,
    function(data)
    {
    
    //limpar Campos editar
        $('#id_contrato_editar').val('');
        $('#identificacao_editar').val('');

        $('#cliente_editar option[value=0]').attr('selected', true);
        $('#cliente_editar option').attr('selected', false);
        
        $('#tipo_contrato_editar option[value=0]').attr('selected', true);
        $('#tipo_contrato_editar option').attr('selected', false);

        $('#status_contrato_editar option[value=0]').attr('selected', true);
        $('#status_contrato_editar option').attr('selected', false);


        //firefox
        $('#cliente_editar option[value=0]').prop('selected','selected');
        $('#tipo_contrato_editar option[value=0]').prop('selected','selected');
        $('#status_contrato_editar option[value=0]').prop('selected','selected');

        
        $('#vigencia_editar').val('');
        $('#vigencia_editar').attr('disabled', false);
        $('.calendario .date').datepicker('remove');
        $('.calendario .date').datepicker({
          format: 'dd/mm/yyyy',
          language: 'pt-BR',
          orientation: 'top auto',
          autoclose: true,
          //startDate: dateP
        });
        
        $('#criterios_reajuste_editar').val('');
        $('#multas_aplicaveis_editar').val('');
        updates();
    //end limpar Campos editar
        
        $('#id_contrato_editar').val(data.id); //id contrato
        
        //seta cliente 
        $('#cliente_editar option[value='+data.client_id+']').attr('selected', true);
        $('#cliente_editar option[value='+data.client_id+']').prop('selected','selected');

        //identificação
        $('#identificacao_editar').val(data.identification);
        
        //seta tipo contrato
        $('#tipo_contrato_editar option[value='+data.type_contract_id+']').attr('selected', true);
        $('#tipo_contrato_editar option[value='+data.type_contract_id+']').prop('selected','selected');
        
        //seta status
        $('#status_contrato_editar option[value='+data.status_contract_id+']').attr('selected', true);
        $('#status_contrato_editar option[value='+data.status_contract_id+']').prop('selected','selected');
        
        //valida se é identermindo ou seta a data
        if(data.date_virgencia !== null && data.date_virgencia.length > 0){
            $('#vigencia_check_editar').prop('checked', false); //tira o check
            $('#vigencia_editar').attr('disabled', false); //habilita campo data
            
            //$('.calendario .date').datepicker('remove');
            $('#vigencia_editar').datepicker('update', formatarData(data.date_virgencia)); //atualiza com a data
            
        } else{
            $('#vigencia_check_editar').prop('checked', true); //check
            $('#vigencia_editar').val(''); //limpa campo data
            $('#vigencia_editar').attr('disabled', true); //desabilita campo dada
        }

        $('#criterios_reajuste_editar').val(data.criterion_readjustment); //criterios
        $('#multas_aplicaveis_editar').val(data.fine_applicable); //multas

        //valida se contrato esta liberado ou não para faturamento
        if(data.project_released == 'S'){
            $('#liberar_faturamento_editar').prop('checked', true); //check
        } else {
            $('#liberar_faturamento_editar').prop('checked', false); //remove check
        }

        updates();
    
    }).fail(function(jqXHR, textStatus, errorThrown){ });

    $('#ModalEditarContrato').modal('show');//abre modal
}

function formatarData(date) //função para formatar data
{
   data = date;
   //split para retirar hora e minutos
   var format = data.split(' ');
   var format = format[0];

   //split para serpara dia, mes e ano
   var format = format.split('-');

   var dia = format[2];
   var mes = format[1];
   var ano = format[0];
   return dia+'-'+mes+'-'+ano; //novo formato
}

function liberadoEncerramento(id) //função valida se contrato esta liberado para encerramento
{
    var result;
    $.ajax({
        type: 'GET',
        async: false,
        url: '/comercial/contrato/liberadoEncerramento/'+id,
        success: function( response )
        {
            result = response;
        }
    });
    return result;
}

function MontarModalRemover(id) //função monta modal remover
{
    var formDados = $('#editar_contrato').serializeArray(); //dados modal
    $.get('/comercial/contrato/edit/'+id,
    function(data)
    {
    
    //limpar Campos remover
        $('#id_contrato_remover').val('');
        $('#identificacao_remover').val('');
        
        $('#vigencia_remover').val('');
        $('#vigencia_remover').attr('disabled', false);
        $('.calendario .date').datepicker('remove');
        $('.calendario .date').datepicker({
          format: 'dd/mm/yyyy',
          language: 'pt-BR',
          orientation: 'top auto',
          autoclose: true,
          //startDate: dateP
        });
        
        $('#criterios_reajuste_remover').val('');
        $('#multas_aplicaveis_remover').val('');
    //end limpar Campos remover
        
        $('#id_contrato_remover').val(data.id); //id contrato
        $('#identificacao_remover').val(data.identification); //identificação

        //seta cliente 
        $('#cliente_remover option[value=0]').attr('selected', true);
        $('#cliente_remover option').attr('selected', false);
        $('#cliente_remover option[value='+data.client_id+']').attr('selected', true);
        $('#cliente_remover option[value='+data.client_id+']').prop('selected','selected');
        
        //seta tipo contrato
        $('#tipo_contrato_remover option[value=0]').attr('selected', true);
        $('#tipo_contrato_remover option').attr('selected', false);
        $('#tipo_contrato_remover option[value='+data.type_contract_id+']').attr('selected', true);
        $('#tipo_contrato_remover option[value='+data.type_contract_id+']').prop('selected','selected');
        
        //seta status
        $('#status_contrato_remover option[value=0]').attr('selected', true);
        $('#status_contrato_remover option').attr('selected', false);
        $('#status_contrato_remover option[value='+data.status_contract_id+']').attr('selected', true);
        $('#status_contrato_remover option[value='+data.status_contract_id+']').prop('selected','selected');
        
        //valida se é identermindo ou seta a data
        if(data.date_virgencia !== null && data.date_virgencia.length > 0){
            $('#vigencia_check_remover').prop('checked', false); //tira o check
            $('#vigencia_remover').attr('disabled', false); //habilita campo data
            
            $('#vigencia_remover').datepicker('update', formatarData(data.date_virgencia)); //atualiza com a data
            
        } else{
            $('#vigencia_check_remover').prop('checked', true); //check
            $('#vigencia_remover').val(''); //limpa campo data
            $('#vigencia_remover').attr('disabled', true); //desabilita campo dada
        }

        $('#criterios_reajuste_remover').val(data.criterion_readjustment); //criterios
        $('#multas_aplicaveis_remover').val(data.fine_applicable); //multas

        //valida se contrato esta liberado ou não para faturamento
        if(data.project_released == 'S'){
            $('#liberar_faturamento_remover').prop('checked', true); //check
        } else {
            $('#liberar_faturamento_remover').prop('checked', false); //remove check
        }

        updates();
    
    }).fail(function(jqXHR, textStatus, errorThrown){ });

    $('#ModalRemoverContrato').modal('show');//abre modal
}

function updates(){
    $('select').select2({width: '100%'});
}