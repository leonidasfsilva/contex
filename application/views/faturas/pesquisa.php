<?php
$prevLink         = null;
$nextLink         = null;
$currentMonthText = null;
?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2 class="pb0">
            <span class="pr20">Resultados de busca para: </span><span class="pr20"><?= $busca ?></span>
        </h2>
        <div class="panel-ctrls">
            <a href="<?= base_url('financeiro/faturas') ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Faturas</a>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <?php
        if ($results) { ?>
            <table class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th class="th_soma hidden" style="width: 10px !important;">Soma</th>
                    <th style="width: 100px !important;">Data Compra</th>
                    <th style="width: 300px !important;">Descrição</th>
                    <th style="width: 200px !important;">Terceiro</th>
                    <th>Qnt Parcelas</th>
                    <th>Valor Parcela (R$) <br> Valor Compra (R$)</th>
                    <th style="width: 100px !important;">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $creditoFatura = 0;
                $debitoFatura  = 0;
                $totalSum      = 0;

                foreach ($results as $r) {
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

                    $data_compra  = date(('d/m/y'), strtotime($r['data_compra']));
                    $debitoFatura += $r['valor_parcela'];
                    $totalSum     += $r['valor_parcela'];

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

                    echo '<tr>';
                    echo '<td class="td_soma hidden"><div class="icheck"><input type="checkbox" class="soma_parcelas"></div></td>';
                    echo '<td class="font-weight-bold">' . $data_compra . '</td>';
                    echo '<td class="font-weight-bold"><a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="editar" title="Detalhes" id_lancamento="' .
                        $r['id_lancamento'] . '" descricao="' . $r['descricao'] . '" observacoes="' . nl2br($r['observacoes']) . '" valor="' . $valor . '" data_compra="' .
                        date('d/m/Y', strtotime($r['data_compra'])) . '" parcelada="' . $r['compra_parcelada'] . '" estorno="' . $r['estorno'] . '" n_parcelas="' . $r['total_parcelas'] .
                        '" valor_parcela="' . number_format($r['valor_parcela'], 2, ',', '.') . '" terceiros="' . $r['compra_terceiros'] . '" nome_cliente="' . $r['nome_cliente'] .
                        '" id_cliente="' . $r['id_cliente'] . '">' . strtoupper($r['descricao']) . $iconObs .
                        '</a></td>';
                    echo '<td class="font-weight-bold">' . strtoupper($r['nome_cliente']) . '</td>';
                    echo '<td class="font-weight-bold">' . $total_parcelas . '</td>';
                    echo '<td class="valor_parcela font-weight-bold" style=" color: ' . $color = null .
                            '"><span>' . number_format($r['valor_parcela'], 2, ',', '.') .
                            '</span><br><span style="color: grey">' . number_format($r['valor_total'], 2, ',', '.') .
                            '</span></td>';

                    echo '<td>';
                    echo '<a type="button" href="' . base_url('financeiro/faturas/detalhes/' . $r['id_fatura'] . '/' . $r['id_cartao']) . '" style="margin-right: 1%" data-toggle="modal" class="btn btn-info btn-sm editar" title="Visualizar fatura desta compra">
                                                            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                                                        </a>';

                    echo '</td>';
                    echo '</tr>';
                } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="m20 note note-info font-weight-bold">
                Nenhum registro encontrado
            </div>
        <?php } ?>

        <?php if ($this->pagination->create_links()) { ?>
            <div class="panel-footer">
                <?= $this->pagination->create_links() ?>
            </div>
        <?php } ?>

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