<?php
include_once("../engine/connect.php");
include_once("../engine/funcoes.php");
$retorno = null;

$conexao = conectar();

$sql = "SELECT horas_trabalhadas FROM configuracoes";
$stmt = $conexao->prepare($sql);
$stmt->execute();

$horasTrabalhadas = null;
while ($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
    $horasTrabalhadas = str_replace(':', '', $linha->horas_trabalhadas);
    $horasTrabalhadasOriginal = $linha->horas_trabalhadas;
}

$totalDiasTrabalhados = 0;
if (isset($_POST['rel'])) {
    $rel = explode('*', $_POST['rel']);
    $periodo = $rel[0];
    $codigo = $rel[1];

    $sqlCabecalho = 'SELECT veiculos.id,
                            veiculos.codigo,
                            veiculos.motorista,
                            min(relatorios.periodo_completo) as periodo_completo,
                            max(relatorios.periodo_completo_final) as periodo_completo_final
                       FROM relatorios
                       JOIN veiculos
                         ON veiculos.id = relatorios.id_veiculo
                      WHERE veiculos.codigo = :codigo
                        AND relatorios.periodo = :periodo
                   GROUP BY veiculos.id,
                            veiculos.codigo,
                            veiculos.motorista';
    $stmtCabecalho = $conexao->prepare($sqlCabecalho);
    $stmtCabecalho->bindValue(':codigo', $codigo);
    $stmtCabecalho->bindValue(':periodo', $periodo);
    $stmtCabecalho->execute();

    $codigoVeiculo = null;
    $motoristaVeiculo = null;
    $periodoInicial = null;
    $periodoFinal = null;
    $idVeiculo = null;
    while ($linha = $stmtCabecalho->fetch(PDO::FETCH_OBJ)) {
        $idVeiculo = $linha->id;        
        $codigoVeiculo = $linha->codigo;
        $motoristaVeiculo = $linha->motorista;
        $periodoInicial = converteData($linha->periodo_completo);
        $periodoFinal = converteData($linha->periodo_completo_final);
    }

    $sqlHorasEsperaVeiculo = 'SELECT data, 
                                     total_horas
                                FROM tempo_espera
                               WHERE id_veiculo = :id_veiculo';
    $stmtHorasEsperaVeiculo = $conexao->prepare($sqlHorasEsperaVeiculo);
    $stmtHorasEsperaVeiculo->bindValue(':id_veiculo', $idVeiculo);
    $stmtHorasEsperaVeiculo->execute();

    $horaDataEsperaVeiculo = [];
    while($linhaVeiculo = $stmtHorasEsperaVeiculo->fetch(PDO::FETCH_OBJ)) {
        $dataEspera = new DateTime($linhaVeiculo->data);
        $dataEspera = $dataEspera->format('d/m/Y');

        $horaDataEsperaVeiculo[$dataEspera] = $linhaVeiculo->total_horas;
    }

    $retorno = '
        <p><b>Motorista:</b> '.$motoristaVeiculo.' ('.$codigoVeiculo.')</p>
        <p><b>Período:</b> '.$periodoInicial.' - '.$periodoFinal.'<p>
        <hr/>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <th>Data</th>
                    <th>Normal</th>
                    <th>Extra 50%</th>
                    <th>Extra 100%</th>
                    <th>Adicional Noturno</th>
                    <th>Horas de espera</th>
                    <th>Total</th>
                </tr>';

    $sqlRelatorio = 'SELECT relatorios.periodo,
                            veiculos.codigo,
                            veiculos.motorista,
                            relatorios.data,
                            relatorios.total_horas,
                            relatorios.total_horas_extra_50,
                            relatorios.total_horas_extra_100,
                            relatorios.total_horas_noturnas
                       FROM relatorios
                       JOIN veiculos
                         ON veiculos.id = relatorios.id_veiculo
                      WHERE veiculos.codigo = :codigo
                        AND relatorios.periodo = :periodo
                   ORDER BY data';
    $stmtRelatorio = $conexao->prepare($sqlRelatorio);
    $stmtRelatorio->bindValue(':codigo', $codigo);
    $stmtRelatorio->bindValue(':periodo', $periodo);
    $stmtRelatorio->execute();

    $segundosHoraNormal = 0;
    $segundosHoraExtra50 = 0;
    $segundosHoraExtra100 = 0;
    $segundosHoraNoturna = 0;
    $segundosHoraEspera = 0;

    $contador = 0;
    while ($linhaRelatorio = $stmtRelatorio->fetch(PDO::FETCH_OBJ)) {
        $contador++;
        $segundosHoraNormalAtual = 0;
        $segundosHoraExtra50Atual = 0;
        $segundosHoraExtra100Atual = 0;
        $segundosHoraNoturnaAtual = 0;
        $segundosHoraEsperaAtual = 0;

        $dataInicialNormal = $linhaRelatorio->data;
        $dataInicialNormalTratada = new \DateTime("$dataInicialNormal 00:00:00");
        $dataFinalNormalTratada   = new \DateTime("$dataInicialNormal $linhaRelatorio->total_horas");
        $segundosHoraNormal += retornaSegundosData($dataInicialNormalTratada, $dataFinalNormalTratada);
        $segundosHoraNormalAtual = retornaSegundosData($dataInicialNormalTratada, $dataFinalNormalTratada);

        if (!empty($linhaRelatorio->total_horas_extra_50)) {
            $dataInicialExtra50 = $linhaRelatorio->data;
            $dataInicialExtra50Tratada = new \DateTime("$dataInicialExtra50 00:00:00");
            $dataFinalExtra50Tratada   = new \DateTime("$dataInicialExtra50 $linhaRelatorio->total_horas_extra_50");

            $segundosHoraExtra50 += retornaSegundosData($dataInicialExtra50Tratada, $dataFinalExtra50Tratada);
            $segundosHoraExtra50Atual = retornaSegundosData($dataInicialExtra50Tratada, $dataFinalExtra50Tratada);
        } else {
            $linhaRelatorio->total_horas_extra_50 = '00:00:00';
        }

        if (!empty($linhaRelatorio->total_horas_extra_100)) {
            $dataInicialExtra100 = $linhaRelatorio->data;
            $dataInicialExtra100Tratada = new \DateTime("$dataInicialExtra100 00:00:00");
            $dataFinalExtra100Tratada   = new \DateTime("$dataInicialExtra100 $linhaRelatorio->total_horas_extra_100");

            $segundosHoraExtra100 += retornaSegundosData($dataInicialExtra100Tratada, $dataFinalExtra100Tratada);

            $segundosHoraExtra100Atual = retornaSegundosData($dataInicialExtra100Tratada, $dataFinalExtra100Tratada);
        } else {
            $linhaRelatorio->total_horas_extra_100 = '00:00:00';
        }

        if (!empty($linhaRelatorio->total_horas_noturnas)) {
            $dataInicialNoturna = $linhaRelatorio->data;
            $dataInicialNoturnaTratada = new \DateTime("$dataInicialNoturna 00:00:00");
            $dataFinalNoturnaTratada = new \DateTime("$dataInicialNoturna $linhaRelatorio->total_horas_noturnas");

            $segundosHoraNoturna += retornaSegundosData($dataInicialNoturnaTratada, $dataFinalNoturnaTratada);
            //$segundosHoraNoturnaAtual = retornaSegundosData($dataInicialNoturnaTratada, $dataFinalNoturnaTratada);
        } else {
            $linhaRelatorio->total_horas_noturnas = '00:00:00';
        }

        $dataRelatorio = new DateTime($linhaRelatorio->data);

        $horasEspera = '00:00:00';
        if (isset($horaDataEsperaVeiculo[$dataRelatorio->format('d/m/Y')])) {
            $horasEspera = $horaDataEsperaVeiculo[$dataRelatorio->format('d/m/Y')];

            $dataInicialEspera = $linhaRelatorio->data;
            $dataInicialEsperaTratada = new \DateTime("$dataInicialEspera 00:00:00");
            $dataFinalEsperaTratada = new \DateTime("$dataInicialEspera $horasEspera");

            $segundosHoraEspera += retornaSegundosData($dataInicialEsperaTratada, $dataFinalEsperaTratada);
            //$segundosHoraEsperaAtual = retornaSegundosData($dataInicialEsperaTratada, $dataFinalEsperaTratada);
        }

        $totalHoras = $segundosHoraExtra50Atual + $segundosHoraNormalAtual + $segundosHoraExtra100Atual;

        $dataNormal = new DateTime('00:00:00');
        $totalNormal = new DateTime('00:00:00');
        $intervaloDataSomatorioSegundos = new DateInterval('PT'.$totalHoras.'S');

        $totalNormal->add($intervaloDataSomatorioSegundos);
        $intervaloTotal = $dataNormal->diff($totalNormal);

        $totalHorasDia = $intervaloTotal->d * 24;
        $totalHorasDia += $intervaloTotal->h;
        $totalMinutosDia = $intervaloTotal->i;
        $totalSegundosDia = $intervaloTotal->s;

        $totalHorasDia = str_pad($totalHorasDia, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutosDia, 2, '0', STR_PAD_LEFT).':'.str_pad($totalSegundosDia, 2, '0', STR_PAD_LEFT);

        $diaSemana = retornaDiaSemana($dataRelatorio->format('N'));

        $totalHorasNormais = str_replace(':', '', $linhaRelatorio->total_horas);
        if ($linhaRelatorio->total_horas >= $horasTrabalhadas) {
            $totalDiasTrabalhados++;
        }

        $textoFeriado = null;
        $feriado = isFeriado($dataRelatorio->format('d/m/Y'));
        if ($feriado) {
            $textoFeriado = '(Feriado)';
        }

        $retorno .= '
        <tr>
            <td>'.$dataRelatorio->format('d/m/Y').' - ('.$diaSemana.') '.$textoFeriado.'</td>
            <td>'.$linhaRelatorio->total_horas.'</td>
            <td>'.$linhaRelatorio->total_horas_extra_50.'</td>
            <td>'.$linhaRelatorio->total_horas_extra_100.'</td>
            <td>'.$linhaRelatorio->total_horas_noturnas.'</td>
            <td>'.$horasEspera.'</td>
            <td>'.$totalHorasDia.'</td>
        </tr>';
    }

    $dataNormal = new DateTime('00:00:00');
    $totalNormal = new DateTime('00:00:00');
    $intervaloDataSomatorioSegundosNormal = new DateInterval('PT'.$segundosHoraNormal.'S');

    $totalNormal->add($intervaloDataSomatorioSegundosNormal);
    $intervaloTotalNomal = $dataNormal->diff($totalNormal);

    $totalHorasDiaNormal = $intervaloTotalNomal->d * 24;
    $totalHorasDiaNormal += $intervaloTotalNomal->h;
    $totalMinutosDiaNormal = $intervaloTotalNomal->i;
    $totalSegundosDiaNormal = $intervaloTotalNomal->s;

    $totalHorasDiaNormal = str_pad($totalHorasDiaNormal, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutosDiaNormal, 2, '0', STR_PAD_LEFT).':'.str_pad($totalSegundosDiaNormal, 2, '0', STR_PAD_LEFT);

    $dataExtra50 = new DateTime('00:00:00');
    $totalExtra50 = new DateTime('00:00:00');
    $intervaloDataSomatorioSegundosExtra50 = new DateInterval('PT'.$segundosHoraExtra50.'S');

    $totalExtra50->add($intervaloDataSomatorioSegundosExtra50);
    $intervaloTotalExtra50 = $dataExtra50->diff($totalExtra50);

    $totalHorasDiaExtra50 = $intervaloTotalExtra50->d * 24;
    $totalHorasDiaExtra50 += $intervaloTotalExtra50->h;
    $totalMinutosDiaExtra50 = $intervaloTotalExtra50->i;
    $totalSegundosDiaExtra50 = $intervaloTotalExtra50->s;

    $totalHorasDiaExtra50 = str_pad($totalHorasDiaExtra50, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutosDiaExtra50, 2, '0', STR_PAD_LEFT).':'.str_pad($totalSegundosDiaExtra50, 2, '0', STR_PAD_LEFT);

    $dataExtra100 = new DateTime('00:00:00');
    $totalExtra100 = new DateTime('00:00:00');

    $intervaloDataSomatorioSegundosExtra100 = new DateInterval('PT'.$segundosHoraExtra100.'S');

    $totalExtra100->add($intervaloDataSomatorioSegundosExtra100);
    $intervaloTotalExtra100 = $dataExtra100->diff($totalExtra100);

    $totalHorasDiaExtra100 = $intervaloTotalExtra100->d * 24;
    $totalHorasDiaExtra100 += $intervaloTotalExtra100->h;
    $totalMinutosDiaExtra100 = $intervaloTotalExtra100->i;
    $totalSegundosDiaExtra100 = $intervaloTotalExtra100->s;

    $totalHorasDiaExtra100 = str_pad($totalHorasDiaExtra100, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutosDiaExtra100, 2, '0', STR_PAD_LEFT).':'.str_pad($totalSegundosDiaExtra100, 2, '0', STR_PAD_LEFT);
    
    $dataNoturna = new DateTime('00:00:00');
    $totalNoturna = new DateTime('00:00:00');

    $intervaloDataSomatorioSegundosNoturna = new DateInterval('PT'.$segundosHoraNoturna.'S');

    $totalNoturna->add($intervaloDataSomatorioSegundosNoturna);
    $intervaloTotalNoturna = $dataNoturna->diff($totalNoturna);

    $totalHorasDiaNoturno = $intervaloTotalNoturna->d * 24;
    $totalHorasDiaNoturno += $intervaloTotalNoturna->h;
    $totalMinutosDiaNoturno = $intervaloTotalNoturna->i;
    $totalSegundosDiaNoturno = $intervaloTotalNoturna->s;

    $totalHorasDiaNoturno = str_pad($totalHorasDiaNoturno, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutosDiaNoturno, 2, '0', STR_PAD_LEFT).':'.str_pad($totalSegundosDiaNoturno, 2, '0', STR_PAD_LEFT);

    $dataEspera = new DateTime('00:00:00');
    $totalEspera = new DateTime('00:00:00');

    $intervaloDataSomatorioSegundosEspera = new DateInterval('PT'.$segundosHoraEspera.'S');

    $totalEspera->add($intervaloDataSomatorioSegundosEspera);
    $intervaloTotalEspera = $dataEspera->diff($totalEspera);

    $totalHorasDiaEspera = $intervaloTotalEspera->d * 24;
    $totalHorasDiaEspera += $intervaloTotalEspera->h;
    $totalMinutosDiaEspera = $intervaloTotalEspera->i;
    $totalSegundosDiaEspera = $intervaloTotalEspera->s;

    $totalHorasDiaEspera = str_pad($totalHorasDiaEspera, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutosDiaEspera, 2, '0', STR_PAD_LEFT).':'.str_pad($totalSegundosDiaEspera, 2, '0', STR_PAD_LEFT);

    $dataTotalGeral = new DateTime('00:00:00');
    $totalGeral = new DateTime('00:00:00');
    $segundosTotal = $segundosHoraExtra100 + $segundosHoraExtra50 + $segundosHoraNormal;
    $intervaloTotalGeral = new DateInterval('PT'.$segundosTotal.'S');

    $totalGeral->add($intervaloTotalGeral);
    $intervaloTotalGeral = $dataTotalGeral->diff($totalGeral);

    $totalHorasGeral = $intervaloTotalGeral->d * 24;
    $totalHorasGeral += $intervaloTotalGeral->h;
    $totalMinutosGeral= $intervaloTotalGeral->i;
    $totalSegundosGeral = $intervaloTotalGeral->s;

    $totalHorasGeral = str_pad($totalHorasGeral, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutosGeral, 2, '0', STR_PAD_LEFT).':'.str_pad($totalSegundosGeral, 2, '0', STR_PAD_LEFT);

    $retorno .= '
            <tr style="font-weight:bold">
                <td></td>
                <td>'.$totalHorasDiaNormal.'</td>
                <td>'.$totalHorasDiaExtra50.'</td>
                <td>'.$totalHorasDiaExtra100.'</td>
                <td>'.$totalHorasDiaNoturno.'</td>
                <td>'.$totalHorasDiaEspera.'</td>
                <td>'.$totalHorasGeral.'</td>
            </tr>
            <tr>
                <td colspan="7"><hr/></td>
            </tr>
            <tr>
                <td colspan="7">Total de dias trabalhados: <b>'.$totalDiasTrabalhados.'</b> dias. <small>(Considerando um dia trabalhado somente acima de <b>'.$horasTrabalhadasOriginal.'</b> horas).</small><br/></td>
            </tr>
        </tbody>
    </table>';

}

echo $retorno;
