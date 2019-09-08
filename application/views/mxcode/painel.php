<!--Action boxes-->
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            <i class="fa fa-dollar fa-lg fa-fw"></i>
            Painel Financeiro
        </h2>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="tile-sparkline">
                    <div class="tile-sparkline-heading clearfix">
                        <div class="pull-left">
                            <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Conta Corrente</span>
                            <span class="tile-sparkline-subheading block mb10">Saldo disponível</span>
                            <h2 class="block">R$ <?= number_format($contaCorrente->total, 2, ',', '.') ?></h2>
                        </div>
                        <div class="pull-right">
                            <span><i class="fa fa-chart-line fa-lg fa-fw"></i></span>
                        </div>
                    </div>
                    <div class="tile-sparkline-footer">
                        <a href="<?= base_url() ?>financeiro/contaCorrente" class="font-weight-bold">Ver detalhes </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="tile-sparkline">
                    <div class="tile-sparkline-heading clearfix">
                        <div class="pull-left">
                            <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Cartão de Crédito</span>
                            <span class="tile-sparkline-subheading block mb10">Valor da fatura atual</span>
                            <h2 class="block">R$ <?= number_format($fatura->total, 2, ',', '.') ?></h2>
                        </div>
                        <div class="pull-right">
                            <span><i class="fa fa-credit-card fa-lg fa-fw"></i></span>
                        </div>
                    </div>
                    <div class="tile-sparkline-footer">
                        <a href="<?= base_url() ?>financeiro/faturas" class="font-weight-bold">Ver detalhes </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="tile-sparkline">
                    <div class="tile-sparkline-heading clearfix">
                        <div class="pull-left">
                            <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Meus Investimentos</span>
                            <span class="tile-sparkline-subheading block mb10">Valor total investido</span>
                            <h2 class="block">R$ <?= number_format($contaPoupanca->total, 2, ',', '.') ?></h2>
                        </div>
                        <div class="pull-right">
                            <span><i class="fas fa-piggy-bank fa-lg fa-fw"></i></span>
                        </div>
                    </div>
                    <div class="tile-sparkline-footer">
                        <a href="<?= base_url() ?>financeiro/contaPoupanca" class="font-weight-bold">Ver detalhes </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            <i class="fa fa-home fa-lg fa-fw"></i>
            Painel Inicial
        </h2>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) : ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>financeiro" class="shortcut-tile tile-green">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-dollar fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">16</span></div>-->
                        </div>
                        <div class="tile-footer">
                            Financeiro
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')): ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>clientes" class="shortcut-tile tile-primary">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-group fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">2</span></div>-->
                        </div>
                        <div class="tile-footer">
                            Clientes
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')): ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>produtos" class="shortcut-tile tile-indigo">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-barcode fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">5</span></div>-->
                        </div>
                        <div class="tile-footer">
                            Produtos
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) : ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>servicos" class="shortcut-tile tile-orange">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-wrench fa-fw"></i></div>
                        </div>
                        <div class="tile-footer">
                            Serviços
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) : ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>os" class="shortcut-tile tile-danger">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-tags fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">3</span></div>-->
                        </div>
                        <div class="tile-footer">
                            OS
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) : ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>vendas" class="shortcut-tile tile-magenta">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-shopping-cart fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">10</span></div>-->
                        </div>
                        <div class="tile-footer">
                            Vendas
                        </div>
                    </a>
                </div>
            <?php endif ?>

        </div>
    </div>
</div>

<!---->
