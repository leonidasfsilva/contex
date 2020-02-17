<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>CONTEX - Sistema de Gestão</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="description" content="Contex Sistema de Gestão">
    <meta name="author" content="Leônidas Ferreira">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/logo_contex.png" type="image/x-icon"/>
    <link href="<?php echo base_url(); ?>assets/css/styles.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<!--    <script src="--><?php //echo base_url(); ?><!--assets/js/jquery-3.4.1.js"></script>-->
<!--    <script src="--><?php //echo base_url(); ?><!--assets/js/application.js"></script>-->
    <?=$css?>
</head>

<body style="background-color: transparent">
<div class="container-fluid">
    <div class="row">
        <?=$topo?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 style="text-align: center">Relatório de Clientes</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table id="example" class="table table-striped table-bordered table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row" style="padding: 15px;">
                    <th style="padding: 10px;">Nome</th>
                    <th style="padding: 10px;">Documento</th>
                    <th style="padding: 10px;">Telefone</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Cadastro</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($clientes as $c) : ?>
                    <?php $dataCadastro = date('d/m/Y', strtotime($c->data_cadastro)) ?>
                    <tr>
                        <td style="padding: 5px;"><?= $c->nome ?></td>
                        <td style="padding: 5px;"><?= $c->cpf ?></td>
                        <td style="padding: 5px;"><?= $c->telefone ?></td>
                        <td style="padding: 5px;"><?= $c->email ?></td>
                        <td style="padding: 5px;"><?= $dataCadastro ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <h5 style="text-align: right">Data do Relatório: <?php echo date('d/m/Y'); ?></h5>
    </div>
</div>
</div>


</body>
</html>







