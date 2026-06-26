<?php
$prevLink         = null;
$nextLink         = null;
$currentMonthText = null;

if (isset($referenceMonth) && $referenceMonth) {
	if ($prevMonth && $nextMonth) {
		$prevLinkTitle = sprintf('%s / %s', $prevMonth, $prevReferenceYear);
		$nextLinkTitle = sprintf('%s / %s', $nextMonth, $nextReferenceYear);
	}
	
	$prevLink         = "<a href='" . base_url(sprintf('financeiro/faturas/terceiros?mesReferencia=%s&anoReferencia=%s&cartao=%s&nome=%s', $prevReferenceMonth, $prevReferenceYear, $idCard, $name))
		. "' title='$prevLinkTitle'><span class='badge badge-primary'><i style='margin: 0 !important;' class='fas fa-angle-double-left'></i></span></a>";
	$currentMonthText = "<a href='#modalSelectMounth' data-toggle='modal' role='button' title='Clique para selecionar um mes específico'><span class='badge badge-primary' style='margin-left: 10px;'>Referência: $referencePeriod</span></a>";
	$nextLink         = "<a href='" . base_url(sprintf('financeiro/faturas/terceiros?mesReferencia=%s&anoReferencia=%s&cartao=%s&nome=%s', $nextReferenceMonth, $nextReferenceYear, $idCard, $name))
		. "'  title='$nextLinkTitle'><span class='badge badge-primary' style='margin-left: 10px;'><i style='margin: 0 !important;' class='fas fa-angle-double-right'></i></span></a>";
}

?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2 class="pb0">
            <span class="pr20">Registro de compras de: </span><span class="pr20"><?= $name ?></span>
        </h2>
        <h2>
            <span class="pt5"><?= ($referenceMonth ? $prevLink . $currentMonthText . $nextLink : null) ?></span>

        </h2>
        <div class="panel-ctrls">
            <a href="<?= base_url('financeiro/faturas?cartao=') . $idCard ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Faturas</a>
            <span class="hidden" id="div_btn_marcar">
                <button class="btn btn-default btn-sm marcar_desmarcar" id="marcar_todos" title="Marcar todos os lançamentos listados">
                    <i class="far fa-square fa-fw"></i>
                    Marcar Todos
                </button>
                <button class="btn btn-default marcar_desmarcar btn-sm hidden" id="desmarcar_todos" title="Desmarcar todos os lançamentos listados">
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
            <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal" title="Filtrar por período">
                <i class="fas fa-filter fa-fw"></i>
                Filtrar
            </button>
        </div>
    </div>
    <div class="panel-body">
		<?php
		if ($results) { ?>
            <div class="accordion-group" id="accordion">
				<?php
				$totalSum = 0;
				
				foreach ($results as $result) {
					$n_cartao               = explode(" ", trim(decriptar($result['cartao']['numero'])));
					$final                  = $n_cartao[3];
					$cartaoAlternativeLabel = $result['cartao']['bandeira'] . ' - FINAL ' . $final;
					?>
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordion" href="#<?= $result['id_fatura'] ?>">
                            <h2 class="bg-midnightblue text-white">
                                <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                                Período: <?= $result['reference'] ?>

                                <span style="padding-left: 10px;">
                                    <i class="fas fa-credit-card fa-lg fa-fw"></i>
                                    <?= $result['cartao']['apelido'] ?: $cartaoAlternativeLabel ?>
                                </span>
                            </h2>
                        </a>
                        <div id="<?= $result['id_fatura'] ?>" class="collapse">
                            <div class="accordion-body" style="padding: 0 !important;">
                                <div class="panel panel-midnightblue no-border" style="margin: 0 !important;">
                                    <div class="panel-body panel-no-padding table-responsive">
                                        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid">
                                            <thead>
                                            <tr role="row">
                                                <th class="th_soma hidden" style="width: 10px !important;">Soma</th>
                                                <th style="width: 100px !important;">Data Compra</th>
                                                <th style="width: 300px !important;">Descrição</th>
                                                <th style="width: 200px !important;">Terceiro</th>
                                                <th>Parcela</th>
                                                <th>Valor Parcela (R$) <br> Valor Compra (R$)</th>
                                                <th style="width: 100px !important;">Ações</th>
                                            </tr>
                                            </thead>
                                            <tbody>
											<?php
											$creditoFatura = 0;
											$debitoFatura  = 0;
											
											foreach ($result['lancamentos'] as $r) {
												if ($r['n_parcela'] < 10) {
													$n_parcela = str_pad($r['n_parcela'], 2, '0', STR_PAD_LEFT);
												} else {
													$n_parcela = $r['n_parcela'];
												}

												if ($r['total_parcelas'] < 10) {
													$total_parcelas = str_pad($r['total_parcelas'], 2, '0', STR_PAD_LEFT);
												} else {
													$total_parcelas = $r['total_parcelas'];
												}

												$data_compra = date(('d/m/y'), strtotime($r['data_compra']));
												$parcelaPaga = (isset($r['parcela_terceiro_pago']) && $r['parcela_terceiro_pago'] == 1);

												if (!$parcelaPaga) {
													$debitoFatura += $r['valor_parcela'];
													$totalSum     += $r['valor_parcela'];
												}
												
												if ($r['valor_total'] < 0) {
													$valor = number_format(abs($r['valor_total']), 2, ',', '.');
												} else {
													$valor = number_format($r['valor_total'], 2, ',', '.');
												}
												
												if (isset($r['observacoes']) && $r['observacoes']) {
													$iconObs = '
                                                            <i class="fas fa-comment-dots fa-fw" title="Observações adicionais"></i>
                                                        ';
												} else {
													$iconObs = '';
												};
												
												echo '<tr' . ($parcelaPaga ? ' class="success"' : '') . '>';
												echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
												echo '<td class="idLancamento hidden">' . $r['id_lancamento'] . '</td>';
												echo '<td>' . $data_compra . '</td>';
												echo '<td><a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="editar font-weight-bold" title="Detalhes" id_lancamento="' .
													$r['id_lancamento'] . '" descricao="' . $r['descricao'] . '" observacoes="' . nl2br($r['observacoes']) . '" valor="' . $valor . '" data_compra="' .
													date('d/m/Y', strtotime($r['data_compra'])) . '" parcelada="' . $r['compra_parcelada'] . '" estorno="' . $r['estorno'] . '" n_parcelas="' . $r['total_parcelas'] .
													'" valor_parcela="' . number_format($r['valor_parcela'], 2, ',', '.') . '" terceiros="' . $r['compra_terceiros'] . '" nome_cliente="' . $r['nome_cliente'] .
													'" id_cliente="' . $r['id_cliente'] . '" id_assoc="' . $r['id_assoc'] . '" acao_pagamento_terceiro="' . ($parcelaPaga ? 'remover' : 'pagar') .
													'" parcela="' . $n_parcela . '/' . $total_parcelas . '" parcela_paga="' . ($parcelaPaga ? '1' : '0') . '">' . strtoupper($r['descricao']) . $iconObs .
													'</a></td>';
												echo '<td class="font-weight-bold">' . strtoupper($r['nome_cliente']) . '</td>';
												echo '<td>' . $n_parcela . '/' . $total_parcelas . '</td>';
												echo '<td class="valor_parcela font-weight-bold" style=" color: ' . $color = null .
														'"><span>' . number_format($r['valor_parcela'], 2, ',', '.') . ($parcelaPaga ? ' <span class="badge badge-pill badge-success">PAGO</span>' : '') .
														'</span><br><span style="color: grey">' . number_format($r['valor_total'], 2, ',', '.') .
														'</span></td>';
												
												echo '<td>';
												echo '<button type="button" style="margin-right: 1%" class="btn ' . ($parcelaPaga ? 'btn-warning' : 'btn-success') . ' btn-sm marcar-parcela-terceiro-pago"
													title="' . ($parcelaPaga ? 'Remover pagamento' : 'Marcar como pago') . '"
													data-toggle="modal"
													data-target="#modalParcelaTerceiroPago"
													data-id-assoc="' . $r['id_assoc'] . '"
													data-acao="' . ($parcelaPaga ? 'remover' : 'pagar') . '"
													data-descricao="' . htmlspecialchars(strtoupper($r['descricao']), ENT_QUOTES, 'UTF-8') . '"
													data-parcela="' . $n_parcela . '/' . $total_parcelas . '"
													data-valor="' . number_format($r['valor_parcela'], 2, ',', '.') . '">
                                                            <i class="fas ' . ($parcelaPaga ? 'fa-undo' : 'fa-hand-holding-circle-dollar') . ' fa-lg fa-fw"></i>
                                                        </button>';
												echo '<a type="button" href="' . base_url('financeiro/faturas/detalhes/' . $r['id_fatura'] . '/' . $result['id_cartao']) . '" style="margin-right: 1%" data-toggle="modal" class="btn btn-info btn-sm editar" title="Visualizar fatura desta compra">
                                                            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                                                        </a>';
												
												echo '</td>';
												echo '</tr>';
											} ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="panel " style="margin: 0 !important;">
                                        <div class="font-weight-bold panel-no-padding p20 bg-gray">
                                            <span>
                                                Saldo devedor na fatura <?= $result['id_fatura'] ?>
                                            </span>
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
                                                    <td colspan="2" style="text-align: left; color: red">(-) SALDO DEVEDOR NA FATURA</td>
                                                    <td colspan="1" style="text-align: right; color: red">
                                                        <input type="hidden" id="debit-balance" value="<?php echo number_format($debitoFatura, 2, ',', '.') ?>">
                                                        <span style="cursor: pointer;" title="Copiar para área de transferência" class="i-copy-debit">
                                                            <i class="fas fa-copy fa-fw hidden" id="icon-debit"></i>
                                                            <?php echo number_format($debitoFatura, 2, ',', '.') ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
            <div id="somatorio_lancamentos" class="panel-footer hidden">
                <table class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                    <thead>
                    <tr>
                        <th colspan="2" style="text-align: left !important;">Descrição</th>
                        <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
                    </tr>
                    </thead>
                    <tr>
                        <td class="font-weight-bold" colspan="2" style="text-align: left;">(=) LANÇAMENTOS SELECIONADOS</td>
                        <td class="font-weight-bold valor_soma_parcelas" colspan="1" style="text-align: right;" id="valor_soma_parcelas">
                            0,00
                        </td>
                    </tr>
                </table>
            </div>
            <div class="panel panel-alizarin" style="margin: 0 !important;">
                <div class="panel-heading font-weight-bold">
                    <div class="pull-left">
                        <h2>
                            Resumo do período:
                        </h2>
                    </div>
                    <div class="pull-right">
                        <h2 class="pull-right">
							<?= $referencePeriod ?>
                        </h2>
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
                            <td colspan="2" style="text-align: left; color: red">(-) SALDO DEVEDOR TOTAL</td>
                            <td colspan="1" style="text-align: right; color: red">
                                <input type="hidden" id="debit-balance" value="<?php echo number_format($totalSum, 2, ',', '.') ?>">
                                <span style="cursor: pointer;" title="Copiar para área de transferência" class="i-copy-debit">
                                    <i class="fas fa-copy fa-fw hidden"></i>
                                    <?php echo number_format($totalSum, 2, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
		<?php } else { ?>
            <div class="note note-info font-weight-bold">
                Nenhum registro encontrado para o período de referência solicitado
            </div>
		<?php } ?>
        <div class="panel-footer note note-alizarin font-weight-bold p0 mt10">
            <div class="pull-left">
                <span>
                    Vencimento:
                </span>
            </div>
            <div class="pull-right">
                <span class="pull-right">
			        <?= $dueDatePeriod ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Modal FILTRAR -->
<div class="modal fade" id="modalFiltrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Filtrar por período</h4>
            </div>
            <form action="<?php echo current_url(); ?>" method="get" id="form_filtro" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-4" id="div_periodo_mensal">
                            <label class="control-label font-weight-bold" for="select_mes">
                                Mês específico
                            </label>
                            <select class="form-control" id="mesReferenciaSelect" name="mesReferencia">
                                <option value="01" <?= ($referenceMonth == '01') ? 'selected' : null ?>>01 - JANEIRO</option>
                                <option value="02" <?= ($referenceMonth == '02') ? 'selected' : null ?>>02 - FEVEREIRO</option>
                                <option value="03" <?= ($referenceMonth == '03') ? 'selected' : null ?>>03 - MARÇO</option>
                                <option value="04" <?= ($referenceMonth == '04') ? 'selected' : null ?>>04 - ABRIL</option>
                                <option value="05" <?= ($referenceMonth == '05') ? 'selected' : null ?>>05 - MAIO</option>
                                <option value="06" <?= ($referenceMonth == '06') ? 'selected' : null ?>>06 - JUNHO</option>
                                <option value="07" <?= ($referenceMonth == '07') ? 'selected' : null ?>>07 - JULHO</option>
                                <option value="08" <?= ($referenceMonth == '08') ? 'selected' : null ?>>08 - AGOSTO</option>
                                <option value="09" <?= ($referenceMonth == '09') ? 'selected' : null ?>>09 - SETEMBRO</option>
                                <option value="10" <?= ($referenceMonth == '10') ? 'selected' : null ?>>10 - OUTUBRO</option>
                                <option value="11" <?= ($referenceMonth == '11') ? 'selected' : null ?>>11 - NOVEMBRO</option>
                                <option value="12" <?= ($referenceMonth == '12') ? 'selected' : null ?>>12 - DEZEMBRO</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4" id="div_periodo_anual">
                            <label class="control-label font-weight-bold" for="select_ano">
                                Ano específico
                            </label>
                            <select class="form-control" id="anoReferenciaSelect" name="anoReferencia">
								<?php if ($yearsList) {
									foreach ($yearsList as $year) { ?>
                                        <option value="<?= $year ?>" <?= ($referenceYear == $year ? 'selected' : '') ?>><?= $year ?></option>
									<?php }
								} ?>
                            </select>
                        </div>
                        <input type="hidden" name="cartao" value="<?= $idCard ?>">
                        <div class="form-group col-lg-4">
                            <label class="tooltips font-weight-bold" title="Filtrar lançamentos por terceiros">Terceiros <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" name="nome">
								<?php if ($terceiros) {
									foreach ($terceiros as $terceiro) { ?>
                                        <option value="<?= $terceiro['nome'] ?>" <?php if ($name == $terceiro['nome']) {
											echo 'selected';
										} ?>><?= $terceiro['nome'] ?>
                                        </option>
									<?php }
								} ?>
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

<!-- Modal SELECAO DE MES -->
<div class="modal fade" id="modalSelectMounth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="form_filtro_mes" method="get">
                <div class="modal-body">
                    <p class="font-weight-bold">Selecione o mês e ano específicos</p>
                    <input type="hidden" name="mesReferencia" class="selectedMonth" value="<?= $referenceMonth ?>"/>
					<?php
					$count = 0;
					foreach ($monthList as $index => $month) {
						$count++;
						if ($referenceMonth == $index) {
							$active = 'active';
						} else {
							$active = null;
						}
						?>
                        <button type="button" style="width: 60px;" class="btn btn-info btn-sm selectMonth <?= $active ?> <?= $month['notification'] ? 'notification-dot' : null ?>" value="<?= $index ?>">
							<?= $month['name'] ?>
                        </button>
						<?php if ($count == 4 && $index != 12) {
							$count = 0;
							?>
                            <br>
                            <br>
						<?php }
					} ?>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="btn-block">
                            <div class="input-group">
                                <span class="input-group-addon">Ano: </span>
                                <select class="form-control" id="anoReferenciaSelect" name="anoReferencia">
									<?php if ($yearsList) {
										foreach ($yearsList as $year) { ?>
                                            <option value="<?= $year ?>" <?= ($referenceYear == $year ? 'selected' : '') ?>>
												<?= $year ?>
                                            </option>
										<?php }
									} ?>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col-xs-6">
                            <button class="btn btn-default btn-sm" data-dismiss="modal" title="Cancelar"><i class="fa fa-times fa-fw"></i>Cancelar</button>
                        </div> -->
                    </div>
                    <input type="hidden" name="cartao" value="<?= $idCard ?>"/>
                    <input type="hidden" name="nome" value="<?= $name ?>"/>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal MARCAR PARCELA PAGA POR TERCEIROs -->
<div class="modal fade" id="modalParcelaTerceiroPago" tabindex="-1" role="dialog" aria-labelledby="modalParcelaTerceiroPagoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url('financeiro/faturas/marcarParcelaTerceiroPago') ?>" method="post" autocomplete="off">
                <div class="modal-header bg-success parcelaTerceiroPagoHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white" id="modalParcelaTerceiroPagoLabel">Confirmar pagamento da parcela</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_assoc" class="parcelaTerceiroPagoIdAssoc">
                    <input type="hidden" name="acao" class="parcelaTerceiroPagoAcao">
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                    <p class="font-weight-bold parcelaTerceiroPagoTexto"></p>
                    <table class="table table-condensed table-bordeless mb0">
                        <tr>
                            <td class="font-weight-bold">Descrição</td>
                            <td class="parcelaTerceiroPagoDescricao"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Parcela</td>
                            <td class="parcelaTerceiroPagoParcela"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Valor</td>
                            <td>R$ <span class="parcelaTerceiroPagoValor"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm parcelaTerceiroPagoSubmit">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
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
                <h4 class="modal-title text-white ">Detalhes da compra</h4>
            </div>
            <!-- <form id="formEditarLancamento" action="<?php echo base_url('financeiro/faturas/editarLancamento') ?>" method="post" autocomplete="off"> -->
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label class="font-weight-bold">Descrição:</label>
                        <input class="form-control descricao" type="text" name="descricao" disabled/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-xs-6">
                        <label class="font-weight-bold">Valor da Compra *</label>
                        <input class="form-control money valor" type="text" name="valor" disabled/>
                    </div>
                    <div class="form-group col-lg-6 col-xs-6">
                        <label class="font-weight-bold">Data da Compra</label>
                        <input class="form-control datepicker dataCompra" type="text" name="data_compra" disabled/>
                    </div>
                </div>
                <div class="row divContainerParcelamento">
                    <div>
                        <div class="form-group col-lg-4 col-xs-6">
                            <label class="font-weight-bold">Parcelas</label>
                            <input class="form-control qntParcelas" type="text" disabled/>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6">
                            <label class="font-weight-bold">Valor da Parcela</label>
                            <input class="form-control valorParcela" type="text" name="valor_parcela" disabled/>
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="font-weight-bold">Nome do terceiro</label>
                            <input class="form-control nomeCliente" type="text" name="nome_cliente" disabled/>
                        </div>
                    </div>
                </div>
                <div class="divObservacoes hidden">
                    <div class="form-group mb0">
                        <label for="observacoes" class="font-weight-bold">Observações</label>
                        <textarea rows="5" class="form-control observacoesTextarea" id="observacoesEditar" name="observacoes" disabled></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="text-left col-xs-4" style="margin-top: -10px;">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control estorno" name="estorno" value="1" disabled>
                        </div>
                        <label class="font-weight-bold">Estorno</label>
                    </div>
                    <div class="col-xs-8">
                        <button type="button" class="btn btn-success btn-sm marcar-parcela-terceiro-pago marcar-parcela-terceiro-pago-modal-detalhes">
                            <i class="fas fa-hand-holding-circle-dollar fa-fw"></i> Marcar como pago
                        </button>
                        <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times fa-fw"></i> Fechar
                        </button>
                        <!-- <button type="button" id="modalCopiar" class="btn btn-info btn-sm modal-copy"><i class="fa fa-copy fa-fw"></i> Copiar</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button> -->
                    </div>
                </div>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>

<script>
    $('.accordion-title').addClass('collapsed');

    $(".i-copy-debit").hover(function () {
        $(this).toggleClass('font-weight-bold')
    });

    $(".i-copy-debit").click(function () {
        var copyText = $(this);
        var textArea = document.createElement("textarea");
        value = copyText[0].innerText;
        valueNew = value.toString().split('.').join('');
        valueNew = valueNew.toString().split('-').join('');
        textArea.value = valueNew;
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

    $(document).on('click', '.editar, .copiar', function (event) {
        $(".id_lancamento").val($(this).attr('id_lancamento'));
        $(".idCliente").val($(this).attr('id_cliente'));
        $(".descricao").val($(this).attr('descricao'));
        $(".valor").val($(this).attr('valor'));
        $(".dataCompra").val($(this).attr('data_compra'));
        $(".nomeCliente").val($(this).attr('nome_cliente'));

        var acaoPagamentoTerceiro = $(this).attr('acao_pagamento_terceiro');
        var parcelaPaga = $(this).attr('parcela_paga') == 1;
        $('.marcar-parcela-terceiro-pago-modal-detalhes')
            .toggleClass('btn-success', !parcelaPaga)
            .toggleClass('btn-warning', parcelaPaga)
            .attr('title', parcelaPaga ? 'Remover pagamento' : 'Marcar como pago')
            .attr('data-id-assoc', $(this).attr('id_assoc'))
            .attr('data-acao', acaoPagamentoTerceiro)
            .attr('data-descricao', $(this).attr('descricao').toUpperCase())
            .attr('data-parcela', $(this).attr('parcela'))
            .attr('data-valor', $(this).attr('valor_parcela'))
            .html(
                parcelaPaga
                    ? '<i class="fas fa-undo fa-fw"></i> Remover pagamento'
                    : '<i class="fas fa-hand-holding-circle-dollar fa-fw"></i> Marcar como pago'
            );

        var estorno = $(this).attr('estorno');
        var terceiros = $(this).attr('terceiros');
        var parcelada = $(this).attr('parcelada');
        if (parcelada == 1) {
            $(".qntParcelas").val($(this).attr('n_parcelas'));
            $(".valorParcela").val($(this).attr('valor_parcela'));
            $('.parcelada').iCheck('check');
        } else {
            $(".qntParcelas").val($(this).attr('n_parcelas'));
            $(".valorParcela").val($(this).attr('valor_parcela'));
            $('.parcelada').iCheck('uncheck');
        }
        if (estorno == 1) {
            $('.estorno').iCheck('check');
        } else {
            $('.estorno').iCheck('uncheck');
        }
        if (terceiros == 1) {
            $('.terceiros').iCheck('check');
            $(".divTerceiros").removeClass('hidden');
        } else {
            $('.terceiros').iCheck('uncheck');
            $(".divTerceiros").addClass('hidden');
        }

        var observacoes = $(this).attr('observacoes');

        if (observacoes) {
            var text = observacoes.replace(/<br \/> /gi, "\n")
            $("#observacoesEditar, #observacoesCopiar").val(text)

            $(".divObservacoes").removeClass('hidden');
            var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
            var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

            obsIcon.removeClass('fa-plus')
            obsIcon.addClass('fa-minus')
            obsText.text('Remover observações')
        } else {
            $(".divObservacoes").addClass('hidden');
            var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
            var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

            obsIcon.removeClass('fa-minus')
            obsIcon.addClass('fa-plus')
            obsText.text('Adicionar observações')
        }
    })

    $(document).on('click', '.marcar-parcela-terceiro-pago', function () {
        var acao = $(this).data('acao');
        var pagar = acao === 'pagar';

        $('.parcelaTerceiroPagoIdAssoc').val($(this).data('id-assoc'));
        $('.parcelaTerceiroPagoAcao').val(acao);
        $('.parcelaTerceiroPagoDescricao').text($(this).data('descricao'));
        $('.parcelaTerceiroPagoParcela').text($(this).data('parcela'));
        $('.parcelaTerceiroPagoValor').text($(this).data('valor'));
        $('.parcelaTerceiroPagoTexto').text(
            pagar
                ? 'Deseja marcar esta parcela como paga pelo terceiro?'
                : 'Deseja remover o pagamento desta parcela?'
        );
        $('.parcelaTerceiroPagoHeader')
            .toggleClass('bg-success', pagar)
            .toggleClass('bg-warning', !pagar);
        $('.parcelaTerceiroPagoSubmit')
            .toggleClass('btn-success', pagar)
            .toggleClass('btn-warning', !pagar)
            .html('<i class="fa fa-check fa-fw"></i> ' + (pagar ? 'Marcar como pago' : 'Remover pagamento'));

        if ($(this).hasClass('marcar-parcela-terceiro-pago-modal-detalhes')) {
            toggleModals($('#modalEditar'), $('#modalParcelaTerceiroPago'), true);
        }
    });
</script>
