<?php
$status_pendencia = $this->input->get('status');
$tipo_pendencia = $this->input->get('tipo');
$cliente = $this->input->get('cliente');
$inicio = $this->input->get('dataInicial');
$fim = $this->input->get('dataFinal');
$periodo_pendencia = $this->input->get('periodo');
?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-money-check-alt fa-lg fa-fw"></i>
            Controle de Pendências
        </h3>
        <div class="panel-ctrls">
            <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal" title="Filtrar pendências">
                <i class="fas fa-filter fa-fw"></i>
                Filtrar
            </button>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                <a href="#modalPendencia" id="pendencia" data-toggle="modal" role="button" class="btn btn-primary btn-sm tip-bottom"
                   title="Registrar nova pendência">
                    <i class="fas fa-plus fa-fw"></i>
                    Nova Pendência
                </a>
            <?php } ?>
        </div>
    </div>

    <?php if ($results) { ?>

        <div class="panel-heading">
            <h2>
                Saldo Resumido
            </h2>
        </div>
        <div class="panel-body panel-no-padding">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th colspan="2" style="text-align: left !important;">Descrição</th>
                    <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
                </tr>
                </thead>
                <tr>
                    <td colspan="2" style="text-align: left; color: green">(+) SALDO PARCIAL A RECEBER</td>
                    <td colspan="1" style="text-align: right; color: green">
                        <?php echo number_format($pendencias_credito->total, 2, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left; color: red">(-) SALDO PARCIAL A PAGAR</td>
                    <td colspan="1" style="text-align: right; color: red">
                        <?php echo number_format($pendencias_debito->total, 2, ',', '.') ?></td>
                </tr>
            </table>
        </div>
    <?php } ?>
</div>

<?php if ($results) { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                Registro de Pendências
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
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th style="text-align: left !important;">Vencimento</th>
                    <th style="text-align: left !important;">Descrição</th>
                    <th style="text-align: left !important;">Cliente</th>
                    <th style="text-align: left !important;">Tipo</th>
                    <th style="text-align: left !important;">Status</th>
                    <th style="text-align: left !important;">Valor (R$)</th>
                    <th style="width: 140px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $totalReceita = 0;
                $totalDespesa = 0;
                $saldo = 0;
                foreach ($results as $r) {
                    $data_pendencia = date(('d/m/Y'), strtotime($r->data_vencimento));

                    if ($r->tipo == 1) {
                        $tipo = 'ENTRADA';
                        $colorTipo = 'primary';
                    } else {
                        $tipo = 'SAÍDA';
                        $colorTipo = 'warning';
                    }

                    if ($r->quitado == 0) {
                        $status = 'Pendente';
                        $color = 'red';
                        $label = 'danger';
                        $icon = 'far fa-check-square';

                    } else {
                        $status = 'Pago';
                        $color = 'green';
                        $label = 'success';
                        $icon = 'fas fa-check-square';

                    };
                    $disabled = '';

                    if ($r->quitado == 1) {
                        $disabled = 'disabled';
                    }

                    echo '<tr>';
                    echo '<td>' . $data_pendencia . '</td>';
//                    echo '<td><span class="badge badge-' . $label . '">' . ucfirst($r->tipo) . '</span></td>';
                    echo '<td>' . strtoupper($r->descricao) . '</td>';
                    foreach ($clientes as $d) {
                        if ($r->id_cliente == $d->id_clientes) {
                            echo '<td><a href="'. base_url() .'clientes/visualizar/'.$d->id_clientes.'">' . strtoupper($d->nome) . '</a></td>';
                        }
                    }
                    echo '<td><span class="label label-' . $colorTipo . '">' . strtoupper($tipo) . '</span></td>';
                    echo '<td><span class="label label-' . $label . '">' . strtoupper($status) . '</span></td>';
                    echo '<td style=" color: ' . $color . '"> ' . number_format($r->valor, 2, ',', '.') . '</td>';

                    if ($r->valor < 0) {
                        $valor = number_format(abs($r->valor), 2, ',', '.');
                    } else {
                        $valor = number_format($r->valor, 2, ',', '.');
                    }

                    echo '<td>';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                        echo '<button ' . $disabled . ' href="#modalPagar" style="margin-right: 1%"  class="btn btn-success btn-sm pagar" data-toggle="modal" title="Pagar" id_pendencia="' . $r->id_pendencia . '">
                                <i class="' . $icon . ' fa-lg fa-fw"></i></button>';

                        echo '<button ' . $disabled . ' href="#modalEditar" style="margin-right: 1%" class="btn btn-primary btn-sm editar" data-toggle="modal" title="Editar" id_pendencia="' .
                            $r->id_pendencia . '" descricao="' . $r->descricao . '" valor="' . $valor . '" data_vencimento="' . date('d/m/Y', strtotime($r->data_vencimento)) .
                            '" pagamento="' . date('d/m/Y', strtotime($r->data_pagamento)) . '" quitado="' .
                            $r->quitado . '" id_cliente="' . $r->id_cliente . '" tipo="' . $r->tipo . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></button>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                        echo '<a href="#modalExcluir" data-toggle="modal" id_pendencia="' . $r->id_pendencia . '" class="btn btn-danger btn-sm excluir" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw"></i></a>';
                    }

                    echo '</td>';
                    echo '</tr>';
                } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php } else { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                Registro de Pendências
            </h2>
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th style="text-align: left !important;">Data</th>
                    <th style="text-align: left !important;">Descrição</th>
                    <th style="text-align: left !important;">Tipo</th>
                    <th style="text-align: left !important;">Status</th>
                    <th style="text-align: left !important;">Valor</th>
                    <th style="width: 140px">Ações</th>
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
    <?php echo $this->pagination->create_links();
} ?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Posição Consolidada
        </h2>
    </div>
    <div class="panel-body panel-no-padding">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <th colspan="2" style="text-align: left !important;">Descrição</th>
                <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
            </tr>
            </thead>
            <tr>
                <td colspan="2" style="text-align: left; color: green">(+) SALDO TOTAL A RECEBER</td>
                <td colspan="1" style="text-align: right; color: green">
                    <?php echo number_format($total_credito->total, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; color: red">(-) SALDO TOTAL A PAGAR</td>
                <td colspan="1" style="text-align: right; color: red">
                    <?php echo number_format($total_debito->total, 2, ',', '.') ?></td>
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
                <h4 class="modal-title">Filtrar pendências</h4>
            </div>
            <form action="<?php echo current_url(); ?>" method="get" id="form_filtro" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-4" style="margin-left: 0">
                            <label for="select_status" class="tooltips font-weight-bold" title="Filtrar pendências por status">Status <i class="fa fa-info-circle fa-fw"></i></label>
                            <select name="status" id="select_status" class="form-control">
                                <option value="todos">Todos</option>
                                <option value="pendente"<?php if ($status_pendencia == 'pendente') {
                                    echo 'selected';
                                } ?>>PENDENTE
                                </option>
                                <option value="pago" <?php if ($status_pendencia == 'pago') {
                                    echo 'selected';
                                } ?>>PAGO
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="tooltips font-weight-bold" for="select_tipo" title="Filtrar pendências por tipo">Tipo <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" id="select_tipo" name="tipo">
                                <option value="todos">Todos</option>
                                <option value="credito" <?php if ($tipo_pendencia == 'credito') {
                                    echo 'selected';
                                } ?>>ENTRADA
                                </option>
                                <option value="debito" <?php if ($tipo_pendencia == 'debito') {
                                    echo 'selected';
                                } ?>>SAÍDA
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
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
                    <div class="row">
                        <div class="form-group col-lg-6" style="margin-left: 0">
                            <label for="select_periodo" class="tooltips font-weight-bold" title="Filtrar pendências por período">Período <i class="fa fa-info-circle fa-fw"></i></label>
                            <select name="periodo" id="select_periodo" class="form-control">
                                <option value=""><< Selecione >></option>
                                <option value="todos" <?php if ($periodo_pendencia == 'todos') {
                                    echo 'selected';
                                } ?>>Todos
                                </option>
                                <option value="3dias"<?php if ($periodo_pendencia == '3dias') {
                                    echo 'selected';
                                } ?>>Últimos 3 dias
                                </option>
                                <option value="5dias" <?php if ($periodo_pendencia == '5dias') {
                                    echo 'selected';
                                } ?>>Últimos 5 dias
                                </option>
                                <option value="7dias" <?php if ($periodo_pendencia == '7dias') {
                                    echo 'selected';
                                } ?>>Últimos 7 dias
                                </option>
                                <option value="15dias" <?php if ($periodo_pendencia == '15dias') {
                                    echo 'selected';
                                } ?>>Últimos 15 dias
                                </option>
                                <option value="30dias" <?php if ($periodo_pendencia == '30dias') {
                                    echo 'selected';
                                } ?>>Últimos 30 dias
                                </option>
                                <option value="60dias" <?php if ($periodo_pendencia == '60dias') {
                                    echo 'selected';
                                } ?>>Últimos 60 dias
                                </option>
                                <option value="90dias" <?php if ($periodo_pendencia == '90dias') {
                                    echo 'selected';
                                } ?>>Últimos 90 dias
                                </option>
                                <option value="especifico"<?php if ($periodo_pendencia == 'especifico') {
                                    echo 'selected';
                                } ?>>PERÍODO ESPECÍFICO
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-6" id="div_intervalo_data" hidden>
                            <label class="tooltips font-weight-bold" title="Filtrar pendências por intervalo de data">Período específico <i class="fa fa-info-circle fa-fw"></i></label>
                            <div class="input-group">
                                <span class="input-group-addon">de</span>
                                <input type="text" class="form-control datepicker" id="dataInicial" name="dataInicial" value="<?= $inicio ?>">
                                <span class="input-group-addon">até</span>
                                <input type="text" class="form-control datepicker" id="dataFinal" name="dataFinal" value="<?= $fim ?>">
                            </div>
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

<!-- MODAL NOVA PENDENCIA -->
<div class="modal fade" id="modalPendencia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Registrar nova pendência</h4>
            </div>
            <form id="formPendencia" action="<?php echo base_url() ?>financeiro/pendencias/adicionar" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricao" type="text" name="descricao"/>
                            <input id="urlPendencia" type="hidden" name="urlAtual" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label for="id_cliente" class="font-weight-bold">Cliente *</label>
                            <select class="form-control" id="id_cliente" name="id_cliente">
                                <option value=""><< Selecione >></option>
                                <?php if ($clientes) {
                                    foreach ($clientes as $d) { ?>
                                        <option value="<?= $d->id_clientes ?>"><?= $d->nome ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="valor" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" id="valor" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="data_vencimento" class="font-weight-bold">Data de Vencimento</label>
                            <input class="form-control datepicker" id="data_vencimento" type="text" name="data_vencimento"/>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="tipo" class="font-weight-bold" title="ENTRADA: entrada nos lançamentos / SAÍDA: saída nos lançamentos">Tipo de Pendência *<i
                                        class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" name="tipo" id="tipo">
                                <option value=""><< Selecione >></option>
                                <option value="1">ENTRADA</option>
                                <option value="2">SAÍDA</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR PENDENCIA -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Editar pendência</h4>
            </div>
            <form id="formPendenciaEditar" action="<?php echo base_url() ?>financeiro/pendencias/editar" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricaoEditar">Descrição *</label>
                            <input class="form-control" id="descricaoEditar" type="text" name="descricao"/>
                            <input id="urlEditarPendencia" type="hidden" name="urlAtual" value=""/>
                            <input type="hidden" id="id_pendencia" name="id_pendencia" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label for="id_clienteEditar" class="font-weight-bold">Cliente *</label>
                            <select class="form-control" id="id_clienteEditar" name="id_cliente">
                                <option value=""><< Selecione >></option>
                                <?php if ($clientes) {
                                    foreach ($clientes as $d) { ?>
                                        <option value="<?= $d->id_clientes ?>"><?= $d->nome ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="valorEditar" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" id="valorEditar" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="data_pendenciaEditar" class="font-weight-bold">Data de Vencimento</label>
                            <input class="form-control datepicker" id="data_vencimentoEditar" type="text" name="data_vencimento"/>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="tipoEditar" class="font-weight-bold" title="ENTRADA: entrada nos lançamentos / SAÍDA: saída nos lançamentos">Tipo de Pendência *<i
                                        class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" name="tipo" id="tipoEditar">
                                <option value=""><< Selecione >></option>
                                <option value="1">ENTRADA</option>
                                <option value="2">SAIDA</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EXCLUIR-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir pendência</h4>
            </div>
            <form id="formNovaFatura" action="<?php echo base_url(); ?>financeiro/pendencias/excluir" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir esta pendência?</p>
                    <input name="id" id="idExcluir" type="hidden" value=""/>
                    <input id="urlExcluir" type="hidden" name="urlAtual" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal PAGAR-->
<div class="modal fade" id="modalPagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Pagar pendência</h4>
            </div>
            <form id="formPagar" action="<?php echo base_url() ?>financeiro/pendencias/pagar" method="post" autocomplete="off">
                <div class="modal-body">
                    <p>Deseja realmente marcar como <label class="label label-success">PAGO</label> esta pendência?</p>
                    <input id="idPagar" type="hidden" name="id_pendencia" value=""/>
                    <input id="urlPagarPendencia" type="hidden" name="urlAtual" value=""/>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="data_pagamento">Data do pagamento</label>
                            <input class="datepicker form-control" id="data_pagamento" type="text" name="data_pagamento"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="forma_pagamento">Forma de pagamento *</label>
                            <select name="forma_pagamento" id="forma_pagamento" class="form-control">
                                <option value=""><< Selecione >></option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $f) { ?>
                                        <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-success btn-sm">
                        <i class="fa fa-check fa-fw"></i> Pagar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">


    jQuery(document).ready(function ($) {

        $('#select_periodo').change(function () {
            const value = $(this).val();
            if (value === 'especifico') {
                $('#div_intervalo_data').show();
            } else {
                $('#div_intervalo_data').hide();
                $('#dataInicial').val('');
                $('#dataFinal').val('');
            }
        });

        $('#select_periodo option:selected').each(function (index, element) {
            if ($(this).val() == 'especifico') {
                $('#div_intervalo_data').show();
            } else {
                $('#div_intervalo_data').hide();
                $('#dataInicial').val('');
                $('#dataFinal').val('');
            }
        });

        $("#formPagar").validate({
            rules: {
                forma_pagamento: {required: true}
            },
            messages: {
                forma_pagamento: {required: 'Selecione a forma de pagamento'},
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

        $("#formPendencia").validate({
            rules: {
                descricao: {required: true},
                id_cliente: {required: true},
                valor: {required: true},
                vencimento: {required: false},
                tipo: {required: true},

            },
            messages: {
                descricao: {required: 'Informe a descrição'},
                id_cliente: {required: 'Selecione o cliente'},
                valor: {required: 'Informe o valor'},
                vencimento: {required: 'Campo obrigatório'},
                tipo: {required: 'Selecione o tipo de pendência'},
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

        $("#formPendenciaEditar").validate({
            rules: {
                descricao: {required: true},
                id_cliente: {required: true},
                valor: {required: true},
                vencimento: {required: false},
                tipo: {required: true}

            },
            messages: {
                descricao: {required: 'Informe a descrição'},
                id_cliente: {required: 'Selecione o cliente'},
                valor: {required: 'Informe o valor'},
                vencimento: {required: 'Campo obrigatório'},
                tipo: {required: 'Selecione o tipo de pendência'}
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

        $(document).on('click', '.excluir', function (event) {
            $("#idExcluir").val($(this).attr('id_pendencia'));
            $("#urlExcluir").val($(location).attr('href'));
        });

        $(document).on('click', '.pagar', function (event) {
            $("#idPagar").val($(this).attr('id_pendencia'));
            $("#urlPagarPendencia").val($(location).attr('href'));
        });

        $(document).on('click', '#pendencia', function () {
            $("#urlPendencia").val($(location).attr('href'));
        });

        $(document).on('click', '.editar', function (event) {
            $("#id_pendencia").val($(this).attr('id_pendencia'));
            $("#descricaoEditar").val($(this).attr('descricao'));
            $("#id_clienteEditar").val($(this).attr('id_cliente'));
            $("#data_vencimentoEditar").val($(this).attr('data_vencimento'));
            $("#valorEditar").val($(this).attr('valor'));
            $("#tipoEditar").val($(this).attr('tipo'));
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

        $(document).on('click', '#btnExcluir', function (event) {
            var id = $("#idExcluir").val();

            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>index.php/financeiro/excluirPendencia",
                data: "id=" + id,
                dataType: 'json',
                success: function (data) {
                    if (data.result == true) {
                        $("#btnCancelExcluir").trigger('click');
                        $("#divLancamentos").html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
                        $("#divLancamentos").load($(location).attr('href') + " #divLancamentos");
                        $("#divSaldoResumido").load($(location).attr('href') + " #divSaldoResumido");
                        $("#divPosicaoConsolidada").load($(location).attr('href') + " #divPosicaoConsolidada");

                        $.toast({
                            //    heading: 'Pendência excluída com sucesso!',
                            text: '<h5>Pendência excluída com sucesso!</h5>',
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

        function pagarPendencia() {
            var id = $("#idPagar").val();
            var data_pagamento = $("#data_pagamento").val();
            var forma_pagamento = $("#forma_pagamento").val();

            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>index.php/financeiro/pagarPendencia",
                data: {
                    id: id,
                    data_pagamento: data_pagamento,
                    forma_pagamento: forma_pagamento
                },
                dataType: 'json',
                success: function (data) {
                    if (data.result == true) {
                        $("#btnCancelPagar").trigger('click');
                        $("#divLancamentos").html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
                        $("#divLancamentos").load($(location).attr('href') + " #divLancamentos");
                        $("#divSaldoResumido").load($(location).attr('href') + " #divSaldoResumido");
                        $("#divPosicaoConsolidada").load($(location).attr('href') + " #divPosicaoConsolidada");

                        $.toast({
                            //    heading: 'Pendência excluída com sucesso!',
                            text: '<h5>Pendência paga com sucesso!</h5>',
                            position: 'top-center',
                            hideAfter: 2000,
                            showHideTransition: 'slide',
                            loader: false,
                            loaderBg: '#5fc55f',
                            bgColor: '#5bb75b',
                            icon: 'success'
                        });

                    } else {
                        $("#btnCancelPagar").trigger('click');
                        alert('Ocorreu um erro ao tentar pagar a pendência.');
                    }
                }
            });
            return false;
        }

    });
</script>