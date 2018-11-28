<?php
    if (isset($_POST['horas-trabalhadas'])) {
        $horasTrabalhadas = filter_var($_POST['horas-trabalhadas'], FILTER_SANITIZE_STRING);
        $horasNoturnaInicial = filter_var($_POST['hora-noturna-inicial'], FILTER_SANITIZE_STRING);
        $horasNoturnaFinal = filter_var($_POST['hora-noturna-final'], FILTER_SANITIZE_STRING);
       
        $sqlConfiguracoesExcluir = "DELETE FROM configuracoes";
        $stmtConfiguracoesExcluir = $conexao->prepare($sqlConfiguracoesExcluir);
        $stmtConfiguracoesExcluir->execute();

        $sql = 'INSERT INTO configuracoes (horas_trabalhadas, 
                                           hora_noturna_inicial,
                                           hora_noturna_final) 
                                   VALUES (:horas_trabalhadas,
                                           :hora_noturna_inicial,
                                           :hora_noturna_final)';
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':horas_trabalhadas', $horasTrabalhadas);
        $stmt->bindValue(':hora_noturna_inicial', $horasNoturnaInicial);
        $stmt->bindValue(':hora_noturna_final', $horasNoturnaFinal);
        $stmt->execute();
    }

    $sql = "SELECT horas_trabalhadas, hora_noturna_inicial, hora_noturna_final FROM configuracoes";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    $horasTrabalhadas = null;
    while ($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
        $horasTrabalhadas = $linha->horas_trabalhadas;
        $horaNoturnaInicial = $linha->hora_noturna_inicial;
        $horaNoturnaFinal = $linha->hora_noturna_final;
    }
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><i class="fa fa-gear fa-fw"></i> Configurações</h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Configurações
            </div>
            <div class="panel-body">
                <form action="" method="post" id="configuracoes">
                    <input type="hidden" name="id" value="<?=$id?>"/>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label style="float:left">Considerar um dia de trabalho, quando houver no mínimo</label>
                                <input class="form-control time" style="max-width:65px;float: left;margin-left: 10px;margin-right: 10px;" name="horas-trabalhadas" value="<?=$horasTrabalhadas?>">
                                <label>horas trabalhadas.</label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Hora noturna inicial</label>
                                <input class="form-control time" style="max-width:65px" value="<?=$horaNoturnaInicial?>" name="hora-noturna-inicial">                                
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Hora noturna final</label>
                                <input class="form-control time" style="max-width:65px" value="<?=$horaNoturnaFinal?>" name="hora-noturna-final">                                
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr/>
                            <button type="submit" class="btn btn-default">Aplicar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
