<?php if ($anuncios) {
    foreach ($anuncios as $a) {
        $format     = "d-m-Y";
        $hoje       = DateTime::createFromFormat($format, date('d-m-Y'));
        $expiracao  = DateTime::createFromFormat($format, date(('d-m-Y'), strtotime($a->data_expiracao)));
        $validade   = $expiracao > $hoje;

        if ($validade == true) {
            if ($a->estilo != 'bg-default') {
                $estilo     = $a->estilo;
                $text_white = 'text-white';
            } else {
                $estilo     = $a->estilo;
                $text_white = '';
            }
?>
            <div class="modal fade modal_anuncio" id="anuncio_<?= $a->id_anuncio ?>">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header <?= $estilo ?>">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title <?= $text_white ?>" id="cabecalho_modal"><?= $a->cabecalho ?></h4>
                        </div>
                        <div class="modal-body">
                            <?php if ($a->titulo) { ?>
                                <h5 class="mt0 pt0" id="titulo_anuncio"><?= $a->titulo ?></h5>
                            <?php } ?>
                            <p class="p0" style="font-size: 13px; border: none; background-color: unset; font-family:'Roboto', sans-serif" id="descricao_anuncio"><?= nl2br($a->descricao) ?></p>
                        </div>
                        <div class="modal-footer <?= $a->exibir_rodape == 1 ? '' : 'hidden' ?>" id="div_rodape">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                                <i class="fas fa-times fa-fw"></i> Fechar
                            </button>
                            <a href="<?= $a->link_botao ?>" class="btn <?= $estilo . ' ' . $text_white ?> btn-sm <?= $a->exibir_botao ? '' : 'hidden' ?>" id="botao_link"><?= $a->rotulo_botao ?></a>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        <?php }
    }
}

if ($direcionados) {
    foreach ($direcionados as $d) {
        $format = "d-m-Y";
        $hoje       = DateTime::createFromFormat($format, date('d-m-Y'));
        $expiracao  = DateTime::createFromFormat($format, date(('d-m-Y'), strtotime($d->data_expiracao)));
        $validade   = $expiracao > $hoje;

        if ($validade == true) {
            if ($d->estilo != 'bg-default') {
                $estilo     = $d->estilo;
                $text_white = 'text-white';
            } else {
                $estilo     = $d->estilo;
                $text_white = '';
            }
        ?>
            <div class="modal fade modal_anuncio" id="anuncio_<?= $d->id_anuncio ?>">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header <?= $estilo ?>">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title <?= $text_white ?>" id="cabecalho_modal"><?= $d->cabecalho ?></h4>
                        </div>
                        <div class="modal-body">
                            <?php if ($d->titulo) { ?>
                                <h5 class="mt0 pt0" id="titulo_anuncio"><?= $d->titulo ?></h5>
                            <?php } ?>
                            <p class="p0" style="font-size: 13px; border: none; background-color: unset; font-family:'Roboto', sans-serif" id="descricao_anuncio"><?= nl2br($d->descricao) ?></p>
                        </div>
                        <div class="modal-footer <?= $d->exibir_rodape == 1 ? '' : 'hidden' ?>" id="div_rodape">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                                <i class="fas fa-times fa-fw"></i> Fechar
                            </button>
                            <a href="<?= $d->link_botao ?>" class="btn <?= $estilo . ' ' . $text_white ?> btn-sm <?= $d->exibir_botao ? '' : 'hidden' ?>" id="botao_link"><?= $d->rotulo_botao ?></a>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
<?php }
    }
} ?>

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
                        <span><i class="fas fa-chart-mixed-up-circle-dollar fa-swap-opacity fa-2x fa-fw"></i></span>
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
                        <span class="tile-sparkline-subheading font-weight-bold mb10" style="font-size: 12pt">Faturas</span>
                        <div class="conteudo-widget " style="<?= ($widgetCartaoCredito == 1 ? '' : 'display: none;') ?>e">
                            <span class="tile-sparkline-subheading block mb10">Saldo de faturas em aberto</span>
                            <h2 class="block">R$ <?= number_format($fatura, 2, ',', '.') ?></h2>
                        </div>
                    </div>
                    <div class="pull-right">
                        <span><i class="fas fa-file-invoice-dollar fa-2x fa-fw"></i></span>
                    </div>
                </div>
                <div class="tile-sparkline-footer-clean">
                    <a href="<?= base_url('financeiro/faturas') ?>" class="font-weight-bold">ver detalhes </a>
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
                        <span><i class="fas fa-money-check-dollar-pen fa-2x fa-fw"></i></span>
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
                        <span><i class="fas fa-hand-holding-circle-dollar fa-2x fa-fw"></i></span>
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

<!--MOSAICO DE LINKS (CARDS)-->
<div class="row">
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url('/financeiro/cartoes') ?>" class="shortcut-tile tile-midnightblue">
                <div class="tile-body">
                    <div class="pull-left"><i class="fad fa-credit-card fa-swap-opacity fa-fw"></i></div>
                    <div class="pull-right"><span class="badge"><?= $this->cartoes_model->countCartoesUsuario(); ?></span></div>
                </div>
                <div class="tile-footer">
                    Cartões
                </div>
            </a>
        </div>
    <?php endif ?>

    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url('/clientes') ?>" class="shortcut-tile tile-green">
                <div class="tile-body">
                    <div class="pull-left"><i class="fal fa-people-group fa-fw"></i></div>
                    <div class="pull-right"><span class="badge"><?= $this->clientes_model->countClientesUsuario(); ?></span></div>
                </div>
                <div class="tile-footer">
                    Clientes
                </div>
            </a>
        </div>
    <?php endif ?>
    
    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vConsumo')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url('/consumo') ?>" class="shortcut-tile tile-info">
                <div class="tile-body">
                    <div class="pull-left"><i class="fal fa-lightbulb-dollar fa-fw"></i></div>
                    <!--                            <div class="pull-right"><span class="badge">16</span></div>-->
                </div>
                <div class="tile-footer">
                    Consumo de Energia
                </div>
            </a>
        </div>
    <?php endif ?>

    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) : ?>
        <div class="col-md-6">
            <a href="<?= base_url('/usuarios') ?>" class="shortcut-tile tile-inverse">
                <div class="tile-body">
                    <div class="pull-left"><i class="fal fa-users-cog fa-fw"></i></div>
                    <!--                            <div class="pull-right"><span class="badge">10</span></div>-->
                </div>
                <div class="tile-footer">
                    Usuários do Sistema
                </div>
            </a>
        </div>
    <?php endif ?>

    <div class="col-md-6">
        <a href="<?= base_url('/notificacoes') ?>" class="shortcut-tile tile-danger">
            <div class="tile-body">
                <div class="pull-left"><i class="fal fa-bell fa-fw"></i></div>
                <!--                            <div class="pull-right"><span class="badge">3</span></div>-->
            </div>
            <div class="tile-footer">
                Notificações
            </div>
        </a>
    </div>

    <div class="col-md-6">
        <a href="<?= base_url('/chamados') ?>" class="shortcut-tile tile-alizarin">
            <div class="tile-body">
                <div class="pull-left"><i class="fal fa-comments-question-check fa-fw"></i></div>
            </div>
            <div class="tile-footer">
                Suporte
            </div>
        </a>
    </div>
</div>

<script>
    $.each($('.conteudo-widget'), function(key, value) {
        if ($(this).css('display') == 'none') {
            $(this).parents().eq(3).find('.collapse-icon').attr('class', 'fas fa-chevron-down fa-fw collapse-icon');
            $(this).parents().eq(3).find(".chevron-label").text('exibir');
        } else {
            $(this).parents().eq(3).find('.collapse-icon').attr('class', 'fas fa-chevron-up fa-fw collapse-icon');
            $(this).parents().eq(3).find(".chevron-label").text('ocultar');
        }
    });

    $('a.widget-collapse').click(function() {
        let widget = $(this).parents().eq(2).find(".conteudo-widget");

        if (widget.css('display') == 'none') {
            $(this).find('.collapse-icon').attr('class', 'fas fa-chevron-up fa-fw collapse-icon');
            $(this).find(".chevron-label").text('ocultar');

            if ($(this).attr('id') == 'widget_lancamentos') {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url(); ?>configuracoes/setWidgetLancamentos",
                    data: "value=" + 1,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_credito') {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url(); ?>configuracoes/setWidgetCartaoCredito",
                    data: "value=" + 1,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_investimentos') {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url(); ?>configuracoes/setWidgetInvestimentos",
                    data: "value=" + 1,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_pendencias') {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url(); ?>configuracoes/setWidgetPendencias",
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
                    url: "<?= base_url(); ?>configuracoes/setWidgetLancamentos",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_credito') {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url(); ?>configuracoes/setWidgetCartaoCredito",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_investimentos') {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url(); ?>configuracoes/setWidgetInvestimentos",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
            if ($(this).attr('id') == 'widget_pendencias') {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url(); ?>configuracoes/setWidgetPendencias",
                    data: "value=" + 0,
                    dataType: 'html',
                });
            }
        }
        widget.slideToggle({
            duration: 200
        });
        return false;
    });
</script>