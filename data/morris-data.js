$(function() {
    $.ajax({
        type: "POST",
        url: 'ajax/RetornaDadosGrafico.php',
        data: {},
        dataType : 'json',            
        success: function(retorno) {
            setaGrafico(retorno);
        }
    });

    function setaGrafico(retorno) {
        Morris.Line({
            element: 'morris-area-chart',
            data: retorno,
            xkey: 'periodo',
            ykeys: ['carro1', 'carro2', 'carro3'],
            labels: ['Anderson Metzner', 'Marcio Alexandre Vieira', 'Wilson Lopes Gerber'],
            pointSize: 2,
            hideHover: 'auto',
            resize: true
        });
    }
});
