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
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.min.js"></script>
</head>

<?php //require_once APPPATH . 'views/includes/css.php'; ?>
<?php //require_once APPPATH . 'views/includes/js.php'; ?>
<?php //$this->load->view('includes/css'); ?>
<?php //$this->load->view('includes/js'); ?>

<body style="background-color: transparent">
<div class="">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 style="text-align: center">Relatório Financeiro</h4>
                </div>
                <div class="panel-body panel-no-padding">
                    <table id="example" class="table table-bordered table-condensed table-striped table-hover no-footer" role="grid" style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="font-size: 1.2em; padding: 5px;">Cliente/Fornecedor</th>
                            <th style="font-size: 1.2em; padding: 5px;">Tipo</th>
                            <th style="font-size: 1.2em; padding: 5px;">Valor</th>
                            <th style="font-size: 1.2em; padding: 5px;">Vencimento</th>
                            <th style="font-size: 1.2em; padding: 5px;">Pagamento</th>
                            <th style="font-size: 1.2em; padding: 5px;">Forma de Pagamento</th>
                            <th style="font-size: 1.2em; padding: 5px;">Situação</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $totalReceita = 0;
                        $totalDespesa = 0;
                        $saldo = 0;
                        foreach ($lancamentos as $l) {
                            $vencimento = date('d/m/Y', strtotime($l->data_lancamento));
                            $pagamento = date('d/m/Y', strtotime($l->data_pagamento));
                            if ($l->baixado == 1) {
                                $situacao = 'Pago';

                            } else {
                                $situacao = 'Pendente';
                            }
                            if ($l->tipo == 1) {
                                $totalReceita += $l->valor;

                            } else {
                                $totalDespesa += $l->valor;
                            }
                            echo '<tr>';
                            echo '<td>' . $l->cliente_fornecedor . '</td>';
                            echo '<td>' . $l->tipo . '</td>';
                            echo '<td>' . $l->valor . '</td>';
                            echo '<td>' . $vencimento . '</td>';
                            echo '<td>' . $pagamento . '</td>';
                            echo '<td>' . $l->forma_pgto . '</td>';
                            echo '<td>' . $situacao . '</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: right; color: green"><strong>Total Receitas:</strong></td>
                            <td colspan="2" style="text-align: left; color: green"><strong>R$ <?php echo number_format($totalReceita, 2, ',', '.') ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: right; color: red"><strong>Total Despesas:</strong></td>
                            <td colspan="2" style="text-align: left; color: red"><strong>R$ <?php echo number_format($totalDespesa, 2, ',', '.') ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: right"><strong>Saldo:</strong></td>
                            <td colspan="2" style="text-align: left;"><strong>R$ <?php echo number_format($totalReceita - $totalDespesa, 2, ',', '.') ?></strong></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <h5 style="text-align: right">Data do Relatório: <?php echo date('d/m/Y'); ?></h5>
        </div>
    </div>
</div>
</body>
</html>








