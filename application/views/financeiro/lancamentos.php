<?php
$status_lancamentos  = $this->input->get('status');
$tipo_lancamentos    = $this->input->get('tipo');
$periodo_lancamentos = $this->input->get('periodo');
$inicio              = $this->input->get('dataInicial');
$fim                 = $this->input->get('dataFinal');
$saidasPendentes     = null;
$entradasPendentes   = null;
$saidasEfetivadas    = null;
$entradasEfetivadas  = null;
$totalSaidas         = null;
$totalEntradas       = null;
$totalGeralMes       = null;
$totalOcultosMes     = null;
$totalGeral          = $total->total;
$saldoProvisorioMes  = null;
$saldoMesComOculto   = null;
$prevLink            = null;
$nextLink            = null;
$currentMonthText    = null;
$monthFilterButtons  = null;

if (isset($referenceMonth) && $referenceMonth) {
    if ($prevMonth && $nextMonth) {
        $prevLinkTitle = sprintf('%s / %s', $prevMonth, $prevReferenceYear);
        $nextLinkTitle = sprintf('%s / %s', $nextMonth, $nextReferenceYear);
    }

    $prevLink         = "<a href=" . base_url(sprintf('financeiro/lancamentos?periodo=mensal&mesReferencia=%s&anoReferencia=%s', $prevReferenceMonth, $prevReferenceYear)) .
        " title='$prevLinkTitle'><span class='badge badge-primary'><i style='margin: 0 !important;' class='fas fa-angle-double-left'></i></span></a>";
    $currentMonthText = "<a href='#modalSelectMounth' data-toggle='modal' role='button' title='Clique para selecionar um mes específico'><span class='badge badge-primary' style='margin-left: 10px;'>Período: $month / $referenceYear</span></a>";
    $nextLink         = "<a href=" . base_url(sprintf('financeiro/lancamentos?periodo=mensal&mesReferencia=%s&anoReferencia=%s', $nextReferenceMonth, $nextReferenceYear)) .
        " title='$nextLinkTitle'><span class='badge badge-primary' style='margin-left: 10px;'><i style='margin: 0 !important;' class='fas fa-angle-double-right'></i></span></a>";

    $monthFilterButtons = $referenceMonth ? $prevLink . $currentMonthText . $nextLink : null;
}
?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-chart-mixed-up-circle-dollar fa-lg fa-fw"></i>
            Lançamentos
        </h3>
        <div class="row mr5 ml5">
            <div class="panel-ctrls ml5">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamentos')) { ?>
                    <a href="#modalEntrada" id="novaEntrada" data-toggle="modal" role="button" class="btn btn-success btn-sm tip-bottom" title="Registrar nova entrada">
                        <i class="fas fa-plus fa-fw"></i>
                        <span>Entrada</span>
                    </a>
                    <a href="#modalSaida" id="novaSaida" data-toggle="modal" role="button" class="btn btn-danger btn-sm tip-bottom" title="Registrar nova saída">
                        <i class="fas fa-plus fa-fw"></i>
                        <span>Saída</span>
                    </a>
                <?php } ?>
            </div>
            <div class="panel-ctrls">
                <div class="btn-group dropdown-hover">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-bars fa-fw"></i>
                        <span>Opções</span>
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
                        <li>
                            <a href="#modalSelectDefaultMounth" data-toggle="modal">
                                <i class="fas fa-cog fa-fw pull-right"></i>
                                <span>Configurações</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-heading">
        <h2>
            Saldo Resumido
        </h2>
        <div class="panel-ctrls">
            <a href="#" class="button-icon close-panel">
                <i class="fas fa-times"></i>
            </a>
            <a href="#" class="button-icon expand">
                <i class="fas fa-expand-arrows-alt expand-icon"></i>
            </a>
            <a href="#" class="button-icon panel-collapse">
                <i class="fas fa-plus"></i>
            </a>
        </div>
    </div>
    <div class="panel-body panel-no-padding" style="display: none;">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <th colspan="2" style="text-align: left !important;">Descrição</th>
                <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
            </tr>
            </thead>
            <tr>
                <td colspan="2" style="text-align: left;">(±) SALDO PROVISÓRIO EM CONTA</td>
                <td colspan="1" style="text-align: right;">
                    <?php echo number_format($total_provisorio->total, 2, ',', '.') ?>
                </td>
            </tr>
            <?php if ($saidas_pendentes->total) { ?>
                <tr>
                    <td colspan="2" style="text-align: left; color: red">(-) SALDO DE SAÍDAS A CONFIRMAR</td>
                    <td colspan="1" style="text-align: right; color: red">
                        <?php echo number_format($saidas_pendentes->total, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($entradas_pendentes->total) { ?>
                <tr>
                    <td colspan="2" style="text-align: left; color: green">(+) SALDO DE ENTRADAS A CONFIRMAR</td>
                    <td colspan="1" style="text-align: right; color: green">
                        <?php echo number_format($entradas_pendentes->total, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="2" style="text-align: left; font-weight: bold">(=) SALDO DISPONÍVEL EM CONTA</td>
                <td colspan="1" style="text-align: right; font-weight: bold">
                    <strong>
                        <?php echo number_format($total->total, 2, ',', '.') ?>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
if (!$results) {
    ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                <span style='margin-right: 10px !important;'>Extrato de Lançamentos</span>
                <br class="visible-xs-block">
                <?= $monthFilterButtons ?? null ?>
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
                    <td colspan="5">
                        Nenhum registro encontrado para o período solicitado
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                <span style='margin-right: 10px !important;'>Extrato de Lançamentos</span>
                <br class="visible-xs-block">
                <?= $monthFilterButtons ?>
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
                    <button class="btn btn-info btn-sm modalCopiarSerie " id="copiar_serie" title="Copiar todos os lançamentos selecionados" disabled>
                        <i class="fass fa-copy fa-fw"></i>
                        Copiar
                    </button>
                    <button class="btn btn-danger btn-sm modalExcluirSerie " id="excluir_serie" title="Excluir todos os lançamentos selecionados" disabled>
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
            </div>
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th class="th_soma hidden" style="width: 10px !important;">Soma</th>
                    <th>Data</th>
                    <th>Descrição<br>Fornecedor</th>
                    <th>Valor (R$)<br>Forma Pagamento</th>
                    <th>Tipo<br>Status</th>
                    <th style="width: 130px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $pendingNotification = null;
                foreach ($results as $r) {
                    if ($r->oculto) {
                        $totalOcultosMes += $r->valor;
                        continue;
                    }

                    $dataLancamentos = date(('d/m/Y'), strtotime($r->data_lancamento));
                    $dataPagamento   = isset($r->data_pagamento) ? date('d/m/Y', strtotime($r->data_pagamento)) : null;
                    $diaDaSemana     = getExtendedWeekDayName($r->data_lancamento);
                    $colorValue      = 'red';
                    $labelType       = 'alizarin';
                    $descriptionType = 'SAÍDA';
                    $iconValue       = '<i class="far fa-arrow-left fa-rotate-by fa-fw" style="--fa-rotate-angle: 45deg !important;"></i>';
                    $status          = 'EFETIVADO';
                    $label_status    = 'primary';
                    $iconTipo        = '<i class="fas fa-check-circle fa-fw"></i>';
                    $iconExpense     = null;
                    $iconInvoice     = null;
                    $iconObs         = null;
                    $fornecedor      = "&nbsp;";

                    if ($r->baixado == 0) {
                        $pendingNotification = 'notification-dot';
                        $status              = 'PENDENTE';
                        $label_status        = 'orange';
                        $iconTipo            = '<i class="far fa-clock fa-fw"></i>';
                    }

                    if ($r->observacoes) {
                        $iconObs = ' <i class="fas fa-comment-dots fa-fw" title="Observações adicionais"></i>';
                    }

                    if ($r->id_fatura) {
                        $iconInvoice = ' <i class="fas fa-file-invoice-dollar fa-fw" title="Fatura ' . $r->id_fatura . '"></i>';
                    }

                    if ($r->id_despesa) {
                        $iconExpense = ' <i class="fas fa-money-bill-transfer fa-fw" title="Despesa ' . $r->id_despesa . '"></i>';
                    }

                    if ($r->tipo == 1) {
                        $colorValue      = 'green';
                        $labelType       = 'green';
                        $descriptionType = 'ENTRADA';
                        $iconValue       = '<i class="far fa-arrow-right fa-rotate-by fa-fw" style="--fa-rotate-angle: 45deg !important;"></i>';
                    }

                    if ($r->cliente_fornecedor) {
                        $fornecedor = $r->cliente_fornecedor;
                    }

                    foreach ($formasPagamento as $f) {
                        if ($f->id_forma == $r->forma_pgto) $forma_pgto = $f->nome;
                    }

                    if ($r->valor > 0) {
                        if ($r->baixado == 0) {
                            $entradasPendentes += $r->valor;
                        } else {
                            $entradasEfetivadas += $r->valor;
                        }
                    }

                    $valor = number_format($r->valor, 2, ',', '.');

                    if ($r->valor < 0) {
                        if ($r->baixado == 0) {
                            $saidasPendentes += $r->valor;
                        } else {
                            $saidasEfetivadas += $r->valor;
                        }
                        $valor = number_format(abs($r->valor), 2, ',', '.');
                    }

                    $totalGeralMes += $r->valor;

                    echo '<tr>';
                    echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                    echo '<td class="idLancamento hidden">' . $r->id_lancamento . '</td>';
                    echo '<td class="font-weight-bold" title="' . $diaDaSemana . '">' . $dataLancamentos . '</td>';
                    echo '<td class="font-weight-bold"><a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="editar" title="Ver detalhes" idLancamento="' .
                        $r->id_lancamento . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor="' . $valor . '" vencimento="' .
                        $dataLancamentos . '" pagamento="' . $dataPagamento . '" baixado="' .
                        $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" oculto="' . $r->oculto . '">' .
                        strtoupper($r->descricao) . $iconObs . $iconInvoice . $iconExpense .
                        '<br><span class="small" style="color: grey;">' . ($fornecedor) . '</span></a></td>';
                    echo '<td class="font-weight-bold"><span class="valor_parcela" style=" color: ' . $colorValue . '"><span>' . number_format($r->valor, 2, ',', '.') . '</span></span><br><span class="small" style="color: grey;">' . ($forma_pgto) . '</td>';
                    echo '<td><span class="text-' . $labelType . '">' . ($iconValue) . '</span> <span class="badge badge-' . $labelType . '">' . ($descriptionType) . '</span>
                            <br>
                            <span class="text-' . $label_status . '">' . ($iconTipo) . '</span> <span class="badge badge-' . $label_status . '">' . ($status) . '</span></td>';
                    echo '<td>';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamentos')) {
                        echo '<button type="button" href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="btn btn-primary btn-sm editar" title="Ver detalhes" idLancamento="' .
                            $r->id_lancamento . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor="' . $valor . '" vencimento="' .
                            $dataLancamentos . '" pagamento="' . $dataPagamento . '" baixado="' .
                            $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" oculto="' . $r->oculto . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></button>';
                        echo '<button type="button" href="#modalCopiar" style="margin-right: 1%" data-toggle="modal" class="btn btn-info btn-sm copiar" title="Copiar" idLancamento="' .
                            $r->id_lancamento . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor="' . $valor . '" vencimento="' .
                            $dataLancamentos . '" pagamento="' . $dataPagamento . '" baixado="' .
                            $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" oculto="' . $r->oculto . '">
                                <i class="fass fa-copy fa-lg fa-fw"></i></button>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamentos')) {
                        echo '<button type="button" href="#modalExcluir" data-toggle="modal" idLancamento="' . $r->id_lancamento . '" class="btn btn-danger btn-sm excluir" title="Excluir"><i class="fas fa-trash-can-xmark fa-lg fa-fw"></i></button>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                $totalEntradas = $entradasEfetivadas + $entradasPendentes;
                $totalSaidas   = $saidasEfetivadas + $saidasPendentes;
                ?>
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
                        <td colspan="1" style="text-align: right; font-weight: bold" class="valor_soma_parcelas">
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

<!-- POSIÇAO CONSOLIDADA-->
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Posição Consolidada
            <?= (isset($referenceMonth) && $referenceMonth ? " do Período: $month / $referenceYear" : null) ?>
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
    <div class="panel-body panel-no-padding">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <th colspan="2" style="text-align: left !important;">Descrição</th>
                <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
            </tr>
            </thead>
            <?php if ($entradasPendentes) {
                $saldoProvisorioMes = $totalGeral + $entradasPendentes;
                ?>
                <tr>
                    <td class="font-weight-bold" colspan="2" style="text-align: left; color: #5cb85c">(+) SALDO DE ENTRADAS PENDENTES</td>
                    <td class="font-weight-bold" colspan="1" style="text-align: right; color: #5cb85c">
                        <?php echo number_format($entradasPendentes, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($entradasEfetivadas) { ?>
                <tr>
                    <td class="font-weight-bold" colspan="2" style="text-align: left; color: green">(+) SALDO DE ENTRADAS EFETIVADAS</td>
                    <td class="font-weight-bold" colspan="1" style="text-align: right;  color: green">
                        <?php echo number_format($entradasEfetivadas, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($entradasEfetivadas && $entradasPendentes) {
                if ($saldoProvisorioMes) {
                    $saldoProvisorioMes = $totalGeral + $entradasPendentes;
                } else {
                    $saldoProvisorioMes = $totalGeral + $entradasPendentes + $entradasEfetivadas;
                }
                ?>
                <tr>
                    <td class="font-weight-bold" colspan="2" style="text-align: left;">(=) SALDO TOTAL DE ENTRADAS</td>
                    <td class="font-weight-bold" colspan="1" style="text-align: right;">
                        <?php echo number_format($totalEntradas, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>

            <?php if ($saidasPendentes) {
                if ($saldoProvisorioMes) {
                    $saldoProvisorioMes = $saldoProvisorioMes + ($saidasPendentes);
                } else {
                    $saldoProvisorioMes = $totalGeral + ($saidasPendentes);
                }
                ?>
                <tr>
                    <td class="font-weight-bold" colspan="2" style="text-align: left; color: red">(-) SALDO DE SAÍDAS PENDENTES</td>
                    <td class="font-weight-bold" colspan="1" style="text-align: right; color: red">
                        <?php echo number_format($saidasPendentes, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($saidasEfetivadas) { ?>
                <tr>
                    <td class="font-weight-bold" colspan="2" style="text-align: left; color: #d9534f">(-) SALDO DE SAÍDAS EFETIVADAS</td>
                    <td class="font-weight-bold" colspan="1" style="text-align: right; color: #d9534f">
                        <?php echo number_format($saidasEfetivadas, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($saidasEfetivadas && $saidasPendentes) { ?>
                <tr>
                    <td class="font-weight-bold" colspan="2" style="text-align: left;">(=) SALDO TOTAL DE SAÍDAS</td>
                    <td class="font-weight-bold" colspan="1" style="text-align: right;">
                        <?php echo number_format($totalSaidas, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($totalGeralMes) { ?>
                <tr class="total-geral">
                    <td class="font-weight-bold text-black" colspan="2" style="text-align: left;">(TP) SALDO TOTAL DO PERÍODO</td>
                    <td class="font-weight-bold text-black" colspan="1" style="text-align: right;">
                        <?php
                        if ($hiddenItems) echo '<i class="fas fa-lock-keyhole fa-fw vault-icon"></i> ';
                        echo number_format($totalGeralMes, 2, ',', '.')
                        ?>
                    </td>
                </tr>
                <?php if ($totalOcultosMes) { ?>
                    <tr class="hidden provisorio-ocultos">
                        <td class="font-weight-bold text-muted" colspan="2" style="text-align: left;">(TO) TOTAL DE OCULTOS DO PERÍODO</td>
                        <td class="font-weight-bold text-muted" colspan="1" style="text-align: right;">
                            <?php echo number_format($totalOcultosMes, 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php }
                if ($hiddenItems) { ?>
                    <tr class="hidden provisorio-ocultos">
                        <td class="font-weight-bold text-gray" colspan="2" style="text-align: left;">(TP+TO) SALDO TOTAL OCULTO DO PERÍODO</td>
                        <td class="font-weight-bold text-inverse" colspan="1" style="text-align: right;">
                            <?php echo number_format(($totalOcultosMes + $totalGeralMes), 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php }
                if ($totalGeralMes < 0 && $saldoProvisorioMes) {
                    ?>
                    <!--<tr class="hidden provisorio-periodo">-->
                    <!--    <td colspan="2" style="text-align: left; font-weight: bold">(±) SALDO PROVISÓRIO DO PERÍODO</td>-->
                    <!--    <td colspan="1" style="text-align: right; font-weight: bold">-->
                    <!--		--><?php //echo number_format(($saldoProvisorioMes), 2, ',', '.') ?>
                    <!--    </td>-->
                    <!--</tr>-->
                <?php }
            } ?>

        </table>
    </div>
</div>

<!-- LANCAMENTOS OCULTOS-->
<?php if (isset($hiddenItems) && $hiddenItems) { ?>
    <div class="panel panel-midnightblue lancamentos-ocultos hidden">
        <div class="panel-heading">
            <h2>
                <span style='margin-right: 10px !important;'>Lançamentos Ocultos</span>
            </h2>
            <div class="panel-ctrls">
                <a href="#" class="button-icon close-panel">
                    <i class="fas fa-times"></i>
                </a>
                <a href="#" class="button-icon expand">
                    <i class="fas fa-expand-arrows-alt expand-icon"></i>
                </a>
                <a href="#" class="button-icon panel-collapse">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="panel-ctrls">
                <span class="hidden" id="div_btn_marcar">
                    <button class="btn btn-info btn-sm modalCopiarSerie " id="copiar_serie" title="Copiar todos os lançamentos selecionados" disabled>
                        <i class="fas fa-copy fa-fw"></i>
                        Copiar
                    </button>
                    <button class="btn btn-danger btn-sm modalExcluirSerie " id="excluir_serie" title="Excluir todos os lançamentos selecionados" disabled>
                        <i class="fas fa-trash-alt fa-fw"></i>
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
                <!--                <button class="btn btn-default btn-sm habilita_desabilita_soma" id="exibir_soma" title="Habilitar soma de lançamentos individuais">-->
                <!--                    <i class="fas fa-toggle-off fa-fw"></i>-->
                <!--                    Habilitar Soma-->
                <!--                </button>-->
                <!--                <button class="btn btn-default btn-sm habilita_desabilita_soma hidden" id="esconder_soma" title="Desabilitar soma de lançamentos individuais">-->
                <!--                    <i class="fas fa-toggle-on fa-fw"></i>-->
                <!--                    Desabilitar Soma-->
                <!--                </button>-->
            </div>
        </div>
        <div class="panel-body panel-no-padding table-responsive" style="display: none;">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <!--                    <th class="th_soma hidden" style="width: 10px !important;">Soma</th>-->
                    <th>Data</th>
                    <th>Descrição<br>Fornecedor</th>
                    <th>Valor (R$)<br>Forma Pagamento</th>
                    <th>Tipo<br>Status</th>
                    <th style="width: 130px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($hiddenItems as $r) {
                    $dataLancamentos = date(('d/m/Y'), strtotime($r->data_lancamento));
                    $dataPagamento   = isset($r->data_pagamento) ? date('d/m/Y', strtotime($r->data_pagamento)) : null;
                    $status          = 'EFETIVADO';
                    $label_status    = 'primary';
                    $iconTipo        = '<i class="fas fa-check-circle fa-fw"></i>';
                    $colorValue      = 'red';
                    $labelType       = 'danger';
                    $descriptionType = 'SAÍDA';
                    $iconValue       = '<i class="far fa-arrow-right-from-bracket fa-rotate-270 fa-fw"></i>';
                    $fornecedor      = "&nbsp;";
                    $iconObs         = null;
                    $iconExpense     = null;

                    if ($r->baixado == 0) {
                        $status       = 'PENDENTE';
                        $label_status = 'warning';
                        $iconTipo     = '<i class="far fa-clock fa-fw"></i>';
                    }

                    if ($r->observacoes) {
                        $iconObs = ' <i class="fas fa-comment-dots fa-fw" title="Observações adicionais"></i>';
                    }

                    if ($r->id_despesa) {
                        $iconExpense = ' <i class="fas fa-money-bill-transfer fa-fw" title="Despesa ' . $r->id_despesa . '"></i>';
                    }


                    if ($r->tipo == 1) {
                        $colorValue      = 'green';
                        $labelType       = 'success';
                        $descriptionType = 'ENTRADA';
                        $iconValue       = '<i class="far fa-arrow-left-to-bracket fa-rotate-270 fa-fw"></i>';
                    }

                    if ($r->cliente_fornecedor) {
                        $fornecedor = $r->cliente_fornecedor;
                    }

                    foreach ($formasPagamento as $f) {
                        if ($f->id_forma == $r->forma_pgto) {
                            $forma_pgto = $f->nome;
                        }
                    }

                    if ($r->valor < 0) {
                        if ($r->baixado == 0) {
                            $saidasPendentes += $r->valor;
                        } else {
                            $saidasEfetivadas += $r->valor;
                        }
                        $valor = number_format(abs($r->valor), 2, ',', '.');
                    } else {
                        if ($r->baixado == 0) {
                            $entradasPendentes += $r->valor;
                        } else {
                            $entradasEfetivadas += $r->valor;
                        }
                        $valor = number_format($r->valor, 2, ',', '.');
                    }

                    $totalGeralMes += $r->valor;

                    echo '<tr>';
                    // echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                    echo '<td class="idLancamento hidden">' . $r->id_lancamento . '</td>';
                    echo '<td class="font-weight-bold">' . $dataLancamentos . '</td>';
                    echo '<td class="font-weight-bold"><a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="editar" title="Detalhes" idLancamento="' .
                        $r->id_lancamento . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor="' . $valor . '" vencimento="' .
                        $dataLancamentos . '" pagamento="' . $dataPagamento . '" baixado="' .
                        $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" oculto="' . $r->oculto . '">' .
                        strtoupper($r->descricao) . $iconObs . $iconExpense .
                        '<br><span class="small" style="color: grey;">' . ($fornecedor) . '</span></a></td>';
                    echo '<td class="font-weight-bold"><span class="valor_parcela" style=" color: ' . $colorValue . '"><span>' .
                        number_format($r->valor, 2, ',', '.') . '</span></span><br><span class="small" style="color: grey;">' . ($forma_pgto) . '</td>';
                    echo '<td><span class="text-' . $labelType . '">' . ($iconValue) . '</span> <span class="badge badge-' . $labelType . '">' . ($descriptionType) . '</span>
                            <br>
                            <span class="text-' . $label_status . '">' . ($iconTipo) . '</span> <span class="badge badge-' . $label_status . '">' . ($status) . '</span></td>';
                    echo '<td>';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamentos')) {
                        echo '<button type="button" href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="btn btn-primary btn-sm editar" title="Detalhes" idLancamento="' .
                            $r->id_lancamento . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor="' . $valor . '" vencimento="' .
                            $dataLancamentos . '" pagamento="' . $dataPagamento . '" baixado="' .
                            $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" oculto="' . $r->oculto . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></button>';
                        echo '<button type="button" href="#modalCopiar" style="margin-right: 1%" data-toggle="modal" class="btn btn-info btn-sm copiar" title="Copiar" idLancamento="' .
                            $r->id_lancamento . '" descricao="' . $r->descricao . '" observacoes="' . nl2br($r->observacoes) . '" valor="' . $valor . '" vencimento="' .
                            $dataLancamentos . '" pagamento="' . $dataPagamento . '" baixado="' .
                            $r->baixado . '" fornecedor="' . $r->cliente_fornecedor . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '" oculto="' . $r->oculto . '">
                                <i class="fas fa-copy fa-lg fa-fw"></i></button>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamentos')) {
                        echo '<button type="button" href="#modalExcluir" data-toggle="modal" idLancamento="' . $r->id_lancamento . '" class="btn btn-danger btn-sm excluir" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw"></i></button>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }

                $totalEntradas = $entradasEfetivadas + $entradasPendentes;
                $totalSaidas   = $saidasEfetivadas + $saidasPendentes;
                ?>
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
                                <option value="efetivado" <?= ($status_lancamentos == 'efetivado') ? 'selected' : null ?>>EFETIVADO
                                </option>
                                <option value="pendente" <?= ($status_lancamentos == 'pendente') ? 'selected' : null ?>>PENDENTE
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6" style="margin-left: 0">
                            <label class="tooltips font-weight-bold" title="Filtrar lançamentos por período específico">Período <i class="fa fa-info-circle fa-fw"></i></label>
                            <select name="periodo" id="select_periodo" class="form-control">
                                <option value="">
                                    << Selecione>>
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
                                <option value="mensal" <?php if (
                                    $periodo_lancamentos == 'mensal' ||
                                    !$periodo_lancamentos
                                ) {
                                    echo 'selected';
                                } ?>>MÊS/ANO ESPECÍFICOS
                                </option>
                                <option value="especifico" <?php if ($periodo_lancamentos == 'especifico') {
                                    echo
                                    'selected';
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
                        <div class="form-group col-lg-6" id="div_periodo_mensal" hidden>
                            <label class="control-label font-weight-bold" for="select_mes">
                                Mês/ano específicos
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">mês</span>
                                <select class="form-control" id="mesReferencia" name="mesReferencia">
                                    <option value="">
                                        << Selecione>>
                                    </option>
                                    <option value="01" <?= ($referenceMonth == '01') ? 'selected' : null ?>>01 - JANEIRO
                                    </option>
                                    <option value="02" <?= ($referenceMonth == '02') ? 'selected' : null ?>>02 - FEVEREIRO
                                    </option>
                                    <option value="03" <?= ($referenceMonth == '03') ? 'selected' : null ?>>03 - MARÇO
                                    </option>
                                    <option value="04" <?= ($referenceMonth == '04') ? 'selected' : null ?>>04 - ABRIL
                                    </option>
                                    <option value="05" <?= ($referenceMonth == '05') ? 'selected' : null ?>>05 - MAIO
                                    </option>
                                    <option value="06" <?= ($referenceMonth == '06') ? 'selected' : null ?>>06 - JUNHO
                                    </option>
                                    <option value="07" <?= ($referenceMonth == '07') ? 'selected' : null ?>>07 - JULHO
                                    </option>
                                    <option value="08" <?= ($referenceMonth == '08') ? 'selected' : null ?>>08 - AGOSTO
                                    </option>
                                    <option value="09" <?= ($referenceMonth == '09') ? 'selected' : null ?>>09 - SETEMBRO
                                    </option>
                                    <option value="10" <?= ($referenceMonth == '10') ? 'selected' : null ?>>10 - OUTUBRO
                                    </option>
                                    <option value="11" <?= ($referenceMonth == '11') ? 'selected' : null ?>>11 - NOVEMBRO
                                    </option>
                                    <option value="12" <?= ($referenceMonth == '12') ? 'selected' : null ?>>12 - DEZEMBRO
                                    </option>
                                </select>
                                <span class="input-group-addon">ano</span>
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
                            <input class="form-control descricao-autocomplete" id="descricao" type="text" name="descricao"/>
                            <input id="urlEntrada" type="hidden" class="urlAtual" name="urlAtual" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedor">Fornecedor</label>
                            <input class="form-control fornecedor-autocomplete" type="text" name="fornecedor"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-xs-6">
                            <label for="valor" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-6 col-xs-6">
                            <label for="vencimento" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker" type="text" name="vencimento"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-xs-6 ">
                            <div class="row">
                                <input type="checkbox" id="recebido" name="pago" class="switch-input pago primary" value="1">
                                <label for="recebido" class="switch-label primary font-weight-bold">Pago</label>
                            </div>
                        </div>
                        <div class="form-group col-lg-6 col-xs-6">
                            <div class="row">
                                <input type="checkbox" id="ocultoEntrada" name="oculto" class="switch-input primary oculto" value="1">
                                <label for="ocultoEntrada" class="switch-label primary font-weight-bold">Oculto</label>
                            </div>
                        </div>
                        <div id="divRecebimento" class="divPagamento hidden">
                            <div class="form-group col-lg-6 col-xs-5">
                                <label for="recebimento" class="font-weight-bold">Data de Pagamento</label>
                                <input class="form-control datepicker" id="recebimento" type="text" name="recebimento"/>
                            </div>
                            <div class="form-group col-lg-6 col-xs-7">
                                <label for="formaPgto" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" class="form-control">
                                    <option value="">
                                        << Selecione>>
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
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4" style="margin-bottom: 0;">
                            <a href="javascript:" class="font-weight-bold obsLink">
                                <i id="obsIcon" class="fas fa-plus fa-fw"></i>
                                <span class="obsText">Adicionar observações</span>
                            </a>
                        </div>
                        <div class="divObservacoes hidden">
                            <div class="form-group col-lg-8 mb0">
                                <label for="observacoes" class="font-weight-bold">Observações</label>
                                <textarea rows="5" class="form-control observacoesTextarea" id="observacoesEntrada" name="observacoes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check fa-fw"></i>
                        Salvar
                    </button>
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
                            <input class="form-control descricao-autocomplete" type="text" name="descricao"/>
                            <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedor">Fornecedor</label>
                            <input class="form-control fornecedor-autocomplete" type="text" name="fornecedor"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-xs-6">
                            <label for="valor" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-6 col-xs-6">
                            <label for="vencimento" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker" type="text" name="vencimento"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-xs-6 ">
                            <div class="row">
                                <input type="checkbox" id="pago" name="pago" class="switch-input pago primary" value="1">
                                <label for="pago" class="switch-label primary font-weight-bold">Pago</label>
                            </div>
                        </div>
                        <div class="form-group col-lg-6 col-xs-6">
                            <div class="row">
                                <input type="checkbox" id="ocultoSaida" name="oculto" class="switch-input primary oculto" value="1">
                                <label for="ocultoSaida" class="switch-label primary font-weight-bold">Oculto</label>
                            </div>
                        </div>
                        <div id="divPagamento" class="divPagamento hidden">
                            <div class="form-group col-lg-6 col-xs-5">
                                <label for="pagamento" class="font-weight-bold">Data de Pagamento</label>
                                <input class="form-control datepicker" id="pagamento" type="text" name="pagamento"/>
                            </div>
                            <div class="form-group col-lg-6 col-xs-7">
                                <label for="formaPgto" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" class="form-control">
                                    <option value="">
                                        << Selecione>>
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
                                <textarea rows="5" class="form-control observacoesTextarea" id="observacoesSaida" name="observacoes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fa fa-check fa-fw"></i>
                        Salvar
                    </button>
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
                <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricaoCopiar">Descrição *</label>
                            <input class="form-control descricao" id="descricaoCopiar" type="text" name="descricao"/>
                            <input type="hidden" class="id" id="idCopiar" name="id" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedorCopiar">Fornecedor</label>
                            <input class="form-control fornecedor" id="fornecedorCopiar" type="text" name="fornecedor"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="valorCopiar" class="font-weight-bold">Valor *</label>
                            <input class="form-control money valor" id="valorCopiar" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="vencimentoCopiar" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker dataLancamentos" id="vencimentoCopiar" type="text" name="vencimento"/>
                        </div>
                        <div class="form-group col-lg-4 col-xs-12">
                            <label for="tipoCopiar" class="font-weight-bold">Tipo</label>
                            <select class="form-control tipo" name="tipo" id="tipoCopiar">
                                <option value="1">ENTRADA</option>
                                <option value="2">SAÍDA</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-xs-6 ">
                            <div class="row">
                                <input type="checkbox" id="pagoCopiar" name="pago" class="switch-input primary pago" value="1">
                                <label for="pagoCopiar" class="switch-label primary font-weight-bold">Pago</label>
                            </div>
                        </div>
                        <div class="form-group col-lg-6 col-xs-6">
                            <div class="row">
                                <input type="checkbox" id="ocultoCopiar" name="oculto" class="switch-input primary oculto" value="1">
                                <label for="ocultoCopiar" class="switch-label primary font-weight-bold">Oculto</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="divPagamento hidden">
                            <div class="form-group col-lg-6 col-xs-5">
                                <label for="pagamentoCopiar" class="font-weight-bold">Data Pagamento</label>
                                <input class="form-control datepicker dataPagamento" id="pagamentoCopiar" type="text" name="pagamento"/>
                            </div>
                            <div class="form-group col-lg-6 col-xs-7">
                                <label for="formaPgtoCopiar" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" id="formaPgtoCopiar" class="form-control formaPgto">
                                    <option value="">
                                        << Selecione>>
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
                                <textarea rows="5" class="form-control observacoesTextarea" id="observacoesCopiar" type="text" name="observacoes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-check fa-fw"></i> Copiar</button>
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
                <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricaoEditar">Descrição *</label>
                            <input class="form-control descricao" id="descricaoEditar" type="text" name="descricao"/>
                            <input type="hidden" class="id" id="idEditar" name="id" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="fornecedorEditar">Fornecedor</label>
                            <input class="form-control fornecedor" id="fornecedorEditar" type="text" name="fornecedor"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="valorEditar" class="font-weight-bold">Valor *</label>
                            <input class="form-control money valor" id="valorEditar" type="text" name="valor"/>
                        </div>
                        <div class="form-group col-lg-4 col-xs-6">
                            <label for="dataLancamentosEditar" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker dataLancamentos" id="dataLancamentosEditar" type="text" name="vencimento"/>
                        </div>
                        <div class="form-group col-lg-4 col-xs-12">
                            <label for="tipoEditar" class="font-weight-bold">Tipo</label>
                            <select class="form-control tipo" name="tipo" id="tipoEditar">
                                <option value="1">ENTRADA</option>
                                <option value="2">SAÍDA</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-xs-6 ">
                            <div class="row">
                                <input type="checkbox" id="pagoEditar" name="pago" class="switch-input pago primary" value="1">
                                <label for="pagoEditar" class="switch-label primary font-weight-bold">Pago</label>
                            </div>
                        </div>
                        <div class="form-group col-lg-6 col-xs-6">
                            <div class="row">
                                <input type="checkbox" id="ocultoEditar" name="oculto" class="switch-input primary oculto" value="1">
                                <label for="ocultoEditar" class="switch-label primary font-weight-bold">Oculto</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="divPagamento hidden">
                            <div class="form-group col-lg-6 col-xs-5">
                                <label for="dataPagamentoEditar" class="font-weight-bold">Data Pagamento</label>
                                <input class="form-control datepicker dataPagamento" id="dataPagamentoEditar" type="text" name="pagamento"/>
                            </div>
                            <div class="form-group col-lg-6 col-xs-7">
                                <label for="formaPgtoEditar" class="font-weight-bold">Forma Pagamento *</label>
                                <select name="formaPgto" id="formaPgtoEditar" class="form-control formaPgto">
                                    <option value="">
                                        << Selecione>>
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
                                <textarea rows="5" class="form-control observacoesTextarea" id="observacoesEditar" name="observacoes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i>
                        Cancelar
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

<!-- Modal EXCLUIR -->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir lançamento</h4>
            </div>
            <form id="formExcluir" action="<?= base_url('financeiro/lancamentos/excluir'); ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Deseja realmente excluir este lançamento?</p>
                    <input id="idExcluir" type="hidden" name="id" value=""/>
                    <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i>
                        Excluir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EXCLUIR SERIE -->
<div class="modal fade" id="modalExcluirSerie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir série de lançamentos</h4>
            </div>
            <form id="formExcluirSerie" action="<?= base_url('financeiro/lancamentos/excluir'); ?>" method="post">
                <div class="modal-body">
                    <p class="font-weight-bold">Deseja realmente excluir os lançamentos selecionados?</p>
                    <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                </div>
                <div id="deleteSerieFormBody"></div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i>
                        Excluir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal COPIAR SERIE -->
<div class="modal fade" id="modalCopiarSerie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Copiar série de lançamentos</h4>
            </div>
            <form id="formCopiarSerie" action="<?= base_url('financeiro/lancamentos/copiar'); ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="vencimento" class="font-weight-bold">Informe a data alvo para cópia</label>
                            <input class="form-control datepicker" type="text" name="vencimento" autocomplete="off"/>
                        </div>
                    </div>
                    <p class="font-weight-bold">Deseja realmente copiar todos os lançamentos selecionados para a data previamente informada?</p>
                    <input class="urlAtual" type="hidden" name="urlAtual" value=""/>
                </div>
                <div id="copiaSerieFormBody"></div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-info btn-sm"><i class="fa fa-check fa-fw"></i>
                        Copiar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal SELECAO DE MES E ANO -->
<div class="modal fade" id="modalSelectMounth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="form_filtro_mes" method="get">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <p class="font-weight-bold">Selecione o mês e ano específicos para visualizar um período </p>
                    <input type="hidden" name="periodo" value="mensal"/>
                    <input type="hidden" name="mesReferencia" class="selectedMonth"/>
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
                                <span class="input-group-addon">Ano</span>
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
                            <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                                Cancelar
                            </button>
                        </div> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal SELECAO DE MES PADRAO DO USUARIO -->
<div class="modal fade" id="modalSelectDefaultMounth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="form_default_month" action="<?= base_url('financeiro/lancamentos/mesPadrao'); ?>" method="post">  <!-- adicionar no form a rota para método do controller que irá persistir a config do usuario em DB -->
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <p class="font-weight-bold">Configurações de Lançamentos </p>
                    <!--                    <p class="font-weight-bold">O mês escolhido será usado como padrão para visualização do módulo de Lançamentos. </p>-->
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="btn-block">
                            <div class="input-group">
                                <span class="input-group-addon">Mês padrão:</span>
                                <select class="form-control" id="mesPadraoSelect" name="mesPadrao">
                                    <option value="" style="font-style: italic;">
                                        << Não definido >>
                                    </option>
                                    <?php if ($monthList) {
                                        foreach ($monthList as $index => $month) { ?>
                                            <option value="<?= $index ?>" <?= ($defaultMonth == $index ? 'selected' : '') ?>>
                                                <?= $month['name'] ?>
                                            </option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal PESQUISAR -->
<div class="modal fade" id="modalSearch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formSearch" action="<?= base_url('financeiro/lancamentos/pesquisa') ?>" method="get">  <!-- adicionar no form a rota para método do controller que irá persistir a config do usuario em DB -->
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <p class="font-weight-bold">Pesquisar por: </p>
                    <!--                    <p class="font-weight-bold">O mês escolhido será usado como padrão para visualização do módulo de Lançamentos. </p>-->
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <!--<label class="font-weight-bold" for="termo">Termo *</label>-->
                            <input class="form-control descricao" type="text" name="search"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search fa-fw"></i>
                        Pesquisar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
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

    $(".descricao, .descricao-autocomplete").autocomplete({
        source: "<?php echo base_url('financeiro/lancamentos/autoCompleteDescricao'); ?>",
        minLength: 2,
        select: function (event, ui) {
            $(".descricao, .descricao-autocomplete").val(ui.item.label);
        }
    });

    $(".fornecedor, .fornecedor-autocomplete").autocomplete({
        source: "<?php echo base_url('financeiro/lancamentos/autoCompleteFornecedor'); ?>",
        minLength: 1,
        select: function (event, ui) {
            $(".fornecedor, .fornecedor-autocomplete").val(ui.item.label);
        }
    });

    $('#modalEntrada, #modalSaida, #modalSearch').on('shown.bs.modal', function (e) {
        $('.descricao').focus();
    })

    $('.dropdown-menu>form').click(function (e) {
        e.stopPropagation();
    });

    $('.total-geral').click(function (e) {
        $('.provisorio-periodo, .lancamentos-ocultos, .somatorio-ocultos, .provisorio-ocultos').toggleClass('hidden');
        $('.vault-icon').toggleClass('fa-lock-keyhole, fa-lock-keyhole-open');
    });

    $(document).on('change', '#mesPadraoSelect', function () {
        $("#form_default_month").submit();
    });

    $(document).ready(function ($) {
        $('#select_periodo').change(function () {
            const value = $(this).val();

            if (value === 'especifico') {
                $('#div_intervalo_data').show();
            } else {
                $('#div_intervalo_data').hide();
                $('#dataInicial').val('');
                $('#dataFinal').val('');
            }

            if (value === 'mensal') {
                $('#div_periodo_mensal').show();
            } else {
                $('#div_periodo_mensal').hide();
                $('#mesReferencia').val('');
            }
        });

        $('#select_periodo option:selected').each(function (index, element) {
            const value = $(this).val();

            if (value == 'especifico') {
                $('#div_intervalo_data').show();
            } else {
                $('#div_intervalo_data').hide();
                $('#dataInicial').val('');
                $('#dataFinal').val('');
            }

            if (value === 'mensal') {
                $('#div_periodo_mensal').show();
            } else {
                $('#div_periodo_mensal').hide();
                $('#mesReferencia').val('');
            }
        });

        $('#modalDetalhes').on('hidden.bs.modal', function () {
            $('#formReceita, #formDespesa').trigger("reset");
        });

        $('.pago').on('change', function (event) {
            togglePaymentDiv(event);
        });

        function togglePaymentDiv(event) {
            const checked = event.target.checked;
            if (checked === true) {
                $('.divPagamento').removeClass('hidden');
            } else {
                $('.divPagamento').addClass('hidden');
            }
            $('.pagamento').val('');
        }

        function calculaValorParcela(parcelas, valor) {
            valor = jquery_format(valor);

            var valor_parcela = valor / parcelas;

            valor_parcela = br_format(valor_parcela);

            return (valor_parcela)
        }

        function jquery_format(valor) {
            // Remove todos os .
            valor = valor.replace(/\./g, "");

            // Troca todas as , por .
            valor = valor.replace(",", ".");

            // Converte para float
            valor = parseFloat(valor);
            valor = parseFloat(valor) || 0.0;

            return valor
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
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
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
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
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
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
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
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }
        });

        $("#formCopiarSerie").validate({
            rules: {
                vencimento: {
                    required: true
                },
            },
            messages: {
                vencimento: {
                    required: 'Preenchimento obrigatório'
                }
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

        $("#formSearch").validate({
            rules: {
                search: {
                    required: true
                },
            },
            messages: {
                search: {
                    required: 'Informe o termo da pesquisa'
                }
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
            $("#idExcluir").val($(this).attr('idLancamento'));
        })
    })

    $(document).on('click', '.editar, .copiar', function (event) {
        $(".id").val($(this).attr('idLancamento'));
        $(".descricao").val($(this).attr('descricao'));
        $(".fornecedor").val($(this).attr('fornecedor'));
        $(".valor").val($(this).attr('valor'));
        $(".dataPagamento").val($(this).attr('pagamento'));
        $(".dataPagamento").datepicker('setDate', $(this).attr('pagamento'));
        $(".dataLancamentos").val($(this).attr('vencimento'));
        $(".dataLancamentos").datepicker('setDate', $(this).attr('vencimento'));
        $(".formaPgto").val($(this).attr('formaPgto'));
        $(".tipo").val($(this).attr('tipo'));
        var baixado = $(this).attr('baixado');
        var oculto = $(this).attr('oculto');

        if (baixado == true) {
            $(".pago").prop('checked', true)
            $(".divPagamento").removeClass('hidden');
        } else {
            $(".pago").prop('checked', false)
            $(".divPagamento").addClass('hidden');
            $('.pagamento').val('');
        }

        if (oculto == true) {
            $(".oculto").prop('checked', true)
        } else {
            $(".oculto").prop('checked', false)
        }

        var observacoes = $(this).attr('observacoes');

        if (observacoes) {
            var text = observacoes.replace(/<br \/> /gi, "\n")
            $(".observacoesTextarea").val(text)

            $(".divObservacoes").removeClass('hidden');
            var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
            var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

            obsIcon.removeClass('fa-plus')
            obsIcon.addClass('fa-minus')
            obsText.text('Remover observações')
        } else {
            $("#observacoesEditar, #observacoesCopiar").val('')
            $(".divObservacoes").addClass('hidden');
            var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
            var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

            obsIcon.removeClass('fa-minus')
            obsIcon.addClass('fa-plus')
            obsText.text('Adicionar observações')
        }
    })

    $('#novaEntrada, #novaSaida').click(function () {
        $('.oculto').prop('checked', false)
        $(".divObservacoes").addClass('hidden');
        $(".observacoesTextarea").val('');
        var obsIcon = $(".divObservacoes").parent().children('div').children('a').children('i')
        var obsText = $(".divObservacoes").parent().children('div').children('a').children('span.obsText')

        obsIcon.removeClass('fa-minus')
        obsIcon.addClass('fa-plus')
        obsText.text('Adicionar observações')
    })
</script>