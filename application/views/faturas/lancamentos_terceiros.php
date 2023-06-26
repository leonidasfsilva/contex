<?php
$prevLink           = null;
$nextLink           = null;
$currentMonthText   = null;

if (isset($referenceMonth) && $referenceMonth) {
    if ($prevMonth && $nextMonth) {
        $prevLinkTitle = sprintf('%s / %s', $prevMonth, $prevReferenceYear);
        $nextLinkTitle = sprintf('%s / %s', $nextMonth, $nextReferenceYear);
    }

    $prevLink       = "<a href='" . base_url(sprintf('financeiro/faturas/terceiros?mesReferencia=%s&anoReferencia=%s&nome=%s', $prevReferenceMonth, $prevReferenceYear, $name))
        . "' title='$prevLinkTitle'><span class='badge badge-primary'><i style='margin: 0 !important;' class='fas fa-angle-double-left'></i></span></a>";
    $currentMonthText  = "<a href='#modalSelectMounth' data-toggle='modal' role='button' title='Clique para selecionar um mes específico'><span class='badge badge-primary' style='margin-left: 10px;'>Referência: $referencePeriod</span></a>";
    $nextLink       = "<a href='" . base_url(sprintf('financeiro/faturas/terceiros?mesReferencia=%s&anoReferencia=%s&nome=%s', $nextReferenceMonth, $nextReferenceYear, $name))
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
                                                    <th style="width: 200px !important;">Cliente</th>
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
                                                    $s = $r;
                                                    
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

                                                    echo '<tr>';
                                                    echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                                                    echo '<td>' . $data_compra . '</td>';
                                                    echo '<td>' .
                                                        strtoupper($s['descricao']) .
                                                        '</td>';
                                                    echo '<td>' . strtoupper($s['nome_cliente']) . '</td>';
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
                                                        <span style="cursor: pointer;" title="Copiar para área de transferência" id="i-copy-debit">
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
                                <span style="cursor: pointer;" title="Copiar para área de transferência" id="i-copy-debit">
                                    <i class="fas fa-copy fa-fw hidden" id="icon-debit"></i>
                                    <?php echo number_format($totalSum, 2, ',', '.') ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        <?php } else { ?>
            <div class="note note-info font-weight-bold">
                Nenhum resultado encontrado para o período de referência solicitado
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
                        <div class="form-group col-lg-6" id="div_periodo_mensal">
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
                        <div class="form-group col-lg-6" id="div_periodo_anual">
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
                    </div>
                    <input type="hidden" id="nome" name="nome" value="<?= $_GET['nome'] ?>">
                    <input type="hidden" name="cartao" value="<?= $idCard ?>">
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
            <div class="modal-body">
                <p class="font-weight-bold">Selecione um mês específico para visualizar</p>
                <form id="form_filtro_mes" method="get">
                    <input type="hidden" name="mesReferencia" class="selectedMonth" />
                    <input type="hidden" name="anoReferencia" value="<?= $referenceYear ?>" />
                    <input type="hidden" name="nome" value="<?= $name ?>" />
                    <?php
                    $count = 0;
                    foreach ($monthList as $index => $month) {
                        $count++;
                        if ($referenceMonth == $index) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = null;
                        }
                    ?>
                        <button type="button" style="width: 60px;" class="btn btn-info btn-sm selectMonth <?= $month['notification'] ? 'notification-dot' : null ?>" value="<?= $index ?>" <?= $disabled ?>>
                            <?= $month['name'] ?>
                        </button>
                        <?php if ($count == 4 && $index != 12) {
                            $count = 0;
                        ?>
                            <br>
                            <br>
                    <?php }
                    } ?>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.accordion-title').addClass('collapsed');

    $("#copy-cpf").hover(function() {
        $('#copy-cpf').toggleClass('font-weight-bold')
    });

    $("#copy-cpf").click(function() {
        var copyText = document.getElementById("copy-cpf");
        var textArea = document.createElement("textarea");
        value = copyText.textContent.trim();
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
</script>