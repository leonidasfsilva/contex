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
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) : ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>financeiro/contaCorrente" class="shortcut-tile tile-blue">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-line-chart fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">2</span></div>-->
                        </div>
                        <div class="tile-footer">
                            Conta Corrente
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPoupanca')): ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>financeiro/contaPoupanca" class="shortcut-tile tile-green">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fas fa-piggy-bank fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">3</span></div>-->
                        </div>
                        <div class="tile-footer">
                            Conta Poupança
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')): ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>financeiro/faturas" class="shortcut-tile tile-midnightblue">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-credit-card fa-fw"></i></div>
<!--                            <div class="pull-right"><span class="badge">5</span></div>-->
                        </div>
                        <div class="tile-footer">
                            Faturas
                        </div>
                    </a>
                </div>
            <?php endif ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPendencias')) : ?>
                <div class="col-md-6">
                    <a href="<?= base_url() ?>financeiro/pendencias" class="shortcut-tile tile-danger">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-thumb-tack fa-fw"></i></div>
                        </div>
                        <div class="tile-footer">
                            Pendências
                        </div>
                    </a>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<!--End-Action boxes-->

<script type="text/javascript">


</script>