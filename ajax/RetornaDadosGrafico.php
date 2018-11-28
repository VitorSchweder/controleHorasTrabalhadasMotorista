<?php
include_once("../engine/connect.php");
$retorno = [];

$conexao = conectar();

$sql = "SELECT periodo, 
               id_veiculo, 
               time_format(SEC_TO_TIME(SUM(TIME_TO_SEC(total_horas))),'%H:%i:%s') AS total_horas 
          FROM relatorios 
      GROUP BY periodo, 
               id_veiculo";
$stmt = $conexao->prepare($sql);
$stmt->execute();

while ($linha = $stmt->fetch(PDO::FETCH_OBJ)) {
    $retorno[] = [
        'periodo' => $linha->periodo, 
        'carro'.$linha->id_veiculo => $linha->total_horas
    ];    
}

echo json_encode($retorno);
