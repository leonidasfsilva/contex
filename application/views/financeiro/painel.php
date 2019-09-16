<div class="row">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url() ?>financeiro/lancamentos" class="shortcut-tile tile-blue">
                <div class="tile-body">
                    <div class="pull-left"><i class="fa fa-line-chart fa-fw"></i></div>
                    <!--                            <div class="pull-right"><span class="badge">2</span></div>-->
                </div>
                <div class="tile-footer">
                    Lançamentos
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
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vInvestimentos')): ?>
        <div class="col-md-6">
            <a href="<?= base_url() ?>financeiro/investimentos" class="shortcut-tile tile-green">
                <div class="tile-body">
                    <div class="pull-left"><i class="fas fa-hand-holding-usd fa-fw"></i></div>
                    <!--                            <div class="pull-right"><span class="badge">3</span></div>-->
                </div>
                <div class="tile-footer">
                    Investimentos
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
