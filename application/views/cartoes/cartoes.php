<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-credit-card fa-lg fa-fw"></i>
            Lista de Cartões
        </h3>
        <div class="panel-ctrls">
            <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal" title="Filtrar faturas">
                <i class="fas fa-filter fa-fw"></i>
                Filtrar
            </button>
            <a href="<?= base_url('financeiro/cartoes/cadastrar') ?>" class="btn btn-primary btn-sm tip-bottom" title="Cadastrar novo cartão">
                <i class="fas fa-plus fa-fw"></i>
                Novo Cartão
            </a>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <th style="text-align: left !important;">Bandeira</th>
                <th style="text-align: left !important;">Final Cartão</th>
                <th style="text-align: left !important;">Nome</th>
                <th style="text-align: left !important;">Tipo Cartão</th>
                <th style="text-align: left !important; width: 180px">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$results) { ?>
                <tr>
                    <td colspan="6">Nenhum cartão cadastrado</td>
                </tr>
            <?php } else {
                foreach ($results as $r) {
                    $n_cartao = explode(" ", trim(decriptar($r->numero)));
                    $final = $n_cartao[3];
                    $mascara = preg_replace('/\d/', '*', $n_cartao);

                    if ($r->principal) {
                        $cartaoPrincipal = 'PRINCIPAL';
                        $labelPrincipal = 'success';
                    } else {
                        $cartaoPrincipal = '';
                    }

                    if ($r->adicional) {
                        if ($r->id_usuario == getUserId()) {
                            $disabled_editar = 'disabled="disabled"';
                            $disabled_excluir = 'disabled="disabled"';
                        } else {
                            $disabled_editar = null;
                            $disabled_excluir = null;
                        }
                        $disabled_adicional = 'disabled="disabled"';
                        $tipo_cartao = 'ADICIONAL';
                        $label_cartao = 'warning';
                    } else {
                        $disabled_editar = null;
                        $disabled_excluir = null;
                        $disabled_adicional = null;
                        $tipo_cartao = 'TITULAR';
                        $label_cartao = 'primary';
                    }
                    ?>
                    <tr>
                        <td><?= $r->bandeira ?></td>
                        <td><?= '**** **** **** ' . $final ?> <span class="label label-<?= $labelPrincipal ?>"><?= $cartaoPrincipal ?></span></td>
                        <td><?= $r->nome ?></td>
                        <td><span class="label label-<?= $label_cartao ?>"><?= $tipo_cartao ?></span></td>
                        <?=
                        '<td>
                            <button href="#modalVisualizarCartao" role="button" data-toggle="modal" numero="' . decriptar($r->numero) . '" validade="' . $r->validade . '" bandeira="' . $r->bandeira . '" cvc="' . decriptar($r->cvc) .
                        '" nome="' . $r->nome . '" class="btn btn-info btn-sm visualizar" title="Visualizar cartão"><i class="fas fa-eye fa-lg fa-fw"></i></button>
                            <a href="' . base_url('financeiro/cartoes/editar/') . $r->id_cartao . '" class="btn btn-primary btn-sm" title="Editar" ' . $disabled_editar . '><i class="fas fa-edit fa-lg fa-fw"></i></a>
                            <a href="' . base_url('financeiro/cartoes/adicional/') . $r->id_cartao . '" class="btn btn-inverse btn-sm" title="Gerar cartão adicional" ' . $disabled_adicional . '><i class="fas fa-credit-card fa-lg fa-fw"></i></a>
                            <button href="#modalExcluir" role="button" data-toggle="modal" id_cartao="' . $r->id_cartao . '" class="btn btn-danger btn-sm excluir" title="Excluir" ' . $disabled_excluir . '><i class="fas fa-trash-alt fa-lg fa-fw" ></i></button>
                        </td>'; ?>
                    </tr>
                <?php }
            } ?>
            </tbody>
        </table>
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
                                <option value="3dias"<?php if ($periodo == '3dias') {
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
                                <option value="aberta"<?php if ($periodo == 'aberta') {
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
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal VISUALIZAR CARTAO-->
<div class="modal fade" id="modalVisualizarCartao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Visualizar cartão</h4>
            </div>
            <form id="formVerCartao" method="post" autocomplete="off">
                <div class="modal-body">
                    <!--                    <p>Defina a data de vencimento padrão para suas faturas.</p>-->
                    <input class="form-control" id="number" name="number" placeholder="Número do cartão *" type="hidden">
                    <input class="form-control" id="name" name="name" placeholder="Nome impresso no cartão" type="hidden">
                    <input class="form-control" id="expiry" name="expiry" placeholder="Validade" type="hidden">
                    <input class="form-control" id="cvc" name="cvc" placeholder="Código de segurança" type="hidden">
                    <div class="row">
                        <div class="card-wrapper"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="ver_cvc" class="btn btn-default btn-sm pull-left" aria-hidden="true">
                        <i class="fa fa-eye fa-fw"></i> Ver CVC
                    </button>
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Fechar
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
                <h4 class="modal-title text-white ">Excluir cartão</h4>
            </div>
            <form id="formNovaFatura" action="<?php echo base_url('financeiro/cartoes/excluir') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir este cartão?</p>
                    <input name="id_cartao" id="id_excluir" type="hidden"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnExcluir"><i class="fa fa-check fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        mountCard('#formVerCartao', '.card-wrapper');

        $(document).on('change', '#select_periodos, #select_status', function () {
            $("#_form_filtro").submit();
        });

        let flipped;
        $('#ver_cvc').click(function () {
            if (flipped == null) {
                $(this).html('<i class="fa fa-eye-slash fa-fw"></i> Ocultar CVC');
                $('.jp-card').toggleClass('jp-card-flipped');
                flipped = true;
            } else {
                $(this).html('<i class="fa fa-eye fa-fw"></i> Ver CVC');
                $('.jp-card').toggleClass('jp-card-flipped');
                flipped = null;
            }
        });

        $('.visualizar').click(function () {
            // $('#number').mask('0000 0000 0000 0000');
            let numero = $(this).attr('numero');
            let nome = $(this).attr('nome');
            let validade = $(this).attr('validade');
            let cvc = $(this).attr('cvc');

            setTimeout(function () {
                var evt = document.createEvent('HTMLEvents');
                evt.initEvent('keyup', false, true);
                document.getElementById('number').dispatchEvent(evt);
                document.getElementById('name').dispatchEvent(evt);
                document.getElementById('expiry').dispatchEvent(evt);
                document.getElementById('cvc').dispatchEvent(evt);
            }, 200);

            setTimeout(function () {
                $("#number").val(numero);
                $("#name").val(nome);
                $("#expiry").val(validade);
                $("#cvc").val(cvc);
            }, 100);
        });

        $(document).on('click', '.fechar', function (event) {
            $("#id_fatura").val($(this).attr('id_fatura'));
            $("#urlFecharFatura").val($(location).attr('href'));
        });

        $(".money").maskMoney();

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

        $("#formNovaFatura").validate({
            rules: {
                vencimento_fatura: {required: true}

            },
            messages: {
                vencimento_fatura: {required: 'Informe a data de vencimento da fatura'}
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

        $("#formPagar").validate({
            rules: {
                forma_pagamento: {required: true}
            },
            messages: {
                forma_pagamento: {required: 'Selecione a forma de pagamento'},
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

        $(document).on('click', '#novaFatura', function () {
            $("#urlFatura").val($(location).attr('href'));
        });

        $(document).on('click', '.excluir', function (event) {
            $("#id_excluir").val($(this).attr('id_cartao'));
        });

        $(document).on('click', '.pagar', function (event) {
            $("#id_fatura_pagar").val($(this).attr('id_fatura'));
            $("#urlPagarFatura").val($(location).attr('href'));
        });

        $(document).on('click', '#pendencia', function () {
            $("#urlPendencia").val($(location).attr('href'));
        });

        $(document).on('click', '#devedor', function () {
            $("#url").val($(location).attr('href'));
        });

        $(document).on('click', '.editar', function (event) {
            $("#id_pendencia").val($(this).attr('id_pendencia'));
            $("#descricaoEditar").val($(this).attr('descricao'));
            $("#id_clienteEditar").val($(this).attr('id_cliente'));
            $("#data_pendenciaEditar").val($(this).attr('data_pendencia'));
            $("#valorEditar").val($(this).attr('valor'));
            $("#urlEditarPendencia").val($(location).attr('href'));
            var baixado = $(this).attr('baixado');
            if (baixado == 1) {
                $("#pagoEditar").attr('checked', true);
                $("#divPagamentoEditar").show();
            } else {
                $("#pagoEditar").attr('checked', false);
                $("#divPagamentoEditar").hide();
            }


        });

        function fecharFatura() {
            var id = $("#idPagar").val();
            var data_pagamento = $("#data_pagamento").val();
            var forma_pagamento = $("#forma_pagamento").val();

            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>index.php/financeiro/pagarPendencia",
                data: {
                    id: id,
                    data_pagamento: data_pagamento,
                    forma_pagamento: forma_pagamento
                },
                dataType: 'json',
                success: function (data) {
                    if (data.result == true) {
                        $("#btnCancelPagar").trigger('click');
                        $("#divLancamentos").html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
                        $("#divLancamentos").load($(location).attr('href') + " #divLancamentos");
                        $("#divSaldoResumido").load($(location).attr('href') + " #divSaldoResumido");
                        $("#divPosicaoConsolidada").load($(location).attr('href') + " #divPosicaoConsolidada");

                        $.toast({
                            heading: 'FEITO!',
                            text: '<h5>Fatura fechada com sucesso!</h5>',
                            position: 'top-center',
                            hideAfter: 3000,
                            showHideTransition: 'fade',
                            loader: false,
                            loaderBg: '#5fc55f',
                            bgColor: '#5bb75b',
                            icon: 'success'
                        });
                    } else {
                        $("#btnCancelPagar").trigger('click');
                        alert('Ocorreu um erro ao tentar pagar a pendência.');
                    }
                }
            });
            return false;
        }
    });
</script>
