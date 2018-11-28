<?php
    if (isset($_POST['titulo'])) {        
        $titulo = filter_var($_POST['titulo'], FILTER_SANITIZE_STRING);
        $observacoes = filter_var($_POST['observacoes'], FILTER_SANITIZE_STRING);
        $segunda = isset($_POST['segunda']) ? 1 : 0;
        $terca = isset($_POST['terca']) ? 1 : 0;
        $quarta = isset($_POST['quarta']) ? 1 : 0;
        $quinta = isset($_POST['quinta']) ? 1 : 0;
        $sexta = isset($_POST['sexta']) ? 1 : 0;
        $sabado = isset($_POST['sabado']) ? 1 : 0;
        $domingo = isset($_POST['domingo']) ? 1 : 0;

        /**
         * Segunda
         */
        $horaInicialSegunda1 = $_POST['horainicialsegunda1'];
        $horaFinalSegunda1 = $_POST['horafinalsegunda1'];
        $horaInicialSegunda2 = $_POST['horainicialsegunda2'];
        $horaFinalSegunda2 = $_POST['horafinalsegunda2'];

        /**
         * Terça
         */
        $horaInicialTerca1 = $_POST['horainicialterca1'];
        $horaFinalTerca1 = $_POST['horafinalterca1'];
        $horaInicialTerca2 = $_POST['horainicialterca2'];
        $horaFinalTerca2 = $_POST['horafinalterca2'];

        /**
         * Quarta
         */
        $horaInicialQuarta1 = $_POST['horainicialquarta1'];
        $horaFinalQuarta1 = $_POST['horafinalquarta1'];
        $horaInicialQuarta2 = $_POST['horainicialquarta2'];
        $horaFinalQuarta2 = $_POST['horafinalquarta2'];

        /**
         * Quinta
         */
        $horaInicialQuinta1 = $_POST['horainicialquinta1'];
        $horaFinalQuinta1 = $_POST['horafinalquinta1'];
        $horaInicialQuinta2 = $_POST['horainicialquinta2'];
        $horaFinalQuinta2 = $_POST['horafinalquinta2'];

        /**
         * Sexta
         */
        $horaInicialSexta1 = $_POST['horainicialsexta1'];
        $horaFinalSexta1 = $_POST['horafinalsexta1'];
        $horaInicialSexta2 = $_POST['horainicialsexta2'];
        $horaFinalSexta2 = $_POST['horafinalsexta2'];

        /**
         * Sábado
         */
        $horaInicialSabado1 = $_POST['horainicialsabado1'];
        $horaFinalSabado1 = $_POST['horafinalsabado1'];
        $horaInicialSabado2 = $_POST['horainicialsabado2'];
        $horaFinalSabado2 = $_POST['horafinalsabado2'];

        /**
         * Domingo
         */
        $horaInicialDomingo1 = $_POST['horainicialdomingo1'];
        $horaFinalDomingo1 = $_POST['horafinaldomingo1'];
        $horaInicialDomingo2 = $_POST['horainicialdomingo2'];
        $horaFinalDomingo2 = $_POST['horafinaldomingo2'];

        if (!empty($_POST['titulo']) && empty($_POST['id'])) {                          
            $sql = 'INSERT INTO horarios (titulo, 
                                        observacoes, 
                                        segunda,
                                        terca,
                                        quarta,
                                        quinta,
                                        sexta,
                                        sabado, 
                                        domingo,
                                        horainicialsegunda1,
                                        horafinalsegunda1,
                                        horainicialsegunda2,
                                        horafinalsegunda2,
                                        horainicialterca1,
                                        horafinalterca1,
                                        horainicialterca2,
                                        horafinalterca2,
                                        horainicialquarta1,
                                        horafinalquarta1,
                                        horainicialquarta2,
                                        horafinalquarta2,
                                        horainicialquinta1,
                                        horafinalquinta1,
                                        horainicialquinta2,
                                        horafinalquinta2,
                                        horainicialsexta1,
                                        horafinalsexta1,
                                        horainicialsexta2,
                                        horafinalsexta2,
                                        horainicialsabado1,
                                        horafinalsabado1,
                                        horainicialsabado2,
                                        horafinalsabado2,
                                        horainicialdomingo1,
                                        horafinaldomingo1,
                                        horainicialdomingo2,
                                        horafinaldomingo2) 
                                VALUES (:titulo, 
                                        :observacoes, 
                                        :segunda,
                                        :terca,
                                        :quarta,
                                        :quinta,
                                        :sexta,
                                        :sabado,
                                        :domingo,
                                        :horainicialsegunda1,
                                        :horafinalsegunda1,
                                        :horainicialsegunda2,
                                        :horafinalsegunda2,
                                        :horainicialterca1,
                                        :horafinalterca1,
                                        :horainicialterca2,
                                        :horafinalterca2,
                                        :horainicialquarta1,
                                        :horafinalquarta1,
                                        :horainicialquarta2,
                                        :horafinalquarta2,
                                        :horainicialquinta1,
                                        :horafinalquinta1,
                                        :horainicialquinta2,
                                        :horafinalquinta2,
                                        :horainicialsexta1,
                                        :horafinalsexta1,
                                        :horainicialsexta2,
                                        :horafinalsexta2,
                                        :horainicialsabado1,
                                        :horafinalsabado1,
                                        :horainicialsabado2,
                                        :horafinalsabado2,
                                        :horainicialdomingo1,
                                        :horafinaldomingo1,
                                        :horainicialdomingo2,
                                        :horafinaldomingo2)';

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':titulo', $titulo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':segunda', $segunda);
            $stmt->bindValue(':terca', $terca);
            $stmt->bindValue(':quarta', $quarta);
            $stmt->bindValue(':quinta', $quinta);
            $stmt->bindValue(':sexta', $sexta);
            $stmt->bindValue(':sabado', $sabado);
            $stmt->bindValue(':domingo', $domingo);
            $stmt->bindValue(':horainicialsegunda1', $horaInicialSegunda1);
            $stmt->bindValue(':horafinalsegunda1', $horaFinalSegunda1);
            $stmt->bindValue(':horainicialsegunda2', $horaInicialSegunda2);
            $stmt->bindValue(':horafinalsegunda2', $horaFinalSegunda2);
            $stmt->bindValue(':horainicialterca1', $horaInicialTerca1);
            $stmt->bindValue(':horafinalterca1', $horaFinalTerca1);
            $stmt->bindValue(':horainicialterca2', $horaInicialTerca2);
            $stmt->bindValue(':horafinalterca2', $horaFinalTerca2);
            $stmt->bindValue(':horainicialquarta1', $horaInicialQuarta1);
            $stmt->bindValue(':horafinalquarta1', $horaFinalQuarta1);
            $stmt->bindValue(':horainicialquarta2', $horaInicialQuarta2);
            $stmt->bindValue(':horafinalquarta2', $horaFinalQuarta2);
            $stmt->bindValue(':horainicialquinta1', $horaInicialQuinta1);
            $stmt->bindValue(':horafinalquinta1', $horaFinalQuinta1);
            $stmt->bindValue(':horainicialquinta2', $horaInicialQuinta2);
            $stmt->bindValue(':horafinalquinta2', $horaFinalQuinta2);
            $stmt->bindValue(':horainicialsexta1', $horaInicialSexta1);
            $stmt->bindValue(':horafinalsexta1', $horaFinalSexta1);
            $stmt->bindValue(':horainicialsexta2', $horaInicialSexta2);
            $stmt->bindValue(':horafinalsexta2', $horaFinalSexta2);
            $stmt->bindValue(':horainicialsabado1', $horaInicialSabado1);
            $stmt->bindValue(':horafinalsabado1', $horaFinalSabado1);
            $stmt->bindValue(':horainicialsabado2', $horaInicialSabado2);
            $stmt->bindValue(':horafinalsabado2', $horaFinalSabado2);
            $stmt->bindValue(':horainicialdomingo1', $horaInicialDomingo1);
            $stmt->bindValue(':horafinaldomingo1', $horaFinalDomingo1);
            $stmt->bindValue(':horainicialdomingo2', $horaInicialDomingo2);
            $stmt->bindValue(':horafinaldomingo2', $horaFinalDomingo2);
            $stmt->execute();
        } else if (!empty($_POST['titulo']) && !empty($_POST['id'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);

            $sql = 'UPDATE horarios
                    SET  titulo = :titulo,
                            observacoes = :observacoes, 
                            segunda = :segunda,
                            terca = :terca,
                            quarta = :quarta,
                            quinta = :quinta,
                            sexta = :sexta,
                            sabado = :sabado, 
                            domingo = :domingo,
                            horainicialsegunda1 = :horainicialsegunda1,
                            horafinalsegunda1 = :horafinalsegunda1,
                            horainicialsegunda2 = :horainicialsegunda2,
                            horafinalsegunda2 = :horafinalsegunda2,
                            horainicialterca1 = :horainicialterca1,
                            horafinalterca1 = :horafinalterca1,
                            horainicialterca2 = :horainicialterca2,
                            horafinalterca2 = :horafinalterca2,
                            horainicialquarta1 = :horainicialquarta1,
                            horafinalquarta1 = :horafinalquarta1,
                            horainicialquarta2 = :horainicialquarta2,
                            horafinalquarta2 = :horafinalquarta2,
                            horainicialquinta1 = :horainicialquinta1,
                            horafinalquinta1 = :horafinalquinta1,
                            horainicialquinta2 = :horainicialquinta2,
                            horafinalquinta2 = :horafinalquinta2,
                            horainicialsexta1= :horainicialsexta1,
                            horafinalsexta1 = :horafinalsexta1,
                            horainicialsexta2 = :horainicialsexta2,
                            horafinalsexta2 = :horafinalsexta2,
                            horainicialsabado1 = :horainicialsabado1,
                            horafinalsabado1 = :horafinalsabado1,
                            horainicialsabado2 = :horainicialsabado2,
                            horafinalsabado2 = :horafinalsabado2,
                            horainicialdomingo1 = :horainicialdomingo1,
                            horafinaldomingo1 = :horafinaldomingo1,
                            horainicialdomingo2= :horainicialdomingo2,
                            horafinaldomingo2 = :horafinaldomingo2
                    WHERE id = :id';

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':titulo', $titulo);
            $stmt->bindValue(':observacoes', $observacoes);
            $stmt->bindValue(':segunda', $segunda);
            $stmt->bindValue(':terca', $terca);
            $stmt->bindValue(':quarta', $quarta);
            $stmt->bindValue(':quinta', $quinta);
            $stmt->bindValue(':sexta', $sexta);
            $stmt->bindValue(':sabado', $sabado);
            $stmt->bindValue(':domingo', $domingo);
            $stmt->bindValue(':horainicialsegunda1', $horaInicialSegunda1);
            $stmt->bindValue(':horafinalsegunda1', $horaFinalSegunda1);
            $stmt->bindValue(':horainicialsegunda2', $horaInicialSegunda2);
            $stmt->bindValue(':horafinalsegunda2', $horaFinalSegunda2);
            $stmt->bindValue(':horainicialterca1', $horaInicialTerca1);
            $stmt->bindValue(':horafinalterca1', $horaFinalTerca1);
            $stmt->bindValue(':horainicialterca2', $horaInicialTerca2);
            $stmt->bindValue(':horafinalterca2', $horaFinalTerca2);
            $stmt->bindValue(':horainicialquarta1', $horaInicialQuarta1);
            $stmt->bindValue(':horafinalquarta1', $horaFinalQuarta1);
            $stmt->bindValue(':horainicialquarta2', $horaInicialQuarta2);
            $stmt->bindValue(':horafinalquarta2', $horaFinalQuarta2);
            $stmt->bindValue(':horainicialquinta1', $horaInicialQuinta1);
            $stmt->bindValue(':horafinalquinta1', $horaFinalQuinta1);
            $stmt->bindValue(':horainicialquinta2', $horaInicialQuinta2);
            $stmt->bindValue(':horafinalquinta2', $horaFinalQuinta2);
            $stmt->bindValue(':horainicialsexta1', $horaInicialSexta1);
            $stmt->bindValue(':horafinalsexta1', $horaFinalSexta1);
            $stmt->bindValue(':horainicialsexta2', $horaInicialSexta2);
            $stmt->bindValue(':horafinalsexta2', $horaFinalSexta2);
            $stmt->bindValue(':horainicialsabado1', $horaInicialSabado1);
            $stmt->bindValue(':horafinalsabado1', $horaFinalSabado1);
            $stmt->bindValue(':horainicialsabado2', $horaInicialSabado2);
            $stmt->bindValue(':horafinalsabado2', $horaFinalSabado2);
            $stmt->bindValue(':horainicialdomingo1', $horaInicialDomingo1);
            $stmt->bindValue(':horafinaldomingo1', $horaFinalDomingo1);
            $stmt->bindValue(':horainicialdomingo2', $horaInicialDomingo2);
            $stmt->bindValue(':horafinaldomingo2', $horaFinalDomingo2);
            $stmt->execute();

            header('location: '.$base_url.'horarios');
        }
    }

    $titulo = null;
    $observacoes = null;
    $segunda = null;
    $terca = null;
    $quarta = null;
    $quinta = null;
    $sexta = null;
    $sabado = null;
    $domingo = null;

    /**
     * Segunda
     */
    $horaInicialSegunda1 = null;
    $horaFinalSegunda1 = null;
    $horaInicialSegunda2 = null;
    $horaFinalSegunda2 = null;

    /**
     * Terça
     */
    $horaInicialTerca1 = null;
    $horaFinalTerca1 = null;
    $horaInicialTerca2 = null;
    $horaFinalTerca2 = null;

    /**
     * Quarta
     */
    $horaInicialQuarta1 = null;
    $horaFinalQuarta1 = null;
    $horaInicialQuarta2 = null;
    $horaFinalQuarta2 = null;

    /**
     * Quinta
     */
    $horaInicialQuinta1 = null;
    $horaFinalQuinta1 = null;
    $horaInicialQuinta2 = null;
    $horaFinalQuinta2 = null;

    /**
     * Sexta
     */
    $horaInicialSexta1 = null;
    $horaFinalSexta1 = null;
    $horaInicialSexta2 = null;
    $horaFinalSexta2 = null;

    /**
     * Sábado
     */
    $horaInicialSabado1 = null;
    $horaFinalSabado1 = null;
    $horaInicialSabado2 = null;
    $horaFinalSabado2 = null;

    /**
     * Domingo
     */
    $horaInicialDomingo1 = null;
    $horaFinalDomingo1 = null;
    $horaInicialDomingo2 = null;
    $horaFinalDomingo2 = null;

    $tituloAcaoBtn = 'Cadastrar';

    if (!empty($id)) {
        if ($acao == 'alterar') {
            $sqlHorarioAlterar = "SELECT * FROM horarios WHERE id = :id";
            $stmtHorarioAlterar = $conexao->prepare($sqlHorarioAlterar);
            $stmtHorarioAlterar->bindValue(':id', $id);
            $stmtHorarioAlterar->execute();

            while ($linha = $stmtHorarioAlterar->fetch(PDO::FETCH_OBJ)) {  
                $titulo = $linha->titulo;
                $observacoes = $linha->observacoes;
                $segunda = $linha->segunda == 1 ? 'checked' : null;
                $terca = $linha->terca == 1 ? 'checked' : null;
                $quarta = $linha->quarta == 1 ? 'checked' : null;
                $quinta = $linha->quinta == 1 ? 'checked' : null;
                $sexta = $linha->sexta == 1 ? 'checked' : null;
                $sabado = $linha->sabado == 1 ? 'checked' : null;
                $domingo = $linha->domingo == 1 ? 'checked' : null;

                 /**
                 * Segunda
                 */
                $horaInicialSegunda1 = $linha->horainicialsegunda1;
                $horaFinalSegunda1 = $linha->horafinalsegunda1;
                $horaInicialSegunda2 = $linha->horainicialsegunda2;
                $horaFinalSegunda2 = $linha->horafinalsegunda2;

                /**
                 * Terça
                 */
                $horaInicialTerca1 = $linha->horainicialterca1;
                $horaFinalTerca1 = $linha->horafinalterca1;
                $horaInicialTerca2 = $linha->horainicialterca2;
                $horaFinalTerca2 = $linha->horafinalterca2;

                /**
                 * Quarta
                 */
                $horaInicialQuarta1 = $linha->horainicialquarta1;
                $horaFinalQuarta1 = $linha->horafinalquarta1;
                $horaInicialQuarta2 = $linha->horainicialquarta2;
                $horaFinalQuarta2 = $linha->horafinalquarta2;

                /**
                 * Quinta
                 */
                $horaInicialQuinta1 = $linha->horainicialquinta1;
                $horaFinalQuinta1 = $linha->horafinalquinta1;
                $horaInicialQuinta2 = $linha->horainicialquinta2;
                $horaFinalQuinta2 = $linha->horafinalquinta2;

                /**
                 * Sexta
                 */
                $horaInicialSexta1 = $linha->horainicialsexta1;
                $horaFinalSexta1 = $linha->horafinalsexta1;
                $horaInicialSexta2 = $linha->horainicialsexta2;
                $horaFinalSexta2 = $linha->horafinalsexta2;

                /**
                 * Sábado
                 */
                $horaInicialSabado1 = $linha->horainicialsabado1;
                $horaFinalSabado1 = $linha->horafinalsabado1;
                $horaInicialSabado2 = $linha->horainicialsabado2;
                $horaFinalSabado2 = $linha->horafinalsabado2;

                /**
                 * Domingo
                 */
                $horaInicialDomingo1 = $linha->horainicialdomingo1;
                $horaFinalDomingo1 = $linha->horafinaldomingo1;
                $horaInicialDomingo2 = $linha->horainicialdomingo2;
                $horaFinalDomingo2 = $linha->horafinaldomingo2;
            }

            $tituloAcaoBtn = 'Alterar';
        } else if ($acao == 'excluir') {
            $sqlHorariosExcluir = "DELETE FROM horarios WHERE id = :id";
            $stmtHorariosExcluir = $conexao->prepare($sqlHorariosExcluir);
            $stmtHorariosExcluir->bindValue(':id', $id);
            $stmtHorariosExcluir->execute();

            header('location: '.$base_url.'horarios');
        }
    }
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><i class="fa fa-clock-o fa-fw"></i> Horários</h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Cadastro de Horários
            </div>
            <div class="panel-body">
                <form action="" method="post" id="horario">
                    <input type="hidden" name="id" value="<?=$id?>"/>
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label>Título</label>
                                <input class="form-control" name="titulo" id="titulo" value=<?=$titulo?>>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <label>Observações</label>
                                <input class="form-control" name="observacoes" value=<?=$observacoes?>>
                            </div>
                        </div>
                        <div class="col-lg-12 horarios-grid">
                            <hr/>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="segunda" <?=$segunda?>><b>Seg</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 1</label>
                                        <input value="<?=$horaInicialSegunda1?>" class="form-control time" name="horainicialsegunda1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 1</label>
                                        <input value="<?=$horaFinalSegunda1?>" class="form-control time" name="horafinalsegunda1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 2</label>
                                        <input value="<?=$horaInicialSegunda2?>" class="form-control time" name="horainicialsegunda2">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 2</label>
                                        <input value="<?=$horaFinalSegunda2?>" class="form-control time" name="horafinalsegunda2">
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="terca" <?=$terca?>><b>Ter</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 1</label>
                                        <input value="<?=$horaInicialTerca1?>" class="form-control time" name="horainicialterca1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 1</label>
                                        <input value="<?=$horaFinalTerca1?>" class="form-control time" name="horafinalterca1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 2</label>
                                        <input value="<?=$horaInicialTerca2?>" class="form-control time" name="horainicialterca2">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 2</label>
                                        <input value="<?=$horaFinalTerca2?>" class="form-control time" name="horafinalterca2">
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="quarta" <?=$quarta?>><b>Qua</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 1</label>
                                        <input value="<?=$horaInicialQuarta1?>" class="form-control time" name="horainicialquarta1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 1</label>
                                        <input value="<?=$horaFinalQuarta1?>"  class="form-control time" name="horafinalquarta1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 2</label>
                                        <input value="<?=$horaInicialQuarta2?>" class="form-control time" name="horainicialquarta2">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 2</label>
                                        <input value="<?=$horaFinalQuarta2?>" class="form-control time" name="horafinalquarta2">
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="quinta" <?=$quinta?>><b>Qui</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 1</label>
                                        <input value="<?=$horaInicialQuinta1?>" class="form-control time" name="horainicialquinta1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 1</label>
                                        <input value="<?=$horaFinalQuinta1?>" class="form-control time" name="horafinalquinta1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 2</label>
                                        <input value="<?=$horaInicialQuinta2?>" class="form-control time" name="horainicialquinta2">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 2</label>
                                        <input value="<?=$horaFinalQuinta2?>" class="form-control time" name="horafinalquinta2">
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sexta" <?=$sexta?>><b>Sex</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 1</label>
                                        <input value="<?=$horaInicialSexta1?>" class="form-control time" name="horainicialsexta1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 1</label>
                                        <input value="<?=$horaFinalSexta1?>" class="form-control time" name="horafinalsexta1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 2</label>
                                        <input value="<?=$horaInicialSexta2?>" class="form-control time" name="horainicialsexta2">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 2</label>
                                        <input value="<?=$horaFinalSexta2?>" class="form-control time" name="horafinalsexta2">
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sabado" <?=$sabado?>><b>Sab</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 1</label>
                                        <input value="<?=$horaInicialSabado1?>" class="form-control time" name="horainicialsabado1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 1</label>
                                        <input value="<?=$horaFinalSabado1?>" class="form-control time" name="horafinalsabado1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 2</label>
                                        <input value="<?=$horaInicialSabado2?>" class="form-control time" name="horainicialsabado2">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 2</label>
                                        <input value="<?=$horaFinalSabado2?>" class="form-control time" name="horafinalsabado2">
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="domingo" <?=$domingo?>><b>Dom</b>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 1</label>
                                        <input value="<?=$horaInicialDomingo1?>" class="form-control time" name="horainicialdomingo1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 1</label>
                                        <input value="<?=$horaFinalDomingo1?>" class="form-control time" name="horafinaldomingo1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Inicial 2</label>
                                        <input value="<?=$horaInicialDomingo2?>" class="form-control time" name="horainicialdomingo2">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário Final 2</label>
                                        <input value="<?=$horaFinalDomingo2?>" class="form-control time" name="horafinaldomingo2">
                                    </div>
                                </div>
                            </div>
                            <hr/>
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
                Listagem de Horários
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
                                                <th>Título</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php     
                                            $sqlHorarios = "SELECT * FROM horarios";
                                            $stmtHorarios = $conexao->prepare($sqlHorarios);
                                            $stmtHorarios->execute();                                                                                                               

                                            while ($linha = $stmtHorarios->fetch(PDO::FETCH_OBJ)) {                                    
                                                echo '
                                                <tr>
                                                    <td>'.$linha->id.'</td>
                                                    <td>'.$linha->titulo.'</td>                                                  
                                                    <td>
                                                        <a class="btn btn-primary btn-xs" href="'.$base_url.'horarios/alterar/'.$linha->id.'">Alterar</a>
                                                        <a class="btn btn-primary btn-xs color-red excluir" href="'.$base_url.'horarios/excluir/'.$linha->id.'">Excluir</a>
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