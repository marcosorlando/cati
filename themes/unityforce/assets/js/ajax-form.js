$(function () {
    
    $('html').on('submit', 'form:not(.ajax_off)', function () {
        var form = $(this);
        var callback = $(this).find("[name='callback']").attr('value');
        var callback_action = $(this).find("[name='callback_action']").attr('value');
        var url = BASE + "/themes/unityforce/_ajax/" + callback + ".ajax.php";
        
        var formData;
        
        formData = new FormData($(this)[0]);
        
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                form.find('.form_load').fadeIn(500);
            },
            success: function (data) {
                //REMOVE LOAD
                form.find('.form_load').fadeOut('slow', function () {
                    
                    //EXIBE CALLBACKS
                    if (data.trigger) {
                        var CallBackPresent = $('.callback_return');
                        if (CallBackPresent.length) {
                            CallBackPresent.html(data.trigger);
                            $('.trigger_ajax').fadeIn('slow');
                        } else {
                            Trigger(data.trigger);
                        }
                    }
                    /*REALÇA DADOS INVALIDOS*/
                    if (data.field) {
                        form.find('.error').removeClass('error');
                        $("[name=" + data.field + "]").addClass('error').focus();
                    } else {
                        form.find('.error').removeClass('error');
                    }
                    
                    /*REALÇA DADOS INVALIDOS*/
                    if (data.clear) {
                        $(form)[0].reset();
                    }
                    
                    //REDIRECIONA
                    if (data.redirect) {
                        $('.workcontrol_upload p').html("Atualizando dados, aguarde!");
                        $('.workcontrol_upload').fadeIn().css('display', 'flex');
                        window.setTimeout(function () {
                            window.location.href = data.redirect;
                            if (window.location.hash) {
                                window.location.reload();
                            }
                        }, 1500);
                    }
                    
                });
            }
        });
        return false;
    });
});
