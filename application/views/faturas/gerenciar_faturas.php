<?php
$situacao = $this->input->get('situacao');
$periodo  = $this->input->get('periodo');

if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturas')) {
    if ($faturaAberta != 0) {
        $disabledFatura     = 'disabled';
        $disabledLancamento = '';
        $statusFaturaAtual  = 'aberta';
    } else {
        $disabledFatura     = '';
        $disabledLancamento = 'disabled';
        $statusFaturaAtual  = 'fechada';
    }
    if (!$cartoes) {
        $disabledFatura = 'disabled';
        $disabledConfig = 'disabled';
    } else {
        foreach ($cartoes as $cartao) {
            if ($cartao->id_usuario != getUserId()) {
                if ($cartao->adicional) {
                    if ($cartaoSelecionado['id_cartao'] == $cartao->id_cartao) {
                        $disabledFatura = '';
                        $disabledConfig = '';
                    }
                }
            } else {
                if ($cartaoSelecionado['id_cartao'] == $cartao->id_cartao) {
                    if ($cartao->adicional) {
                        $disabledConfig = 'disabled';
                    } else {
                        $disabledConfig = '';
                    }
                }
            }
        }
    }
    if (!$existe_configuracao) {
        $disabledFatura = 'disabled';
    }
} ?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
            Controle de Faturas
        </h3>
        <div class="panel-ctrls">
            <div class="pull-right">
                <input type="hidden" id="cartoes" value="<?= $cartaoSelecionado['id_cartao'] ?? null ?>">
                <div class="btn-group dropdown-hover">
                    <?php if ($cartoes) { ?>
                        <button type="button" class="form-control btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <?= $cartaoSelecionado['cartaoLabel'] ?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-hover arrow" role="menu">
                            <?php foreach ($cartoes as $cartao) {
                                $n_cartao      = explode(" ", trim(decriptar($cartao->numero)));
                                $final         = $n_cartao[3];
                                $cartao_config = $cartao->apelido ?: $cartao->bandeira;
                                $cartao_config = sprintf('%s - %s', $cartao_config, $final);

                                if ($cartaoSelecionado['id_cartao'] == $cartao->id_cartao) {
                                    $selected                  = 'active';
                                    $cartaoSemFaturaencontrada = $cartao_config;
                                } else {
                                    $selected = '';
                                }
                                ?>
                                <li class="<?= $selected ?>"><a href="<?= sprintf(base_url('financeiro/faturas?cartao=%s'), $cartao->id_cartao) ?>"><?= $cartao_config ?></a></li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <button type="button" class="form-control btn btn-default" disabled>
                            Não há cartões cadastrados
                        </button>
                    <?php }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body panel-no-padding">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <thead>
            <tr>
                <th colspan="2" style="text-align: left !important;">Descrição</th>
                <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
            </tr>
            </thead>
            <tr>
                <td colspan="2" style="text-align: left; color: green" class="font-weight-bold">SALDO DE FATURAS QUITADAS</td>
                <td colspan="1" style="text-align: right; color: green" class="font-weight-bold">
                    <?php if (isset($saldoQuitado)) echo number_format($saldoQuitado->total, 2, ',', '.');
                    else echo '0,00' ?></td>
            </tr>
            <?php if (isset($saldoVencidas) && $saldoVencidas->total > 0) { ?>
                <tr>
                    <td colspan="2" style="text-align: left; color: red" class="font-weight-bold">SALDO DE FATURAS VENCIDAS</td>
                    <td colspan="1" style="text-align: right; color: red" class="font-weight-bold">
                        <?php echo number_format($saldoVencidas->total, 2, ',', '.') ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="2" style="text-align: left" class="font-weight-bold">SALDO DE FATURAS A VENCER</td>
                <td colspan="1" style="text-align: right" class="font-weight-bold">
                    <?php if (isset($saldoPendente)) echo number_format($saldoPendente->total, 2, ',', '.');
                    else echo '0,00' ?></td>
            </tr>
        </table>
    </div>
</div>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Registro de Faturas
        </h2>
        <div class="panel-ctrls">
            <div class="btn-group dropdown-hover">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-bars fa-fw"></i>
                    <text class="visible-lg-inline">Opções</text>
                </button>
                <ul class="dropdown-menu dropdown-menu-hover arrow" role="menu">
                    <li>
                        <a href="#modalSearch" data-toggle="modal">
                            <i class="fas fa-search fa-fw pull-right"></i>
                            <span>Pesquisar</span>
                        </a>
                    </li>
                    <li>
                        <a href="#modalFiltrar" data-toggle="modal">
                            <i class="fas fa-filter fa-fw pull-right"></i>
                            <span>Filtrar</span>
                        </a>
                    </li>
                    <li>
                        <a href="#modalConfiguracoes" data-toggle="modal" id="configurar_fatura">
                            <i class="fas fa-cog fa-fw pull-right"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                </ul>
            </div>

            <button href="#modalVincularFaturas" id="vincularFaturas" data-toggle="modal" role="button" class="btn btn-primary btn-sm tip-bottom" title=Gerenciar vínculo de faturas
            " <?= !isset($cartao) ? 'disabled' : '' ?>>
            <i class="fas fa-link fa-fw"></i>
            <text class="visible-lg-inline">Gerenciar Vínculo</text>
            </button>
            <button href="#modalNovaFatura" id="novaFatura" data-toggle="modal" role="button" class="btn btn-primary btn-sm tip-bottom" title="Abrir nova fatura" <?= $disabledFatura ?>>
                <i class="fas fa-plus fa-fw"></i>
                <text class="visible-lg-inline">Nova Fatura</text>
            </button>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <th>Referência</th>
                <th>Vencimento</th>
                <th>Valor (R$)</th>
                <th>Status</th>
                <th>Pagamento</th>
                <th style="width: 210px !important;">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($results) && $results) {
                $totalReceita = 0;
                $totalDespesa = 0;
                $saldo        = 0;

                foreach ($results as $r) {
                    $dateFormatter = new \IntlDateFormatter(
                        'pt_BR',
                        \IntlDateFormatter::FULL,
                        \IntlDateFormatter::NONE,
                        'America/Sao_Paulo',
                        \IntlDateFormatter::GREGORIAN,
                        "MMM"
                    );
                    $dateObj       = DateTime::createFromFormat('!m', ($r->mes_referencia));
                    $mes           = str_replace('.', '', strtoupper($dateFormatter->format($dateObj)));
                    $ano           = ($r->ano_referencia);
                    $disabled      = '';
                    $disabledPagar = '';

                    if ($r->fatura_aberta == 0) {
                        //FATURA FECHADA
                        $status = 'FECHADA';
                        $label  = 'inverse';
                        // $disabled        = 'disabled';
                        $hrefFechar      = '#modalReabrir';
                        $titleFechar     = 'Reabrir fatura';
                        $colorFechar     = 'inverse';
                        $iconFechar      = 'fas fa-lock-keyhole';
                        $iconFaturaAtual = null;
                    } else if ($r->fatura_aberta == 2) {
                        //FATURA FUTURA
                        $status          = '';
                        $label           = '';
                        $disabledPagar   = 'disabled';
                        $disabled        = 'disabled';
                        $hrefFechar      = '#modalFechar';
                        $titleFechar     = 'Fatura futura';
                        $colorFechar     = 'inverse';
                        $iconFechar      = 'fas fa-clock';
                        $iconFaturaAtual = null;
                    } else {
                        //FATURA ABERTA
                        $status          = 'ABERTA';
                        $label           = 'primary';
                        $hrefFechar      = '#modalFechar';
                        $titleFechar     = 'Fechar fatura';
                        $disabledPagar   = 'disabled';
                        $colorFechar     = 'default';
                        $iconFechar      = 'fas fa-lock-keyhole-open';
                        $iconFaturaAtual = ' <i class="text-alizarin fas fa-long-arrow-alt-left fa-lg fa-fw" title="Fatura atual"></i>';
                    }

                    if (!getVinculoFatura($r->id_fatura)) {
                        $statusVinculo = null;
                        $hrefVinculo   = '#modalVincular';
                        $titleVinculo  = 'Fatura não vinculada';
                        $colorVinculo  = 'warning';
                        $iconVinculo   = '<i class="fas fa-unlink fa-lg fa-fw"></i>';
                    } else {
                        $statusVinculo = ' <i class="fas fa-link fa-fw" title="Fatura vinculada"></i>';
                        $hrefVinculo   = '#modalDesvincular';
                        $titleVinculo  = 'Fatura vinculada';
                        $colorVinculo  = 'success';
                        $iconVinculo   = '<i class="fas fa-link fa-lg fa-fw"></i>';
                    }

                    if ($r->fatura_paga == 2) {
                        $pagamento = 'PENDENTE';
                        $labelPgto = 'alizarin';
                        $color     = 'red';
                        $iconPagar = 'fas fa-circle-dollar-to-slot';
                    } else if ($r->fatura_paga == 1) {
                        $pagamento     = 'PAGA';
                        $labelPgto     = 'success';
                        $color         = 'green';
                        $iconPagar     = 'fas fa-file-check';
                        $disabledPagar = 'disabled';
                    } else {
                        $pagamento = '';
                        $labelPgto = '';
                        $color     = 'red';
                        $iconPagar = 'fal fa-file-check';
                    }

                    $valor_total = $this->fatura_model->getValorTotalFatura($r->id_fatura);

                    echo '<tr>';
                    echo '<td class="font-weight-bold"><a href="' . base_url('financeiro/faturas/detalhes/' . $r->id_fatura . '/' . $cartaoSelecionado['id_cartao']) . '" title="Acessar fatura">' . $mes . ' / ' . $ano . $iconFaturaAtual . '</a></td>';
                    echo '<td>' . date(('d/m/Y'), strtotime($r->vencimento)) . $statusVinculo . '</td>';

                    echo '<td style="cursor: pointer; color: ' . $color . '" class="i-copy-total font-weight-bold"><i class="fas fa-copy fa-fw hidden icon-total"></i> ' . number_format($valor_total, 2, ',', '.') . '</td>';
                    echo '<td><span class="badge badge-' . $label . '">' . strtoupper($status) . '</span></td>';
                    echo '<td><span class="badge badge-' . $labelPgto . '">' . strtoupper($pagamento) . '</span></td>';

                    echo '<td>';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
                        echo '<button ' . $disabledPagar . ' href="#modalPagar" style="margin-right: 1%"  class="btn btn-success btn-sm pagar" data-toggle="modal" title="Pagar Fatura" id_fatura="' . $r->id_fatura . '">
                                <i class="' . $iconPagar . ' fa-lg fa-fw"></i></button>';

                        echo '<button ' . $disabled . ' href="' . $hrefFechar . '" style="margin-right: 1%"  class="btn btn-' . $colorFechar . ' btn-sm fechar" data-toggle="modal" title="' . $titleFechar . '" id_fatura="' . $r->id_fatura . '">
                                <i class="' . $iconFechar . ' fa-lg fa-fw"></i></button>';

                        echo '<button href="' . $hrefVinculo . '" style="margin-right: 1%"  class="btn btn-' . $colorVinculo . ' btn-sm vinculo" data-toggle="modal" title="' . $titleVinculo . '" id_fatura="' . $r->id_fatura . '">
                                ' . $iconVinculo . '</button>';

                        echo '<a href="' . base_url('financeiro/faturas/detalhes/') . $r->id_fatura . '/' . $cartaoSelecionado['id_cartao'] . '" type="button" id="btn_detalhes" style="margin-right: 1%" class="btn btn-primary btn-sm detalhes" title="Acessar fatura" id_fatura="' .
                            $r->id_fatura . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturas')) {
                        echo '<a href="#modalExcluir" data-toggle="modal" id_fatura="' . $r->id_fatura . '" class="btn btn-danger btn-sm excluir" title="Excluir fatura"><i class="fas fa-trash-can-xmark fa-lg fa-fw"></i></a>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            } else { ?>
                <tr>
                    <td colspan="6">Nenhuma fatura encontrada <span class="font-weight-bold"><?= isset($cartaoSemFaturaencontrada) ? 'para o cartão ' . $cartaoSemFaturaencontrada : null ?></span></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php if ($this->pagination->create_links()) { ?>
            <div class="panel-footer">
                <?= $this->pagination->create_links() ?>
            </div>
        <?php } ?>
    </div>
</div>

<form id="form_cartao" action="<?php echo base_url('financeiro/faturas'); ?>" method="get">
    <input type="hidden" id="id_cartao" name="cartao">
</form>

<!-- Modal PESQUISAR -->
<div class="modal fade" id="modalSearch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formSearch" action="<?= base_url('financeiro/faturas/pesquisa') ?>" method="get" autocomplete="off">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <p class="font-weight-bold">Pesquisar por: </p>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <!--<label class="font-weight-bold" for="termo">Termo *</label>-->
                            <input class="form-control descricao" type="text" name="busca"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search fa-fw"></i>
                        Pesquisar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal FILTRAR -->
<div class="modal fade" id="modalFiltrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Filtrar faturas</h4>
            </div>
            <form action="<?php echo current_url(); ?>" method="get" id="form_filtro">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-6" style="margin-left: 0">
                            <label class="tip-top" title="Filtrar faturas por período específico">Período <i class="fa fa-info-circle fa-fw"></i></label>
                            <select name="periodo" id="select_periodos" class="form-control">
                                <option value="">Selecione o período</option>
                                <option value="3dias" <?php if ($periodo == '3dias') {
                                    echo 'selected';
                                } ?>>Últimos 3 dias
                                </option>
                                <option value="5dias" <?php if ($periodo == '5dias') {
                                    echo 'selected';
                                } ?>>Últimos 5 dias
                                </option>
                                <option value="7dias" <?php if ($periodo == '7dias') {
                                    echo 'selected';
                                } ?>>Últimos 7 dias
                                </option>
                                <option value="15dias" <?php if ($periodo == '15dias') {
                                    echo 'selected';
                                } ?>>Últimos 15 dias
                                </option>
                                <option value="30dias" <?php if ($periodo == '30dias') {
                                    echo 'selected';
                                } ?>>Últimos 30 dias
                                </option>
                                <option value="60dias" <?php if ($periodo == '60dias') {
                                    echo 'selected';
                                } ?>>Últimos 60 dias
                                </option>
                                <option value="90dias" <?php if ($periodo == '90dias') {
                                    echo 'selected';
                                } ?>>Últimos 90 dias
                                </option>
                                <option value="todos" <?php if ($periodo == 'todos') {
                                    echo 'selected';
                                } ?>>Todos
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label class="tip-top" title="Filtrar faturas por status">Status <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" id="select_status" name="status">
                                <option value="">Selecione o status</option>
                                <option value="aberta" <?php if ($periodo == 'aberta') {
                                    echo 'selected';
                                } ?>>Aberta
                                </option>
                                <option value="fechada" <?php if ($periodo == 'fechada') {
                                    echo 'selected';
                                } ?>>Fechada
                                </option>
                                <option value="futura" <?php if ($periodo == 'futura') {
                                    echo 'selected';
                                } ?>>Futura
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal CONFIGURACOES DE FATURA -->
<div class="modal fade" id="modalConfiguracoes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Configurações de fatura</h4>
            </div>
            <form id="formConfigurarFatura" action="<?= base_url('financeiro/faturas/configurar') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p class="font-weight-bold">Defina o dia de vencimento padrão para as faturas do cartão selecionado:</p>
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label font-weight-bold" for="vencimento_fatura">
                                Dia de vencimento *
                            </label>
                            <select class="form-control" id="select_dia" name="dia_vencimento">
                                <option value="">
                                    << Selecione>>
                                </option>
                                <option value="05" <?= $dia_vencimento == 5 ? 'selected' : '' ?>>TODO DIA 05</option>
                                <option value="08" <?= $dia_vencimento == 8 ? 'selected' : '' ?>>TODO DIA 08</option>
                                <option value="09" <?= $dia_vencimento == 9 ? 'selected' : '' ?>>TODO DIA 09</option>
                                <option value="10" <?= $dia_vencimento == 10 ? 'selected' : '' ?>>TODO DIA 10</option>
                                <option value="12" <?= $dia_vencimento == 12 ? 'selected' : '' ?>>TODO DIA 12</option>
                                <option value="15" <?= $dia_vencimento == 15 ? 'selected' : '' ?>>TODO DIA 15</option>
                                <option value="20" <?= $dia_vencimento == 20 ? 'selected' : '' ?>>TODO DIA 20</option>
                                <option value="25" <?= $dia_vencimento == 25 ? 'selected' : '' ?>>TODO DIA 25</option>
                                <option value="28" <?= $dia_vencimento == 28 ? 'selected' : '' ?>>TODO DIA 28</option>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <p class="note note-info font-weight-bold"><?= $cartaoSelecionado['cartaoLabel'] ?></p>
                        </div>
                        <input class="id_cartao" type="hidden" name="id_cartao"/>
                        <input class="urlAtual" type="hidden" name="urlAtual"/>
                    </div>
                    <p class="font-weight-bold">Vinculação automática de novas faturas geradas:</p>
                    <div>
                        <input type="checkbox" id="invoice-link-switch" name="invoiceAutoLink" class="switch-input primary" <?= $autoLinkInvoices ? 'checked' : '' ?>>
                        <label for="invoice-link-switch" id="auto-link-label" class="switch-label primary font-weight-bold"><?= $autoLinkInvoices ? 'Auto vínculo ativado' : 'Auto vínculo desativado' ?></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm">
                        <i class="fa fa-check fa-fw"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ABRIR NOVA FATURA -->
<div class="modal fade" id="modalNovaFatura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Abrir nova fatura</h4>
            </div>
            <form id="formNovaFatura" action="<?= base_url('financeiro/faturas/abrir') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p class="font-weight-bold">Defina o mês de referência para a nova fatura:</p>
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label font-weight-bold" for="select_mes">
                                Mês de referência *
                            </label>
                            <select class="form-control" id="mes_referencia" name="mes_referencia">
                                <option value="">
                                    << Selecione>>
                                </option>
                                <option value="01">01 - JANEIRO</option>
                                <option value="02">02 - FEVEREIRO</option>
                                <option value="03">03 - MARÇO</option>
                                <option value="04">04 - ABRIL</option>
                                <option value="05">05 - MAIO</option>
                                <option value="06">06 - JUNHO</option>
                                <option value="07">07 - JULHO</option>
                                <option value="08">08 - AGOSTO</option>
                                <option value="09">09 - SETEMBRO</option>
                                <option value="10">10 - OUTUBRO</option>
                                <option value="11">11 - NOVEMBRO</option>
                                <option value="12">12 - DEZEMBRO</option>
                            </select>
                        </div>
                        <input id="id_cartao_nova_fatura" type="hidden" name="id_cartao"/>
                        <input class="urlAtual" type="hidden" name="urlAtual"/>

                        <div class="col-lg-6 form-group">
                            <label for="vencimento" class="control-label font-weight-bold">Data de vencimento da nova fatura:</label>
                            <input class="form-control font-weight-bold" id="vencimento" type="text" value="" placeholder="Selecione o mês de referência" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="note note-info font-weight-bold">
                                <i class="fas fa-info-circle fa-lg fa-fw"></i> O mês de vencimento da fatura será sempre subsequente ao mês de referência.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm">
                        <i class="fa fa-check fa-fw"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EXCLUIR -->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir fatura</h4>
            </div>
            <form action="<?php echo base_url() ?>financeiro/faturas/excluir" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Deseja realmente excluir esta fatura?</p>
                    <?php ?>
                    <p class="note note-danger"><i class="text-danger fa fa-exclamation-triangle fa-fw fa-lg"></i> Caso esta fatura possua um vínculo ativo no módulo de Lançamentos, o mesmo será excluído.</p>
                    <?php ?>
                    <input name="id_fatura" id="idExcluir" type="hidden" value=""/>
                    <input id="urlExcluirFatura" type="hidden" name="urlAtual" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnExcluir"><i class="fa fa-check fa-fw"></i> Excluir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ALERTA CONFIGURACAO -->
<?php if (isset($cartao) && !$existe_configuracao) { ?>
    <div class="modal fade alerta-usuario" id="modalAlerta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white ">Configuração pendente</h4>
                </div>
                <div class="modal-body">
                    <div class="note note-danger font-weight-bold">
                        <span>Este cartão não possui uma data de vencimento padrão configurada para faturas.</span>
                        <br>
                        <span>Sem este parâmentro configurado não é possível abrir novas faturas.</span>
                        <br>
                        <span>Defina uma data de vencimento padrão para fatura clicando no botão: <span class="label label-primary"> <i class="fas fa-cog fa-fw"></i> Configurar Fatura</span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Fechar
                    </button>
                    <button href="#modalConfiguracoes" class="btn btn-primary btn-sm" id="configurar_fatura" data-dismiss="modal" data-toggle="modal" title="Configurações de fatura" <?= $disabledConfig ?>>
                        <i class="fas fa-cog fa-fw"></i> Configurar Fatura
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<!-- Modal ALERTA AUSENCIA DE CARTAO -->
<?php if (!isset($cartao)) { ?>
    <div class="modal fade alerta-usuario" id="modalAlerta" tabindex="-10" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white ">Configuração pendente</h4>
                </div>
                <div class="modal-body">
                    <div class="note note-danger font-weight-bold">
                        <span>Não há nenhum cartão de crédito cadastrado.</span>
                        <br>
                        <span>Não é possível abrir novas faturas sem um cartão previamente cadastrado.</span>
                        <br>
                        <span>Cadastre seu primeiro cartão clicando no botão: <span class="label label-primary"> <i class="fas fa-cog fa-fw"></i> Cadastrar Cartão</span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Fechar
                    </button>
                    <a href="<?= base_url('financeiro/cartoes/cadastrar') ?>" class="btn btn-primary btn-sm" title="Cadastrar novo cartão">
                        <i class="fas fa-credit-card fa-fw"></i> Cadastrar Cartão
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<!-- Modal FECHAR FATURA -->
<div class="modal fade" id="modalFechar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Fechar fatura</h4>
            </div>
            <form id="formFechar" action="<?php echo base_url('financeiro/faturas/fechar') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Confirma o fechamento desta fatura?</p>
                    <input class="id_fatura" type="hidden" name="id_fatura" value=""/>
                    <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-inverse btn-sm" id="btnFechar">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal REABRIR FATURA -->
<div class="modal fade" id="modalReabrir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title ">Reabrir fatura</h4>
            </div>
            <form id="formFechar" action="<?php echo base_url('financeiro/faturas/reabrir') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Confirma a reabertura desta fatura?</p>
                    <input class="id_fatura" type="hidden" name="id_fatura" value=""/>
                    <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm" id="btnFechar">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VINCULAR/DESVINCULAR FATURAS DE TODOS OS CARTOES -->
<div class="modal fade" id="modalVincularFaturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Gerenciamento de vínculo de faturas</h4>
            </div>
            <form id="formVincularFaturas" action="<?php echo base_url('financeiro/faturas/vinculoFaturas') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Defina o mês e ano de referência para gerenciar o vínculo de todas as faturas dos cartões ativos:</p>
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label font-weight-bold" for="select_mes">
                                Mês de referência *
                            </label>
                            <select class="form-control" id="mes_referencia" name="mesReferencia">
                                <?php $mesAtual = date('m'); ?>
                                <option value="">
                                    << Selecione>>
                                </option>
                                <option value="01" <?= $mesAtual == '01' ? 'selected' : ''; ?>>01 - JANEIRO</option>
                                <option value="02" <?= $mesAtual == '02' ? 'selected' : ''; ?>>02 - FEVEREIRO</option>
                                <option value="03" <?= $mesAtual == '03' ? 'selected' : ''; ?>>03 - MARÇO</option>
                                <option value="04" <?= $mesAtual == '04' ? 'selected' : ''; ?>>04 - ABRIL</option>
                                <option value="05" <?= $mesAtual == '05' ? 'selected' : ''; ?>>05 - MAIO</option>
                                <option value="06" <?= $mesAtual == '06' ? 'selected' : ''; ?>>06 - JUNHO</option>
                                <option value="07" <?= $mesAtual == '07' ? 'selected' : ''; ?>>07 - JULHO</option>
                                <option value="08" <?= $mesAtual == '08' ? 'selected' : ''; ?>>08 - AGOSTO</option>
                                <option value="09" <?= $mesAtual == '09' ? 'selected' : ''; ?>>09 - SETEMBRO</option>
                                <option value="10" <?= $mesAtual == '10' ? 'selected' : ''; ?>>10 - OUTUBRO</option>
                                <option value="11" <?= $mesAtual == '11' ? 'selected' : ''; ?>>11 - NOVEMBRO</option>
                                <option value="12" <?= $mesAtual == '12' ? 'selected' : ''; ?>>12 - DEZEMBRO</option>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="control-label font-weight-bold" for="select_mes">
                                Ano de referência *
                            </label>
                            <select class="form-control" id="anoReferenciaSelect" name="anoReferencia">
                                <?php if ($yearsList) {
                                    foreach ($yearsList as $year) { ?>
                                        <option value="<?= $year ?>" <?= (date('Y') == $year ? 'selected' : '') ?>><?= $year ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <p class="note note-info"><i class="text-info fa fa-info-circle fa-fw fa-lg"></i>
                        Esta ação irá vincular ou desvincular do módulo de Lançamentos todas as faturas dos cartões ativos referentes ao mês e ano selecionados.
                    </p>
                    <!-- <p class="note note-info"><i class="text-info fa fa-info-circle fa-fw fa-lg"></i>
                        Todas as atualizações de valores das faturas serão refletidas automaticamente no módulo de Lançamentos
                    </p> -->
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                    <input id="desvincularFaturas" type="hidden" name="desvincularFaturas"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnDesvincular">
                        <i class="fa fa-unlink fa-fw"></i> Desvincular
                    </button>
                    <button class="btn btn-primary btn-sm" id="btnVincular">
                        <i class="fa fa-link fa-fw"></i> Vincular
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VINCULAR FATURA UNICA -->
<div class="modal fade" id="modalVincular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Vincular fatura</h4>
            </div>
            <form id="formFechar" action="<?php echo base_url('financeiro/faturas/vincular') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Confirma o vínculo desta fatura ao módulo de Lançamentos?</p>
                    <p class="note note-info"><i class="text-info fa fa-info-circle fa-fw fa-lg"></i> Todas as atualizações de valores desta fatura serão refletidas automaticamente no módulo de Lançamentos.</p>
                    <input class="idFatura" type="hidden" name="idFatura"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-success btn-sm" id="btnFechar">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal DESVINCULAR FATURA -->
<div class="modal fade" id="modalDesvincular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Desvincular fatura</h4>
            </div>
            <form id="formFechar" action="<?php echo base_url('financeiro/faturas/desvincular') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Confirma o desvinculo desta fatura do módulo de Lançamentos?</p>
                    <p class="note note-info"><i class="text-info fa fa-info-circle fa-fw fa-lg"></i> Todas as atualizações de valores desta fatura deixarão de ser refletidas automaticamente no módulo de Lançamentos.</p>
                    <input class="idFatura" type="hidden" name="idFatura"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-warning btn-sm" id="btnFechar">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal PAGAR FATURA -->
<div class="modal fade" id="modalPagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Pagar fatura</h4>
            </div>
            <form id="formPagar" action="<?php echo base_url('financeiro/faturas/pagar') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p>Confirma o pagamento desta fatura?</p>
                    <input id="id_fatura_pagar" type="hidden" name="id_fatura" value=""/>
                    <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="data_pagamento">Data do pagamento</label>
                            <input class="datepicker form-control" id="data_pagamento" type="text" name="data_pagamento"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="forma_pagamento">Forma de pagamento *</label>
                            <select name="forma_pagamento" id="forma_pagamento" class="form-control">
                                <option value="">
                                    << Selecione>>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $cartao) { ?>
                                        <option value="<?= $cartao->id_forma ?>"><?= $cartao->nome ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelPagar">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-success btn-sm" id="btnPagar">
                        <i class="fa fa-check fa-fw"></i> Pagar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#invoice-link-switch').change(function () {
            if ($(this).is(':checked')) {
                $('#auto-link-label').html('Auto vínculo ativado');
            } else {
                $('#auto-link-label').html('Auto vínculo desativado');
            }
        })

        $('#modalSearch').on('shown.bs.modal', function (e) {
            $('.descricao').focus();
        })

        $('#btnDesvincular').click(function (event) {
            var form = this;
            event.preventDefault();
            $('#desvincularFaturas').val(true);
            $('#formVincularFaturas').submit();
        });

        $(".i-copy-total").hover(function (e) {
            $(e.target).toggleClass('font-weight-bold')
        });

        $(".i-copy-total").click(function (e) {
            e = e || window.event;
            var target = e.target || e.srcElement,
                text = target.textContent.trim() || target.innerText.trim();

            var copyText = document.getElementById(this);
            var textArea = document.createElement("textarea");
            textArea.value = target.textContent.trim();
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("Copy");
            textArea.remove();
            console.log('text: ' + text)
            console.log('textArea: ' + textArea.value)

            Swal.fire({
                toast: true,
                position: "top-right",
                showConfirmButton: false,
                showCloseButton: true,
                timer: 2000,
                timerProgressBar: true,
                icon: 'success',
                title: 'Valor copiado!',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
        })

        let dia;
        $.ajax({
            url: 'faturas/ajaxDiaVencimentoFatura',
            type: 'POST',
            dataType: 'json',
            data: {
                id_cartao: $('#cartoes').val()
            },
            success: function (response) {
                dia = response;
            },
            error: function () {
                console.log('ERRO na função: ajaxDiaVencimentoFatura')
            }
        });

        $('#mes_referencia').change(function (e) {
            let input = $(this).val();
            let cartao = $('#id_cartao_nova_fatura').val();
            let ano = new Date().getFullYear();

            if (input == '') {
                $('#vencimento').val('');
                $('#vencimento').attr('placeholder', 'Selecione o mês de referência para exibir');
                return;
            }
            let mes = input;
            mes++
            if (mes == 13) {
                mes = '01'
                ano++
            } else {
                if (mes <= 9) {
                    mes = '0' + mes
                }
            }
            $('#vencimento').val(dia + '/' + mes + '/' + ano);
        });

        $('.alerta-usuario').each(function (key, value) {
            setTimeout(function () {
                $('.alerta-usuario').modal('show');
            }, 500)
        });

        $(document).on('change', '#select_periodos, #select_status', function () {
            $("#_form_filtro").submit();
        });

        $('#cartoes').change(function () {
            let id_cartao = $(this).val();
            $('#id_cartao').val(id_cartao);
            $('#form_cartao').submit();
        });

        $(document).on('click', '#_btn_detalhes', function () {
            var id = $(this).attr('id_fatura');
            $("#urlDetalhesFatura").val($(location).attr('href'));
            $('#id_fatura_detalhes').val(id);
            $('#form_detalhes').submit();
        });

        $(document).on('click', '.fechar', function (event) {
            $(".id_fatura").val($(this).attr('id_fatura'));
            $("#urlFecharFatura").val($(location).attr('href'));
        });

        $(document).on('click', '.vinculo', function (event) {
            $(".idFatura").val($(this).attr('id_fatura'));
            $(".urlAtual").val($(location).attr('href'));
        });

        $(".money").maskMoney();

        $('#pago').click(function (event) {
            var flag = $(this).is(':checked');
            if (flag == true) {
                $('#divPagamento').show();
            } else {
                $('#divPagamento').hide();
            }
        });

        $('#recebido').click(function (event) {
            var flag = $(this).is(':checked');
            if (flag == true) {
                $('#divRecebimento').show();
            } else {
                $('#divRecebimento').hide();
            }
        });

        $('#pagoEditar').click(function (event) {
            var flag = $(this).is(':checked');
            if (flag == true) {
                $('#divPagamentoEditar').show();
            } else {
                $('#divPagamentoEditar').hide();
            }
        });

        $("#formConfigurarFatura").validate({
            rules: {
                dia_vencimento: {
                    required: true
                }
            },
            messages: {
                dia_vencimento: {
                    required: 'Selecione o dia de vencimento'
                }
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }

        });

        $("#formNovaFatura").validate({
            rules: {
                mes_referencia: {
                    required: true
                },
            },
            messages: {
                mes_referencia: {
                    required: 'Selecione o mês de referência'
                },
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }
        });

        $("#formVincularFaturas").validate({
            rules: {
                mes_referencia: {
                    required: true
                },
            },
            messages: {
                mes_referencia: {
                    required: 'Selecione o mês de referência'
                },
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }
        });

        $("#formPagar").validate({
            rules: {
                forma_pagamento: {
                    required: true
                }
            },
            messages: {
                forma_pagamento: {
                    required: 'Selecione a forma de pagamento'
                },
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }

        });

        $(document).on('click', '#configurar_fatura', function () {
            let id_cartao = $('#cartoes').val();
            $('.id_cartao').val(id_cartao);
        });

        $(document).on('click', '#novaFatura', function () {
            $("#id_cartao_nova_fatura").val($('#cartoes').val());
        });

        $(document).on('click', '.excluir', function (event) {
            $("#idExcluir").val($(this).attr('id_fatura'));
        });

        $(document).on('click', '.pagar', function (event) {
            $("#id_fatura_pagar").val($(this).attr('id_fatura'));
        });

        $(document).on('click', '#pendencia', function () {
            $("#urlPendencia").val($(location).attr('href'));
        });

        $(document).on('click', '.editar', function (event) {
            $("#id_pendencia").val($(this).attr('id_pendencia'));
            $("#descricaoEditar").val($(this).attr('descricao'));
            $("#id_clienteEditar").val($(this).attr('id_cliente'));
            $("#data_pendenciaEditar").val($(this).attr('data_pendencia'));
            $("#valorEditar").val($(this).attr('valor'));
            $("#urlEditarPendencia").val($(location).attr('href'));
            var baixado = $(this).attr('baixado');
            if (baixado == 1) {
                $("#pagoEditar").attr('checked', true);
                $("#divPagamentoEditar").show();
            } else {
                $("#pagoEditar").attr('checked', false);
                $("#divPagamentoEditar").hide();
            }
        });
    });
</script>