/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
$(function() {
    $('#side-menu').metisMenu();
    $(".time").inputmask("h:s",{ "placeholder": "00:00" });
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent();

    while (true) {
        if (element.is('li')) {
            element = element.parent().addClass('in').parent();
        } else {
            break;
        }
    }

    $('button.btn-visualizar').on('click', function() {
        var rel = $(this).attr('rel');
        var parametro = {"rel" : rel};

        $.ajax({
            type: "POST",
            url: 'ajax/RetornaHorariosVeiculo.php',
            data: parametro,
            dataType : 'html',
            success: function(retorno) {
                $('div.modal-body').html(retorno);
            }
        });
    });

    $('form#horario button[type="submit"]').click(function(e) {
        e.preventDefault();

        var msg;
        var titulo = $('form#horario input#titulo').val();

        if (!titulo) {
            msg = 'O campo Título é obrigatório.';
        }

        if (msg) {
            alert(msg);
        } else {
           $('form#horario').submit();
        }
    });

    $('form#veiculo button[type="submit"]').click(function(e) {
        e.preventDefault();

        var msg;
        var motorista = $('input[name=motorista]').val();
        var veiculo = $('input[name=veiculo]').val();
        var codigo = $('input[name=codigo]').val();

        if (!motorista) {
            msg = 'O campo Motorista é obrigatório.';
        } else if (!veiculo) {
            msg = 'O campo Veículo é obrigatório.';
        } else if (!codigo) {
            msg = 'O campo Código é obrigatório.';
        }

        if (msg) {
            alert(msg);
        } else {
           $('form#veiculo').submit();
        }
    });

    $('a.excluir').on('click', function(e) {
        e.preventDefault();

        if (confirm('Deseja realmente Excluir o registro?')) {
            window.location = $(this).attr('href');
        }
    });

    $('button#imprimir').on('click', function() {
        window.print();
    });
});

$().ready(function () {
    $('.modal.printable').on('shown.bs.modal', function () {
        $('.modal-dialog', this).addClass('focused');
        $('body').addClass('modalprinter');

    }).on('hidden.bs.modal', function () {
        $('.modal-dialog', this).removeClass('focused');
        $('body').removeClass('modalprinter');
    });
});
