<?php $situacao = $this->input->get('situacao');
$periodo = $this->input->get('periodo');
?>

<style type="text/css">

    label.error {
        color: #b94a48;
    }

    input.error {
        border-color: #b94a48;
    }

    input.valid {
        border-color: #5bb75b;
    }

    .table-bordeless td, .table-bordeless th {
        border: none;
    }

    table {
        font-family: Arial;
        font-size: 11px;
    }

</style>

<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) {
    if ($status_fatura == 1) {
        $disabled_lancamento_1 = '';
        $statusFatura = 'ABERTA';
        $label_status = 'default';
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

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $dateObj = DateTime::createFromFormat('!m', $mes_referencia);
    $month = $dateObj->format('M'); // March
    $nome_mes = padronizarString(strftime('%b', strtotime($month)));
    ?>
<?php } ?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2 style="font-size: 12pt">
            <i class="fa fa-credit-card fa-lg fa-fw"></i>
            Detalhes da Fatura: # <?= $id_fatura ?>
        </h2>
        <div class="panel-ctrls">
            <a href="<?php echo base_url() ?>financeiro/faturas" class="btn btn-sm btn-default"><i class="fa fa-arrow-left fa-fw"></i> Faturas</a>
            <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal" title="Filtrar faturas">
                <i class="fa fa-filter fa-fw"></i>
                Filtrar
            </button>
            <button href="#modalLancamento" id="novoLancamento" data-toggle="modal" class="btn btn-primary btn-sm"
                    title="Cadastrar novo lançamento"<?= $disabled_lancamento_1 ?>>
                <i class="fa fa-plus-square fa-fw"></i>
                Novo Lançamento
            </button>
        </div>
    </div>
    <div class="panel-body">
        <div class="col-lg-3">
            <div class="input-group">
                <span class="input-group-addon font-weight-bold">Referência:</span>
                <div class="input-group-addon"><span class="label label-inverse"><?= $nome_mes . ' / ' . $ano_referencia ?></span></div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="input-group">
                <span class="input-group-addon font-weight-bold">Vencimento:</span>
                <div class="input-group-addon"><span class="label label-inverse"><?= date(('d/m/Y'), strtotime($fatura->vencimento)) ?></span></div>
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
                    <td colspan="5">Nenhum lançamento encontrado</td>
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
                <span class="hidden" id="div_btn_marcar">
                    <button class="btn btn-default btn-sm marcar_desmarcar" id="marcar_todos" title="Marcar todos os lançamentos da fatura">
                        <i class="fa fa-check-square fa-fw"></i>
                        Marcar Todos
                    </button>
                    <button class="btn btn-default marcar_desmarcar btn-sm hidden" id="desmarcar_todos" title="Desmarcar todos os lançamentos da fatura">
                        <i class="fa fa-square-o fa-fw"></i>
                        Desmarcar Todos
                    </button>
                </span>
                <button class="btn btn-default btn-sm habilita_desabilita_soma" id="exibir_soma" title="Habilitar soma de lançamentos individuais">
                    <i class="fa fa-toggle-on fa-fw"></i>
                    Habilitar Soma
                </button>
                <button class="btn btn-default btn-sm habilita_desabilita_soma hidden" id="esconder_soma" title="Desabilitar soma de lançamentos individuais">
                    <i class="fa fa-toggle-off fa-fw"></i>
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
                    <th style="width: 500px !important;">Descrição</th>
                    <th>Parcela</th>
                    <th>Valor Parcela (R$)</th>
                    <th style="width: 100px !important;">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $debitoFatura = 0;

                foreach ($results as $r) {
                    foreach ($subresults as $s) {
                        if ($this->fatura_model->getLancamentoEditavel($r->id_lancamento, $mes_referencia, $ano_referencia)->num_rows() > 0) {
                            $disabled_lancamento_2 = '';
                        } else {
                            $disabled_lancamento_2 = 'disabled';
                        }

                        if ($s->id_lancamento == $r->id_lancamento) {


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

                            $data_compra = date(('d/m/Y'), strtotime($r->data_compra));

                            $debitoFatura += $r->valor_parcela;


                            echo '<tr>';
                            echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                            echo '<td>' . $data_compra . '</td>';
                            echo '<td>' . strtoupper($s->descricao) . '</td>';
                            echo '<td>' . $n_parcela . '/' . $total_parcelas . '</td>';
                            echo '<td class="valor_parcela" style=" color: ' . $color . '"><span>' . number_format($r->valor_parcela, 2, ',', '.') . '</span></td>';

                            if ($r->valor_total < 0) {
                                $valor = number_format(abs($r->valor_total), 2, ',', '.');
                            } else {
                                $valor = number_format($r->valor_total, 2, ',', '.');
                            }

                            echo '<td>';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                                echo '<button type="button" href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="btn btn-primary btn-sm editar" title="Detalhes" id_lancamento="' .
                                    $s->id_lancamento . '" descricao="' . $s->descricao . '" valor="' . $valor . '" data_compra="' .
                                    date('d/m/Y', strtotime($s->data_compra)) . '" parcelada="' . $s->compra_parcelada . '" estorno="' . $s->estorno . '" n_parcelas="' . $r->total_parcelas . '" valor_parcela="' .
                                    number_format($r->valor_parcela, 2, ',', '.') . '" ' . $disabled_lancamento_1 . ' ' . $disabled_lancamento_2 . '>
                                <i class="fa fa-search-plus fa-lg fa-fw"></i></button>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                                echo '<button type="button" href="#modalExcluir" data-toggle="modal" id_lancamento="' . $s->id_lancamento . '" class="btn btn-danger btn-sm excluir" title="Excluir" ' . $disabled_lancamento_1 . ' ' . $disabled_lancamento_2 . '>
                                            <i class="fa fa-trash-o fa-lg fa-fw"></i></button>';
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
                        <td colspan="2" style="text-align: left; font-weight: bold">(=) LANÇAMENTOS MARCADOS</td>
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
                    <?php echo number_format($debitoFatura, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; font-weight: bold">(=) SALDO TOTAL DA FATURA</td>
                <td colspan="1" style="text-align: right; font-weight: bold">
                    <strong><?php echo number_format($creditoFatura - $debitoFatura, 2, ',', '.') ?></strong>
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
            <form action="<?php echo current_url(); ?>" method="get" id="form_filtro">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-6" style="margin-left: 0">
                            <label class="tooltips font-weight-bold" title="Filtrar faturas por período específico">Período <i class="fa fa-info-circle fa-fw"></i></label>
                            <select name="periodo" id="select_periodos" class="form-control">
                                <option value="">Selecione o período</option>
                                <option value="3dias"<?php if ($periodo == '3dias') {
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
                            <label class="tip-top font-weight-bold" title="Filtrar faturas por status">Status <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" id="select_status" name="status">
                                <option value="">Selecione o status</option>
                                <option value="aberta"<?php if ($periodo == 'aberta') {
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
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Filtrar</button>
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
            <form id="formNovoLancamento" action="<?php echo base_url() ?>financeiro/faturas/novoLancamento" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricao" type="text" name="descricao"/>
                            <input id="urlLancamento" type="hidden" name="urlAtual" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valor" class="font-weight-bold">Valor da Compra *</label>
                            <input class="form-control money" id="valor" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="data_compra" class="font-weight-bold">Data da Compra</label>
                            <input class="form-control datepicker" id="data_compra" type="text" name="data_compra"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4" style="margin-top: -10px;" id="div_parcelada">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="parcelada" name="compra_parcelada" value="1">
                            </div>
                            <label for="parcelada" class="font-weight-bold">Compra parcelada?</label>
                        </div>

                        <div id="divParcelamento" class="hidden">
                            <div class="form-group col-lg-4">
                                <label for="qnt_parcelas" class="font-weight-bold">Nº Parcelas</label>
                                <select name="qnt_parcelas" id="qnt_parcelas" class="form-control">
                                    <option value="">-- Selecione --</option>
                                    <?php if ($parcelas) {
                                        foreach ($parcelas as $k => $v) { ?>
                                            <option value="<?= $k ?>"><?= $v ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="valor_parcela" class="font-weight-bold">Valor da Parcela *</label>
                                <input class="form-control money" id="valor_parcela" type="text" name="valor_parcela"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4" style="margin-top: -10px;">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="estorno" name="estorno" value="1">
                            </div>
                            <label for="estorno" class="font-weight-bold">Estorno</label>
                        </div>

                        <div class="col-xs-8">
                            <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-times fa-fw"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Registrar</button>

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
            <form id="formEditarLancamento" action="<?php echo base_url() ?>financeiro/faturas/editarLancamento" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricaoEditar" type="text" name="descricao"/>
                            <input id="urlLancamentoEditar" type="hidden" name="urlAtual" value=""/>
                            <input id="id_lancamentoEditar" type="hidden" name="id_lancamento" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valor" class="font-weight-bold">Valor da Compra *</label>
                            <input class="form-control money" id="valorEditar" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="data_compra" class="font-weight-bold">Data da Compra</label>
                            <input class="form-control datepicker" id="data_compraEditar" type="text" name="data_compra"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4" id="div_parceladaEditar">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="parceladaEditar" name="compra_parcelada" value="1">
                            </div>
                            <label for="parceladaEditar" class="font-weight-bold">Compra parcelada?</label>
                        </div>

                        <div id="divParcelamentoEditar" class="hidden">
                            <div class="form-group col-lg-4">
                                <label for="qnt_parcelasEditar" class="font-weight-bold">Nº Parcelas *</label>
                                <select name="qnt_parcelas" id="qnt_parcelasEditar" class="form-control">
                                    <option value="">-- Selecione --</option>
                                    <?php if ($parcelas) {
                                        foreach ($parcelas as $k => $v) { ?>
                                            <option value="<?= $k ?>"><?= $v ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="valor_parcelaEditar" class="font-weight-bold">Valor da Parcela *</label>
                                <input class="form-control money" id="valor_parcelaEditar" type="text" name="valor_parcela"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4" style="margin-top: -10px;">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="estornoEditar" name="estorno" value="1">
                            </div>
                            <label for="estornoEditar" class="font-weight-bold">Estorno</label>
                        </div>

                        <div class="col-xs-8">
                            <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-times fa-fw"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Registrar</button>

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
            <form id="formNovaFatura" action="<?php echo base_url() ?>financeiro/faturas/excluirLancamento" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir este lançamento?</p>
                    <input name="id_lancamento" id="idExcluir" type="hidden" value=""/>
                    <input id="urlExcluirLancamento" type="hidden" name="urlAtual" value=""/>
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
    $(document).ready(function ($) {
        var marcados = false;

        somaValorParcelas();

        $('#marcar_todos, #desmarcar_todos').click(function () {
            marcarTodosiCheck();
            $('#marcar_todos').toggleClass('hidden');
            $('#desmarcar_todos').toggleClass('hidden');
        });

        $('.habilita_desabilita_soma').click(function () {
            $('.th_soma').toggleClass('hidden');
            $('.td_soma').toggleClass('hidden');
            $('#div_btn_marcar').toggleClass('hidden');
            $('#somatorio_lancamentos').toggleClass('hidden');
            $('#exibir_soma').toggleClass('hidden');
            $('#esconder_soma').toggleClass('hidden');
        });

        $('.soma_parcelas').on('ifChanged', function (event) {
            const icheck = event.target.checked;
            somaValorParcelas();
        });

        // Calculate the total invoice amount from selected items only
        function somaValorParcelas() {
            console.log(marcados);
            var Soma = 0;
            // iterate through each td based on class and add the values
            $(".valor_parcela").each(function () {
                //Check if the checkbox is checked
                if ($(this).closest('tr').find('.soma_parcelas').is(':checked')) {
                    var value = $('span', this).text();
                    value = jquery_format(value);
                    console.log('valor do elemento: ' + value);
                    // add only if the value is number
                    if (!isNaN(value) && value.length != 0) {
                        Soma += parseFloat(value);
                    } else {
                        console.log('erro no método somaValorParcelas()');
                    }
                }
            });
            var Sum = br_format(Soma);

            $('#valor_soma_parcelas').text(Sum);
        }

        function marcarTodosiCheck() {
            if (marcados == false) {
                $(".soma_parcelas").each(function () {
                    $('.soma_parcelas').iCheck('check');
                    marcados = true;
                });
            } else {
                $(".soma_parcelas").each(function () {
                    $('.soma_parcelas').iCheck('uncheck');
                    marcados = false;
                });
            }
        }


        $('#qnt_parcelas').on('change', function () {
            var parcelas = $(this).val();
            var valor = $('#valor').val();

            var result = calculaValorParcela(parcelas, valor);
            $('#valor_parcela').val(result);
        });

        $('#qnt_parcelasEditar').on('change', function () {
            var parcelas = $(this).val();
            var valor = $('#valorEditar').val();

            var result = calculaValorParcela(parcelas, valor);
            $('#valor_parcelaEditar').val(result);
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

        $(".money").maskMoney();

        $('#modalLancamento').on('hidden.bs.modal', function () {
            $('#formNovoLancamento').trigger("reset");
        });


        var parcelada = $('#parcelada').iCheck('update')[0].checked;
        var estorno = $('#estorno').iCheck('update')[0].checked;

        $.each($(parcelada), function (key, value) {
            if (parcelada == true) {
                $('#divParcelamento').removeClass('hidden');
            } else {
                $('#divParcelamento').addClass('hidden');
            }
        });

        $.each($(estorno), function (key, value) {
            if (estorno == true) {
                $('#div_parcelada, #div_parceladaEditar').addClass('hidden');
            } else {
                $('#div_parcelada, #div_parceladaEditar').removeClass('hidden');
            }
        });

        $('#parcelada, #parceladaEditar').on('ifChanged', function (event) {
            mudaICheckParcelamento(event);
        });
        $('#estorno, #estornoEditar').on('ifChanged', function (event) {
            mudaICheckEstorno(event);
        });

        function mudaICheckParcelamento(event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#divParcelamento, #divParcelamentoEditar').removeClass('hidden');
            } else {
                $('#divParcelamento, #divParcelamentoEditar').addClass('hidden');
            }
        }

        function mudaICheckEstorno(event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#div_parcelada, #div_parceladaEditar').addClass('hidden');
            } else {
                $('#div_parcelada, #div_parceladaEditar').removeClass('hidden');
            }
        }

        $("#formNovoLancamento").validate({
            rules: {
                descricao: {required: true},
                valor: {required: true},
                data_compra: {required: false},
                qnt_parcelas: {required: true},
                valor_parcela: {required: true},

            },
            messages: {
                descricao: {required: 'Informe uma descrição'},
                valor: {required: 'Informe o valor da compra'},
                data_compra: {required: 'Informe a data da compra'},
                qnt_parcelas: {required: 'Informe o número de parcelas'},
                valor_parcela: {required: 'Informe o valor das parcelas'},
            }
        });

        $("#formEditarLancamento").validate({
            rules: {
                descricao: {required: true},
                valor: {required: true},
                data_compra: {required: false},
                qnt_parcelas: {required: true},
                valor_parcela: {required: true},

            },
            messages: {
                descricao: {required: 'Informe uma descrição'},
                valor: {required: 'Informe o valor da compra'},
                data_compra: {required: 'Informe a data da compra'},
                qnt_parcelas: {required: 'Informe o número de parcelas'},
                valor_parcela: {required: 'Informe o valor das parcelas'},
            }
        });


        $(document).on('click', '.excluir', function (event) {
            $("#idExcluir").val($(this).attr('id_lancamento'));
            $("#urlExcluirLancamento").val($(location).attr('href'));
        });

        $(document).on('click', '#novoLancamento', function () {
            $("#urlLancamento").val($(location).attr('href'));
        });

        $(document).on('click', '.editar', function (event) {
            $("#id_lancamentoEditar").val($(this).attr('id_lancamento'));
            $("#descricaoEditar").val($(this).attr('descricao'));
            $("#valorEditar").val($(this).attr('valor'));
            $("#valorParcelaEditar").val($(this).attr('valor_parcela'));
            $("#data_compraEditar").val($(this).attr('data_compra'));
            $("#valor_parcelaEditar").val($(this).attr('valor_parcela'));
            $("#qnt_parcelasEditar").val($(this).attr('n_parcelas'));
            $("#urlLancamentoEditar").val($(location).attr('href'));
            console.log($(this).attr('n_parcelas'));
            var estorno = $(this).attr('estorno');
            var parcelada = $(this).attr('parcelada');
            if (parcelada == 1) {
                $('#parceladaEditar').iCheck('check');
                $("#divParcelamentoEditar").removeClass('hidden');
            } else {
                $('#parceladaEditar').iCheck('uncheck');
                $("#divParcelamentoEditar").addClass('hidden');
            }
            if (estorno == 1) {
                $('#estornoEditar').iCheck('check');
                $("#div_parceladaEditar").addClass('hidden');
            } else {
                $('#estornoEditar').iCheck('uncheck');
                $("#div_parceladaEditar").removeClass('hidden');
            }

        });

        $(document).on('click', '#_btnExcluir', function (event) {
            var id = $("#idExcluir").val();

            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>index.php/financeiro/excluirLancamento",
                data: "id=" + id,
                dataType: 'json',
                success: function (data) {
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

        $(".datepicker").datepicker({dateFormat: 'dd/mm/yy'});

    });
</script>