<?php
function limpaUrl($str) {
    $str = strtolower(utf8_decode($str));
    $i = 1;
    $str = strtr($str, utf8_decode('àáâãäåæçèéêëìíîïñòóôõöøùúûýýÿ'), 'aaaaaaaceeeeiiiinoooooouuuyyy');
    $str = preg_replace("/([^a-z0-9])/", '-', utf8_encode($str));
    while ($i > 0)
        $str = str_replace('--', '-', $str, $i);
    if (substr($str, -1) == '-')
        $str = substr($str, 0, -1);
    return $str;
}
function mostraTitulo($str) {
    $str =  ucwords(str_replace("-", " ", $str));
    return $str;
}
function exibeImagem($id, $pasta, $tabela){
    $sql = mysql_query("SELECT * FROM ". $tabela ." WHERE id = '". $id ."'");
    $dados = mysql_fetch_array($sql);
    if (empty($dados['img'])){
        $valor = './images/sem-imagem.jpg';
    }else{
        $valor = "./images/".$pasta."/".$id."/".$dados['img'];
    }
    return $valor;
}
function resumeTexto($texto,$inicio,$tamanho) {
    $resumo = substr($texto, $inicio, $tamanho);
    if (strlen($texto) > strlen($resumo)) {
        $complemento = '...';
        return substr($resumo,0,strrpos($resumo, " ")) . $complemento;
    } else {
        return $resumo;
    }
}
function mostraData($data) {
    if ($data != '') {
        return (substr($data, 8, 2) . '/' . substr($data, 5, 2) . '/' . substr($data, 0, 4));
    } else {
        return '';
    }
}

function gravaData ($data) {
    if ($data != '') {
        return (substr($data,6,4).'/'.substr($data,3,2).'/'.substr($data,0,2));
    }
    else { return ''; }
}

function primeiroNome($str){
    $nome = explode(" ",$str);
    $primeiro_nome = $nome[0];
    unset($nome[0]);
    $resto = implode(" ", $nome);
    return $primeiro_nome;
}

function converteData($data) {
    if (strpos($data, '/') !== false) {
        $data = explode('/', $data);
        $data = $data[2].'-'.$data[1].'-'.$data[0];
    } else {        
        $data = explode('-', $data);
        $data = $data[2].'/'.$data[1].'/'.$data[0];        
    }

    return $data;
}

function isFeriado($data) {      
    $dataTratada = explode('/', $data);
    $ano = $dataTratada[2];    
   
    $pascoa = easter_date($ano); 
    $diaPascoa = date('j', $pascoa);
    $mesPascoa = date('n', $pascoa);
    $anoPascoa = date('Y', $pascoa);

    $feriados = [
        date('d/m/Y', mktime(0, 0, 0, 1,  1, $ano)), // Confraternização Universal - Lei nº 662, de 06/04/49
        date('d/m/Y', mktime(0, 0, 0, 4,  21, $ano)), // Tiradentes - Lei nº 662, de 06/04/49
        date('d/m/Y', mktime(0, 0, 0, 5,  1, $ano)), // Dia do Trabalhador - Lei nº 662, de 06/04/49
        date('d/m/Y', mktime(0, 0, 0, 9,  7, $ano)), // Dia da Independência - Lei nº 662, de 06/04/49
        date('d/m/Y', mktime(0, 0, 0, 10,  12, $ano)), // N. S. Aparecida - Lei nº 6802, de 30/06/80
        date('d/m/Y', mktime(0, 0, 0, 11,  2, $ano)), // Todos os santos - Lei nº 662, de 06/04/49
        date('d/m/Y', mktime(0, 0, 0, 11, 15, $ano)), // Proclamação da republica - Lei nº 662, de 06/04/49
        date('d/m/Y', mktime(0, 0, 0, 12, 25, $ano)), // Natal - Lei nº 662, de 06/04/49

        date('d/m/Y', mktime(0, 0, 0, $mesPascoa, $diaPascoa - 48,  $anoPascoa)),//2ºferia Carnaval
        date('d/m/Y', mktime(0, 0, 0, $mesPascoa, $diaPascoa - 47,  $anoPascoa)),//3ºferia Carnaval	
        date('d/m/Y', mktime(0, 0, 0, $mesPascoa, $diaPascoa - 2 ,  $anoPascoa)),//6ºfeira Santa  
        date('d/m/Y', mktime(0, 0, 0, $mesPascoa, $diaPascoa     ,  $anoPascoa)),//Pascoa
        date('d/m/Y', mktime(0, 0, 0, $mesPascoa, $diaPascoa + 60,  $anoPascoa)),//Corpus Cirist
    ];

    return in_array($data, $feriados);
}

function retornaSegundosData($dataInicial, $dataFinal) {        
    $intervaloData = $dataInicial->diff($dataFinal);

    $segundosDiferenca = $intervaloData->s;
    $segundosDiferenca += ($intervaloData->i * 60);
    $segundosDiferenca += ($intervaloData->h * 3600); 
    
    return $segundosDiferenca;
}

function retornaDiaSemana($numeroDia) {
    $diasSemana = [
        1 => 'Seg',
        2 => 'Ter',
        3 => 'Qua',
        4 => 'Qui',
        5 => 'Sex',
        6 => 'Sab',
        7 => 'Dom'
    ];

    return $diasSemana[$numeroDia];
}