<?php
if (isset($acao) && $acao == 'excluir') {
    $dadosExclusao = explode('*', $id);
    $periodoExclusao = $dadosExclusao[0].'/'.$dadosExclusao[1];
    $idVeiculoExcluir = $dadosExclusao[2];

    $sqlRelatoriosExcluir = "DELETE FROM relatorios WHERE id_veiculo = :id_veiculo AND periodo = :periodo";
    $stmtRelatoriosExcluir = $conexao->prepare($sqlRelatoriosExcluir);
    $stmtRelatoriosExcluir->bindValue(':id_veiculo', $idVeiculoExcluir);
    $stmtRelatoriosExcluir->bindValue(':periodo', $periodoExclusao);
    $stmtRelatoriosExcluir->execute();

    $sqlExcluiTempoEspera = "DELETE FROM tempo_espera WHERE id_veiculo = :id_veiculo AND periodo = :periodo";
    $stmtExcluiTempoEspera = $conexao->prepare($sqlExcluiTempoEspera);
    $stmtExcluiTempoEspera->bindValue(':id_veiculo', $idVeiculoExcluir);
    $stmtExcluiTempoEspera->bindValue(':periodo', $periodoExclusao);
    $stmtExcluiTempoEspera->execute();

    header('location: '.$base_url.'relatorios');
}

$textoPdf = null;
$idVeiculoInserir = 0;
$idVeiculoInserirTempoEspera = null;
if (isset($_FILES["arquivo"])) {
    foreach ($_FILES["arquivo"]['name'] as $linha => $nomeArquivo) {        
        $extensaoArquivo = explode('.', $nomeArquivo);
        $extensaoArquivo = $extensaoArquivo[1];
        
        if ($extensaoArquivo != 'pdf' && $extensaoArquivo != 'htm' && $extensaoArquivo != 'html') {
            echo '<div class="text-danger bg-danger" style="margin-bottom:10px;padding:5px">Arquivo inválido</div>';
            die();
        }

        $nomeTemporario = $_FILES["arquivo"]['tmp_name'][$linha];
        if (move_uploaded_file($nomeTemporario, '1.'.$extensaoArquivo)) {
            if ($extensaoArquivo == 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile('1.'.$extensaoArquivo);

                $textoPdf = $pdf->getText();

                $periodoRelatorio = explode('/',substr($textoPdf, 190, 10));
                
                $diaMesPeriodoRelatorio = $periodoRelatorio;

                $periodoRelatorio = $periodoRelatorio[1].'/'.$periodoRelatorio[2];
                $periodoRelatorioCompleto = substr($textoPdf, 190, 10);
                $periodoRelatorioCompletoFinal = substr($textoPdf, 207, 10);

                $codigoVeiculo = substr($textoPdf, 173, 7);

                $sqlIdVeiculo = 'SELECT id, carga_horaria FROM veiculos WHERE codigo = :codigo';
                $stmtIdVeiculo = $conexao->prepare($sqlIdVeiculo);
                $stmtIdVeiculo->bindValue(':codigo', $codigoVeiculo);
                $stmtIdVeiculo->execute();

                $idVeiculoInserir = 0;
                $cargaHoraria = '08:00';
                while ($linhaPlaca = $stmtIdVeiculo->fetch(PDO::FETCH_OBJ)) {
                    $idVeiculoInserir = $linhaPlaca->id;
                    $cargaHoraria = $linhaPlaca->carga_horaria;
                }

                if ($idVeiculoInserir) {
                    $sqlPeriodoCompleto = 'SELECT periodo_completo,
                                                  total_horas,
                                                  id
                                             FROM relatorios
                                            WHERE id_veiculo = :id_veiculo
                                              AND periodo = :periodo';
                    $stmtPeriodoCompleto = $conexao->prepare($sqlPeriodoCompleto);
                    $stmtPeriodoCompleto->bindValue(':periodo', $periodoRelatorio);
                    $stmtPeriodoCompleto->bindValue(':id_veiculo', $idVeiculoInserir);
                    $stmtPeriodoCompleto->execute();

                    $sqlDadosConfiguracao = 'SELECT hora_noturna_inicial,
                                                    hora_noturna_final                                                  
                                               FROM configuracoes';
                    $stmtDadosConfiguracao = $conexao->prepare($sqlDadosConfiguracao);                   
                    $stmtDadosConfiguracao->execute();

                    $horaNoturnaInicialValidar = 22;
                    $horaNoturnaInicialValidar = 05;
                    while ($linhaConfiguracao = $stmtDadosConfiguracao->fetch(PDO::FETCH_OBJ)) {
                        $horaNoturnaInicialValidar = substr($linhaConfiguracao->hora_noturna_inicial, 0, 2);
                        $horaNoturnaFinalValidar = substr($linhaConfiguracao->hora_noturna_final, 0, 2);
                    }

                    $periodoRelatorioCompletoAnterior = null;
                    $totalHorasAnterior = 0;
                    $idRelatorio = 0;
                    while ($linhaPeriodo = $stmtPeriodoCompleto->fetch(PDO::FETCH_OBJ)) {
                        $periodoRelatorioCompletoAnterior = $linhaPeriodo->periodo_completo;
                        $totalHorasAnterior = $linhaPeriodo->total_horas;
                        $idRelatorio = $linhaPeriodo->id;
                    }

                    preg_match_all("/DESLIGADA(.*)/", $textoPdf, $matches);

                    foreach ($matches[0] as $linha) {
                        $textoPdf = str_replace($linha, '', $textoPdf);
                    }

                    preg_match_all("/LIGADA(.*)/", $textoPdf, $matches);

                    $segundosDiferenca = [];
                    $segundosDiferencaNoturno = [];
                    $dadosIgnicaoLigada = $matches[0];
                    foreach($dadosIgnicaoLigada as $contador => $dados) {
                        $linha = trim(str_replace('LIGADA', '', $dados));

                        $dataInicial = substr($linha, 0, 10);
                        $dataInicialSemHora = $dataInicial;
                        $dataInicial = converteData($dataInicial);
                        $horaInicial = substr($linha, 11,8);

                        $dataFinal = substr($linha, 21,10);
                        $dataFinal = converteData($dataFinal);
                        $horaFinal = substr($linha, 32,8);

                        $diaMesDataInicial = explode('-', $dataInicial);

                        //if (($diaMesDataInicial[2] >= $diaMesPeriodoRelatorio[0] && $diaMesDataInicial[1] >= $diaMesPeriodoRelatorio[1])) {
                            if ($dataInicial != $dataFinal) {                             
                                // Se a linha for a primeira e o mês inicial for diferente do periodo do cabecalho
                                if ($contador == 0) {
                                    if ($diaMesDataInicial[2] != $diaMesPeriodoRelatorio[0] && $diaMesDataInicial[1] != $diaMesPeriodoRelatorio[1]) {
                                        $dataInicialDiferenteProximoDia = new \DateTime("$dataFinal $horaFinal");
                                        $dataFinalDiferenteProximoDia = new \DateTime("$dataFinal 00:00:00");

                                        if (!isset($segundosDiferenca[$dataInicialSemHora])) {
                                            $segundosDiferenca[converteData($dataFinal)] = retornaSegundosData($dataInicialDiferenteProximoDia, $dataFinalDiferenteProximoDia);
                                        } else {
                                            $segundosDiferenca[converteData($dataFinal)] += retornaSegundosData($dataInicialDiferenteProximoDia, $dataFinalDiferenteProximoDia);
                                        }
                                    }                    
                                } else {
                                    $horaInicialSemMinutos = (int) substr($horaInicial,0,2);
                                    $horaFinalSemMinutos = (int) substr($horaFinal,0,2);

                                    if ($horaInicialSemMinutos >= $horaNoturnaInicialValidar) {
                                        $dataInicialDiferenteDiaNoturno = new \DateTime("$dataInicial $horaInicial");
                                        $dataFinalDiferenteDiaNoturno = new \DateTime("$dataInicial 23:59:59");

                                        $horaFinalNoturna = $horaFinal;
                                        if ($horaFinalSemMinutos > $horaNoturnaFinalValidar) {
                                            $horaFinalNoturna = '05:00:00';
                                        }

                                        $dataInicialDiferenteProximoDiaNoturno = new \DateTime("$dataFinal $horaFinalNoturna");
                                        $dataFinalDiferenteProximoDiaNoturno = new \DateTime("$dataFinal 00:00:00");

                                        if (!isset($segundosDiferencaNoturno[$dataInicialSemHora])) {
                                            $segundosDiferencaNoturno[$dataInicialSemHora] = retornaSegundosData($dataInicialDiferenteDiaNoturno, $dataFinalDiferenteDiaNoturno);
                                            $segundosDiferencaNoturno[converteData($dataFinal)] = retornaSegundosData($dataInicialDiferenteProximoDiaNoturno, $dataFinalDiferenteProximoDiaNoturno);
                                        } else {
                                            $segundosDiferencaNoturno[$dataInicialSemHora] += retornaSegundosData($dataInicialDiferenteDiaNoturno, $dataFinalDiferenteDiaNoturno);
                                            $segundosDiferencaNoturno[converteData($dataFinal)] = retornaSegundosData($dataInicialDiferenteProximoDiaNoturno, $dataFinalDiferenteProximoDiaNoturno);
                                        }
                                    }

                                    $dataInicialDiferenteDia = new \DateTime("$dataInicial $horaInicial");
                                    $dataFinalDiferenteDia = new \DateTime("$dataInicial 23:59:59");

                                    $dataInicialDiferenteProximoDia = new \DateTime("$dataFinal $horaFinal");
                                    $dataFinalDiferenteProximoDia = new \DateTime("$dataFinal 00:00:00");

                                    if (!isset($segundosDiferenca[$dataInicialSemHora])) {
                                        $segundosDiferenca[$dataInicialSemHora] = retornaSegundosData($dataInicialDiferenteDia, $dataFinalDiferenteDia);
                                    } else {
                                        $segundosDiferenca[$dataInicialSemHora] += retornaSegundosData($dataInicialDiferenteDia, $dataFinalDiferenteDia);
                                    }

                                    if (!isset($segundosDiferenca[$dataFinal])) {
                                        $segundosDiferenca[converteData($dataFinal)] = retornaSegundosData($dataInicialDiferenteProximoDia, $dataFinalDiferenteProximoDia);
                                    } else {
                                        $segundosDiferenca[converteData($dataFinal)] += retornaSegundosData($dataInicialDiferenteProximoDia, $dataFinalDiferenteProximoDia);
                                    }
                                }
                            } else {      
                                $horaInicialSemMinutos = (int) substr($horaInicial, 0,2);

                                if ($horaInicialSemMinutos >= $horaNoturnaInicialValidar) {
                                    $dataInicialNoturno = new \DateTime("$dataInicial $horaInicial");
                                    $dataFinalNoturno = new \DateTime("$dataFinal $horaFinal");

                                    if (!isset($segundosDiferencaNoturno[$dataInicialSemHora])) {
                                        $segundosDiferencaNoturno[$dataInicialSemHora] = retornaSegundosData($dataInicialNoturno, $dataFinalNoturno);
                                    } else {
                                        $segundosDiferencaNoturno[$dataInicialSemHora] += retornaSegundosData($dataInicialNoturno, $dataFinalNoturno);
                                    }
                                }

                                $dataInicial = new \DateTime("$dataInicial $horaInicial");
                                $dataFinal   = new \DateTime("$dataFinal $horaFinal");

                                if (!isset($segundosDiferenca[$dataInicialSemHora])) {
                                    $segundosDiferenca[$dataInicialSemHora] = retornaSegundosData($dataInicial, $dataFinal);
                                } else {
                                    $segundosDiferenca[$dataInicialSemHora] += retornaSegundosData($dataInicial, $dataFinal);
                                }
                            }
                        //}
                    }

                    if (count($segundosDiferenca) > 0) {
                        if (!$periodoRelatorioCompletoAnterior || ($periodoRelatorioCompletoAnterior == $periodoRelatorioCompleto)) {
                            $sqlDeletaRelatorio = 'DELETE FROM relatorios WHERE periodo = :periodo AND id_veiculo = :id_veiculo';
                            $stmtDeletaRelatorio = $conexao->prepare($sqlDeletaRelatorio);
                            $stmtDeletaRelatorio->bindValue(':periodo', $periodoRelatorio);
                            $stmtDeletaRelatorio->bindValue(':id_veiculo', $idVeiculoInserir);
                            $stmtDeletaRelatorio->execute();
                        }

                        foreach ($segundosDiferenca as $dataTrabalhada => $totalHorasSegundos) {                       
                            $dataDia = new DateTime('00:00:00');
                            $totalDia = new DateTime('00:00:00');
                            $intervaloDataSomatorioSegundos = new DateInterval('PT'.$totalHorasSegundos.'S');

                            $totalDia->add($intervaloDataSomatorioSegundos);
                            $intervaloTotalDia = $dataDia->diff($totalDia);

                            /**
                             * Total de horas trabalhadas no dia
                             */
                            $totalHorasTrabalhadasDia = $intervaloTotalDia->format('%H:%I:%S');

                            $totalHorasTrabalhadasDiaNoturno = '00:00:00';
                            if (isset($segundosDiferencaNoturno[$dataTrabalhada])) {
                                $totalHorasSegundosNoturno = $segundosDiferencaNoturno[$dataTrabalhada];
                                $dataDiaNoturno = new DateTime('00:00:00');
                                $totalDiaNoturno = new DateTime('00:00:00');
                                $intervaloDataSomatorioSegundosNoturno = new DateInterval('PT'.$totalHorasSegundosNoturno.'S');

                                $totalDiaNoturno->add($intervaloDataSomatorioSegundosNoturno);
                                $intervaloTotalDiaNoturno = $dataDiaNoturno->diff($totalDiaNoturno);

                                /**
                                 * Total de horas trabalhadas noturno no dia
                                 */
                                $totalHorasTrabalhadasDiaNoturno = $intervaloTotalDiaNoturno->format('%H:%I:%S');                            
                            }

                            $totalHorasExtrasDia50 = 0;
                            $totalHorasExtrasDia100 = 0;

                            $dataTrabalhadaTratada = new \DateTime(converteData($dataTrabalhada));

                            /**
                             * 1 - segunda
                             * 7 - domingo
                             * Feriados e domingo - 100% extra
                             */
                            $diaSemana = $dataTrabalhadaTratada->format('N');
                            $feriado = isFeriado($dataTrabalhadaTratada->format('d/m/Y'));

                            if ($feriado || $diaSemana == 7) {
                                $totalHorasExtrasDia100 = $totalHorasTrabalhadasDia;
                                $totalHorasTrabalhadasDia = '00:00:00';
                            } else {
                                if ($intervaloTotalDia->format('%H') >= 8 && $diaSemana != 6) {
                                    $dataInicialDia = new DateTime(converteData($dataTrabalhada).' '.$cargaHoraria.':00');
                                    $dataFinalDia = new DateTime(converteData($dataTrabalhada).' '.$totalHorasTrabalhadasDia);

                                    $totalHorasExtrasDia50 = $dataInicialDia->diff($dataFinalDia)->format('%H:%I:%S');

                                    $totalHorasTrabalhadasDia = $cargaHoraria.':00';
                                }

                                /**
                                 * Se for sábado, caso passar de 4 horas extras entra nos 50%
                                 */
                                if ($diaSemana == 6 && $intervaloTotalDia->format('%H') >= 4) {
                                    $dataInicialDia = new DateTime(converteData($dataTrabalhada).' 04:00:00');
                                    $dataFinalDia = new DateTime(converteData($dataTrabalhada).' '.$totalHorasTrabalhadasDia);

                                    $totalHorasExtrasDia50 = $dataInicialDia->diff($dataFinalDia)->format('%H:%I:%S');

                                    $totalHorasTrabalhadasDia = '04:00:00';
                                }
                            }

                                $periodoInserir = explode('/', $dataTrabalhada);
                                $periodoInserir = $periodoInserir[1].'/'.$periodoInserir[2];

                                $sqlVerificaPossuiDiaPeriodo = 'SELECT total_horas,
                                                                    total_horas_extra_50,
                                                                    total_horas_extra_100,
                                                                    total_horas_noturnas,
                                                                    data
                                                                FROM relatorios
                                                                WHERE id_veiculo = :id_veiculo
                                                                AND periodo = :periodo
                                                                AND data = :data';
                                $stmtVerificaPossuiDiaPeriodo = $conexao->prepare($sqlVerificaPossuiDiaPeriodo);
                                $stmtVerificaPossuiDiaPeriodo->bindValue(':data', converteData($dataTrabalhada));
                                $stmtVerificaPossuiDiaPeriodo->bindValue(':id_veiculo', $idVeiculoInserir);
                                $stmtVerificaPossuiDiaPeriodo->bindValue(':periodo', $periodoInserir);
                                $stmtVerificaPossuiDiaPeriodo->execute();

                                while ($linhaVerifica = $stmtVerificaPossuiDiaPeriodo->fetch(PDO::FETCH_OBJ)) {
                                    $totalHorasAtualizarExtra = 0;
                                    $totalHorasAtualizar = $linhaVerifica->total_horas;
                                    $totalHorasAtualizarExtra50 = $linhaVerifica->total_horas_extra_50;
                                    $totalHorasAtualizarExtra100 = $linhaVerifica->total_horas_extra_100;
                                    $totalHorasAtualizarNoturnas = $linhaVerifica->total_horas_noturnas;

                                    $dataHoraNormalCompararBanco = "$linhaVerifica->data $linhaVerifica->total_horas";
                                    $dataHoraNormalCompararArquivo = "$linhaVerifica->data $totalHorasTrabalhadasDia";
                                    
                                    $dataHoraExtraCompararBanco = "$linhaVerifica->data $linhaVerifica->total_horas_extra_50";
                                    $dataHoraExtraCompararArquivo = "$linhaVerifica->data $totalHorasExtrasDia50";

                                    $dataHoraExtra100CompararBanco = "$linhaVerifica->data $linhaVerifica->total_horas_extra_100";
                                    $dataHoraExtra100CompararArquivo = "$linhaVerifica->data $totalHorasExtrasDia100"; 

                                    $dataHoraNoturnaCompararBanco = "$linhaVerifica->data $linhaVerifica->total_horas_noturnas";
                                    $dataHoraNoturnaCompararArquivo = "$linhaVerifica->data $totalHorasTrabalhadasDiaNoturno";

                                    if (empty($totalHorasExtrasDia50) 
                                    && $dataHoraNormalCompararBanco != $dataHoraNormalCompararArquivo 
                                    && $dataHoraNormalCompararArquivo > 0) {                                                                       
                                        //Normal
                                        $dataInicialVerifica = $linhaVerifica->data;
                                        $dataInicialVerificaTratadaDia = new \DateTime("$dataInicialVerifica 00:00:00");
                                        $dataInicialVerificaTratadaTotal = new \DateTime("$dataInicialVerifica 00:00:00");

                                        $dataFinalVerificaTratadaDia = new \DateTime("$dataInicialVerifica $totalHorasTrabalhadasDia");
                                        $dataFinalVerificaTratadaTotal = new \DateTime("$dataInicialVerifica $linhaVerifica->total_horas");

                                        $segundosVerifica = retornaSegundosData($dataInicialVerificaTratadaDia, $dataFinalVerificaTratadaDia);
                                        $segundosVerifica += retornaSegundosData($dataInicialVerificaTratadaTotal, $dataFinalVerificaTratadaTotal);

                                        $dataNormalVerifica = new DateTime('00:00:00');
                                        $totalNormalVerifica = new DateTime('00:00:00');
                                        $intervaloDataSomatorioSegundosVerifica = new DateInterval('PT'.$segundosVerifica.'S');

                                        $totalNormalVerifica->add($intervaloDataSomatorioSegundosVerifica);
                                        $intervaloTotalVerifica = $dataNormalVerifica->diff($totalNormalVerifica);

                                        if ($intervaloTotalVerifica->format('%H') > 8
                                        || ($intervaloTotalVerifica->format('%H') == '08' && $intervaloTotalVerifica->format('%I') <> '00')
                                        || ($intervaloTotalVerifica->format('%H') == '08' && $intervaloTotalVerifica->format('%I') == '00') && $intervaloTotalVerifica->format('%S') <> '00'
                                        ) {

                                            $horasUltrapassam = $intervaloTotalVerifica->format("%H:%I:%S");
                                            $data1 = new DateTime("$dataInicialVerifica $horasUltrapassam");
                                            $data2 = new DateTime("$dataInicialVerifica 08:00:00");

                                            $diferencaData = $data1->diff($data2);
                                            $diferencaDataExtra = $diferencaData->format("%H:%I:%S");

                                            if (empty($linhaVerifica->total_horas_extra_50)) {
                                                $linhaVerifica->total_horas_extra_50 = '00:00:00';
                                            }

                                            $dataFinalVerificaTratadaDia = new \DateTime("$dataInicialVerifica $diferencaDataExtra");
                                            $dataFinalVerificaTratadaTotalExtra = new \DateTime("$dataInicialVerifica $linhaVerifica->total_horas_extra_50");

                                            $segundosVerifica = retornaSegundosData($dataInicialVerificaTratadaDia, $dataFinalVerificaTratadaDia);
                                            $segundosVerifica += retornaSegundosData($dataInicialVerificaTratadaTotal, $dataFinalVerificaTratadaTotalExtra);

                                            $dataNormalVerifica = new DateTime('00:00:00');
                                            $totalNormalVerifica = new DateTime('00:00:00');
                                            $intervaloDataSomatorioSegundosVerifica = new DateInterval('PT'.$segundosVerifica.'S');

                                            $totalNormalVerifica->add($intervaloDataSomatorioSegundosVerifica);
                                            $intervaloTotalVerificaExtra = $dataNormalVerifica->diff($totalNormalVerifica);

                                            $totalHorasAtualizar = '08:00:00';
                                            $totalHorasAtualizarExtra50 = $intervaloTotalVerificaExtra->format("%H:%I:%S");
                                        } else {
                                            $totalHorasAtualizar = $intervaloTotalVerifica->format("%H:%I:%S");
                                        }
                                    }

                                    if (!empty($totalHorasExtrasDia50) 
                                    && $dataHoraExtraCompararBanco != $dataHoraExtraCompararArquivo 
                                    && $dataHoraExtraCompararArquivo > 0) {
                                        
                                        //Extra 50%
                                        $dataInicialVerifica = $linhaVerifica->data;
                                        $dataInicialVerificaTratadaDia = new \DateTime("$dataInicialVerifica 00:00:00");
                                        $dataInicialVerificaTratadaTotal = new \DateTime("$dataInicialVerifica 00:00:00");

                                        if (empty($linhaVerifica->total_horas_extra_50)) {
                                            $linhaVerifica->total_horas_extra_50 = '00:00:00';
                                        }

                                        $dataFinalVerificaTratadaDia = new \DateTime("$dataInicialVerifica $totalHorasExtrasDia50");
                                        $dataFinalVerificaTratadaTotal = new \DateTime("$dataInicialVerifica $linhaVerifica->total_horas_extra_50");

                                        $segundosVerifica = retornaSegundosData($dataInicialVerificaTratadaDia, $dataFinalVerificaTratadaDia);
                                        $segundosVerifica += retornaSegundosData($dataInicialVerificaTratadaTotal, $dataFinalVerificaTratadaTotal);

                                        if (!empty($totalHorasAtualizarExtra)) {
                                            $dataFinalVerificaTratadaTotalExtra = new \DateTime("$dataInicialVerifica $totalHorasAtualizarExtra");
                                            $segundosVerifica += retornaSegundosData($dataInicialVerificaTratadaTotal, $dataFinalVerificaTratadaTotalExtra);
                                        }

                                        $dataNormalVerifica = new DateTime('00:00:00');
                                        $totalNormalVerifica = new DateTime('00:00:00');
                                        $intervaloDataSomatorioSegundosVerifica = new DateInterval('PT'.$segundosVerifica.'S');

                                        $totalNormalVerifica->add($intervaloDataSomatorioSegundosVerifica);
                                        $intervaloTotalVerificaExtra = $dataNormalVerifica->diff($totalNormalVerifica);
                                        $totalHorasAtualizarExtra50 = $intervaloTotalVerificaExtra->format("%H:%I:%S");
                                    }

                                    if (!empty($totalHorasExtrasDia100) 
                                        && $dataHoraExtra100CompararBanco != $dataHoraExtra100CompararArquivo 
                                        && $dataHoraExtra100CompararArquivo > 0) {
                                        //Extra 100%
                                        $dataInicialVerifica = $linhaVerifica->data;
                                        $dataInicialVerificaTratadaDia = new \DateTime("$dataInicialVerifica 00:00:00");
                                        $dataInicialVerificaTratadaTotal = new \DateTime("$dataInicialVerifica 00:00:00");

                                        if (empty($linhaVerifica->total_horas_extra_100)) {
                                            $linhaVerifica->total_horas_extra_100 = '00:00:00';
                                        }

                                        $dataFinalVerificaTratadaDia = new \DateTime("$dataInicialVerifica $totalHorasTrabalhadasDia");
                                        $dataFinalVerificaTratadaTotal = new \DateTime("$dataInicialVerifica $linhaVerifica->total_horas_extra_100");

                                        $segundosVerifica = retornaSegundosData($dataInicialVerificaTratadaDia, $dataFinalVerificaTratadaDia);
                                        $segundosVerifica += retornaSegundosData($dataInicialVerificaTratadaTotal, $dataFinalVerificaTratadaTotal);

                                        $dataNormalVerifica = new DateTime('00:00:00');
                                        $totalNormalVerifica = new DateTime('00:00:00');
                                        $intervaloDataSomatorioSegundosVerifica = new DateInterval('PT'.$segundosVerifica.'S');

                                        $totalNormalVerifica->add($intervaloDataSomatorioSegundosVerifica);
                                        $intervaloTotalVerifica = $dataNormalVerifica->diff($totalNormalVerifica);
                                        $totalHorasAtualizarExtra100 = $intervaloTotalVerifica->format("%H:%I:%S");
                                    }

                                    if (!empty($totalHorasTrabalhadasDiaNoturno) 
                                        && $dataHoraNoturnaCompararBanco != $dataHoraNoturnaCompararArquivo
                                        && $dataHoraNoturnaCompararArquivo > 0) {
                                    
                                        $dataInicialVerifica = $linhaVerifica->data;
                                        $dataInicialVerificaTratadaDia = new \DateTime("$dataInicialVerifica 00:00:00");
                                        $dataInicialVerificaTratadaTotal = new \DateTime("$dataInicialVerifica 00:00:00");

                                        if (empty($linhaVerifica->total_horas_noturna)) {
                                            $linhaVerifica->total_horas_noturna = '00:00:00';
                                        }

                                        $dataFinalVerificaTratadaDia = new \DateTime("$dataInicialVerifica $totalHorasTrabalhadasDiaNoturno");
                                        $dataFinalVerificaTratadaTotal = new \DateTime("$dataInicialVerifica $linhaVerifica->total_horas_noturna");

                                        $segundosVerifica = retornaSegundosData($dataInicialVerificaTratadaDia, $dataFinalVerificaTratadaDia);
                                        $segundosVerifica += retornaSegundosData($dataInicialVerificaTratadaTotal, $dataFinalVerificaTratadaTotal);

                                        $dataNormalVerifica = new DateTime('00:00:00');
                                        $totalNormalVerifica = new DateTime('00:00:00');
                                        $intervaloDataSomatorioSegundosVerifica = new DateInterval('PT'.$segundosVerifica.'S');

                                        $totalNormalVerifica->add($intervaloDataSomatorioSegundosVerifica);
                                        $intervaloTotalVerifica = $dataNormalVerifica->diff($totalNormalVerifica);
                                        $totalHorasAtualizarNoturnas = $intervaloTotalVerifica->format("%H:%I:%S");
                                    }

                                    $sqlAtualizar = 'UPDATE relatorios
                                                        SET total_horas = :total_horas,
                                                            total_horas_extra_50 = :total_horas_extra_50,
                                                            total_horas_extra_100 = :total_horas_extra_100,
                                                            total_horas_noturnas = :total_horas_noturnas
                                                    WHERE id_veiculo = :id_veiculo
                                                        AND periodo = :periodo
                                                        AND data = :data';
                                    $stmtAtualizar = $conexao->prepare($sqlAtualizar);
                                    $stmtAtualizar->bindValue(':data', converteData($dataTrabalhada));
                                    $stmtAtualizar->bindValue(':id_veiculo', $idVeiculoInserir);
                                    $stmtAtualizar->bindValue(':periodo', $periodoInserir);
                                    $stmtAtualizar->bindValue(':total_horas', $totalHorasAtualizar);
                                    $stmtAtualizar->bindValue(':total_horas_extra_50', $totalHorasAtualizarExtra50);
                                    $stmtAtualizar->bindValue(':total_horas_extra_100', $totalHorasAtualizarExtra100);
                                    $stmtAtualizar->bindValue(':total_horas_noturnas', $totalHorasAtualizarNoturnas);
                                    $stmtAtualizar->execute();
                                }

                                if ($stmtVerificaPossuiDiaPeriodo->rowCount() <= 0) {
                                    $sqlInsereRelatorio = 'INSERT INTO relatorios (periodo,
                                                                                    id_veiculo,
                                                                                    total_horas,
                                                                                    periodo_completo,
                                                                                    data,
                                                                                    total_horas_extra_50,
                                                                                    total_horas_extra_100,
                                                                                    periodo_completo_final,
                                                                                    total_horas_noturnas)
                                                                            VALUES (:periodo,
                                                                                    :id_veiculo,
                                                                                    :total_horas,
                                                                                    :periodo_completo,
                                                                                    :data,
                                                                                    :total_horas_extra_50,
                                                                                    :total_horas_extra_100,
                                                                                    :periodo_completo_final,
                                                                                    :total_horas_noturnas)';
                                    $stmtInsereRelatorio = $conexao->prepare($sqlInsereRelatorio);
                                    $stmtInsereRelatorio->bindValue(':periodo', $periodoInserir);
                                    $stmtInsereRelatorio->bindValue(':id_veiculo', $idVeiculoInserir);
                                    $stmtInsereRelatorio->bindValue(':periodo_completo', converteData($periodoRelatorioCompleto));
                                    $stmtInsereRelatorio->bindValue(':periodo_completo_final', converteData($periodoRelatorioCompletoFinal));
                                    $stmtInsereRelatorio->bindValue(':data', converteData($dataTrabalhada));
                                    $stmtInsereRelatorio->bindValue(':total_horas', $totalHorasTrabalhadasDia);
                                    $stmtInsereRelatorio->bindValue(':total_horas_extra_50', $totalHorasExtrasDia50);
                                    $stmtInsereRelatorio->bindValue(':total_horas_extra_100', $totalHorasExtrasDia100);
                                    $stmtInsereRelatorio->bindValue(':total_horas_noturnas', $totalHorasTrabalhadasDiaNoturno);
                                    $stmtInsereRelatorio->execute();
                                }
                        }
                    }
                }
            } else {              
                $document = new DOMDocument();
                @$document->loadHTML(file_get_contents('1.'.$extensaoArquivo));
                $finder = new DOMXPath($document);   

                $idVeiculoInserirTempoEspera = null;

                $elementosMotorista = $finder->query("//div[contains(@class, 'cfn10')]");
                if($elementosMotorista->length > 0) {
                    foreach($elementosMotorista as $linha => $elemento) {
                        if ($linha > 0) {  
                            $nomeMotorista = trim($elemento->nodeValue);        
                            
                            $sqlIdVeiculo = 'SELECT id FROM veiculos WHERE motorista = :motorista';
                            $stmtIdVeiculo = $conexao->prepare($sqlIdVeiculo);
                            $stmtIdVeiculo->bindValue(':motorista', $nomeMotorista);
                            $stmtIdVeiculo->execute();
                                                        
                            while ($linhaMotorista = $stmtIdVeiculo->fetch(PDO::FETCH_OBJ)) {
                                $idVeiculoInserirTempoEspera = $linhaMotorista->id;
                            }
                        }
                    }
                }

                $elementosData = $finder->query("//div[contains(@class, 'cfn4')]");
                if ($elementosData->length > 0) {
                    foreach ($elementosData as $elemento) {
                        $periodo = substr($elemento->nodeValue, 6, 7);                              
                    }
                }
                
                $excluiuHoraEspera = false;
                // Se encontrou o motorista pelo nome
                if ($idVeiculoInserirTempoEspera) {                   
                    $elementosDiv = $document->getElementsByTagName('div');
                    foreach ($elementosDiv as $elemento) {
                        $divsPonto = explode('<br />', nl2br($elemento->nodeValue));

                        foreach($divsPonto as $linha => $valorLinha) {
                            if (substr_count($valorLinha, '/') == 2 && substr_count($valorLinha, '-') == 1) {
                                $indiceHoraEspera = $linha + 10;

                                if (isset($divsPonto[$indiceHoraEspera]) && !empty(trim($divsPonto[$indiceHoraEspera]))) {
                                    $horaEspera = trim($divsPonto[$indiceHoraEspera]).':00';

                                    $dadosDataDiaSemana = explode('-', trim($valorLinha));
                                    $diaSemanaPonto = $dadosDataDiaSemana[1];  
                                    
                                    $dataCartaoPonto = explode('/', $dadosDataDiaSemana[0]);     
                                    
                                    $dataCartaoPontoAno = (int) $dataCartaoPonto[2] + 2000;
                                    $dataCartaoPontoMes = $dataCartaoPonto[1];
                                    $dataCartaoPontoDia = $dataCartaoPonto[0];

                                    $dataCartaoPonto = $dataCartaoPontoAno.'-'.$dataCartaoPontoMes.'-'.$dataCartaoPontoDia;

                                    if (!$excluiuHoraEspera) {
                                        $sqlExcluiTempoEspera = "DELETE FROM tempo_espera WHERE id_veiculo = :id_veiculo AND periodo = :periodo";
                                        $stmtExcluiTempoEspera = $conexao->prepare($sqlExcluiTempoEspera);
                                        $stmtExcluiTempoEspera->bindValue(':id_veiculo', $idVeiculoInserirTempoEspera);
                                        $stmtExcluiTempoEspera->bindValue(':periodo', $periodo);
                                        $stmtExcluiTempoEspera->execute();
                                        $excluiuHoraEspera = true;
                                    }
                                                                
                                    $sqlInsereTempoEspera = 'INSERT INTO tempo_espera (id_veiculo, data, total_horas, periodo) VALUES (:id_veiculo, :data, :total_horas, :periodo)';
                                    $stmtInsereTempoEspera = $conexao->prepare($sqlInsereTempoEspera);
                                    $stmtInsereTempoEspera->bindValue(':id_veiculo', $idVeiculoInserirTempoEspera);
                                    $stmtInsereTempoEspera->bindValue(':data', $dataCartaoPonto);
                                    $stmtInsereTempoEspera->bindValue(':periodo', $periodo);
                                    $stmtInsereTempoEspera->bindValue(':total_horas', $horaEspera);
                                    $stmtInsereTempoEspera->execute();
                                }
                            }
                        }
                    }                                    
                }
            }
        }
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><i class="fa fa-list fa-fw"></i> Relatórios</h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php
        if (!$idVeiculoInserir && $textoPdf) {
            echo '<div class="text-danger bg-danger" style="margin-bottom:10px;padding:5px">Não existe veículo cadastrado para esse código: '.$codigoVeiculo.'</div>';
        } else if ($textoPdf || $idVeiculoInserirTempoEspera) {
            echo '<script>alert("Relatório gerado com sucesso");</script>';
        }
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                Gerar Relatório
            </div>
            <div class="panel-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Selecione o arquivo (.pdf)</label>
                                <input type="file" name="arquivo[]" multiple="multiple">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr/>
                            <button type="submit" class="btn btn-default">Gerar Relatório</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Listagem de Relatórios
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
                                                <th>Período</th>
                                                <th>Veículo</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlRelatorio = 'SELECT MIN(relatorios.id) as id,
                                                                    relatorios.periodo,
                                                                    veiculos.codigo,
                                                                    veiculos.motorista,
                                                                    veiculos.id as id_veiculo
                                                               FROM relatorios
                                                               JOIN veiculos
                                                                 ON veiculos.id = relatorios.id_veiculo
                                                           GROUP BY periodo,codigo,motorista, id_veiculo
                                                           ORDER BY relatorios.periodo';
                                            $stmtRelatorio = $conexao->prepare($sqlRelatorio);
                                            $stmtRelatorio->execute();

                                            while ($linhaRelatorio = $stmtRelatorio->fetch(PDO::FETCH_OBJ)) {
                                                $periodoTratado = explode('/',$linhaRelatorio->periodo);
                                                $periodoTratado = implode('*', $periodoTratado);
                                            ?>
                                            <tr>
                                                <td><?=$linhaRelatorio->periodo?></td>
                                                <td><?=$linhaRelatorio->codigo?> (<?=$linhaRelatorio->motorista?>)</td>
                                                <td>
                                                    <button type="button" data-toggle="modal" rel="<?=$linhaRelatorio->periodo.'*'.$linhaRelatorio->codigo?>" data-target="#myModal" class="btn btn-primary btn-xs btn-visualizar">Visualizar</button>
                                                    <a class="btn btn-primary btn-xs color-red excluir" href="<?=$base_url.'relatorios/excluir/'.$periodoTratado.'*'.$linhaRelatorio->id_veiculo?>">Excluir</a>
                                                </td>
                                            </tr>
                                            <?php
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


<div class="modal fade printable autoprint" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 870px;width: auto;" role="document">
        <div class="printable">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Relatório do Veículo</h3>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="imprimir">Imprimir</button>
                    <button type="button" class="btn btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>
