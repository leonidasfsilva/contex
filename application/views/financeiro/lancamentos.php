<?php
$status_lancamentos = $this->input->get('status');
$tipo_lancamentos = $this->input->get('tipo');
$periodo_lancamentos = $this->input->get('periodo');
$inicio = $this->input->get('dataInicial');
$fim = $this->input->get('dataFinal');
?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-chart-line fa-lg fa-fw"></i>
            Lançamentos
        </h3>
        <div class="row mr5 ml5">
            <div class="panel-ctrls ml5">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                    <a href="#modalEntrada" id="entrada" data-toggle="modal" role="button" class="btn btn-success btn-sm tip-bottom" title="Registrar nova entrada">
                        <i class="fas fa-plus fa-fw"></i>
                        Nova Entrada
                    </a>
                    <a href="#modalSaida" id="saida" data-toggle="modal" role="button" class="btn btn-danger btn-sm tip-bottom" title="Registrar nova saída">
                        <i class="fas fa-plus fa-fw"></i>
                        Nova Saída
                    </a>
                <?php } ?>
            </div>
            <div class="panel-ctrls">
                <div class="btn-group" id="div_pesquisa">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" id="btn_pesquisa">
                        <i class="fas fa-search fa-fw"></i>
                        Pesquisar
                    </button>
                    <ul class="dropdown-menu">
                        <form action="<?php echo base_url('financeiro/lancamentos/pesquisa') ?>" style="margin: 0 15px 0 15px" method="get" autocomplete="off">
                            <div class="input-group">
                                <input type="text" class="form-control" id="input_pesquisa" name="search" placeholder="Pesquisar" style="margin-top: 6px;" required>
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search fa-fw"></i></button>
                                </span>
                            </div>
                        </form>
                    </ul>
                </div>
                <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal" title="Filtrar lançamentos">
                    <i class="fas fa-filter fa-fw"></i>
                    Filtrar
                </button>
            </div>

        </div>
    </div>
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
                <td colspan="2" style="text-align: left; font-weight: bold">(=) SALDO DISPONÍVEL</td>
                <td colspan="1" style="text-align: right; font-weight: bold">
                    <strong><?php echo number_format($total->total, 2, ',', '.') ?></strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php if (!$results) { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                Extrato de Lançamentos
            </h2>
        </div>
        <div class="panel-body panel-no-padding ">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                    <tr role="row">
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Valor (R$)</th>
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
                Extrato de Lançamentos
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
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Valor (R$)</th>
                        <th style="width: 130px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalReceita = 0;
                    $totalDespesa = 0;
                    $saldo = 0;
                    foreach ($results as $r) {
                        $vencimento = date(('d/m'), strtotime($r->data_lancamento));

                        if ($r->baixado == 0) {
                            $status = 'Pendente';
                            $label_status = 'warning';
                        } else {
                            $status = 'Efetivado';
                            $label_status = 'primary';
                        };

                        if ($r->tipo == 1) {
                            $color = 'green';
                            $label_tipo = 'success';
                            $tipo = 'ENTRADA';
                        } else {
                            $color = 'red';
                            $label_tipo = 'danger';
                            $tipo = 'SAÍDA';
                        }

                        if ($r->cliente_fornecedor) {
                            $fornecedor = $r->cliente_fornecedor;
                        } else {
                            $fornecedor = "&nbsp;";
                        }

                        if ($r->tipo == 1) {
                            $tipo = 'ENTRADA';
                        } else {
                            $tipo = 'SAÍDA';
                        }

                        foreach ($formasPagamento as $f) {
                            if ($f->id_forma == $r->forma_pgto) {
                                $forma_pgto = $f->nome;
                            }
                        }


                        echo '<tr>';
                        echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                        echo '<td>' . $vencimento . '</td>';
                        //                    echo '<td><span class="badge badge-' . $label . '">' . ucfirst($r->tipo) . '</span></td>';
                        echo '<td>' . strtoupper($r->descricao) . '<br><span class="small">' . ($fornecedor) . '</span></td>';
                        echo '<td><span class="label label-' . $label_tipo . '">' . strtoupper($tipo) . '</span><br><span class="label label-' . $label_status . '">' . strtoupper($status) . '</span></td>';
                        echo '<td><span class="valor_parcela" style=" color: ' . $color . '"><span>' . number_format($r->valor, 2, ',', '.') . '</span></span><br><span class="small">' . ($forma_pgto) . '</td>';

                        if ($r->valor < 0) {
                            $valor = number_format(abs($r->valor), 2, ',', '.');
                        } else {
                            $valor = number_format($r->valor, 2, ',', '.');
                        }

                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                            echo '<a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="btn btn-primary btn-sm editar" title="Detalhes" idLancamento="' .
                                $r->id_lancamento . '" descricao="' . $r->descricao . '" valor="' . $valor . '" vencimento="' .
                                date('d/m/Y', strtotime($r->data_lancamento)) . '" pagamento="' . date('d/m/Y', strtotime($r->data_pagamento)) . '" baixado="' .
                                $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></a>';
                            echo '<a href="#modalCopiar" style="margin-right: 1%" data-toggle="modal" class="btn btn-info btn-sm copiar" title="Copiar" idLancamento="' .
                                $r->id_lancamento . '" descricao="' . $r->descricao . '" valor="' . $valor . '" vencimento="' .
                                date('d/m/Y', strtotime($r->data_lancamento)) . '" pagamento="' . date('d/m/Y', strtotime($r->data_pagamento)) . '" baixado="' .
                                $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '">
                                <i class="fas fa-copy fa-lg fa-fw"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                            echo '<a href="#modalExcluir" data-toggle="modal" idLancamento="' . $r->id_lancamento . '" class="btn btn-danger btn-sm excluir" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw"></i></a>';
                        }

                        echo '</td>';
                        echo '</tr>';
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
            <?php if ($this->pagination->create_links()) { ?>
                <div class="panel-footer">
                    <?= $this->pagination->create_links() ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>

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
                <td colspan="2" style="text-align: left;">(+) SALDO PROVISÓRIO EM CONTA</td>
                <td colspan="1" style="text-align: right;">
                    <?php echo number_format($total_provisorio->total, 2, ',', '.') ?></td>
            </tr>
            <?php if ($saidas_pendentes->total) { ?>
                <tr>
                    <td colspan="2" style="text-align: left; color: red">(-) SALDO DE SAÍDAS A CONFIRMAR</td>
                    <td colspan="1" style="text-align: right; color: red">
                        <?php echo number_format($saidas_pendentes->total, 2, ',', '.') ?></td>
                </tr>
            <?php } ?>
            <?php if ($entradas_pendentes->total) { ?>
                <tr>
                    <td colspan="2" style="text-align: left; color: green">(+) SALDO DE ENTRADAS A CONFIRMAR</td>
                    <td colspan="1" style="text-align: right; color: green">
                        <?php echo number_format($entradas_pendentes->total, 2, ',', '.') ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="2" style="text-align: left; font-weight: bold">(=) SALDO DISPONÍVEL EM CONTA</td>
                <td colspan="1" style="text-align: right; font-weight: bold">
                    <strong><?php echo number_format($total->total, 2, ',', '.') ?></strong>
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
            <form action="<?php echo current_url(); ?>" method="get" id="form_filtro" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="tooltips font-weight-bold" title="Filtrar lançamentos por tipo">Tipo <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" id="select_tipo" name="tipo">
                                <option value="todos">
                                    << Todos>>
                                </option>
                                <option value="entrada" <?php if ($tipo_lancamentos == 'entrada') {
                                                            echo 'selected';
                                                        } ?>>ENTRADA
                                </option>
                                <option value="saida" <?php if ($tipo_lancamentos == 'saida') {
                                                            echo 'selected';
                                                        } ?>>SAÍDA
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="tooltips font-weight-bold" title="Filtrar lançamentos por status">Status <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" id="select_status" name="status">
                                <option value="todos">
                                    << Todos>>
                                </option>
                                <option value="efetivado" <?php if ($status_lancamentos == 'efetivado') {
                                                                echo 'selected';
                                                            } ?>>EFETIVADO
                                </option>
                                <option value="pendente" <?php if ($status_lancamentos == 'pendente') {
                                                                echo 'selected';
                                                            } ?>>PENDENTE
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6" style="margin-left: 0">
                            <label class="tooltips font-weight-bold" title="Filtrar lançamentos por período específico">Período <i class="fa fa-info-circle fa-fw"></i></label>
                            <select name="periodo" id="select_periodo" class="form-control">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <option value="todos" <?php if ($periodo_lancamentos == 'todos') {
                                                            echo 'selected';
                                                        } ?>>Todos
                                </option>
                                <option value="3dias" <?php if ($periodo_lancamentos == '3dias') {
                                                            echo 'selected';
                                                        } ?>>Últimos 3 dias
                                </option>
                                <option value="5dias" <?php if ($periodo_lancamentos == '5dias') {
                                                            echo 'selected';
                                                        } ?>>Últimos 5 dias
                                </option>
                                <option value="7dias" <?php if ($periodo_lancamentos == '7dias') {
                                                            echo 'selected';
                                                        } ?>>Últimos 7 dias
                                </option>
                                <option value="15dias" <?php if ($periodo_lancamentos == '15dias') {
                                                            echo 'selected';
                                                        } ?>>Últimos 15 dias
                                </option>
                                <option value="30dias" <?php if ($periodo_lancamentos == '30dias') {
                                                            echo 'selected';
                                                        } ?>>Últimos 30 dias
                                </option>
                                <option value="60dias" <?php if ($periodo_lancamentos == '60dias') {
                                                            echo 'selected';
                                                        } ?>>Últimos 60 dias
                                </option>
                                <option value="90dias" <?php if ($periodo_lancamentos == '90dias') {
                                                            echo 'selected';
                                                        } ?>>Últimos 90 dias
                                </option>
                                <option value="especifico" <?php if ($periodo_lancamentos == 'especifico') {
                                                                echo 'selected';
                                                            } ?>>PERÍODO ESPECÍFICO
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6" id="div_intervalo_data" hidden>
                            <label class="tooltips font-weight-bold" title="Filtrar lançamentos por intervalo de data">Período específico <i class="fa fa-info-circle fa-fw"></i></label>
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

<!-- Modal NOVA ENTRADA -->
<div class="modal fade" id="modalEntrada" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Registrar nova entrada</h4>
            </div>
            <form id="formReceita" action="<?= base_url('financeiro/lancamentos/entrada') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricao" type="text" name="descricao" />
                            <input id="urlEntrada" type="hidden" class="urlAtual" name="urlAtual" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedor">Fornecedor</label>
                            <input class="form-control" type="text" name="fornecedor" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valor" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="vencimento" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker" type="text" name="vencimento" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="recebido" name="recebido" value="1">
                            </div>
                            <label for="recebido" class="font-weight-bold">Pago?</label>
                        </div>

                        <div id="divRecebimento" class="hidden">
                            <div class="form-group col-lg-4">
                                <label for="recebimento" class="font-weight-bold">Data de Pagamento</label>
                                <input class="form-control datepicker" id="recebimento" type="text" name="recebimento" />
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="formaPgto" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" class="form-control">
                                    <option value="">
                                        << Selecione >>
                                    </option>
                                    <?php if ($formasPagamento) {
                                        foreach ($formasPagamento as $f) { ?>
                                            <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal NOVA SAIDA -->
<div class="modal fade" id="modalSaida" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Registrar nova saída</h4>
            </div>
            <form id="formDespesa" action="<?= base_url('financeiro/lancamentos/saida') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" type="text" name="descricao" />
                            <input class="urlAtual" type="hidden" name="urlAtual" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedor">Fornecedor</label>
                            <input class="form-control" type="text" name="fornecedor" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valor" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="vencimento" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker" type="text" name="vencimento" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="pago" name="pago" value="1">
                            </div>
                            <label for="pago" class="font-weight-bold">Pago?</label>
                        </div>
                        <div id="divRecebimento" class="hidden">
                            <div class="form-group col-lg-4">
                                <label for="pagamento" class="font-weight-bold">Data de Pagamento</label>
                                <input class="form-control datepicker" id="pagamento" type="text" name="pagamento" />
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="formaPgto" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" class="form-control">
                                    <option value="">
                                        << Selecione >>
                                    </option>
                                    <?php if ($formasPagamento) {
                                        foreach ($formasPagamento as $f) { ?>
                                            <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal DETALHES -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Detalhes do lançamento</h4>
            </div>
            <form id="formEditar" action="<?= base_url('financeiro/lancamentos/editar') ?>" method="post" autocomplete="off">
                <input class="urlAtual" type="hidden" name="urlAtual" value="" />
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricaoEditar">Descrição *</label>
                            <input class="form-control" id="descricaoEditar" type="text" name="descricao" />
                            <input type="hidden" id="idEditar" name="id" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedorEditar">Fornecedor</label>
                            <input class="form-control" id="fornecedorEditar" type="text" name="fornecedor" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="valorEditar" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" id="valorEditar" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="vencimentoEditar" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker" id="vencimentoEditar" type="text" name="vencimento" />
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="tipoEditar" class="font-weight-bold">Tipo</label>
                            <select class="form-control" name="tipo" id="tipoEditar">
                                <option value="1">ENTRADA</option>
                                <option value="2">SAÍDA</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="pagoEditar" name="pago" value="1">
                            </div>
                            <label for="pagoEditar" class="font-weight-bold">Pago?</label>
                        </div>
                        <div id="divPagamentoEditar" class="hidden">
                            <div class="form-group col-lg-4">
                                <label for="pagamentoEditar" class="font-weight-bold">Data Pagamento</label>
                                <input class="form-control datepicker" id="pagamentoEditar" type="text" name="pagamento" />
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="formaPgtoEditar" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" id="formaPgtoEditar" class="form-control">
                                    <option value="">
                                        << Selecione >>
                                    </option>
                                    <?php if ($formasPagamento) {
                                        foreach ($formasPagamento as $f) { ?>
                                            <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal COPIAR -->
<div class="modal fade" id="modalCopiar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Copiar lançamento</h4>
            </div>
            <form id="formCopiar" action="<?= base_url('financeiro/lancamentos/copiar') ?>" method="post" autocomplete="off">
                <input class="urlAtual" type="hidden" name="urlAtual" value="" />
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricaoCopiar">Descrição *</label>
                            <input class="form-control" id="descricaoCopiar" type="text" name="descricao" />
                            <input type="hidden" id="idCopiar" name="id" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedorCopiar">Fornecedor</label>
                            <input class="form-control" id="fornecedorCopiar" type="text" name="fornecedor" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="valorCopiar" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" id="valorCopiar" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="vencimentoCopiar" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker" id="vencimentoCopiar" type="text" name="vencimento" />
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="tipoCopiar" class="font-weight-bold">Tipo</label>
                            <select class="form-control" name="tipo" id="tipoCopiar">
                                <option value="1">ENTRADA</option>
                                <option value="2">SAÍDA</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="pagoCopiar" name="pago" value="1">
                            </div>
                            <label for="pagoCopiar" class="font-weight-bold">Pago?</label>
                        </div>
                        <div id="divPagamentoCopiar" class="hidden">
                            <div class="form-group col-lg-4">
                                <label for="pagamentoCopiar" class="font-weight-bold">Data Pagamento</label>
                                <input class="form-control datepicker" id="pagamentoCopiar" type="text" name="pagamento" />
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="formaPgtoCopiar" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" id="formaPgtoCopiar" class="form-control">
                                    <option value="">
                                        << Selecione >>
                                    </option>
                                    <?php if ($formasPagamento) {
                                        foreach ($formasPagamento as $f) { ?>
                                            <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Copiar</button>
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
                <h4 class="modal-title text-white ">Excluir lançamento</h4>
            </div>
            <form id="formExcluir" action="<?= base_url('financeiro/lancamentos/excluir'); ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir este lançamento?</p>
                    <input id="idExcluir" type="hidden" name="id" value="" />
                    <input class="urlAtual" type="hidden" name="urlAtual" value="" />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#div_pesquisa').on('shown.bs.dropdown', function(e) {
        $('#input_pesquisa').focus();
    });

    $('.dropdown-menu>form').click(function(e) {
        e.stopPropagation();
    });

    $(document).on('change', '#select_periodo, #select_situacao', function() {
        // $("#form_filtro").submit();
    });


    jQuery(document).ready(function($) {

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
                    // console.log('valor do elemento: ' + value);
                    // add only if the value is number
                    if (!isNaN(value) && value.length != 0) {
                        Soma += parseFloat(value);
                        // console.log('valor do elemento: ' + value);
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


        $('#select_periodo').change(function() {
            const value = $(this).val();
            if (value === 'especifico') {
                $('#div_intervalo_data').show();
            } else {
                $('#div_intervalo_data').hide();
                $('#dataInicial').val('');
                $('#dataFinal').val('');
            }
        });

        $('#select_periodo option:selected').each(function(index, element) {
            if ($(this).val() == 'especifico') {
                $('#div_intervalo_data').show();
            } else {
                $('#div_intervalo_data').hide();
                $('#dataInicial').val('');
                $('#dataFinal').val('');
            }
        });

        $('#modalReceita, #modalDespesa').on('hidden.bs.modal', function() {
            $('#formReceita, #formDespesa').trigger("reset");
        });

        var recebido = $('#recebido').iCheck('update')[0].checked;
        $.each($(recebido), function(key, value) {
            if (recebido == true) {
                $('#divRecebimento').removeClass('hidden');
            } else {
                $('#divRecebimento').addClass('hidden');
            }
        });

        $('#pago, #recebido, #pagoEditar, #pagoCopiar').on('ifChanged', function(event) {
            mudaICheck(event);
        });

        function mudaICheck(event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#divRecebimento, #divPagamentoEditar, #divPagamentoCopiar, #divPagamento').removeClass('hidden');
            } else {
                $('#divRecebimento, #divPagamentoEditar, #divPagamentoCopiar, #divPagamento').addClass('hidden');
            }
        }

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


        $("#formReceita").validate({
            rules: {
                descricao: {
                    required: true
                },
                cliente: {
                    required: false
                },
                valor: {
                    required: true
                },
                vencimento: {
                    required: false
                },
                formaPgto: {
                    required: true
                }

            },
            messages: {
                descricao: {
                    required: 'Informe a descrição'
                },
                cliente: {
                    required: 'Campo obrigatório'
                },
                valor: {
                    required: 'Informe o valor'
                },
                vencimento: {
                    required: 'Campo obrigatório'
                },
                formaPgto: {
                    required: 'Selecione a forma de pagamento'
                }
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

        $("#formDespesa").validate({
            rules: {
                descricao: {
                    required: true
                },
                cliente: {
                    required: false
                },
                valor: {
                    required: true
                },
                vencimento: {
                    required: false
                },
                formaPgto: {
                    required: true
                }

            },
            messages: {
                descricao: {
                    required: 'Informe a descrição'
                },
                cliente: {
                    required: 'Campo obrigatório'
                },
                valor: {
                    required: 'Informe o valor'
                },
                vencimento: {
                    required: 'Campo obrigatório'
                },
                formaPgto: {
                    required: 'Selecione a forma de pagamento'
                }
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

        $("#formEditar").validate({
            rules: {
                descricao: {
                    required: true
                },
                cliente: {
                    required: false
                },
                valor: {
                    required: true
                },
                vencimento: {
                    required: false
                },
                formaPgto: {
                    required: true
                }

            },
            messages: {
                descricao: {
                    required: 'Campo obrigatório'
                },
                cliente: {
                    required: 'Campo obrigatório'
                },
                valor: {
                    required: 'Campo obrigatório'
                },
                vencimento: {
                    required: 'Campo obrigatório'
                },
                formaPgto: {
                    required: 'Selecione a forma de pagamento'
                }
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
                cliente: {
                    required: false
                },
                valor: {
                    required: true
                },
                vencimento: {
                    required: false
                },
                formaPgto: {
                    required: true
                }

            },
            messages: {
                descricao: {
                    required: 'Campo obrigatório'
                },
                cliente: {
                    required: 'Campo obrigatório'
                },
                valor: {
                    required: 'Campo obrigatório'
                },
                vencimento: {
                    required: 'Campo obrigatório'
                },
                formaPgto: {
                    required: 'Selecione a forma de pagamento'
                }
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
            $("#idExcluir").val($(this).attr('idLancamento'));
        });

        $(document).on('click', '.editar, .copiar', function(event) {
            $("#idEditar, #idCopiar").val($(this).attr('idLancamento'));
            $("#descricaoEditar, #descricaoCopiar").val($(this).attr('descricao'));
            $("#fornecedorEditar, #fornecedorCopiar").val($(this).attr('fornecedor'));
            $("#valorEditar, #valorCopiar").val($(this).attr('valor'));
            $("#vencimentoEditar, #vencimentoCopiar").val($(this).attr('vencimento'));
            $("#pagamentoEditar, #pagamentoCopiar").val($(this).attr('pagamento'));
            $("#formaPgtoEditar, #formaPgtoCopiar").val($(this).attr('formaPgto'));
            $("#tipoEditar, #tipoCopiar").val($(this).attr('tipo'));
            var baixado = $(this).attr('baixado');
            if (baixado == 1) {
                $("#pagoEditar, #pagoCopiar").iCheck('check');
                $("#divPagamentoEditar, #divPagamentoCopiar").removeClass('hidden');
            } else {
                $("#pagoEditar, #pagoCopiar").iCheck('uncheck');
                $("#divPagamentoEditar, #divPagamentoCopiar").addClass('hidden');
            }
        });
    });
</script>