/**
 * Created by Kratos on 21/03/2017.
 */
$(function() {
    $('.btn-gravar').on('click', function () {
        debugger;
        if($('.input-rodada').val() == ''){
            $('.input-rodada').parent('div.form-group').addClass('has-error');
            return false;
        }
        $('form').submit();
    })

});