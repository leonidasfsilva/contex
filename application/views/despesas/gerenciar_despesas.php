<?php
$situacao = $this->input->get('situacao');
$periodo  = $this->input->get('periodo');
?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-money-bill-transfer fa-lg fa-fw"></i>
            Controle de Despesas
        </h3>
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
                    <!--<li>-->
                    <!--    <a href="#modalConfiguracoes" data-toggle="modal" id="configurar_fatura">-->
                    <!--        <i class="fas fa-cog fa-fw pull-right"></i>-->
                    <!--        <span>Configurações</span>-->
                    <!--    </a>-->
                    <!--</li>-->
                </ul>
            </div>
            <button href="#modalGerenciarVinculoDespesas" id="vincularDespesas" data-toggle="modal" role="button" class="btn btn-primary btn-sm tip-bottom" title="Vínculo de despesas">
                <i class="fas fa-link fa-fw"></i>
                <text class="visible-lg-inline">Gerenciar Vínculo</text>
            </button>
            <button href="#modalNovaDespesa" id="novaDespesa" data-toggle="modal" role="button" class="btn btn-primary btn-sm tip-bottom" title="Registrar nova despesa">
                <i class="fas fa-plus fa-fw"></i>
                Nova Despesa
            </button>
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
            <tr class="font-weight-bold">
                <td colspan="2" style="text-align: left; color: green">SALDO DE DESPESAS QUITADAS</td>
                <td colspan="1" style="text-align: right; color: green">
                    <?php if (isset($saldoQuitado)) echo number_format($saldoQuitado->total, 2, ',', '.');
                    else echo '0,00' ?></td>
            </tr>
            <?php if (isset($saldoVencidas) && $saldoVencidas->total > 0) { ?>
                <tr class="font-weight-bold">
                    <td colspan="2" style="text-align: left; color: red">SALDO DE DESPESAS VENCIDAS</td>
                    <td colspan="1" style="text-align: right; color: red">
                        <?php echo number_format($saldoVencidas->total, 2, ',', '.') ?></td>
                </tr>
            <?php } ?>
            <tr class="font-weight-bold">
                <td colspan="2" style="text-align: left">SALDO DE FATURAS A VENCER</td>
                <td colspan="1" style="text-align: right">
                    <?php if (isset($saldoPendente)) echo number_format($saldoPendente->total, 2, ',', '.');
                    else echo '0,00' ?></td>
            </tr>
        </table>
    </div>
</div>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Registro de Despesas
        </h2>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <!--<th>Data Cadastro</th>-->
                <th>Descrição<br>Fornecedor</th>
                <th>Valor (R$)<br>Forma Pagamento</th>
                <th>Parcelamento<br>Terceiro</th>
                <th>Tipo Despesa<br>Integração Auto</th>
                <th style="width: 165px">Ações<br><br></th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($results) && $results):
                foreach ($results as $r):
                    $valor                = number_format($r->valor_total, 2, ',', '.');
                    $valor_parcela        = null;
                    $terceiro             = null;
                    $parcelas             = null;
                    $iconObs              = null;
                    $labelStatusClass     = 'warning';
                    $activateBtnTitle     = 'Ativar integração automática';
                    $activeBtnClass       = 'btn btn-deeporange btn-sm ativar';
                    $activeBtnIconClass   = 'fas fa-stop fa-lg fa-fw';
                    $activeBtnModalTarget = '#modalAtivar';
                    $colorValue           = 'text-alizarin';
                    $activeIcon           = '<i class="fas fa-stop fa-fw text-danger"></i> <span class="badge badge-red">INATIVA</span>';
                    $iconValue            = '<i class="far fa-arrow-left fa-rotate-by fa-fw" style="--fa-rotate-angle: 45deg !important;"></i>-';
                    $tipo                 = '<i class="far fa-arrows-rotate fa-fw text-primary"></i> <span class="badge badge-primary">RECORRENTE</span>';


                    if ($r->observacoes) {
                        $iconObs = '
                                <i class="fas fa-comment-dots fa-fw" title="Observações adicionais"></i>
                            ';
                    }

                    if ($r->valor_parcela) {
                        $valor_parcela = number_format($r->valor_parcela, 2, ',', '.');
                    }

                    if ($r->despesa_parcelada) {
                        $parcelas = '<span class="text-orange font-weight-bold">' . abs($r->total_parcelas) . ' x ' . $valor_parcela . '</span>';
                    }

                    if ($r->despesa_terceiros == 1) {
                        $terceiro   = $r->nome_terceiro;
                        $colorValue = 'text-green';
                        $iconValue  = '<i class="far fa-arrow-right fa-rotate-by fa-fw" style="--fa-rotate-angle: 45deg !important;"></i>';
                    }

                    if ($r->tipo_despesa == 1) {
                        $tipo = '<i class="far fa-circle-1 fa-fw text-warning"></i> <span class="badge badge-orange">ÚNICA</span>';
                    }

                    if ($r->auto_vinculo == 1) {
                        $activeIcon           = '<i class="fas fa-play fa-fw text-success"></i> <span class="badge badge-green">ATIVA</span>';
                        $labelStatusClass     = 'success';
                        $activateBtnTitle     = 'Desativar integração automática';
                        $activeBtnClass       = 'btn btn-green btn-sm desativar';
                        $activeBtnIconClass   = 'fas fa-play fa-lg fa-fw';
                        $activeBtnModalTarget = '#modalDesativar';
                    }

                    echo '<tr>';
                    // echo '<td>' . date(('d/m/Y'), strtotime($r->criado_em)) . '</td>';

                    echo '<td class="font-weight-bold"><a href="#modalEditar" data-toggle="modal" class="editar mr1" title="#' . $r->id . '" id_despesa="' .
                        $r->id . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor_parcela="' . $valor_parcela . '" valor="' . $valor .
                        '" dia_vencimento="' . $r->dia_vencimento . '" data_pagamento="' . date('d/m/Y', strtotime($r->data_pagamento)) . '" total_parcelas="' . $r->total_parcelas .
                        '" fornecedor="' . $r->fornecedor . '" despesa_parcelada="' . $r->despesa_parcelada . '" despesa_terceiros="' . $r->despesa_terceiros . '" nome_terceiro="' . $r->nome_terceiro .
                        '" forma_pagamento="' . $r->id_forma_pagamento . '" tipo="' . $r->tipo_despesa . '">' . strtoupper($r->descricao) . $iconObs . '<br><span class="small text-muted" >' . ($r->fornecedor) . '</span></a></td>';

                    echo '<td class="font-weight-bold"><span class="font-11 font-weight-bold ' . $colorValue . '">' . $iconValue . ($valor) . '</span><br><span class="small text-muted">' . ($r->descricao_pagamento) . '</span></td>';

                    echo '<td class="font-weight-bold">' . $parcelas . '
                            <br><span class="small text-muted">' . $terceiro . '</span></td>';

                    echo '<td>' . $tipo . '<br>' . $activeIcon . '</td>';

                    echo '<td>';

                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eDespesas')):
                        echo '<a href="' . base_url('financeiro/despesas/detalhes/') . $r->id . '" type="button" id="btn_detalhes" style="margin-right: 1%" class="btn btn-primary btn-sm detalhes" title="Acessar vínculos da despesa" id_despesa="' .
                            $r->id . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i>
                              </a>';

                        echo '<button type="button" href="#modalCopiar" data-toggle="modal" class="btn btn-info btn-sm copiar mr1" title="Copiar" id_despesa="' .
                            $r->id . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor_parcela="' . $valor_parcela . '" valor="' . $valor .
                            '" dia_vencimento="' . $r->dia_vencimento . '" data_pagamento="' . date('d/m/Y', strtotime($r->data_pagamento)) . '" total_parcelas="' . $r->total_parcelas .
                            '" fornecedor="' . $r->fornecedor . '" despesa_parcelada="' . $r->despesa_parcelada . '" despesa_terceiros="' . $r->despesa_terceiros . '" nome_terceiro="' . $r->nome_terceiro .
                            '" forma_pagamento="' . $r->id_forma_pagamento . '" tipo="' . $r->tipo_despesa . '">
                                <i class="fas fa-copy fa-lg fa-fw"></i>
                              </button>';

                        echo '<button href="' . $activeBtnModalTarget . '" class="mr1 ' . $activeBtnClass . '" data-toggle="modal" title="' . $activateBtnTitle . '" id_despesa="' . $r->id . '">
                                <i class="' . $activeBtnIconClass . '"></i>
                              </button>';
                    endif;

                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dDespesas')):
                        echo '<a href="#modalExcluir" data-toggle="modal" id_despesa="' . $r->id . '" class="btn btn-danger btn-sm excluir" title="Excluir despesa">
                                <i class="fas fa-trash-can-xmark fa-lg fa-fw"></i>
                              </a>';
                    endif;

                    echo '</td>';
                    echo '</tr>';
                endforeach;
            else: ?>
                <tr>
                    <td colspan="6">Nenhum registro encontrado</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php if ($this->pagination->create_links()) { ?>
            <div class="panel-footer">
                <?= $this->pagination->create_links() ?>
            </div>
        <?php } ?>
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

<!-- Modal COPIAR DESPESA -->
<div class="modal fade" id="modalCopiar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Copiar despesa</h4>
            </div>
            <form id="formCopiarDespesa" action="<?= base_url('financeiro/despesas/copiar') ?>" method="post" autocomplete="off">
                <input class="urlAtual" type="hidden" name="urlAtual"/>
                <input class="id" type="hidden" name="idDespesa"/>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold">Descrição *</label>
                            <input class="form-control description" type="text" name="descricao"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-3 col-xs-4">
                            <label class="font-weight-bold">Valor *</label>
                            <input class="form-control money value" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-9 col-xs-8">
                            <label class="font-weight-bold">Fornecedor</label>
                            <input class="form-control supplier" type="text" name="fornecedor"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-6">
                            <label class="font-weight-bold">Forma Pagamento *</label>
                            <select name="forma_pagamento" class="form-control paymentForm">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $f) { ?>
                                        <option value="<?= $f->id_forma ?>">
                                            <?= $f->nome ?>
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6">
                            <label class="font-weight-bold">Tipo *</label>
                            <select class="form-control expenseType" name="tipo">
                                <option value=""><< Selecione >></option>
                                <option value="unica">ÚNICA</option>
                                <option value="recorrente">RECORRENTE</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6 divDiaVencimento">
                            <label class="font-weight-bold">Dia de Vencimento</label>
                            <input class="form-control datepicker expirationDay" type="text" name="dia_vencimento"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class=" divContainerParcelamento hidden">
                            <div class="form-group col-lg-4 col-xs-12 mt20 pb5">
                                <div class="row">
                                    <input type="checkbox" class="switch-input primary installmentExpense" id="despesaParceladaCopiar" name="despesa_parcelada" value="1">
                                    <label for="despesaParceladaCopiar" class="switch-label primary font-weight-bold">Despesa parcelada</label>
                                </div>
                            </div>
                            <div class="divParcelas hidden">
                                <div class="form-group col-lg-4 col-xs-6">
                                    <label class="font-weight-bold">Nº Parcelas *</label>
                                    <input class="form-control installments" type="tel" name="qnt_parcelas"/>
                                </div>
                                <div class="form-group col-lg-4 col-xs-6">
                                    <label class="font-weight-bold">Valor da Parcela *</label>
                                    <input class="form-control installmentValue" type="text" name="valor_parcela" readonly/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-5 mt20">
                            <div class="row">
                                <input type="checkbox" class="switch-input primary thirdPartyExpense" id="despesaTerceirosCopiar" name="despesa_terceiros" value="1">
                                <label for="despesaTerceirosCopiar" class="switch-label primary font-weight-bold">Despesa de terceiros</label>
                            </div>
                        </div>
                        <div class="divTerceiros hidden">
                            <div class="form-group col-lg-8 col-xs-7">
                                <label class="font-weight-bold">Nome do terceiro *</label>
                                <input class="form-control thirdName" type="text" name="nome_terceiro"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4" style="margin-bottom: 0;">
                            <a href="javascript:" class="font-weight-bold obsLink">
                                <i class="fas fa-plus fa-fw obsIcon"></i>
                                <span class="obsText">Adicionar observações</span>
                            </a>
                        </div>
                        <div class="divObservacoes hidden">
                            <div class="form-group col-lg-8 mb0">
                                <label for="observacoesEditar" class="font-weight-bold">Observações</label>
                                <textarea rows="5" class="form-control observationsTextarea" id="observacoesEditar" name="observacoes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i>
                        Fechar
                    </button>
                    <button type="submit" class="btn btn-info btn-sm">
                        <i class="fa fa-check fa-fw"></i>
                        Copiar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal RESUMO/EDITAR DESPESA -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Resumo da despesa # <span class="expenseIdText"></span></h4>
            </div>
            <form id="formResumoDespesa" action="<?= base_url('financeiro/despesas/editar') ?>" method="post" autocomplete="off">
                <input class="urlAtual" type="hidden" name="urlAtual"/>
                <input class="expenseId" type="hidden" name="id_despesa"/>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricaoEditar">Descrição *</label>
                            <input class="form-control description" id="descricaoEditar" type="text" name="descricao"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-3 col-xs-4">
                            <label class="font-weight-bold">Valor *</label>
                            <input class="form-control money value" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-9 col-xs-8">
                            <label class="font-weight-bold" for="fornecedorEditar">Fornecedor</label>
                            <input class="form-control supplier" id="fornecedorEditar" type="text" name="fornecedor"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="forma_pagamento" class="font-weight-bold">Forma Pagamento *</label>
                            <select name="forma_pagamento" class="form-control paymentForm">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $f) { ?>
                                        <option value="<?= $f->id_forma ?>">
                                            <?= $f->nome ?>
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="tipoEditar" class="font-weight-bold">Tipo Despesa *</label>
                            <select class="form-control expenseType" name="tipo" id="tipoEditar">
                                <option value=""><< Selecione >></option>
                                <option value="unica">ÚNICA</option>
                                <option value="recorrente">RECORRENTE</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6 divDiaVencimento">
                            <label class="font-weight-bold">Dia de Vencimento</label>
                            <input class="form-control datepicker expirationDay" type="text" name="dia_vencimento"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class=" divContainerParcelamento hidden">
                            <div class="form-group col-lg-4 col-xs-12 mt20 pb5">
                                <div class="row">
                                    <input type="checkbox" class="switch-input primary installmentExpense" id="despesaParceladaEditar" name="despesa_parcelada" value="1">
                                    <label for="despesaParceladaEditar" class="switch-label primary font-weight-bold">Despesa parcelada</label>
                                </div>
                            </div>
                            <div class="divParcelas hidden">
                                <div class="form-group col-lg-4 col-xs-6">
                                    <label class="font-weight-bold">Nº Parcelas *</label>
                                    <input class="form-control installments" type="tel" name="qnt_parcelas"/>
                                </div>
                                <div class="form-group col-lg-4 col-xs-6">
                                    <label class="font-weight-bold">Valor da Parcela *</label>
                                    <input class="form-control installmentValue" type="text" name="valor_parcela" readonly/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-5 mt20">
                            <div class="row">
                                <input type="checkbox" class="switch-input primary thirdPartyExpense" id="despesaTerceirosEditar" name="despesa_terceiros" value="1">
                                <label for="despesaTerceirosEditar" class="switch-label primary font-weight-bold">Despesa de terceiros</label>
                            </div>
                        </div>
                        <div class="divTerceiros hidden">
                            <div class="form-group col-lg-8 col-xs-7">
                                <label class="font-weight-bold">Nome do terceiro *</label>
                                <input class="form-control thirdName" type="text" name="nome_terceiro"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4" style="margin-bottom: 0;">
                            <a href="javascript:" class="font-weight-bold obsLink">
                                <i class="fas fa-plus fa-fw obsIcon"></i>
                                <span class="obsText">Adicionar observações</span>
                            </a>
                        </div>
                        <div class="divObservacoes hidden">
                            <div class="form-group col-lg-8 mb0">
                                <label for="observacoesEditar" class="font-weight-bold">Observações</label>
                                <textarea rows="5" class="form-control observationsTextarea" id="observacoesEditar" name="observacoes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-default-alt btn-sm expenseDetailsLink pull-left">
                        <i class="fa fa-search-plus fa-fw"></i>
                        Detalhes
                    </a>
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i>
                        Fechar
                    </button>
                    <button type="button" id="modalCopiar" class="btn btn-info btn-sm modal-copy">
                        <i class="fa fa-copy fa-fw"></i>
                        Copiar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-check fa-fw"></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal NOVA DESPESA -->
<div class="modal fade" id="modalNovaDespesa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Registrar nova despesa</h4>
            </div>
            <form id="formDespesa" action="<?= base_url('financeiro/despesas/registrar') ?>" method="post" autocomplete="off">
                <input class="urlAtual" type="hidden" name="urlAtual"/>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control description" id="descricao" type="text" name="descricao"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-3 col-xs-4">
                            <label class="font-weight-bold">Valor *</label>
                            <input class="form-control money value" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-9 col-xs-8">
                            <label class="font-weight-bold" for="fornecedor">Fornecedor</label>
                            <input class="form-control supplier" id="fornecedor" type="text" name="fornecedor"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="forma_pagamento" class="font-weight-bold">Forma Pagamento *</label>
                            <select name="forma_pagamento" class="form-control paymentForm">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $f) { ?>
                                        <option value="<?= $f->id_forma ?>">
                                            <?= $f->nome ?>
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="tipo" class="font-weight-bold">Tipo Despesa *</label>
                            <select class="form-control expenseType" name="tipo" id="tipo">
                                <option value=""><< Selecione >></option>
                                <option value="unica">ÚNICA</option>
                                <option value="recorrente">RECORRENTE</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6 divDiaVencimento">
                            <label class="font-weight-bold">Dia de Vencimento</label>
                            <input class="form-control datepicker expirationDay" type="text" name="dia_vencimento"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class=" divContainerParcelamento hidden">
                            <div class="form-group col-lg-4 col-xs-12 mt20 pb5">
                                <div class="row">
                                    <input type="checkbox" class="switch-input primary installmentExpense" id="despesaParcelada" name="despesa_parcelada" value="1">
                                    <label for="despesaParcelada" class="switch-label primary font-weight-bold">Despesa parcelada</label>
                                </div>
                            </div>
                            <div class="divParcelas hidden">
                                <div class="form-group col-lg-4 col-xs-6">
                                    <label class="font-weight-bold">Nº Parcelas *</label>
                                    <input class="form-control qntParcelas installments" type="tel" name="qnt_parcelas"/>
                                </div>
                                <div class="form-group col-lg-4 col-xs-6">
                                    <label class="font-weight-bold">Valor da Parcela *</label>
                                    <input class="form-control installmentValue" type="text" name="valor_parcela" readonly/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-5 mt20">
                            <div class="row">
                                <input type="checkbox" class="switch-input primary thirdPartyExpense" id="despesaTerceiros" name="despesa_terceiros" value="1">
                                <label for="despesaTerceiros" class="switch-label primary font-weight-bold">Despesa de terceiros</label>
                            </div>
                        </div>
                        <div class="divTerceiros hidden">
                            <div class="form-group col-lg-8 col-xs-7">
                                <label class="font-weight-bold">Nome do terceiro *</label>
                                <input class="form-control thirdName" type="text" name="nome_terceiro"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4" style="margin-bottom: 0;">
                            <a href="javascript:" class="font-weight-bold obsLink">
                                <i class="fas fa-plus fa-fw obsIcon"></i>
                                <span class="obsText">Adicionar observações</span>
                            </a>
                        </div>
                        <div class="divObservacoes hidden">
                            <div class="form-group col-lg-8 mb0">
                                <label for="observacoes" class="font-weight-bold">Observações</label>
                                <textarea rows="5" class="form-control observationsTextarea" id="observacoes" name="observacoes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4">
                            <div class="row"><input type="checkbox" class="switch-input primary autoVinculo" id="autoVinculo" name="autoVinculo">
                                <label for="autoVinculo" class="switch-label primary font-weight-bold">Integração automática</label>
                            </div>
                        </div>
                        <div class="col-xs-8 modal-form-buttons">
                            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ATIVAR INTEGRACAO-->
<div class="modal fade" id="modalAtivar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Ativar integração automática</h4>
            </div>
            <form action="<?= base_url('financeiro/despesas/ativar') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Ativar a integração automática para esta despesa?</p>
                    <!--<p class="note note-info"><i class="text-info fa fa-info-circle fa-fw fa-lg"></i> Ao ativar o cartão você poderá criar novas faturas ou acessar faturas já existentes deste cartão</p>-->
                    <input name="id" class="id" type="hidden"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-success btn-sm"><i class="fa fa-check fa-fw"></i> Ativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal DESATIVAR INTEGRACAO-->
<div class="modal fade" id="modalDesativar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-alizarin">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Desativar integração automática</h4>
            </div>
            <form action="<?= base_url('financeiro/despesas/desativar') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Desativar a integração automática para esta despesa?</p>
                    <!--<p class="note note-info"><i class="text-info fa fa-info-circle fa-fw fa-lg"></i> Ao ativar o cartão você poderá criar novas faturas ou acessar faturas já existentes deste cartão</p>-->
                    <input name="id" class="id" type="hidden"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-deeporange btn-sm"><i class="fa fa-check fa-fw"></i> Desativar</button>
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
                <h4 class="modal-title text-white ">Excluir despesa</h4>
            </div>
            <form action="<?php echo base_url('financeiro/despesas/excluir') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Deseja realmente excluir esta despesa?</p>
                    <input name="idDespesa" class="id" type="hidden"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i> Excluir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VINCULAR/DESVINCULAR TODAS AS DESPESAS ATIVAS -->
<div class="modal fade" id="modalGerenciarVinculoDespesas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Gerenciar vínculos de despesas</h4>
            </div>
            <form id="formVincularFaturas" action="<?php echo base_url('financeiro/despesas/gerenciarVinculos') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Defina o mês e ano de referência para gerenciar o vínculo de todas as despesas ativas:</p>
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label font-weight-bold" for="select_mes">
                                Mês de referência *
                            </label>
                            <select class="form-control" id="mes_referencia" name="mesReferencia">
                                <?php $mesAtual = date('m'); ?>
                                <option value="">
                                    << Selecione >>
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
                        Esta ação irá vincular ou desvincular do módulo de Lançamentos todas as despesas ativas referentes ao mês e ano selecionados.
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

<!-- Modal CONFIGURACOES DE DESPESAS -->
<div class="modal fade" id="modalConfiguracoes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Configurações de despesas</h4>
            </div>
            <form id="formConfigurarFatura" action="<?= base_url('financeiro/despesas/configurar') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p class="font-weight-bold">Vinculação automática de despesas ativas:</p>
                    <div>
                        <input type="checkbox" id="expense-link-switch" name="expenseAutoLink" class="switch-input primary" <?= $autoLinkExpesnses ? 'checked' : '' ?>>
                        <label for="invoice-link-switch" id="auto-link-label" class="switch-label primary font-weight-bold"><?= $autoLinkExpesnses ? 'Auto vínculo ativado' : 'Auto vínculo desativado' ?></label>
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
                    <input id="id_fatura_pagar" type="hidden" name="id_fatura"/>
                    <input id="urlPagarFatura" type="hidden" name="urlAtual"/>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="data_pagamento">Data do pagamento</label>
                            <input class="datepicker form-control" id="data_pagamento" type="text" name="data_pagamento"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="forma_pagamento">Forma de pagamento *</label>
                            <select name="forma_pagamento" id="forma_pagamento" class="form-control">
                                <option value="">
                                    << Selecione >>
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
    $('.installments').mask("99")
    $('.expirationDay').mask("99")

    $('.obsLink').click(function () {
        var obsIcon = $(this).children('i')
        var obsText = $(this).children('span.obsText')
        var obsTextarea = $('.divObservacoes').children().children('textarea')

        obsIcon.toggleClass('fa-plus fa-minus')
        $('.divObservacoes').toggleClass('hidden')

        if (obsIcon.hasClass('fa-minus')) {
            obsText.text('Remover observações')
        } else {
            obsText.text('Adicionar observações')
            obsTextarea.val('')
        }
    })

    $(document).ready(function () {
        $('.thirdPartyExpense').on('change', function (event) {
            toggleThirdsDiv(event.target.checked)
        });

        function toggleThirdsDiv(event = null) {
            let checked

            checked = $('.thirdPartyExpense').prop('checked')

            if (event != null) {
                checked = event
            }

            if (checked === true) {
                $('.divTerceiros').removeClass('hidden')
            } else {
                $('.divTerceiros').addClass('hidden')
            }
        }

        $('.expenseType').on('change', function (event) {
            toggleEntryDateDiv(event.target.value)
        });

        function toggleEntryDateDiv(event = null) {
            let value

            value = $('.expenseType').val()

            if (event != null) {
                value = event
            }

            if (value == 'unica') {
                // $('.divDataVencimento').removeClass('hidden')
                // $('.divDiaVencimento').addClass('hidden')
                $('.divContainerParcelamento').removeClass('hidden')
            } else if (value == 'recorrente') {
                // $('.divDataVencimento').addClass('hidden')
                // $('.divDiaVencimento').removeClass('hidden')
                $('.divContainerParcelamento').addClass('hidden')
            } else {
                // $('.divDataVencimento').addClass('hidden')
                // $('.divDiaVencimento').addClass('hidden')
                $('.divContainerParcelamento').addClass('hidden')
            }
        }

        $('.installmentExpense').on('change', function (event) {
            toggleInstallmentCheckbox(event.target.checked)
        })

        function toggleInstallmentCheckbox(event = null) {
            let checked

            checked = $('.installmentExpense').prop('checked')

            if (event != null) {
                checked = event
            }

            if (checked === true) {
                $('.divParcelas').removeClass('hidden')
            } else {
                $('.divParcelas').addClass('hidden')
            }
        }

        // Metodos para função de parcelamento
        $('.installments').keyup(function (event) {
            var parcelas = event.target.value
            var form = event.target.form
            var valorCompra = $(form).find('.value').val()
            var result = calculaValorParcela(parcelas, valorCompra)

            if (parcelas != '' && valorCompra != '' && parcelas != undefined && valorCompra != undefined) {
                $('.installmentValue').val(result)
            }
        })

        $('.value').keyup(function (event) {
            var valorCompra = event.target.value
            var form = event.target.form
            var parcelas = $(form).find('.installments').val()
            var result = calculaValorParcela(parcelas, valorCompra)

            if (parcelas != '' && valorCompra != '' && parcelas != undefined && valorCompra != undefined) {
                $('.installmentValue').val(result)
            }
        })

        function calculaValorParcela(parcela, valor) {
            var parcelas = parcela
            valor = jqueryFormat(valor)

            var valor_parcela = valor / parcelas
            valor_parcela = adjustFloatResult(valor_parcela)

            return valor_parcela
        }

        function jqueryFormat(valor) {
            // Remove todos os .
            valor = valor.replace(/\./g, "")

            // Troca todas as , por .
            valor = valor.replace(",", ".")

            // Converte para float
            valor = parseFloat(valor)
            valor = parseFloat(valor) || 0.0

            return valor
        }

        function br_format(n) {
            return n.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, "$1.")
        }

        function adjustFloatResult(value) {
            const orderOfMagnitude = Math.pow(10, 2)
            var result = Math.trunc(value * orderOfMagnitude) / orderOfMagnitude
            return br_format(result)
        }

        $('#modalNovaDespesa').on('shown.bs.modal', function (e) {
            $('.description').focus();
        })

        $(".description").autocomplete({
            source: "<?php echo base_url('financeiro/despesas/autoCompleteDescricao'); ?>",
            minLength: 2,
            select: function (event, ui) {
                $(event.target).val(ui.item.label);
            }
        });

        $(".supplier").autocomplete({
            source: "<?php echo base_url('financeiro/despesas/autoCompleteFornecedor'); ?>",
            minLength: 1,
            select: function (event, ui) {
                $(event.target).val(ui.item.label);
            }
        });

        $(".thirdName").autocomplete({
            source: "<?php echo base_url('financeiro/despesas/autoCompleteTerceiros') ?>",
            minLength: 1,
            select: function (event, ui) {
                $(event.target).val(ui.item.label)
            }
        })

        $("#formDespesa").validate({
            rules: {
                descricao: {
                    required: true
                },
                tipo: {
                    required: true
                },
                fornecedor: {
                    required: false
                },
                valor: {
                    required: true
                },
                qnt_parcelas: {
                    required: true,
                    max: 48,
                    min: 2
                },
                valor_parcela: {
                    required: true
                },
                data_vencimento: {
                    required: false
                },
                dia_vencimento: {
                    required: false
                },
                forma_pagamento: {
                    required: true
                },
                nome_terceiro: {
                    required: true
                },
            },
            messages: {
                descricao: {
                    required: 'Informe a descrição'
                },
                tipo: {
                    required: 'Selecione o tipo da despesa'
                },
                fornecedor: {
                    required: 'Campo obrigatório'
                },
                valor: {
                    required: 'Informe o valor'
                },
                qnt_parcelas: {
                    required: 'Informe o número de parcelas',
                    max: 'Máximo de parcelas: 48x ',
                    min: 'Mínimo de parcelas: 2x '
                },
                valor_parcela: {
                    required: 'Informe o valor da parcela'
                },
                data_vencimento: {
                    required: 'Campo obrigatório'
                },
                dia_vencimento: {
                    required: 'Informe o dia do vencimento'
                },
                forma_pagamento: {
                    required: 'Selecione a forma de pagamento'
                },
                nome_terceiro: {
                    required: 'Informe o nome do terceiro'
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
        })

        $("#formResumoDespesa").validate({
            rules: {
                descricao: {
                    required: true
                },
                tipo: {
                    required: true
                },
                fornecedor: {
                    required: false
                },
                valor: {
                    required: true
                },
                qnt_parcelas: {
                    required: true,
                    max: 48,
                    min: 2
                },
                valor_parcela: {
                    required: true
                },
                data_vencimento: {
                    required: false
                },
                dia_vencimento: {
                    required: false
                },
                forma_pagamento: {
                    required: true
                },
                nome_terceiro: {
                    required: true
                },
            },
            messages: {
                descricao: {
                    required: 'Informe a descrição'
                },
                tipo: {
                    required: 'Selecione o tipo da despesa'
                },
                fornecedor: {
                    required: 'Campo obrigatório'
                },
                valor: {
                    required: 'Informe o valor'
                },
                qnt_parcelas: {
                    required: 'Informe o número de parcelas',
                    max: 'Máximo de parcelas: 48x ',
                    min: 'Mínimo de parcelas: 2x '
                },
                valor_parcela: {
                    required: 'Informe o valor da parcela'
                },
                data_vencimento: {
                    required: 'Campo obrigatório'
                },
                dia_vencimento: {
                    required: 'Informe o dia do vencimento'
                },
                forma_pagamento: {
                    required: 'Selecione a forma de pagamento'
                },
                nome_terceiro: {
                    required: 'Informe o nome do terceiro'
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
        })


        // ========================================= OLD CODE BELOW


        $('#btnDesvincular').click(function (event) {
            var form = this;
            event.preventDefault();
            $('#desvincularFaturas').val(true);
            $('#formVincularFaturas').submit();
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


        $(document).on('click', '.excluir, .ativar, .desativar', function (event) {
            $(".id").val($(this).attr('id_despesa'));
        });

        $(document).on('click', '.pagar', function (event) {
            $("#id_fatura_pagar").val($(this).attr('id_fatura'));
        });

        $(document).on('click', '.editar, .copiar', function (event) {
            var expenseId = $(this).attr('id_despesa')
            $(".expenseId").val(expenseId)
            $(".expenseIdText").text(expenseId)
            $(".description").val($(this).attr('descricao'))
            $(".supplier").val($(this).attr('fornecedor'))
            $(".thirdName").val($(this).attr('nome_terceiro'))
            $(".dueDate").val($(this).attr('data_vencimento'))
            $(".expirationDay").val($(this).attr('dia_vencimento'))
            $(".value").val($(this).attr('valor'))
            $(".installmentValue").val($(this).attr('valor_parcela'))
            $(".installments").val($(this).attr('total_parcelas'))
            $(".paymentForm").val($(this).attr('forma_pagamento'))
            $(".expenseDetailsLink").attr("href", "<?= base_url('financeiro/despesas/detalhes/') ?>" + expenseId)

            var installmentExpense = $(this).attr('despesa_parcelada')
            var thirdPartyExpense = $(this).attr('despesa_terceiros')
            var expenseType = $(this).attr('tipo')
            var observacoes = $(this).attr('observacoes')

            if (installmentExpense == 1) {
                $(".installmentExpense").prop('checked', true)
                toggleInstallmentCheckbox(true)
            } else {
                $(".installmentExpense").prop('checked', false)
                toggleInstallmentCheckbox(false)
            }

            if (thirdPartyExpense == 1) {
                $(".thirdPartyExpense").prop('checked', true)
                toggleThirdsDiv(true)
            } else {
                $(".thirdPartyExpense").prop('checked', false)
                toggleThirdsDiv(false)
            }

            if (expenseType == 1) {
                $(".expenseType").val('unica')
                toggleEntryDateDiv('unica')
            } else {
                $(".expenseType").val('recorrente')
                toggleEntryDateDiv('recorrente')
            }

            if (observacoes) {
                var text = observacoes.replace(/<br \/> /gi, "\n")
                $("#observacoesEditar, #observacoesCopiar").val(text)

                $(".divObservacoes").removeClass('hidden')
                var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
                var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

                obsIcon.removeClass('fa-plus')
                obsIcon.addClass('fa-minus')
                obsText.text('Remover observações')
            } else {
                $("#observacoesEditar, #observacoesCopiar").val('')
                $(".divObservacoes").addClass('hidden')
                var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
                var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

                obsIcon.removeClass('fa-minus')
                obsIcon.addClass('fa-plus')
                obsText.text('Adicionar observações')
            }
        })

        $(document).on('click', '#novaDespesa', function () {
            var formNewExpense = $("#formDespesa")
            var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
            var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

            formNewExpense.find("input[type=text], input[type=tel], textarea, select").val("")
            formNewExpense.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected')
            obsIcon.removeClass('fa-minus')
            obsIcon.addClass('fa-plus')
            obsText.text('Adicionar observações')
            $(".divObservacoes").addClass('hidden')
            toggleThirdsDiv(false)
            toggleEntryDateDiv(false)
            toggleInstallmentCheckbox(false)
        })
    })
</script>