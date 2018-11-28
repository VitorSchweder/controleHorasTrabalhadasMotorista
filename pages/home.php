<?php
    $sqlTotalVeiculos = "SELECT count(1) as total FROM veiculos";
    $stmt = $conexao->prepare($sqlTotalVeiculos);
    $stmt->execute();

    $totalVeiculos = 0;
    while ($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
        $totalVeiculos = $linha->total;
    }

//    $sqlTotalHorarios = "SELECT count(1) as total FROM horarios";
//    $stmt = $conexao->prepare($sqlTotalHorarios);
//    $stmt->execute();
//
//    $totalHorarios = 0;
//    while ($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
//        $totalHorarios = $linha->total;
//    }

    $sqlTotalRelatorio = "SELECT id_veiculo FROM relatorios group by id_veiculo";
    $stmt = $conexao->prepare($sqlTotalRelatorio);
    $stmt->execute();

    $totalRelatorios = 0;
    while ($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
        $totalRelatorios++;
    }
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><i class="fa fa-home fa-fw"></i>Home</h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-car fa-fw fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?=$totalVeiculos?></div>
                        <div>Veículos</div>
                    </div>
                </div>
            </div>
            <a href="./veiculos">
                <div class="panel-footer">
                    <span class="pull-left">Ver detalhes</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
<!--
    <div class="col-lg-4 col-md-4">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-clock-o fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"></div>
                        <div>Horários</div>
                    </div>
                </div>
            </div>
            <a href="./horarios">
                <div class="panel-footer">
                    <span class="pull-left">Ver detalhes</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
-->
    <div class="col-lg-6 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?=$totalRelatorios?></div>
                        <div>Relatórios</div>
                    </div>
                </div>
            </div>
            <a href="./relatorios">
                <div class="panel-footer">
                    <span class="pull-left">Ver detalhes</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Desempenho dos Motoristas
            </div>
            <div class="panel-body">
                <div id="morris-area-chart"></div>
            </div>
        </div>
    </div>
</div>
