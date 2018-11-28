<?php
$atual      = (isset($_GET['in'])) ? $_GET['in'] : 'home';
$permissao  = array('home', 'veiculos', 'horarios', 'relatorios', 'login', 'logout', 'configuracoes');
$pasta      = 'pages';

if (substr_count($atual, '/') > 0) {
    $atual  = explode('/', $atual);
    $pagina = (file_exists("{$pasta}/".$atual[0].'.php') && in_array($atual[0], $permissao)) ? $atual[0] : 'home';
    $acao  = filter_var($atual[1], FILTER_SANITIZE_STRING);
    $id    = $atual[2];
} else {
    $pagina = (file_exists("{$pasta}/".$atual.'.php') && in_array($atual, $permissao)) ? $atual : 'home';
    $id = 0;
}
