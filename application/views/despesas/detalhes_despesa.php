<?php
$situacao                 = $this->input->get('situacao');
$periodo                  = $this->input->get('periodo');
$autoIntegrationBtnStatus = '<a href="' . base_url('financeiro/despesas/ativar/') . $despesa->id . '" class="btn btn-sm btn-deeporange"><i class="fas fa-stop fa-fw"></i> INATIVA</a>';

if ($despesa->auto_vinculo == 1) {
    $autoIntegrationBtnStatus = '<a href="' . base_url('financeiro/despesas/desativar/') . $despesa->id . '" class="btn btn-sm btn-success"><i class="fas fa-play fa-fw"></i> ATIVA</a>';
}
?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3 class="pr-md">
            <i class="fas fa-money-bill-transfer fa-lg fa-fw"></i>
            Detalhes da Despesa #<?= sprintf('%s', $despesa->id) ?>
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('financeiro/despesas') ?>" class="btn btn-default btn-sm" title="Voltar para despesas">
                <i class="fas fa-arrow-left fa-fw"></i>
                Despesas
            </a>
            <?= $autoIntegrationBtnStatus ?>
            <a href="#" class="button-icon close-panel">
                <i class="fas fa-times"></i>
            </a>
            <a href="#" class="button-icon expand">
                <i class="fas fa-expand expand-icon"></i>
            </a>
            <a href="#" class="button-icon panel-collapse">
                <i class="fas fa-minus"></i>
            </a>
        </div>
    </div>
    <div class="panel-body panel-no-padding">
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-lg-6 col-xs-12">
                    <label class="font-weight-bold" for="descricaoEditar">Descrição</label>
                    <input class="form-control" type="text" value="<?= $despesa->descricao ?>" disabled/>
                </div>
                <?php if ($despesa->fornecedor) { ?>
                    <div class="form-group col-lg-6 col-xs-12">
                        <label class="font-weight-bold" for="fornecedorEditar">Fornecedor</label>
                        <input class="form-control" type="text" value="<?= $despesa->fornecedor ?>" disabled/>
                    </div>
                <?php } ?>
            </div>
            <div class="row">
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Valor</label>
                    <input class="form-control font-weight-bold <?= $despesa->despesa_terceiros ? 'text-green' : 'text-alizarin' ?>" type="text" value="<?= number_format($despesa->valor_total, 2, ',', '.') ?>" disabled/>
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Forma de pagamento</label>
                    <input class="form-control" type="text" value="<?= $despesa->descricao_pagamento ?>" disabled/>
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Tipo de despesa</label>
                    <input class="form-control" type="text" value="<?= $despesa->tipo_despesa == 1 ? 'ÚNICA' : 'RECORRENTE' ?>" disabled/>
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Vencimento</label>
                    <input class="form-control" type="text" value="Todo dia <?= $despesa->dia_vencimento ?>" disabled/>
                </div>
            </div>
            <div class="row">
                <?php if ($despesa->despesa_parcelada) { ?>
                    <div class="form-group col-lg-3 col-xs-6">
                        <label class="font-weight-bold">Parcelamento</label>
                        <input class="form-control" type="text" value="<?= $parcelamento ?>x" disabled/>
                    </div>
                    <div class="form-group col-lg-3 col-xs-6">
                        <label class="font-weight-bold">Status das parcelas</label>
                        <input class="form-control" type="text" value="<?= ($parcelasRestantes = $despesa->total_parcelas - $parcelasPagas) > 1 ? $parcelasRestantes . ' restantes' : $parcelasRestantes . 'restante' ?>" disabled/>
                    </div>
                    <div class="form-group col-lg-3 col-xs-6">
                        <label class="font-weight-bold">Valor da parcela</label>
                        <input class="form-control font-weight-bold <?= $despesa->despesa_terceiros ? 'text-green' : 'text-alizarin' ?>" type="text" value="<?= number_format($despesa->valor_parcela, 2, ',', '.') ?>" disabled/>
                    </div>
                <?php }
                if ($despesa->despesa_terceiros) { ?>
                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="font-weight-bold">Terceiro</label>
                        <input class="form-control" type="text" value="<?= $despesa->nome_terceiro ?>" disabled/>
                    </div>
                <?php } ?>
            </div>
            <?php if ($despesa->observacoes) { ?>
                <div class="row">
                    <div class="form-group col-lg-12 col-xs-12 mb0">
                        <label for="observacoesEditar" class="font-weight-bold">Observações</label>
                        <textarea rows="5" class="form-control" disabled><?= $despesa->observacoes ?></textarea>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Registros da Despesa
        </h2>
        <div class="panel-ctrls">
                <span class="hidden" id="div_btn_marcar">
                    <!--<button class="btn btn-info btn-sm modalCopiarSerie " id="copiar_serie" title="Copiar todos os lançamentos selecionados" disabled>-->
                    <!--    <i class="fas fa-copy fa-fw"></i>-->
                    <!--    Copiar-->
                    <!--</button>-->
                    <button class="btn btn-danger btn-sm modalExcluirSerie" id="excluir_serie" title="Excluir todos os lançamentos selecionados" disabled>
                        <i class="fas fa-trash-can-xmark fa-fw"></i>
                        Excluir
                    </button>
                    <button class="btn btn-default btn-sm marcar_desmarcar" id="marcar_todos" title="Marcar todos os lançamentos da fatura">
                        <i class="far fa-square fa-fw"></i>
                        Marcar Todos
                    </button>
                    <button class="btn btn-default marcar_desmarcar btn-sm hidden" id="desmarcar_todos" title="Desmarcar todos os lançamentos da fatura">
                        <i class="fas fa-check-square fa-fw"></i>
                        Desmarcar Todos
                    </button>
                </span>
            <button class="btn btn-default btn-sm habilita_desabilita_soma" id="exibir_soma" title="Habilitar soma de lançamentos individuais">
                <i class="fas fa-toggle-off fa-fw"></i>
                Habilitar Soma
            </button>
            <button class="btn btn-default btn-sm habilita_desabilita_soma hidden" id="esconder_soma" title="Desabilitar soma de lançamentos individuais">
                <i class="fas fa-toggle-on fa-fw"></i>
                Desabilitar Soma
            </button>
            <a href="#modalNovoRegistroDespesa" data-toggle="modal" class="btn btn-primary btn-sm" title="Registrar lançamento da despesa">
                <i class="fas fa-plus fa-fw"></i>
                Novo Registro
            </a>
            <a href="#" class="button-icon close-panel">
                <i class="fas fa-times"></i>
            </a>
            <a href="#" class="button-icon expand">
                <i class="fas fa-expand expand-icon"></i>
            </a>
            <a href="#" class="button-icon panel-collapse">
                <i class="fas fa-minus"></i>
            </a>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <th class="th_soma hidden" style="width: 10px !important;">Soma</th>
                <th>Vencimento<br><br></th>
                <th>Descrição<br><?= $despesa->despesa_parcelada ? 'Parcela' : 'Fornecedor' ?></th>
                <th><?= $despesa->despesa_parcelada ? 'Valor parcela' : 'Valor' ?> (R$)<br>Forma pagamento</th>
                <th>Status vínculo<br>Status pagamento</th>
                <th style="width: 130px">Ações<br>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($results) && $results) {
                foreach ($results as $r) {
                    $value                = number_format($r->valor, 2, ',', '.');
                    $installmentValue     = null;
                    $thirdName            = null;
                    $installments         = null;
                    $link                 = '#';
                    $aditionalDescription = $despesa->fornecedor;
                    $dueDate              = null;
                    $dueDateIcon          = null;
                    $disableLinkBtn       = null;
                    $disablePaymentBtn    = null;
                    $dueDateColor         = 'text-gray';
                    $colorValue           = $despesa->despesa_terceiros ? 'text-green' : 'text-alizarin';
                    $iconValue            = '<i class="far fa-arrow-left fa-rotate-by fa-fw mr1" style="--fa-rotate-angle: 45deg !important;"></i>';
                    $linkStatus           = '<i class="far fa-link-slash fa-fw text-orange"></i> <span class="badge badge-orange">SEM VÍNCULO</span>';
                    $paymentStatus        = '<i class="far fa-times fa-fw text-danger"></i> <span class="badge badge-red">PENDENTE</span>';


                    if ($r->mes_referencia && $r->ano_referencia)
                        $dueDate = sprintf('%s/%s/%s', $despesa->dia_vencimento, $r->mes_referencia, $r->ano_referencia);

                    if ($despesa->valor_parcela)
                        // $value = number_format($despesa->valor_parcela, 2, ',', '.');


                        if ($despesa->despesa_parcelada) {
                            $aditionalDescription = sprintf('PARCELA %s/%s', $r->num_parcela, $despesa->total_parcelas);
                            $installments         = '<span class="badge badge-alizarin font-weight-bold">' . $r->num_parcela . '/' . $despesa->total_parcelas . '</span>';
                        }

                    if ($despesa->despesa_terceiros) {
                        $thirdName = $despesa->nome_terceiro;
                        $iconValue = '<i class="far fa-arrow-right fa-rotate-by fa-fw mr1" style="--fa-rotate-angle: 45deg !important;"></i>';
                    }

                    if ($r->registro_vinculado) {
                        $link           = base_url(sprintf('financeiro/lancamentos?periodo=mensal&mesReferencia=%s&anoReferencia=%s',
                                $r->mes_referencia,
                                $r->ano_referencia)
                        );
                        $linkStatus     = '<i class="far fa-link fa-fw text-primary"></i> <span class="badge badge-primary">VINCULADO</span>';
                        $dueDateIcon    = ' <i class="far fa-arrow-up-right-from-square fa-fw text-primary"></i>';
                        $dueDateColor   = 'text-primary';
                        $disableLinkBtn = 'disabled';
                    }

                    if ($r->registro_pago == 1) {
                        $paymentStatus     = '<i class="far fa-check fa-fw text-success"></i> <span class="badge badge-green">EFETIVADO</span>';
                        $disablePaymentBtn = 'disabled';
                    }

                    echo '<tr>';

                    echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';

                    echo '<td class="idLancamento hidden">' . $r->id . '</td>';

                    echo '<td class="font-weight-bold"><a href="' . $link . '" title="Acessar período de referência" class="' . $dueDateColor . '">' . $dueDate . $dueDateIcon . '</a></td>';

                    echo '<td class="font-weight-bold">' . strtoupper($despesa->descricao) . '<br><span class="small text-muted" >' . ($aditionalDescription) . '</td>';

                    echo '<td class="font-weight-bold valor_parcela"><span class="font-11 ' . $colorValue . '"> ' . $iconValue . ($value) . '</span><br><span class="small text-muted">' . ($despesa->descricao_pagamento) . '</span></td>';

                    echo '<td>' . $linkStatus . '<br>' . $paymentStatus . '</td>';

                    echo '<td>';

                    echo '<button type="button" href="#modalVincular" data-toggle="modal" id_lancamento_despesa="' . $r->id . '" id_despesa="' . $despesa->id .
                        '" data_vencimento="' . $r->data_vencimento . '" class="btn btn-info btn-sm vincular mr1" title="Vincular este registro de despesa" ' . $disableLinkBtn . '>
                            <i class="fas fa-link fa-lg fa-fw"></i>
                          </button>';

                    echo '<button href="#modalPagar" data-toggle="modal" id_despesa="' . $r->id . '" class="btn btn-green btn-sm pagar mr1" title="Pagar este registro de despesa" ' . $disablePaymentBtn . '>
                            <i class="fas fa-circle-dollar-to-slot fa-lg fa-fw"></i>
                          </button>';

                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dDespesas'))
                        echo '<a href="#modalExcluir" data-toggle="modal" id_despesa="' . $r->id . '" class="btn btn-deeporange btn-sm excluir" title="Excluir este registro de despesa"><i class="fas fa-trash-can-xmark fa-lg fa-fw"></i></a>';

                    echo '</td>';

                    echo '</tr>';
                }
            } else { ?>
                <tr>
                    <td colspan="6">Nenhum registro encontrado</td>
                </tr>
            <?php } ?>
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
                    <td class="font-weight-bold" colspan="2" style="text-align: left;">(=) REGISTROS SELECIONADOS</td>
                    <td class="font-weight-bold valor_soma_parcelas" colspan="1" style="text-align: right;" id="valor_soma_parcelas">
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


<!-- Modal ALERTA CONFIGURACAO -->
<?php if (isset($forma) && !$existe_configuracao) { ?>
    <div class="modal fade alerta-usuario" id="modalAlerta" tabindex="-10" role="dialog" aria-labelledby="myModalLabel">
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

<!-- Modal GERENCIAR VINCULOS DA DESPESA -->
<div class="modal fade" id="modalGerenciarVinculos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Gerenciar Vínculos da Despesa</h4>
            </div>
            <form id="formRegistrarLancamentosDespesa" action="<?php echo base_url('financeiro/despesas/vincularDespesas') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">
                        Defina o mês e ano de referência para vincular ou desvincular todos os registros desta despesa para o período solicitado:
                    </p>
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

                    <p class="note note-info">
                        <i class="text-info fa fa-info-circle fa-fw fa-lg"></i>
                        Esta ação irá vincular/desvincular do módulo de Lançamentos o registro desta despesa referente ao mês e ano selecionados.
                    </p>
                    <!-- <p class="note note-info"><i class="text-info fa fa-info-circle fa-fw fa-lg"></i>
                        Todas as atualizações de valores das faturas serão refletidas automaticamente no módulo de Lançamentos
                    </p> -->
                    <input type="hidden" name="idDespesa" value="<?= $despesa->id ?>"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                    <input class="desvincularDespesas" type="hidden" name="desvincularDespesas"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm btnDesvincular">
                        <i class="fa fa-unlink fa-fw"></i> Desvincular
                    </button>
                    <button class="btn btn-primary btn-sm btnVincular">
                        <i class="fal fa-link fa-fw"></i> Vincular
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal NOVO REGISTRO DE DESPESA -->
<div class="modal fade" id="modalNovoRegistroDespesa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Novo Registro de Despesa</h4>
            </div>
            <form id="formRegistrarLancamentosDespesa" action="<?php echo base_url('financeiro/despesas/registrarLancamentoDespesa') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">
                        Defina o mês e ano de referência para efetuar o registro desta despesa para o período solicitado:
                    </p>
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
                    <input type="hidden" name="idDespesa" value="<?= $despesa->id ?>"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-6">
                            <div class="row"><input type="checkbox" class="switch-input primary autoVinculo" id="autoVinculo" name="vincular">
                                <label for="autoVinculo" class="switch-label primary font-weight-bold">Vincular em Lançamentos</label>
                            </div>
                        </div>
                        <div class="col-xs-6 modal-form-buttons">
                            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VINCULAR REGISTRO INDIVIDUAL -->
<div class="modal fade" id="modalVincular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Vincular registro de despesa</h4>
            </div>
            <form id="formFechar" action="<?php echo base_url('financeiro/despesas/vincular') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Confirma o vínculo deste registro de despesa ao módulo de Lançamentos?</p>
                    <p class="note note-info">
                        <i class="text-info fa fa-info-circle fa-fw fa-lg"></i>
                        Esta ação irá vincular este registro de despesa ao módulo de Lançamentos referente à data de vencimento.
                    </p>
                    <input class="expenseId" type="hidden" name="idDespesa"/>
                    <input class="registerId" type="hidden" name="idRegistro"/>
                    <input class="dueDate" type="hidden" name="dataVencimento"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-info btn-sm">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal PAGAR REGISTRO -->
<div class="modal fade" id="modalPagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Pagar registro de despesa</h4>
            </div>
            <form id="formPagar" action="<?php echo base_url('financeiro/despesas/pagar') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p>Confirma o pagamento deste registro?</p>
                    <input class="expenseId" type="hidden" name="idLancamentoDespesa"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="data_pagamento">Data do pagamento</label>
                            <input class="datepicker form-control" id="data_pagamento" type="text" name="dataPagamento"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="forma_pagamento">Forma de pagamento *</label>
                            <select name="formaPagamento" id="forma_pagamento" class="form-control">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $forma) { ?>
                                        <option value="<?= $forma->id_forma ?>"><?= $forma->nome ?></option>
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

<!-- Modal EXCLUIR REGISTRO-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir registro de despesa</h4>
            </div>
            <form action="<?php echo base_url('financeiro/despesas/excluirLancamento') ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Confirma a exclusão deste registro de despesa?</p>
                    <?php ?>
                    <p class="note note-danger"><i class="text-danger fa fa-exclamation-triangle fa-fw fa-lg"></i>
                        Caso este registro possua um vínculo ativo no módulo de Lançamentos, o mesmo será excluído.
                    </p>
                    <?php ?>
                    <input name="idLancamentoDespesa" class="id" type="hidden"/>
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
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

<!-- Modal EXCLUIR SERIE DE REGISTROS-->
<div class="modal fade" id="modalExcluirSerie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir série de registros</h4>
            </div>
            <form id="formExcluirSerie" action="<?= base_url('financeiro/despesas/excluirSerieLancamentos'); ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Confirma a exclusão dos registros selecionados?</p>
                </div>
                <div id="deleteSerieFormBody"></div>
                <div class="modal-footer">
                    <div class="modal-form-buttons">
                        <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                            Cancelar
                        </button>
                        <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i>
                            Excluir
                        </button>
                    </div>
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
                    required: 'Selecione o tipo'
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


        $("#formPagar").validate({
            rules: {
                formaPagamento: {
                    required: true
                }
            },
            messages: {
                formaPagamento: {
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

        // ========================================= OLD CODE BELOW


        $('.btnDesvincular').click(function (event) {
            var btn = $(this)
            event.preventDefault();
            $('.desvincularDespesas').val(1);
            $('#formRegistrarLancamentosDespesa').attr('action', '<?php echo base_url('financeiro/despesas/desvincularDespesas') ?>').submit();
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


        $(document).on('click', '.excluir', function (event) {
            $(".id").val($(this).attr('id_despesa'));
        });

        $(document).on('click', '.editar, .vincular, .pagar', function (event) {
            var expenseId = $(this).attr('id_despesa')
            $(".expenseId").val(expenseId)
            $(".description").val($(this).attr('descricao'))
            $(".registerId").val($(this).attr('id_lancamento_despesa'))
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