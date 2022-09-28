<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Registro de Compras: <?= $nome ?>
        </h2>
        <div class="panel-ctrls">
            <button onclick="javascript:history.back()" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Voltar</button>
            <?php
            ?>
        </div>
    </div>
    <div class="panel-body">
        <?php
        if ($results) { ?>
            <div class="accordion-group" id="accordion">
                <?php
                foreach ($results as $result) {
                    $n_cartao = explode(" ", trim(decriptar($result['cartao']->numero)));
                    $final = $n_cartao[3];
                    $cartaoAlternativeLabel = $result['cartao']->bandeira . ' - FINAL ' . $final;
                ?>
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordion" href="#<?= $result['id_fatura'] ?>">
                            <h2>
                                <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                                Fatura: #<?= $result['id_fatura'] . ' - ' . $result['referencia'] ?>

                                <span style="padding-left: 20px;">
                                    <i class="fas fa-credit-card fa-lg fa-fw"></i>
                                    <?= $result['cartao']->apelido ? $result['cartao']->apelido : $cartaoAlternativeLabel ?>
                                </span>
                            </h2>
                        </a>
                        <div id="<?= $result['id_fatura'] ?>" class="collapse">
                            <div class="accordion-body" style="padding: 0 !important;">
                                <div class="panel" style="margin: 0 !important;">
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
                                                    if (is_array($lancamentoEditavel)) {
                                                        if (in_array($r['id_lancamento'], $lancamentoEditavel, true)) {
                                                            $disabled_lancamento_2 = '';
                                                        } else {
                                                            $disabled_lancamento_2 = 'disabled';
                                                        }
                                                    }

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

                                                    if ($s['estorno'] == 1) {
                                                        $color = 'green';
                                                    } else {
                                                        $color = 'black';
                                                    }

                                                    $data_compra = date(('d/m/y'), strtotime($r['data_compra']));
                                                    $debitoFatura += $r['valor_parcela'];

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
                                                    echo '<td class="valor_parcela" style=" color: ' . $color .
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
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
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