<?php
$situacao = $this->input->get('situacao');
$periodo = $this->input->get('periodo');
$cliente = $this->input->get('cliente');

if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
    if ($status_fatura == 1) {
        if ($id_usuario != getUserId()) {
            $disabled_lancamento_1 = 'disabled';
        } else {
            $disabled_lancamento_1 = '';
        }
        $statusFatura = 'ABERTA';
        $label_status = 'primary';
    } else if ($status_fatura == 2) {
        $disabled_lancamento_1 = 'disabled';
        $statusFatura = 'FUTURA';
        $label_status = 'inverse';
    } else {
        $disabledFatura = '';
        $disabled_lancamento_1 = 'disabled';
        $statusFatura = 'FECHADA';
        $label_status = 'inverse';
    }

    if ($fatura_paga == 1) {
        $pagamentoFatura = 'PAGA';
        $label_pgto = 'success';
    } else if ($fatura_paga == 2) {
        $pagamentoFatura = 'PENDENTE';
        $label_pgto = 'danger';
    } else {
        $pagamentoFatura = '';
        $label_pgto = '';
    }

    $dateFormatter = new \IntlDateFormatter(
        'pt_BR',
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::NONE,
        'America/Sao_Paulo',
        \IntlDateFormatter::GREGORIAN,
        "MMM"
    );

    $dateFormatterExtended = new \IntlDateFormatter(
        'pt_BR',
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::NONE,
        'America/Sao_Paulo',
        \IntlDateFormatter::GREGORIAN,
        "MMMM"
    );

    $dateObj = DateTime::createFromFormat('!m', ($mes_referencia));
    $nome_mes = str_replace('.', '', strtoupper($dateFormatterExtended->format($dateObj)));
    $creditoFatura = 0;
    $debitoFatura = 0;

    $n_cartao = explode(" ", trim(decriptar($cartao->numero)));
    $final = $n_cartao[3];
    $cartao_config = $cartao->apelido ? $cartao->apelido : $cartao->bandeira;
    $cartaoAlternativeLabel = $cartao->bandeira . ' - FINAL ' . $final;
?>
<?php } ?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3 style="margin: 0 15px 0 0;">
            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
            Fatura: #<?= $id_fatura . ' - ' . $nome_mes ?>
        </h3>
        <h3>
            <i class="fas fa-credit-card fa-lg fa-fw"></i>
            <?= $cartao->apelido ? $cartao->apelido : $cartaoAlternativeLabel ?>
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('financeiro/faturas?cartao=') . $id_cartao ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Faturas</a>
            <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal" title="Filtrar faturas">
                <i class="fas fa-filter fa-fw"></i>
                Filtrar
            </button>
            <button href="#modalLancamento" id="novoLancamento" data-toggle="modal" class="btn btn-primary btn-sm" title="Registrar novo lançamento" <?= $disabled_lancamento_1 ?>>
                <i class="fas fa-plus fa-fw"></i>
                Novo Lançamento
            </button>
        </div>
    </div>
    <div class="panel-body">
        <div class="col-lg-3">
            <div class="input-group">
                <span class="input-group-addon font-weight-bold">Referência:</span>
                <div class="input-group-addon"><span class="label label-default"><?= $nome_mes ?></span></div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="input-group">
                <span class="input-group-addon font-weight-bold">Vencimento:</span>
                <div class="input-group-addon"><span class="label label-default"><?= date(('d/m/Y'), strtotime($fatura->vencimento)) ?></span></div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="input-group">
                <span class="input-group-addon font-weight-bold">Status:</span>
                <div class="input-group-addon"><span class="label label-<?= $label_status ?>"><?= $statusFatura ?></span></div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="input-group">
                <span class="input-group-addon font-weight-bold">Pagamento:</span>
                <div class="input-group-addon"><span class="label label-<?= $label_pgto ?>"><?= $pagamentoFatura ?></span></div>
            </div>
        </div>
    </div>
</div>

<?php if (!$results) { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                Lançamentos da Fatura
            </h2>
        </div>
        <div class="panel-body panel-no-padding">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                    <tr role="row">
                        <th style="width: 100px !important;">Data Compra</th>
                        <th style="width: 500px !important;">Descrição</th>
                        <th>Parcela</th>
                        <th>Valor Parcela (R$)</th>
                        <th style="width: 100px !important;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5">Nenhum registro encontrado</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                Lançamentos da Fatura
            </h2>
            <div class="panel-ctrls">
                <a href="#" class="button-icon close-panel">
                    <i class="fas fa-times"></i>
                </a>
                <a href="#" class="button-icon expand">
                    <i class="fas fa-expand-arrows-alt expand-icon"></i>
                </a>
                <a href="#" class="button-icon panel-collapse">
                    <i class="fas fa-minus"></i>
                </a>
            </div>
            <div class="panel-ctrls">
                <span class="hidden" id="div_btn_marcar">
                    <button class="btn btn-default btn-sm marcar_desmarcar" id="marcar_todos" title="Marcar todos os lançamentos da fatura">
                        <i class="fas fa-check-square fa-fw"></i>
                        Marcar Todos
                    </button>
                    <button class="btn btn-default marcar_desmarcar btn-sm hidden" id="desmarcar_todos" title="Desmarcar todos os lançamentos da fatura">
                        <i class="far fa-square fa-fw"></i>
                        Desmarcar Todos
                    </button>
                </span>
                <button class="btn btn-default btn-sm habilita_desabilita_soma" id="exibir_soma" title="Habilitar soma de lançamentos individuais">
                    <i class="fas fa-toggle-on fa-fw"></i>
                    Habilitar Soma
                </button>
                <button class="btn btn-default btn-sm habilita_desabilita_soma hidden" id="esconder_soma" title="Desabilitar soma de lançamentos individuais">
                    <i class="fas fa-toggle-off fa-fw"></i>
                    Desabilitar Soma
                </button>
            </div>
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                    <tr role="row">
                        <th class="th_soma hidden" style="width: 10px !important;">Soma</th>
                        <th style="width: 100px !important;">Data Compra</th>
                        <th style="width: 300px !important;">Descrição</th>
                        <th style="width: 200px !important;">Terceiro</th>
                        <th>Parcela</th>
                        <th>Valor Parcela (R$) <br> Valor Compra (R$)</th>
                        <th style="width: 130px !important;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($results as $r) {
                        foreach ($subresults as $s) {
                            if ($s->id_lancamento == $r->id_lancamento) {
                                if (is_array($lancamentoEditavel)) {
                                    if (in_array($r->id_lancamento, $lancamentoEditavel, true)) {
                                        $disabled_lancamento_2 = '';
                                    } else {
                                        $disabled_lancamento_2 = 'disabled';
                                    }
                                }

                                if ($r->n_parcela < 10) {
                                    $n_parcela = str_pad($r->n_parcela, 2, '0', STR_PAD_LEFT);
                                } else {
                                    $n_parcela = $r->n_parcela;
                                }

                                if ($r->total_parcelas < 10) {
                                    $total_parcelas = str_pad($r->total_parcelas, 2, '0', STR_PAD_LEFT);
                                } else {
                                    $total_parcelas = $r->total_parcelas;
                                }

                                if ($s->estorno == 1) {
                                    $color = 'green';
                                } else {
                                    $color = 'black';
                                }

                                $data_compra = date(('d/m/y'), strtotime($r->data_compra));
                                $debitoFatura += $r->valor_parcela;

                                if ($r->valor_total < 0) {
                                    $valor = number_format(abs($r->valor_total), 2, ',', '.');
                                } else {
                                    $valor = number_format($r->valor_total, 2, ',', '.');
                                }

                                echo '<tr>';
                                echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                                echo '<td>' . $data_compra . '</td>';
                                echo '<td><a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="editar" title="Detalhes" id_lancamento="' .
                                    $s->id_lancamento . '" descricao="' . $s->descricao . '" valor="' . $valor . '" data_compra="' .
                                    date('d/m/Y', strtotime($s->data_compra)) . '" parcelada="' . $s->compra_parcelada . '" estorno="' . $s->estorno . '" n_parcelas="' . $r->total_parcelas .
                                    '" valor_parcela="' . number_format($r->valor_parcela, 2, ',', '.') . '" terceiros="' . $s->compra_terceiros . '" nome_cliente="' . $s->nome_cliente .
                                    '" id_cliente="' . $s->id_cliente . '" ' . $disabled_lancamento_1 . ' ' . $disabled_lancamento_2 . '>' .
                                    strtoupper($s->descricao) .
                                    '</a></td>';
                                echo '<td><a href="' . sprintf(base_url('financeiro/faturas/terceiros?mesReferencia=%s&anoReferencia=%s&cartao=%s&nome=%s'), $mes_referencia, $s->ano_referencia, $cartao->id_cartao, $s->nome_cliente) . '">' . strtoupper($s->nome_cliente) . '</a></td>';
                                echo '<td>' . $n_parcela . '/' . $total_parcelas . '</td>';
                                echo '<td class="valor_parcela" style=" color: ' . $color .
                                    '"><span>' . number_format($r->valor_parcela, 2, ',', '.') .
                                    '</span><br><span style="color: grey">' . number_format($r->valor_total, 2, ',', '.') .
                                    '</span></td>';

                                echo '<td>';
                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                                    echo '<button type="button" href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="btn btn-primary btn-sm editar" title="Detalhes" id_lancamento="' .
                                        $s->id_lancamento . '" descricao="' . $s->descricao . '" valor="' . $valor . '" data_compra="' .
                                        date('d/m/Y', strtotime($s->data_compra)) . '" parcelada="' . $s->compra_parcelada . '" estorno="' . $s->estorno . '" n_parcelas="' . $r->total_parcelas .
                                        '" valor_parcela="' . number_format($r->valor_parcela, 2, ',', '.') . '" terceiros="' . $s->compra_terceiros . '" 
                                    nome_cliente="' . $s->nome_cliente . '" id_cliente="' . $s->id_cliente . '" ' . $disabled_lancamento_1 . ' ' . $disabled_lancamento_2 . '>
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></button>';
                                    echo '<button type="button" href="#modalCopiar" style="margin-right: 1%" data-toggle="modal" class="btn btn-info btn-sm copiar" title="Detalhes" id_lancamento="' .
                                        $s->id_lancamento . '" descricao="' . $s->descricao . '" valor="' . $valor . '" data_compra="' .
                                        date('d/m/Y', strtotime($s->data_compra)) . '" parcelada="' . $s->compra_parcelada . '" estorno="' . $s->estorno . '" n_parcelas="' . $r->total_parcelas .
                                        '" valor_parcela="' . number_format($r->valor_parcela, 2, ',', '.') . '" terceiros="' . $s->compra_terceiros . '" 
                                    nome_cliente="' . $s->nome_cliente . '" id_cliente="' . $s->id_cliente . '" ' . $disabled_lancamento_1 . ' ' . $disabled_lancamento_2 . '>
                                <i class="fas fa-copy fa-lg fa-fw"></i></button>';
                                }
                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                                    echo '<button type="button" href="#modalExcluir" data-toggle="modal" id_lancamento="' . $s->id_lancamento . '" class="btn btn-danger btn-sm excluir" title="Excluir" ' . $disabled_lancamento_1 . ' ' . $disabled_lancamento_2 . '>
                                            <i class="fas fa-trash-alt fa-lg fa-fw"></i></button>';
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                    } ?>
                </tbody>
            </table>
            <div id="somatorio_lancamentos" class="panel-footer hidden">
                <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                    <thead>
                        <tr>
                            <th colspan="2" style="text-align: left !important;">Descrição</th>
                            <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
                        </tr>
                    </thead>
                    <tr>
                        <td colspan="2" style="text-align: left; font-weight: bold">(=) LANÇAMENTOS SELECIONADOS</td>
                        <td colspan="1" style="text-align: right; font-weight: bold" id="valor_soma_parcelas">
                            0,00
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php echo $this->pagination->create_links();
} ?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Resumo da Fatura
        </h2>
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
                <td colspan="2" style="text-align: left; color: green">(+) SALDO DE PAGAMENTO DA FATURA</td>
                <td colspan="1" style="text-align: right; color: green">
                    <?php echo number_format($creditoFatura, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; color: red">(-) SALDO DE DÉBITO DA FATURA</td>
                <td colspan="1" style="text-align: right; color: red">
                    <input type="hidden" id="debit-balance" value="<?php echo number_format($debitoFatura, 2, ',', '.') ?>">
                    <span style="cursor: pointer;" title="Copiar para área de transferência" id="i-copy-debit">
                        <i class="fas fa-copy fa-fw hidden" id="icon-debit"></i>
                        <?php echo number_format($debitoFatura, 2, ',', '.') ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; font-weight: bold">(=) SALDO TOTAL DA FATURA</td>
                <td colspan="1" style="text-align: right; font-weight: bold">
                    <input type="hidden" id="total-balance" value="<?php echo number_format($creditoFatura - $debitoFatura, 2, ',', '.') ?>">
                    <strong style="cursor: pointer;" title="Copiar para área de transferência" id="i-copy-total">
                        <i class="fas fa-copy fa-fw hidden" id="icon-total"></i>
                        <?php echo number_format($creditoFatura - $debitoFatura, 2, ',', '.') ?>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Modal FILTRAR -->
<div class="modal fade" id="modalFiltrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Filtrar lançamentos</h4>
            </div>
            <form action="<?= current_url(); ?>" method="get" id="form_filtro">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-6" style="margin-left: 0">
                            <label class="tooltips font-weight-bold" title="Filtrar faturas por período específico">Período <i class="fa fa-info-circle fa-fw"></i></label>
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
                        <div class="form-group col-lg-6">
                            <label class="tooltips font-weight-bold" for="select_clientes" title="Filtrar pendências por cliente">Cliente <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" id="select_clientes" name="cliente">
                                <option value="">Todos</option>
                                <?php if ($clientes) {
                                    foreach ($clientes as $d) { ?>
                                        <option value="<?= $d->id_clientes ?>" <?php if ($selected_cliente == $d->id_clientes) {
                                                                                    echo 'selected';
                                                                                } ?>><?= $d->nome ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fas fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-check fa-fw"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal NOVO LANÇAMENTO -->
<div class="modal fade" id="modalLancamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Novo lançamento na fatura</h4>
            </div>
            <form id="formNovoLancamento" action="<?= base_url('financeiro/faturas/novoLancamento/') . $id_fatura ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricao" type="text" name="descricao" />
                            <input id="urlLancamento" type="hidden" name="urlAtual" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valor" class="font-weight-bold">Valor do lançamento *</label>
                            <input class="form-control money valor" id="valor" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="data_compra" class="font-weight-bold">Data do lançamento</label>
                            <input class="form-control datepicker" id="data_compra" type="text" name="data_compra" />
                        </div>
                    </div>
                    <div class="row divContainerParcelamento">
                        <div class="form-group col-lg-4" style="margin-top: -10px;" id="div_parcelada">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control parcelada" name="compra_parcelada" value="1">
                            </div>
                            <label for="parcelada" class="font-weight-bold">Compra parcelada?</label>
                        </div>
                        <div id="divParcelamento" class="hidden">
                            <div class="form-group col-lg-4">
                                <label for="qnt_parcelas" class="font-weight-bold">Nº parcelas</label>
                                <select name="qnt_parcelas" id="qnt_parcelas" class="form-control qnt_parcelas">
                                    <option value="">
                                        << Selecione>>
                                    </option>
                                    <?php if ($parcelas) {
                                        foreach ($parcelas as $k => $v) { ?>
                                            <option value="<?= $k ?>"><?= $v ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="valor_parcela" class="font-weight-bold">Valor da parcela *</label>
                                <input class="form-control money parcela" id="valor_parcela" type="text" name="valor_parcela" />
                            </div>
                        </div>
                    </div>
                    <div class="row divContainerTerceiros">
                        <div class="form-group col-lg-6" style="margin-top: -10px;" id="div_terceiros">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control terceiros" name="compra_terceiros" value="1">
                            </div>
                            <label for="terceiros" class="font-weight-bold">Compra de terceiros?</label>
                        </div>
                        <div id="divTerceiros" class="hidden">
                            <div class="form-group col-lg-6">
                                <label for="nome_cliente" class="font-weight-bold">Nome do terceiro</label>
                                <input class="form-control" id="nome_cliente" type="text" name="nome_cliente" />
                                <input id="id_cliente" type="hidden" name="id_cliente" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4" style="margin-top: -10px;">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control estorno" name="estorno" value="1">
                            </div>
                            <label for="estorno" class="font-weight-bold">Estorno</label>
                        </div>
                        <div class="col-xs-8">
                            <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-times fa-fw"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal COPIAR LANÇAMENTO -->
<div class="modal fade" id="modalCopiar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Copiar lançamento</h4>
            </div>
            <form id="formCopiar" action="<?= base_url('financeiro/faturas/copiarLancamento') ?>" method="post" autocomplete="off">
                <input class="urlAtual" type="hidden" name="urlAtual" value="" />
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold">Descrição *</label>
                            <input class="form-control descricao" type="text" name="descricao" />
                            <input class="urlAtual" type="hidden" name="urlAtual" />
                            <input class="id_lancamento" type="hidden" name="id_lancamento" />
                            <input type="hidden" name="id_fatura" value="<?= $id_fatura ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold">Valor da Compra *</label>
                            <input class="form-control money valor" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold">Data da Compra</label>
                            <input class="form-control datepicker dataCompra" type="text" name="data_compra" />
                        </div>
                    </div>
                    <div class="row divContainerParcelamento">
                        <div class="form-group col-lg-4">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control parcelada" name="compra_parcelada" value="1">
                            </div>
                            <label class="font-weight-bold">Compra parcelada?</label>
                        </div>
                        <div class="divParcelas hidden">
                            <div class="form-group col-lg-4">
                                <label class="font-weight-bold">Nº Parcelas *</label>
                                <select name="qnt_parcelas" class="form-control qntParcelas">
                                    <option value="">
                                        << Selecione>>
                                    </option>
                                    <?php if ($parcelas) {
                                        foreach ($parcelas as $k => $v) { ?>
                                            <option value="<?= $k ?>"><?= $v ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label class="font-weight-bold">Valor da Parcela *</label>
                                <input class="form-control money parcela valorParcela" type="text" name="valor_parcela" />
                            </div>
                        </div>
                    </div>
                    <div class="row divContainerTerceiros">
                        <div class="form-group col-lg-6" style="margin-top: -10px;">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control terceiros" name="compra_terceiros" value="1">
                            </div>
                            <label class="font-weight-bold">Compra de terceiros?</label>
                        </div>
                        <div class="divTerceiros hidden">
                            <div class="form-group col-lg-6">
                                <label class="font-weight-bold">Cliente</label>
                                <input class="form-control nomeCliente" type="text" name="nome_cliente" />
                                <input class="idCliente" type="hidden" name="id_cliente" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4" style="margin-top: -10px;">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control estorno" name="estorno" value="1">
                            </div>
                            <label class="font-weight-bold">Estorno</label>
                        </div>
                        <div class="col-xs-8">
                            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-times fa-fw"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check fa-fw"></i> Copiar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal DETALHES LANÇAMENTO-->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Detalhes do lançamento</h4>
            </div>
            <form id="formEditarLancamento" action="<?php echo base_url('financeiro/faturas/editarLancamento') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold">Descrição *</label>
                            <input class="form-control descricao" type="text" name="descricao" />
                            <input class="urlAtual" type="hidden" name="urlAtual" />
                            <input class="id_lancamento" type="hidden" name="id_lancamento" />
                            <input type="hidden" name="id_fatura" value="<?= $id_fatura ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold">Valor da Compra *</label>
                            <input class="form-control money valor" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold">Data da Compra</label>
                            <input class="form-control datepicker dataCompra" type="text" name="data_compra" />
                        </div>
                    </div>
                    <div class="row divContainerParcelamento">
                        <div class="form-group col-lg-4">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control parcelada" name="compra_parcelada" value="1">
                            </div>
                            <label class="font-weight-bold">Compra parcelada?</label>
                        </div>
                        <div class="divParcelas hidden">
                            <div class="form-group col-lg-4">
                                <label class="font-weight-bold">Nº Parcelas *</label>
                                <select name="qnt_parcelas" class="form-control qntParcelas">
                                    <option value="">
                                        << Selecione>>
                                    </option>
                                    <?php if ($parcelas) {
                                        foreach ($parcelas as $k => $v) { ?>
                                            <option value="<?= $k ?>"><?= $v ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label class="font-weight-bold">Valor da Parcela *</label>
                                <input class="form-control money parcela valorParcela" type="text" name="valor_parcela" />
                            </div>
                        </div>
                    </div>
                    <div class="row divContainerTerceiros">
                        <div class="form-group col-lg-6" style="margin-top: -10px;">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control terceiros" name="compra_terceiros" value="1">
                            </div>
                            <label class="font-weight-bold">Compra de terceiros?</label>
                        </div>
                        <div class="divTerceiros hidden">
                            <div class="form-group col-lg-6">
                                <label class="font-weight-bold">Cliente</label>
                                <input class="form-control nomeCliente" type="text" name="nome_cliente" />
                                <input class="idCliente" type="hidden" name="id_cliente" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4" style="margin-top: -10px;">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control estorno" name="estorno" value="1">
                            </div>
                            <label class="font-weight-bold">Estorno</label>
                        </div>
                        <div class="col-xs-8">
                            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-times fa-fw"></i> Cancelar
                            </button>
                            <button type="button" href="#modalCopiar" data-dismiss="modal" data-toggle="modal" class="btn btn-info btn-sm"><i class="fa fa-copy fa-fw"></i> Copiar</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EXCLUIR LANÇAMENTO-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir lançamento</h4>
            </div>
            <form id="formNovaFatura" action="<?php echo base_url('financeiro/faturas/excluirLancamento') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Deseja realmente excluir este lançamento?</p>
                    <input name="id_lancamento" id="idExcluir" type="hidden" value="" />
                    <input id="urlExcluirLancamento" type="hidden" name="urlAtual" value="" />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnExcluir"><i class="fa fa-check fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#modalLancamento').on('shown.bs.modal', function(e) {
        $('#descricao').focus();
    })

    $(document).ready(function($) {
        $('#novoLancamento').click(function() {
            $(".descricao").val('');
            $(".valor").val('');
            $(".valorParcela").val('');
            $(".dataCompra").val('');
            $(".nomeCliente").val('');
            $(".qntParcelas").val('');
            $('.parcelada').iCheck('uncheck');
            $('.terceiros').iCheck('uncheck');
            $('.estorno').iCheck('uncheck');
        })

        $("#i-copy-total").hover(function() {
            $('#icon-total').toggleClass('hidden')
        });

        $("#i-copy-debit").hover(function() {
            $('#icon-debit').toggleClass('hidden')
        });


        $("#i-copy-total").click(function() {
            var copyText = document.getElementById("i-copy-total");
            var textArea = document.createElement("textarea");
            textArea.value = copyText.textContent.trim();
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("Copy");
            textArea.remove();

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

        $("#i-copy-debit").click(function(element) {
            var copyText = document.getElementById("i-copy-debit");
            var textArea = document.createElement("textarea");
            textArea.value = copyText.textContent.trim();
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("Copy");
            textArea.remove();

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

        $("#nome_cliente, .nomeCliente").autocomplete({
            source: "<?php echo base_url(); ?>financeiro/faturas/autoCompleteCliente",
            minLength: 1,
            select: function(event, ui) {
                $("#id_cliente, .idCliente").val(ui.item.label);
            }
        });

        var marcados = false;

        somaValorParcelas();

        $('#marcar_todos, #desmarcar_todos').click(function() {
            marcarTodosiCheck();
            $('#marcar_todos').toggleClass('hidden');
            $('#desmarcar_todos').toggleClass('hidden');
        });

        $('.habilita_desabilita_soma').click(function() {
            $('.th_soma').toggleClass('hidden');
            $('.td_soma').toggleClass('hidden');
            $('#div_btn_marcar').toggleClass('hidden');
            $('#somatorio_lancamentos').toggleClass('hidden');
            $('#exibir_soma').toggleClass('hidden');
            $('#esconder_soma').toggleClass('hidden');
        });

        $('.soma_parcelas').on('ifChanged', function(event) {
            const icheck = event.target.checked;
            somaValorParcelas();
        });

        // Calculate the total invoice amount from selected items only
        function somaValorParcelas() {
            var Soma = 0;
            // iterate through each td based on class and add the values
            $(".valor_parcela").each(function() {
                //Check if the checkbox is checked
                if ($(this).closest('tr').find('.soma_parcelas').is(':checked')) {
                    var value = $('span', this).text();
                    value = jquery_format(value);
                    // add only if the value is number
                    if (!isNaN(value) && value.length != 0) {
                        Soma += parseFloat(value);
                    } else {
                        // console.log('erro no método somaValorParcelas()');
                    }
                }
            });
            var Sum = br_format(Soma);

            $('#valor_soma_parcelas').text(Sum);
        }

        function marcarTodosiCheck() {
            if (marcados == false) {
                $(".soma_parcelas").each(function() {
                    $('.soma_parcelas').iCheck('check');
                    marcados = true;
                });
            } else {
                $(".soma_parcelas").each(function() {
                    $('.soma_parcelas').iCheck('uncheck');
                    marcados = false;
                });
            }
        }

        // Metodos para função de parcelamento
        $('#qnt_parcelas, #valor').on('change', function() {
            var parcelas = $('#qnt_parcelas').val();
            var valor = $('#valor').val();

            var result = calculaValorParcela(parcelas, valor);
            if (parcelas != '' && valor != '') {
                $('.parcela').val(result);
            } else {
                $('.parcela').val('');
            }
        });

        $('.qntParcelas, .valor').on('change', function() {
            var parcelas = undefined;
            var valor = undefined;
            var result = null;

            $('.modal-body').each(function() {
                parcelas = $(this).find('.qntParcelas').val();
                valor = $(this).find('.valor').val();
                valorParcela = $(this).find('.valorParcela');

                if ((parcelas != '' && valor != '') && (parcelas != undefined && valor != undefined)) {
                    result = calculaValorParcela(parcelas, valor)
                    valorParcela.val(result);
                } else {
                    valorParcela.val('');
                }
                console.log(
                    {
                        // 'qntParcela': parcelas
                    }
                )
            });
        });

        function calculaValorParcela(parcela, valor) {
            var parcelas = parcela;
            var valor = valor;

            valor = jquery_format(valor);

            var valor_parcela = valor / parcelas;

            valor_parcela = br_format(valor_parcela);

            return (valor_parcela);
        }

        function jquery_format(valor) {
            // Remove todos os .
            valor = valor.replace(/\./g, "");

            // Troca todas as , por .
            valor = valor.replace(",", ".");

            // Converte para float
            valor = parseFloat(valor);
            valor = parseFloat(valor) || 0.0;

            return valor;
        }

        function br_format(n) {
            return n.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
        }

        $('#parcelada, .parcelada').on('ifChanged', function(event) {
            mudaICheckParcelamento(event);
        });

        $('#estorno, .estorno').on('ifChanged', function(event) {
            mudaICheckEstorno(event);
        });

        $('#terceiros, .terceiros').on('ifChanged', function(event) {
            mudaICheckTerceiros(event);
        });

        function mudaICheckParcelamento(event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#divParcelamento, .divParcelas').removeClass('hidden');
            } else {
                $('#divParcelamento, .divParcelas').addClass('hidden');
            }
        }

        function mudaICheckEstorno(event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('.divContainerTerceiros, .divContainerParcelamento').addClass('hidden');
            } else {
                $('.divContainerTerceiros, .divContainerParcelamento').removeClass('hidden');
            }
        }

        function mudaICheckTerceiros(event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#divTerceiros, .divTerceiros').removeClass('hidden');
            } else {
                $('#divTerceiros, .divTerceiros').addClass('hidden');
            }
        }

        $("#formNovoLancamento").validate({
            rules: {
                descricao: {
                    required: true
                },
                valor: {
                    required: true
                },
                data_compra: {
                    required: false
                },
                qnt_parcelas: {
                    required: true
                },
                valor_parcela: {
                    required: true
                },

            },
            messages: {
                descricao: {
                    required: 'Informe uma descrição'
                },
                valor: {
                    required: 'Informe o valor da compra'
                },
                data_compra: {
                    required: 'Informe a data da compra'
                },
                qnt_parcelas: {
                    required: 'Informe o número de parcelas'
                },
                valor_parcela: {
                    required: 'Informe o valor das parcelas'
                },
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }

        });

        $("#formEditarLancamento").validate({
            rules: {
                descricao: {
                    required: true
                },
                valor: {
                    required: true
                },
                data_compra: {
                    required: false
                },
                qnt_parcelas: {
                    required: true
                },
                valor_parcela: {
                    required: true
                },

            },
            messages: {
                descricao: {
                    required: 'Informe uma descrição'
                },
                valor: {
                    required: 'Informe o valor da compra'
                },
                data_compra: {
                    required: 'Informe a data da compra'
                },
                qnt_parcelas: {
                    required: 'Informe o número de parcelas'
                },
                valor_parcela: {
                    required: 'Informe o valor das parcelas'
                },
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }


        });

        $("#formCopiar").validate({
            rules: {
                descricao: {
                    required: true
                },
                valor: {
                    required: true
                },
                data_compra: {
                    required: false
                },
                qnt_parcelas: {
                    required: true
                },
                valor_parcela: {
                    required: true
                },

            },
            messages: {
                descricao: {
                    required: 'Informe uma descrição'
                },
                valor: {
                    required: 'Informe o valor da compra'
                },
                data_compra: {
                    required: 'Informe a data da compra'
                },
                qnt_parcelas: {
                    required: 'Informe o número de parcelas'
                },
                valor_parcela: {
                    required: 'Informe o valor das parcelas'
                },
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }


        });

        $(document).on('click', '.excluir', function(event) {
            $("#idExcluir").val($(this).attr('id_lancamento'));
            $("#urlExcluirLancamento").val($(location).attr('href'));
        });

        $(document).on('click', '#novoLancamento', function() {
            $("#urlLancamento").val($(location).attr('href'));
        });

        $(document).on('click', '.editar, .copiar', function(event) {
            $(".id_lancamento").val($(this).attr('id_lancamento'));
            $(".idCliente").val($(this).attr('id_cliente'));
            $(".descricao").val($(this).attr('descricao'));
            $(".valor").val($(this).attr('valor'));
            $(".dataCompra").val($(this).attr('data_compra'));
            $(".nomeCliente").val($(this).attr('nome_cliente'));

            var estorno = $(this).attr('estorno');
            var terceiros = $(this).attr('terceiros');
            var parcelada = $(this).attr('parcelada');
            if (parcelada == 1) {
                $(".qntParcelas").val($(this).attr('n_parcelas'));
                $(".valorParcela").val($(this).attr('valor_parcela'));
                $('.parcelada').iCheck('check');
                $(".divParcelamento").removeClass('hidden');
            } else {
                // $(".qntParcelas").val($(this).attr('n_parcelas'));
                $(".valorParcela").val('');
                $('.parcelada').iCheck('uncheck');
                $(".divParcelamento").addClass('hidden');
            }
            if (estorno == 1) {
                $('.estorno').iCheck('check');
                $(".div_parcelada").addClass('hidden');
            } else {
                $('.estorno').iCheck('uncheck');
                $(".div_parcelada").removeClass('hidden');
            }
            if (terceiros == 1) {
                $('.terceiros').iCheck('check');
                $(".divTerceiros").removeClass('hidden');
            } else {
                $('.terceiros').iCheck('uncheck');
                $(".divTerceiros").addClass('hidden');
            }

        });

        $(document).on('click', '#_btnExcluir', function(event) {
            var id = $("#idExcluir").val();

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('financeiro/excluirLancamento'); ?>",
                data: "id=" + id,
                dataType: 'json',
                success: function(data) {
                    if (data.result == true) {
                        $("#btnCancelExcluir").trigger('click');
                        $("#divLancamentos").html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
                        $("#divLancamentos").load($(location).attr('href') + " #divLancamentos");
                        $("#divPosicaoConsolidada").load($(location).attr('href') + " #divPosicaoConsolidada");

                        $.toast({
                            //    heading: 'Receita adicionada com sucesso!',
                            text: '<h5>Lançamento excluído com sucesso</h5>',
                            position: 'top-center',
                            hideAfter: 2000,
                            showHideTransition: 'slide',
                            loader: false,
                            loaderBg: '#5fc55f',
                            bgColor: '#5bb75b',
                            icon: 'success'
                        });

                    } else {
                        $("#btnCancelExcluir").trigger('click');
                        alert('Ocorreu um erro ao tentar excluir produto.');
                    }
                }
            });
            return false;
        });
    });
</script>