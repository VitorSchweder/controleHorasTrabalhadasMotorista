<?php
if (!empty($_SESSION['id'])) {
    header('Location: ./home');
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title align-center">Relatório de Rastreamento de Frotas</h3>
                </div>
                <div class="align-center">
                    <img src="img/logo.png" class="logo">
                </div>
                <div class="panel-body">
                    <form action="includes/processaLogin.php" method="POST">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" name="username" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="password" type="password" value="">
                            </div>
                            <input type="submit" name="submit" value="Entrar" class="btn btn-lg btn-success btn-block" />
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<hr/>
