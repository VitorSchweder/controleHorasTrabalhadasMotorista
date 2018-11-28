<?php
    if (isset($_POST['veiculo'])) {        
        $veiculo = filter_var($_POST['veiculo'], FILTER_SANITIZE_STRING);
        $motorista = filter_var($_POST['motorista'], FILTER_SANITIZE_STRING);
        $codigo = filter_var($_POST['codigo'], FILTER_SANITIZE_STRING); 
        $cargaHoraria = filter_var($_POST['carga-horaria'], FILTER_SANITIZE_STRING); 

        if (!empty($_POST['veiculo']) && empty($_POST['id'])) {                          
            $sql = 'INSERT INTO veiculos (nome, motorista, codigo, carga_horaria) VALUES (:nome, :motorista, :codigo, :carga_horaria)';

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':nome', $veiculo);
            $stmt->bindValue(':motorista', $motorista);
            $stmt->bindValue(':codigo', $codigo);
            $stmt->bindValue(':carga_horaria', $cargaHoraria);
            $stmt->execute();
        } else if (!empty($_POST['veiculo']) && !empty($_POST['id'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);

            $sql = 'UPDATE veiculos
                       SET nome = :nome,
                           motorista = :motorista,
                           codigo = :codigo,
                           carga_horaria = :carga_horaria
                     WHERE id = :id';

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':nome', $veiculo);
            $stmt->bindValue(':motorista', $motorista);
            $stmt->bindValue(':codigo', $codigo);
            $stmt->bindValue(':carga_horaria', $cargaHoraria);
            $stmt->execute();

            header('location: '.$base_url.'veiculos');
        }
    }

    $nomeVeiculo = null;
    $codigoVeiculo = null;
    $cargaHorariaVeiculo = null;
    $motoristaVeiculo = null;
    $tituloAcaoBtn = 'Cadastrar';

    if (!empty($id)) {
        if ($acao == 'alterar') {
            $sqlVeiculosAlterar = "SELECT nome,codigo,motorista,carga_horaria FROM veiculos WHERE id = :id";
            $stmtVeiculosAlterar = $conexao->prepare($sqlVeiculosAlterar);
            $stmtVeiculosAlterar->bindValue(':id', $id);
            $stmtVeiculosAlterar->execute();

            while ($linha = $stmtVeiculosAlterar->fetch(PDO::FETCH_OBJ)) {  
                $nomeVeiculo = $linha->nome;
                $codigoVeiculo = $linha->codigo;
                $cargaHorariaVeiculo = $linha->carga_horaria;
                $motoristaVeiculo = $linha->motorista;
            }

            $tituloAcaoBtn = 'Alterar';
        } else if ($acao == 'excluir') {
            $sqlVeiculosExcluir = "DELETE FROM veiculos WHERE id = :id";
            $stmtVeiculosExcluir = $conexao->prepare($sqlVeiculosExcluir);
            $stmtVeiculosExcluir->bindValue(':id', $id);
            $stmtVeiculosExcluir->execute();

            header('location: '.$base_url.'veiculos');
        }
    }
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><i class="fa fa-car fa-fw"></i> Veículos</h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Cadastro de Veículos
            </div>
            <div class="panel-body">
                <form action="" method="post" id="veiculo">
                    <input type="hidden" name="id" value="<?=$id?>"/>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Motorista</label>
                                <input class="form-control" name="motorista" value="<?=$motoristaVeiculo?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Veículo</label>
                                        <input class="form-control" name="veiculo" value="<?=$nomeVeiculo?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Código</label>
                                        <input class="form-control" name="codigo" value="<?=$codigoVeiculo?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Carga horária</label>
                                        <input class="form-control time" name="carga-horaria" value="<?=$cargaHorariaVeiculo?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr/>
                            <button type="submit" class="btn btn-default"><?=$tituloAcaoBtn?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Listagem de Veículos
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form role="form">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Veículo</th>
                                                <th>Motorista</th>
                                                <th>Código</th>
                                                <th>Carga horária</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php     
                                            $sqlVeiculos = "SELECT id,nome,codigo,motorista,carga_horaria FROM veiculos";
                                            $stmtVeiculos = $conexao->prepare($sqlVeiculos);
                                            $stmtVeiculos->execute();
                                                                                                                                                           
                                            while ($linha = $stmtVeiculos->fetch(PDO::FETCH_OBJ)) {                                    
                                                echo '
                                                <tr>
                                                    <td>'.$linha->id.'</td>
                                                    <td>'.$linha->nome.'</td>
                                                    <td>'.$linha->motorista.'</td>
                                                    <td>'.$linha->codigo.'</td>
                                                    <td>'.$linha->carga_horaria.'</td>
                                                    <td>
                                                        <a class="btn btn-primary btn-xs" href="'.$base_url.'veiculos/alterar/'.$linha->id.'">Alterar</a>
                                                        <a class="btn btn-primary btn-xs color-red excluir" href="'.$base_url.'veiculos/excluir/'.$linha->id.'">Excluir</a>
                                                    </td>
                                                </tr>';
                                            }
                                        ?>                                                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
