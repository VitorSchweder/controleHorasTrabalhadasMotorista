<?php
ob_start();
session_start();
include_once("engine/connect.php");
include_once("engine/funcoes.php");
include_once("engine/query.php");
include_once("engine/config.php");
include_once("vendor/autoload.php");

$conexao = conectar();
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Madeico - Relatórios de Rastreamento de Frota</title>
        <base href="<?=$base_url?>" />
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link href="vendor/morrisjs/morris.css" rel="stylesheet">
        <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="non-printable">
        <?php
        if($atual == "login"){
            include_once("pages/login.php");
        }
        if($atual != "login"){
            require("includes/verificaLogin.php");
        ?>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">Madeico - Relatório de Rastreamento de Frotas</a>
                </div>
                <?php include_once("includes/topNav.php"); ?>
                <?php include_once("includes/leftNav.php"); ?>
            </nav>
            <div id="page-wrapper">
                <?php include_once("{$pasta}/{$pagina}.php"); ?>
            </div>
        </div>
        <?php
        }
        ?>
        <!-- jQuery -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <!-- Hour Mask JavaScript -->
        <script type="text/javascript" src="https://pastebin.com/raw/QkBYGVub"></script>
        <script type="text/javascript" src="https://pastebin.com/raw/neg3Zijg" ></script>
        <script type="text/javascript" src="https://pastebin.com/raw/10z8dxLQ"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="vendor/metisMenu/metisMenu.min.js"></script>
        <!-- Morris Charts JavaScript -->
        <script src="vendor/raphael/raphael.min.js"></script>
        <script src="vendor/morrisjs/morris.min.js?v=<?=time()?>"></script>
        <script src="data/morris-data.js?v=<?=time()?>"></script>
        <!-- Custom Theme JavaScript -->
        <script src="js/functions.js?v=<?=time()?>"></script>
    </body>
</html>
<?php
ob_end_flush();
?>
