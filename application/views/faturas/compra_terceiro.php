<?php
$parcelasPagas = 0;
$saldoPago     = 0;
$saldoDevedor  = 0;
$totalCompra   = 0;

foreach ($parcelas as $parcela) {
	$parcelaPaga = (isset($parcela['parcela_terceiro_pago']) && $parcela['parcela_terceiro_pago'] == 1);
	$totalCompra += $parcela['valor_parcela'];

	if ($parcelaPaga) {
		$parcelasPagas++;
		$saldoPago += $parcela['valor_parcela'];
	} else {
		$saldoDevedor += $parcela['valor_parcela'];
	}
}

$compraPaga   = ($parcelasPagas == count($parcelas));
$acaoCompra   = $compraPaga ? 'remover' : 'pagar';
$numeroCartao = decriptar($compra->cartao_numero);
$partesCartao = explode(' ', trim($numeroCartao));
$finalCartao  = end($partesCartao);
$cartaoLabel  = $compra->cartao_apelido ?: $compra->cartao_bandeira . ' - FINAL ' . $finalCartao;
$valorParcela    = $parcelas[0]['valor_parcela'];
$parcelasAbertas = count($parcelas) - $parcelasPagas;
$statusParcelas  = $parcelasAbertas > 0 ? $parcelasAbertas . ($parcelasAbertas > 1 ? ' pendentes' : ' pendente') : 'Todas pagas';
?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3 class="pr-md">
            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
            Detalhes da Compra
        </h3>
        <div class="panel-ctrls">
            <a href="<?= $voltarUrl ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Voltar</a>
        </div>
    </div>
    <div class="panel-body panel-no-padding">
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-lg-6 col-xs-12">
                    <label class="font-weight-bold">Descrição</label>
                    <input class="form-control" type="text" value="<?= strtoupper($compra->descricao) ?>" disabled/>
                </div>
                <div class="form-group col-lg-6 col-xs-12">
                    <label class="font-weight-bold">Terceiro</label>
                    <input class="form-control" type="text" value="<?= strtoupper($compra->nome_cliente) ?>" disabled/>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Valor da compra</label>
                    <input class="form-control font-weight-bold text-green" type="text" value="<?= number_format($compra->valor_total, 2, ',', '.') ?>" disabled/>
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Data da compra</label>
                    <input class="form-control" type="text" value="<?= date('d/m/Y', strtotime($compra->data_compra)) ?>" disabled/>
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Cartão</label>
                    <input class="form-control" type="text" value="<?= $cartaoLabel ?>" disabled/>
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Parcelamento</label>
                    <input class="form-control" type="text" value="<?= $compra->total_parcelas ?>x" disabled/>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Valor da parcela</label>
                    <input class="form-control font-weight-bold text-green" type="text" value="<?= number_format($valorParcela, 2, ',', '.') ?>" disabled/>
                </div>
                <div class="form-group col-lg-3 col-xs-6">
                    <label class="font-weight-bold">Status das parcelas</label>
                    <input class="form-control" type="text" value="<?= $statusParcelas ?>" disabled/>
                </div>
            </div>
            <?php if ($compra->observacoes) { ?>
                <div class="row">
                    <div class="form-group col-lg-12 col-xs-12 mb0">
                        <label class="font-weight-bold">Observações</label>
                        <textarea rows="5" class="form-control" disabled><?= $compra->observacoes ?></textarea>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>Parcelas da Compra</h2>
        <div class="panel-ctrls">
            <button type="button"
                    class="btn <?= $compraPaga ? 'btn-warning' : 'btn-success' ?> btn-sm marcar-compra-terceiro-pago"
                    title="<?= $compraPaga ? 'Remover pagamento da compra' : 'Pagar compra inteira' ?>"
                    data-toggle="modal"
                    data-target="#modalCompraTerceiroPago"
                    data-id-assoc="<?= $parcelas[0]['id_assoc'] ?>"
                    data-acao="<?= $acaoCompra ?>"
                    data-descricao="<?= htmlspecialchars(strtoupper($compra->descricao), ENT_QUOTES, 'UTF-8') ?>"
                    data-total-parcelas="<?= $compra->total_parcelas ?>"
                    data-valor-compra="<?= number_format($compra->valor_total, 2, ',', '.') ?>">
                <i class="fal <?= $compraPaga ? 'fa-undo' : 'fa-hand-holding-dollar' ?> fa-fw"></i>
				<?= $compraPaga ? 'Remover pagamento' : 'Pagar compra' ?>
            </button>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid">
            <thead>
            <tr role="row">
                <th>Fatura</th>
                <th>Referência</th>
                <th>Vencimento</th>
                <th>Parcela</th>
                <th>Valor Parcela (R$)</th>
                <th>Status</th>
                <th style="width: 100px !important;">Ações</th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($parcelas as $parcela) {
				$nParcela      = $parcela['n_parcela'] < 10 ? str_pad($parcela['n_parcela'], 2, '0', STR_PAD_LEFT) : $parcela['n_parcela'];
				$totalParcelas = $parcela['total_parcelas'] < 10 ? str_pad($parcela['total_parcelas'], 2, '0', STR_PAD_LEFT) : $parcela['total_parcelas'];
				$parcelaPaga   = (isset($parcela['parcela_terceiro_pago']) && $parcela['parcela_terceiro_pago'] == 1);
				$acaoParcela   = $parcelaPaga ? 'remover' : 'pagar';
				$referencia    = getExtendedMonthName($parcela['mes_referencia']) . ' / ' . $parcela['ano_referencia'];
				?>
                    <tr<?= $parcelaPaga ? ' class="success"' : '' ?>>
                        <td class="font-weight-bold">#<?= $parcela['id_fatura'] ?></td>
                        <td class="font-weight-bold">
                            <a href="<?= base_url('financeiro/faturas/detalhes/' . $parcela['id_fatura'] . '/' . $parcela['id_cartao']) ?>" title="Visualizar fatura desta parcela">
								<?= $referencia ?>
                            </a>
                        </td>
                        <td class="font-weight-bold"><?= date('d/m/Y', strtotime($parcela['vencimento'])) ?></td>
                        <td class="font-weight-bold"><?= $nParcela . '/' . $totalParcelas ?></td>
                        <td class="font-weight-bold"><?= number_format($parcela['valor_parcela'], 2, ',', '.') ?></td>
                        <td>
                            <span class="badge badge-pill badge-<?= $parcelaPaga ? 'success' : 'warning' ?>">
								<?= $parcelaPaga ? 'PAGO' : 'PENDENTE' ?>
                            </span>
                        </td>
                        <td>
                            <button type="button"
                                    style="margin-right: 1%"
                                    class="btn <?= $parcelaPaga ? 'btn-warning' : 'btn-success' ?> btn-sm marcar-parcela-terceiro-pago"
                                    title="<?= $parcelaPaga ? 'Remover pagamento' : 'Marcar como pago' ?>"
                                    data-toggle="modal"
                                    data-target="#modalParcelaTerceiroPago"
                                    data-id-assoc="<?= $parcela['id_assoc'] ?>"
                                    data-acao="<?= $acaoParcela ?>"
                                    data-descricao="<?= htmlspecialchars(strtoupper($parcela['descricao']), ENT_QUOTES, 'UTF-8') ?>"
                                    data-parcela="<?= $nParcela . '/' . $totalParcelas ?>"
                                    data-total-parcelas="<?= $parcela['total_parcelas'] ?>"
                                    data-valor="<?= number_format($parcela['valor_parcela'], 2, ',', '.') ?>"
                                    data-valor-compra="<?= number_format($parcela['valor_total'], 2, ',', '.') ?>">
                                <i class="fal <?= $parcelaPaga ? 'fa-undo' : 'fa-hand-holding-dollar' ?> fa-lg fa-fw"></i>
                            </button>
                            <a type="button"
                               href="<?= base_url('financeiro/faturas/detalhes/' . $parcela['id_fatura'] . '/' . $parcela['id_cartao']) ?>"
                               style="margin-right: 1%"
                               class="btn btn-primary btn-sm"
                               title="Visualizar fatura desta parcela">
                                <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                            </a>
                        </td>
                    </tr>
			<?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-alizarin">
    <div class="panel-heading font-weight-bold">
        <div class="pull-left">
            <h2>Resumo da compra:</h2>
        </div>
        <div class="pull-right">
            <h2 class="pull-right"><?= strtoupper($compra->nome_cliente) ?></h2>
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
                <td class="font-weight-bold" colspan="2" style="text-align: left; color: green">(+) SALDO TOTAL PAGO DA COMPRA</td>
                <td class="font-weight-bold" colspan="1" style="text-align: right; color: green">
					<?= number_format($saldoPago, 2, ',', '.') ?>
                </td>
            </tr>
            <tr>
                <td class="font-weight-bold" colspan="2" style="text-align: left; color: red">(-) SALDO TOTAL DEVEDOR DA COMPRA</td>
                <td class="font-weight-bold" colspan="1" style="text-align: right; color: red">
                    <span style="cursor: pointer;" title="Copiar para área de transferência" class="i-copy-value" data-copy-value="<?= number_format($totalCompra, 2, ',', '.') ?>">
                        <i class="fas fa-copy fa-fw"></i>
						<?= number_format($totalCompra, 2, ',', '.') ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="font-weight-bold" colspan="2" style="text-align: left;">(=) SALDO TOTAL DA COMPRA</td>
                <td class="font-weight-bold" colspan="1" style="text-align: right;">
                    <span style="cursor: pointer;" title="Copiar para área de transferência" class="i-copy-value" data-copy-value="<?= number_format($saldoDevedor, 2, ',', '.') ?>">
                        <i class="fas fa-copy fa-fw"></i>
						<?= number_format($saldoDevedor, 2, ',', '.') ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Modal MARCAR PARCELA PAGA POR TERCEIROs -->
<div class="modal fade" id="modalParcelaTerceiroPago" tabindex="-1" role="dialog" aria-labelledby="modalParcelaTerceiroPagoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formParcelaTerceiroPago" action="<?php echo base_url('financeiro/faturas/marcarParcelaTerceiroPago') ?>" method="post" autocomplete="off">
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
                    <button class="btn btn-default btn-sm hidden-xs" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" form="formParcelaTerceiroPago" class="btn btn-success btn-sm parcelaTerceiroPagoSubmit">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal MARCAR COMPRA PAGA POR TERCEIROs -->
<div class="modal fade" id="modalCompraTerceiroPago" tabindex="-1" role="dialog" aria-labelledby="modalCompraTerceiroPagoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCompraTerceiroPago" action="<?php echo base_url('financeiro/faturas/marcarCompraTerceiroPago') ?>" method="post" autocomplete="off">
                <div class="modal-header bg-success compraTerceiroPagoHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white" id="modalCompraTerceiroPagoLabel">Confirmar pagamento da compra</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_assoc" class="compraTerceiroPagoIdAssoc">
                    <input type="hidden" name="acao" class="compraTerceiroPagoAcao">
                    <input class="urlAtual" type="hidden" name="urlAtual"/>
                    <p class="font-weight-bold compraTerceiroPagoTexto"></p>
                    <p class="note note-info compraTerceiroPagoNota"></p>
                    <table class="table table-condensed table-bordeless mb0">
                        <tr>
                            <td class="font-weight-bold">Descrição</td>
                            <td class="compraTerceiroPagoDescricao"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Parcelamento</td>
                            <td class="compraTerceiroPagoParcelamento"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Valor da compra</td>
                            <td>R$ <span class="compraTerceiroPagoValorCompra"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm hidden-xs" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" form="formCompraTerceiroPago" class="btn btn-success btn-sm compraTerceiroPagoSubmit">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).on('mouseenter mouseleave', '.i-copy-value', function () {
        $(this).toggleClass('font-weight-bold')
    });

    $(document).on('click', '.i-copy-value', function () {
        var copyText = $(this);
        var textArea = document.createElement("textarea");
        var value = copyText.attr('data-copy-value');
        var valueNew = value.toString().split('.').join('');
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

    $(document).on('click', '.marcar-parcela-terceiro-pago', function () {
        var botao = $(this);
        var acao = botao.attr('data-acao');
        var pagar = acao === 'pagar';

        $('.parcelaTerceiroPagoIdAssoc').val(botao.attr('data-id-assoc'));
        $('.parcelaTerceiroPagoAcao').val(acao);
        $('.parcelaTerceiroPagoDescricao').text(botao.attr('data-descricao'));
        $('.parcelaTerceiroPagoParcela').text(botao.attr('data-parcela'));
        $('.parcelaTerceiroPagoValor').text(botao.attr('data-valor'));
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
    });

    $(document).on('click', '.marcar-compra-terceiro-pago', function () {
        var botao = $(this);
        var acao = botao.attr('data-acao');
        var pagar = acao === 'pagar';

        $('.compraTerceiroPagoIdAssoc').val(botao.attr('data-id-assoc'));
        $('.compraTerceiroPagoAcao').val(acao);
        $('.compraTerceiroPagoDescricao').text(botao.attr('data-descricao'));
        $('.compraTerceiroPagoParcelamento').text(botao.attr('data-total-parcelas') + 'x');
        $('.compraTerceiroPagoValorCompra').text(botao.attr('data-valor-compra'));
        $('.compraTerceiroPagoTexto').text(
            pagar
                ? 'Deseja marcar a compra inteira como paga pelo terceiro?'
                : 'Deseja remover o pagamento da compra inteira?'
        );
        $('.compraTerceiroPagoNota').text(
            pagar
                ? 'Todas as parcelas desta compra serão marcadas como pagas.'
                : 'Todas as parcelas desta compra voltarão a ficar em aberto.'
        );
        $('.compraTerceiroPagoHeader')
            .toggleClass('bg-success', pagar)
            .toggleClass('bg-warning', !pagar);
        $('.compraTerceiroPagoSubmit')
            .toggleClass('btn-success', pagar)
            .toggleClass('btn-warning', !pagar)
            .html('<i class="fa fa-check fa-fw"></i> ' + (pagar ? 'Pagar compra' : 'Remover pagamento'));
    });
</script>
