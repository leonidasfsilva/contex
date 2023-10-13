<?php
$prevLink           = null;
$nextLink           = null;
$currentMonthText   = null;

if (isset($referenceMonth) && $referenceMonth) {
    if ($prevMonth && $nextMonth) {
        $prevLinkTitle = sprintf('%s / %s', $prevMonth, $prevReferenceYear);
        $nextLinkTitle = sprintf('%s / %s', $nextMonth, $nextReferenceYear);
    }

    $prevLink       = "<a href='" . base_url(sprintf('financeiro/faturas/terceiros?mesReferencia=%s&anoReferencia=%s&cartao=%s&nome=%s', $prevReferenceMonth, $prevReferenceYear, $idCard, $name))
        . "' title='$prevLinkTitle'><span class='badge badge-primary'><i style='margin: 0 !important;' class='fas fa-angle-double-left'></i></span></a>";
    $currentMonthText  = "<a href='#modalSelectMounth' data-toggle='modal' role='button' title='Clique para selecionar um mes específico'><span class='badge badge-primary' style='margin-left: 10px;'>Referência: $referencePeriod</span></a>";
    $nextLink       = "<a href='" . base_url(sprintf('financeiro/faturas/terceiros?mesReferencia=%s&anoReferencia=%s&cartao=%s&nome=%s', $nextReferenceMonth, $nextReferenceYear, $idCard, $name))
        . "'  title='$nextLinkTitle'><span class='badge badge-primary' style='margin-left: 10px;'><i style='margin: 0 !important;' class='fas fa-angle-double-right'></i></span></a>";
}

?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            <span style='margin-right: 10px !important;'>Registro de Compras: <?= $name ?></span>
            <br class="visible-xs-block">
            <?= ($referenceMonth ? $prevLink . $currentMonthText . $nextLink : null) ?>
        </h2>

        <div class="panel-ctrls">
            <a href="<?= base_url('financeiro/faturas?cartao=') . $idCard ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Voltar</a>
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
                    $n_cartao = explode(" ", trim(decriptar($result['cartao']['numero'])));
                    $final = $n_cartao[3];
                    $cartaoAlternativeLabel = $result['cartao']['bandeira'] . ' - FINAL ' . $final;
                ?>
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordion" href="#<?= $result['id_fatura'] ?>">
                            <h2>
                                <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                                Período: <?= $result['reference'] ?>

                                <span style="padding-left: 10px;">
                                    <i class="fas fa-credit-card fa-lg fa-fw"></i>
                                    <?= $result['cartao']['apelido'] ? $result['cartao']['apelido'] : $cartaoAlternativeLabel ?>
                                </span>
                            </h2>
                        </a>
                        <div id="<?= $result['id_fatura'] ?>" class="collapse">
                            <div class="accordion-body" style="padding: 0 !important;">
                                <div class="panel panel-midnightblue" style="margin: 0 !important;">
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
                                                $debitoFatura = 0;

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

                                                    $data_compra    = date(('d/m/y'), strtotime($r['data_compra']));
                                                    $debitoFatura   += $r['valor_parcela'];
                                                    $totalSum       += $r['valor_parcela'];

                                                    if ($r['valor_total'] < 0) {
                                                        $valor = number_format(abs($r['valor_total']), 2, ',', '.');
                                                    } else {
                                                        $valor = number_format($r['valor_total'], 2, ',', '.');
                                                    }

                                                    if (isset($r['observacoes'])) {
                                                        $iconObs = ' 
                                                            <i class="fas fa-comment-dots fa-fw" title="Observações adicionais"></i>
                                                        ';
                                                    } else {
                                                        $iconObs = '';
                                                    };

                                                    echo '<tr>';
                                                    echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                                                    echo '<td>' . $data_compra . '</td>';
                                                    echo '<td><a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="editar" title="Detalhes" id_lancamento="' .
                                                        $r['id_lancamento'] . '" descricao="' . $r['descricao'] . '" observacoes="' . nl2br($r['observacoes']) . '" valor="' . $valor . '" data_compra="' .
                                                        date('d/m/Y', strtotime($r['data_compra'])) . '" parcelada="' . $r['compra_parcelada'] . '" estorno="' . $r['estorno'] . '" n_parcelas="' . $r['total_parcelas'] .
                                                        '" valor_parcela="' . number_format($r['valor_parcela'], 2, ',', '.') . '" terceiros="' . $r['compra_terceiros'] . '" nome_cliente="' . $r['nome_cliente'] .
                                                        '" id_cliente="' . $r['id_cliente'] . '">' . strtoupper($r['descricao']) . $iconObs .
                                                        '</a></td>';
                                                    echo '<td>' . strtoupper($r['nome_cliente']) . '</td>';
                                                    echo '<td>' . $n_parcela . '/' . $total_parcelas . '</td>';
                                                    echo '<td class="valor_parcela" style=" color: ' . $color = null .
                                                        '"><span>' . number_format($r['valor_parcela'], 2, ',', '.') .
                                                        '</span><br><span style="color: grey">' . number_format($r['valor_total'], 2, ',', '.') .
                                                        '</span></td>';

                                                    echo '<td>';
                                                    echo '<a type="button" href="' . base_url('financeiro/faturas/detalhes/' . $r['id_fatura'] . '/' . $result['id_cartao']) . '" style="margin-right: 1%" data-toggle="modal" class="btn btn-primary btn-sm editar" title="Acessar fatura deste lançamento">
                                                            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                                                        </a>';

                                                    echo '</td>';
                                                    echo '</tr>';
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="panel " style="margin: 0 !important;">
                                        <div class="panel-footer bg-midnightblue font-weight-bold text-white panel-no-padding">
                                            <p>
                                                Resumo de Gastos na Fatura #<?= $result['id_fatura'] ?>
                                            </p>
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
            <div class="panel panel-midnightblue" style="margin: 0 !important;">
                <div class="panel-heading font-weight-bold">
                    <h2>
                        Resumo Total do Período <?= $result['reference'] ?>
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
        <div class="panel-footer font-weight-bold">
            <span class="pull-right">
                Vencimento: <?= $dueDatePeriod ?>
            </span>
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
                    <input type="hidden" name="mesReferencia" class="selectedMonth" value="<?= $referenceMonth ?>" />
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
                    <input type="hidden" name="cartao" value="<?= $idCard ?>" />
                    <input type="hidden" name="nome" value="<?= $name ?>" />
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
            <!-- <form id="formEditarLancamento" action="<?php echo base_url('financeiro/faturas/editarLancamento') ?>" method="post" autocomplete="off"> -->
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-lg-12">
                        <label class="font-weight-bold">Descrição:</label>
                        <input class="form-control descricao" type="text" name="descricao" readonly />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label class="font-weight-bold">Valor da Compra *</label>
                        <input class="form-control money valor" type="text" name="valor" readonly />
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="font-weight-bold">Data da Compra</label>
                        <input class="form-control datepicker dataCompra" type="text" name="data_compra" readonly />
                    </div>
                </div>
                <div class="row divContainerParcelamento">
                    <div>
                        <div class="form-group col-lg-4">
                            <label class="font-weight-bold">Parcelas</label>
                            <input class="form-control qntParcelas" type="text" readonly />
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="font-weight-bold">Valor da Parcela</label>
                            <input class="form-control valorParcela" type="text" name="valor_parcela" readonly />
                        </div>
                        <div class="form-group col-lg-4">
                            <label class="font-weight-bold">Nome do terceiro</label>
                            <input class="form-control nomeCliente" type="text" name="nome_cliente" readonly />
                        </div>
                    </div>
                </div>
                <div class="divObservacoes hidden">
                    <div class="form-group mb0">
                        <label for="observacoes" class="font-weight-bold">Observações</label>
                        <textarea rows="5" class="form-control observacoesTextarea" id="observacoesEditar" name="observacoes" readonly></textarea>
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

    $(".i-copy-debit").hover(function() {
        $(this).toggleClass('font-weight-bold')
    });

    $(".i-copy-debit").click(function() {
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
</script>