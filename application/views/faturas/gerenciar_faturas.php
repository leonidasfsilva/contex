<?php
$situacao = $this->input->get('situacao');
$periodo = $this->input->get('periodo');

if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturas')) {
    if ($faturaAberta != 0) {
        $disabledFatura = 'disabled';
        $disabledLancamento = '';
        $statusFaturaAtual = 'aberta';
    } else {
        $disabledFatura = '';
        $disabledLancamento = 'disabled';
        $statusFaturaAtual = 'fechada';
    }
    if (!$cartoes) {
        $disabledFatura = 'disabled';
        $disabledConfig = 'disabled';
    } else {
        foreach ($cartoes as $c) {
            if ($c->id_usuario != id_usuario()) {
                if ($c->adicional) {
                    if ($cartao_selecionado->id_cartao == $c->id_cartao) {
                        $disabledFatura = 'disabled';
                        $disabledConfig = 'disabled';
                    }
                }
            } else {
                if ($cartao_selecionado->id_cartao == $c->id_cartao) {
                    if ($c->adicional) {
                        $disabledConfig = 'disabled';
                    } else {
                        $disabledConfig = '';
                    }
                }
            }
        }
    }
    if(!$existe_configuracao) {
        $disabledFatura = 'disabled';
    }
} ?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
            Controle de Faturas
        </h3>
        <div class="panel-ctrls">
            <div class=" mt10 pull-right">
                <?php if ($cartoes) { ?>
                    <select name="cartoes" id="cartoes" class="form-control">
                        <?php foreach ($cartoes as $c) {
                            $n_cartao = explode(" ", trim(decriptar($c->numero)));
                            $final = $n_cartao[3]; ?>
                            <!--                <option value=""><< Selecione um Cartão >></option>-->
                            <option value="<?= $c->id_cartao ?>" <?php if ($cartao_selecionado->id_cartao == $c->id_cartao) {
                                echo 'selected';
                                $cartao_config = $c->bandeira . ' - FINAL ' . $final;
                            } else {
                                echo '';
                            } ?>><?= $c->bandeira . ' - FINAL ' . $final ?></option>
                        <?php } ?>
                    </select>
                <?php } else { ?>
                    <input type="text" name="cartoes" id="cartoes" class="form-control"
                           placeholder="Não há cartões cadastrados" disabled/>
                <?php }
                ?>
            </div>
        </div>
    </div>
    <div class="panel-body panel-no-padding">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer"
               role="grid" style="width: 100%;">
            <thead>
            <tr>
                <th colspan="2" style="text-align: left !important;">Descrição</th>
                <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
            </tr>
            </thead>
            <tr>
                <td colspan="2" style="text-align: left; color: green">SALDO DE FATURAS QUITADAS</td>
                <td colspan="1" style="text-align: right; color: green">
                    <?php echo number_format($saldoQuitado->total, 2, ',', '.') ?></td>
            </tr>
            <?php if ($saldoVencidas->total > 0) { ?>
                <tr>
                    <td colspan="2" style="text-align: left; color: red">SALDO DE FATURAS VENCIDAS</td>
                    <td colspan="1" style="text-align: right; color: red">
                        <?php echo number_format($saldoVencidas->total, 2, ',', '.') ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="2" style="text-align: left">SALDO DE FATURAS A VENCER</td>
                <td colspan="1" style="text-align: right">
                    <?php echo number_format($saldoPendente->total, 2, ',', '.') ?></td>
            </tr>

        </table>
    </div>
</div>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Registro de Faturas
        </h2>
        <div class="panel-ctrls">
            <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal"
                    title="Filtrar faturas">
                <i class="fas fa-filter fa-fw"></i>
                Filtrar
            </button>
            <button href="#modalConfiguracoes" class="btn btn-default btn-sm" id="configurar_fatura" data-toggle="modal"
                    title="Configurações de faturas" <?= $disabledConfig ?>>
                <i class="fas fa-cog fa-fw"></i>
            </button>
            <button href="#modalNovaFatura" id="novaFatura" data-toggle="modal" role="button"
                    class="btn btn-primary btn-sm tip-bottom"
                    title="Abrir nova fatura" <?= $disabledFatura ?>>
                <i class="fas fa-plus fa-fw"></i>
                Nova Fatura
            </button>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover" role="grid"
               style="width: 100%;">
            <thead>
            <tr role="row">
                <th>Referência</th>
                <th>Vencimento</th>
                <th>Valor (R$)</th>
                <th>Status</th>
                <th>Pagamento</th>
                <th style="width: 180px !important;">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($results) {
                $totalReceita = 0;
                $totalDespesa = 0;
                $saldo = 0;
                foreach ($results as $r) {
                    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                    date_default_timezone_set('America/Sao_Paulo');
                    $dateObj = DateTime::createFromFormat('!m', $r->mes_referencia);
                    $month = $dateObj->format('M'); // March
//                        echo strftime('%A, %d de %B de %Y', strtotime('today'));
                    $mes = padronizarString(strftime('%b', strtotime($month)));
                    $ano = ($r->ano_referencia);
                    $disabled = '';
                    $disabledPagar = '';

                    if ($r->fatura_aberta == 0) {
                        $status = 'FECHADA';
                        $label = 'inverse';
                        $disabled = 'disabled';
                        $iconFechar = 'fas fa-lock';

                    } else if ($r->fatura_aberta == 2) {
                        $status = '';
                        $label = '';
                        $disabledPagar = 'disabled';
                        $disabled = 'disabled';
                        $iconFechar = 'fas fa-unlock';

                    } else {
                        $status = 'ABERTA';
                        $label = 'default';
                        $disabledPagar = 'disabled';
                        $iconFechar = 'fas fa-unlock';

                    }

                    if ($r->fatura_paga == 2) {
                        $pagamento = 'PENDENTE';
                        $labelPgto = 'danger';
                        $color = 'red';
                        $iconPagar = 'far fa-check-square';

                    } else if ($r->fatura_paga == 1) {
                        $pagamento = 'PAGA';
                        $labelPgto = 'success';
                        $color = 'green';
                        $iconPagar = 'fas fa-check-square';
                        $disabledPagar = 'disabled';

                    } else {
                        $pagamento = '';
                        $labelPgto = '';
                        $color = 'red';
                        $iconPagar = 'far fa-check-square';
                    }

                    $valor_total = $this->fatura_model->getValorTotalFatura($r->id_fatura);

                    echo '<tr>';
                    echo '<td>' . $mes . ' / ' . $ano . '</td>';
                    echo '<td>' . date(('d/m/Y'), strtotime($r->vencimento)) . '</td>';

                    echo '<td style=" color: ' . $color . '"> ' . number_format($valor_total, 2, ',', '.') . '</td>';
                    echo '<td><span class="label label-' . $label . '">' . strtoupper($status) . '</span></td>';
                    echo '<td><span class="label label-' . $labelPgto . '">' . strtoupper($pagamento) . '</span></td>';

                    echo '<td>';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturas')) {
                        echo '<button ' . $disabledPagar . ' href="#modalPagar" style="margin-right: 1%"  class="btn btn-success btn-sm pagar" data-toggle="modal" title="Pagar Fatura" id_fatura="' . $r->id_fatura . '">
                                <i class="' . $iconPagar . ' fa-lg fa-fw"></i></button>';

                        echo '<button ' . $disabled . ' href="#modalFechar" style="margin-right: 1%"  class="btn btn-inverse btn-sm fechar" data-toggle="modal" title="Fechar Fatura" id_fatura="' . $r->id_fatura . '">
                                <i class="' . $iconFechar . ' fa-lg fa-fw"></i></button>';

                        echo '<a href="' . base_url() . 'financeiro/faturas/detalhes/' . $r->id_fatura . '/' . $cartao_selecionado->id_cartao . '" type="button" id="btn_detalhes" style="margin-right: 1%" class="btn btn-primary btn-sm detalhes" title="Detalhes da Fatura" id_fatura="' .
                            $r->id_fatura . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturas')) {
                        echo '<a href="#modalExcluir" data-toggle="modal" id_fatura="' . $r->id_fatura . '" class="btn btn-danger btn-sm excluir" title="Excluir Fatura"><i class="fas fa-trash-alt fa-lg fa-fw"></i></a>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            } else { ?>
                <tr>
                    <td colspan="6">Nenhuma fatura encontrada</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->pagination->create_links(); ?>

<form id="form_cartao" action="<?php echo base_url(); ?>financeiro/faturas" method="get">
    <input type="hidden" id="id_cartao" name="id_cartao">
</form>

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
                            <label class="tip-top" title="Filtrar faturas por período específico">Período <i
                                        class="fa fa-info-circle fa-fw"></i></label>
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
                            <label class="tip-top" title="Filtrar faturas por status">Status <i
                                        class="fa fa-info-circle fa-fw"></i></label>
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
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal CONFIGURACOES DA FATURA -->
<div class="modal fade" id="modalConfiguracoes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Configurações da fatura</h4>
            </div>
            <form id="formConfigurarFatura" action="<?= base_url('financeiro/faturas/configurar') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p>Defina o dia de vencimento padrão para as faturas do cartão selecionado:</p>
                    <p class="note note-info font-weight-bold"><?= $cartao_config ?></p>
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label class="control-label font-weight-bold" for="vencimento_fatura">
                                Dia de vencimento *
                            </label>
                            <select class="form-control" id="select_dia" name="dia_vencimento">
                                <option value=""><< Selecione >></option>
                                <option value="5"<?= $dia_vencimento == 5 ? 'selected' : '' ?>>Todo dia 05</option>
                                <option value="9"<?= $dia_vencimento == 9 ? 'selected' : '' ?>>Todo dia 09</option>
                                <option value="10"<?= $dia_vencimento == 10 ? 'selected' : '' ?>>Todo dia 10</option>
                                <option value="15"<?= $dia_vencimento == 15 ? 'selected' : '' ?>>Todo dia 15</option>
                                <option value="20"<?= $dia_vencimento == 20 ? 'selected' : '' ?>>Todo dia 20</option>
                                <option value="25"<?= $dia_vencimento == 25 ? 'selected' : '' ?>>Todo dia 25</option>
                                <option value="28"<?= $dia_vencimento == 28 ? 'selected' : '' ?>>Todo dia 28</option>
                            </select>
                        </div>
                        <input class="id_cartao" type="hidden" name="id_cartao"/>
                        <input class="urlAtual" type="hidden" name="urlAtual"/>
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

<!-- Modal ABRIR NOVA FATURA-->
<div class="modal fade" id="modalNovaFatura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Abrir nova fatura</h4>
            </div>
            <form id="formNovaFatura" action="<?= base_url('financeiro/faturas/abrir') ?>" method="post"
                  autocomplete="off">
                <div class="modal-body">
                    <p>Defina a data de vencimento padrão para suas faturas.</p>
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label font-weight-bold" for="vencimento_fatura">
                                Data de vencimento
                            </label>
                            <input class="form-control datepicker" id="vencimento_fatura" type="text" name="vencimento_fatura"/>
                        </div>
                        <input id="id_cartao_nova_fatura" type="hidden" name="id_cartao"/>
                        <input id="urlFatura" type="hidden" name="urlAtual"/>
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

<!-- Modal EXCLUIR -->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir fatura</h4>
            </div>
            <form action="<?php echo base_url() ?>financeiro/faturas/excluir" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir esta fatura?</p>
                    <input name="id_fatura" id="idExcluir" type="hidden" value=""/>
                    <input id="urlExcluirFatura" type="hidden" name="urlAtual" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true"
                            id="btnCancelExcluir">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnExcluir"><i class="fa fa-check fa-fw"></i> Excluir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ALERTA CONFIGURACAO -->
<?php if (!$existe_configuracao) { ?>
    <div class="modal fade alerta-usuario" id="modalAlerta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white ">Configuração pendente</h4>
                </div>
                <div class="modal-body">
                    <p>Não é possível abrir novas faturas porque este cartão não possui parâmetros configurados.</p>
                    <p>Configure os parâmetros da fatura clicando no botão: <i class="fas fa-cog fa-fw"></i></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Modal FECHAR FATURA-->
<div class="modal fade" id="modalFechar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Fechar fatura</h4>
            </div>
            <form id="formFechar" action="<?php echo base_url() ?>financeiro/faturas/fechar" method="post">
                <div class="modal-body">
                    <p>Confirma o fechamento desta fatura?</p>
                    <input id="id_fatura" type="hidden" name="id_fatura" value=""/>
                    <input id="urlFecharFatura" type="hidden" name="urlAtual" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-inverse btn-sm" id="btnFechar">
                        <i class="fa fa-check fa-fw"></i> Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal PAGAR FATURA-->
<div class="modal fade" id="modalPagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Pagar fatura</h4>
            </div>
            <form id="formPagar" action="<?php echo base_url() ?>financeiro/faturas/pagar" method="post"
                  autocomplete="off">
                <div class="modal-body">
                    <p>Confirma o pagamento desta fatura?</p>
                    <input id="id_fatura_pagar" type="hidden" name="id_fatura" value=""/>
                    <input id="urlPagarFatura" type="hidden" name="urlAtual" value=""/>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="data_pagamento">Data do pagamento</label>
                            <input class="datepicker form-control" id="data_pagamento" type="text"
                                   name="data_pagamento"/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="forma_pagamento">Forma de pagamento *</label>
                            <select name="forma_pagamento" id="forma_pagamento" class="form-control">
                                <option value=""><< Selecione >></option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $c) { ?>
                                        <option value="<?= $c->id_forma ?>"><?= $c->nome ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="registrar" name="registrar" value="1">
                            </div>
                            <label for="registrar" class="font-weight-bold">Registrar este pagamento em
                                Lançamentos</label>
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
    $(document).ready(function () {
        $('.alerta-usuario').each(function (key, value) {
            $('.alerta-usuario').modal('show');
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

        $("#formConfigurarFatura").validate({
            rules: {
                dia_vencimento: {required: true}
            },
            messages: {
                dia_vencimento: {required: 'Selecione o dia de vencimento'}
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

        $(document).on('click', '#configurar_fatura', function () {
            let id_cartao = $('#cartoes').val();
            $('.id_cartao').val(id_cartao);
        });

        $(document).on('click', '#novaFatura', function () {
            $("#id_cartao_nova_fatura").val($('#cartoes').val());
            $("#urlFatura").val($(location).attr('href'));
        });

        $(document).on('click', '.excluir', function (event) {
            $("#idExcluir").val($(this).attr('id_fatura'));
            $("#urlExcluirFatura").val($(location).attr('href'));
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
    });
</script>
