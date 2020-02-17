<!--WIDGETS-->
<div class="row">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
        <div class="col-lg-6">
            <div class="tile-sparkline">
                <div class="tile-sparkline-heading clearfix">
                    <div class="pull-left">
                        <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Lançamentos</span>
                        <div class="conteudo-widget " style="<?= ($widgetLancamentos == 1 ? '' : 'display: none;') ?>">
                            <span class="tile-sparkline-subheading block mb10">Saldo disponível</span>
                            <h2 class="block">R$ <?= number_format($lancamentos->total, 2, ',', '.') ?></h2>
                        </div>
                    </div>
                    <div class="pull-right">
                        <span><i class="fas fa-chart-line fa-lg fa-fw"></i></span>
                    </div>
                </div>
                <div class="tile-sparkline-footer-clean">
                    <a href="<?= base_url() ?>financeiro/lancamentos" class="font-weight-bold">ver detalhes </a>
                    <a href="#" style="color: #607d8b" class="pull-right widget-collapse" id="widget_lancamentos">
                        <span class="chevron-label">ocultar</span>
                        <i class="fas fa-chevron-up fa-fw collapse-icon"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) { ?>
        <div class="col-lg-6">
            <div class="tile-sparkline">
                <div class="tile-sparkline-heading clearfix">
                    <div class="pull-left">
                        <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Cartão de Crédito</span>
                        <div class="conteudo-widget " style="<?= ($widgetCartaoCredito == 1 ? '' : 'display: none;') ?>e">
                            <span class="tile-sparkline-subheading block mb10">Valor da fatura atual</span>
                            <h2 class="block">R$ <?= number_format($fatura->total, 2, ',', '.') ?></h2>
                        </div>
                    </div>
                    <div class="pull-right">
                        <span><i class="fas fa-credit-card fa-lg fa-fw"></i></span>
                    </div>
                </div>
                <div class="tile-sparkline-footer-clean">
                    <a href="<?= base_url() ?>financeiro/faturas" class="font-weight-bold">ver detalhes </a>
                    <a href="#" style="color: #607d8b" class="pull-right widget-collapse" id="widget_credito">
                        <span class="chevron-label">ocultar</span>
                        <i class="fas fa-chevron-up fa-fw collapse-icon"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="row">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPendencias')) { ?>
        <div class="col-lg-6">
            <div class="tile-sparkline">
                <div class="tile-sparkline-heading clearfix">
                    <div class="pull-left">
                        <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Pendências</span>
                        <div class="conteudo-widget " style="<?= ($widgetPendencias == 1 ? '' : 'display: none;') ?>">
                            <span class="tile-sparkline-subheading block mb10">Pendências a pagar</span>
                            <h2 class="block">R$ <?= number_format($pendencias->total, 2, ',', '.') ?></h2>
                        </div>
                    </div>
                    <div class="pull-right">
                        <span><i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i></span>
                    </div>
                </div>
                <div class="tile-sparkline-footer-clean">
                    <a href="<?= base_url() ?>financeiro/pendencias" class="font-weight-bold">ver detalhes </a>
                    <a href="#" style="color: #607d8b" class="pull-right widget-collapse" id="widget_pendencias">
                        <span class="chevron-label">ocultar</span>
                        <i class="fas fa-chevron-up fa-fw collapse-icon"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vInvestimentos')) { ?>
        <div class="col-lg-6">
            <div class="tile-sparkline">
                <div class="tile-sparkline-heading clearfix">
                    <div class="pull-left">
                        <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Investimentos</span>
                        <div class="conteudo-widget " style="<?= ($widgetInvestimentos == 1 ? '' : 'display: none;') ?>">
                            <span class="tile-sparkline-subheading block mb10">Valor total investido</span>
                            <h2 class="block">R$ <?= number_format($investimentos->total, 2, ',', '.') ?></h2>
                        </div>
                    </div>
                    <div class="pull-right">
                        <span><i class="fas fa-hand-holding-usd fa-lg fa-fw"></i></span>
                    </div>
                </div>
                <div class="tile-sparkline-footer-clean">
                    <a href="<?= base_url() ?>financeiro/investimentos" class="font-weight-bold">ver detalhes </a>
                    <a href="#" style="color: #607d8b" class="pull-right widget-collapse" id="widget_investimentos">
                        <span class="chevron-label">ocultar</span>
                        <i class="fas fa-chevron-up fa-fw collapse-icon"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<!--PAINEL DE LINKS-->
<div class="row">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url() ?>financeiro" class="shortcut-tile tile-sky">
                <div class="tile-body">
                    <div class="pull-left"><i class="fas fa-dollar fa-fw"></i></div>
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
            <a href="<?= base_url() ?>clientes" class="shortcut-tile tile-indigo">
                <div class="tile-body">
                    <div class="pull-left"><i class="fas fa-group fa-fw"></i></div>
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
            <a href="<?= base_url() ?>produtos" class="shortcut-tile tile-green">
                <div class="tile-body">
                    <div class="pull-left"><i class="fas fa-barcode fa-fw"></i></div>
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
            <a href="<?= base_url() ?>servicos" class="shortcut-tile tile-blue">
                <div class="tile-body">
                    <div class="pull-left"><i class="fas fa-wrench fa-fw"></i></div>
                </div>
                <div class="tile-footer">
                    Serviços
                </div>
            </a>
        </div>
    <?php endif ?>

    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url() ?>os" class="shortcut-tile tile-grape">
                <div class="tile-body">
                    <div class="pull-left"><i class="fas fa-tags fa-fw"></i></div>
                    <!--                            <div class="pull-right"><span class="badge">3</span></div>-->
                </div>
                <div class="tile-footer">
                    Ordens de Serviço
                </div>
            </a>
        </div>
    <?php endif ?>

    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url() ?>vendas" class="shortcut-tile tile-info">
                <div class="tile-body">
                    <div class="pull-left"><i class="fas fa-shopping-cart fa-fw"></i></div>
                    <!--                            <div class="pull-right"><span class="badge">10</span></div>-->
                </div>
                <div class="tile-footer">
                    Vendas
                </div>
            </a>
        </div>
    <?php endif ?>
</div>

<script>
    $.each($('.conteudo-widget'), function (key, value) {
        if ($(this).css('display') == 'none') {
            $(this).parents().eq(3).find('.collapse-icon').attr('class', 'fas fa-chevron-down fa-fw collapse-icon');
            $(this).parents().eq(3).find(".chevron-label").text('exibir');
        } else {
            $(this).parents().eq(3).find('.collapse-icon').attr('class', 'fas fa-chevron-up fa-fw collapse-icon');
            $(this).parents().eq(3).find(".chevron-label").text('ocultar');
        }
    });

    $('a.widget-collapse').click(function () {
        let n = $(this).parents().eq(2).find(".conteudo-widget");

        if (n.css('display') == 'none') {
            $(this).find('.collapse-icon').attr('class', 'fas fa-chevron-up fa-fw collapse-icon');
            $(this).find(".chevron-label").text('ocultar');

            if ($(this).attr('id') == 'widget_lancamentos') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetLancamentos",
                    data: "value=" + 1,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_credito') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetCartaoCredito",
                    data: "value=" + 1,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_investimentos') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetInvestimentos",
                    data: "value=" + 1,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_pendencias') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetPendencias",
                    data: "value=" + 1,
                    dataType: 'html',
                });
            }
        } else {
            $(this).find('.collapse-icon').attr('class', 'fas fa-chevron-down fa-fw collapse-icon');
            $(this).find(".chevron-label").text('exibir');

            if ($(this).attr('id') == 'widget_lancamentos') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetLancamentos",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_credito') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetCartaoCredito",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_investimentos') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetInvestimentos",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_pendencias') {
                $.ajax({
                    type: "POST",
                    url: "<?=base_url();?>configuracoes/setWidgetPendencias",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
        }
        n.slideToggle({duration: 200});
        return false;
    });
</script>
