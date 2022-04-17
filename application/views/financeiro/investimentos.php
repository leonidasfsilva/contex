<?php $situacao = $this->input->get('situacao');
$periodo = $this->input->get('periodo');
?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-hand-holding-usd fa-lg fa-fw"></i>
            Investimentos
        </h3>
        <div class="panel-ctrls">
            <button href="#modalFiltrar" class="btn btn-default btn-sm" id="filtrar" data-toggle="modal" title="Filtrar lançamentos">
                <i class="fa fa-filter fa-fw"></i>
                Filtrar
            </button>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aInvestimentos')) { ?>
                <a href="#modalAplicacao" id="entrada" data-toggle="modal" role="button" class="btn btn-success btn-sm tip-bottom" title="Registrar nova aplicação">
                    <i class="fas fa-plus fa-fw"></i>
                    Nova Aplicação
                </a>
                <a href="#modalResgate" id="saida" data-toggle="modal" role="button" class="btn btn-danger btn-sm tip-bottom" title="Registrar novo resgate">
                    <i class="fas fa-plus fa-fw"></i>
                    Novo Resgate
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="panel-heading">
        <h2>
            Saldo Resumido
        </h2>
    </div>
    <div class="panel-body panel-no-padding">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <thead>
                <tr role="row">
                    <th colspan="2" style="text-align: left !important;">Descrição</th>
                    <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
                </tr>
            </thead>
            <tr>
                <td colspan="2" style="text-align: left; font-weight: bold">(=) SALDO DISPONÍVEL</td>
                <td colspan="1" style="text-align: right; font-weight: bold">
                    <strong><?php echo number_format($total->total, 2, ',', '.') ?></strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php if (!$results) { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                Extrato de Lançamentos
            </h2>
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
                        <td colspan="5">Nenhum registro encontrado</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                Extrato de Investimentos
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
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                    <tr role="row">
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Valor (R$)</th>
                        <th style="width: 100px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalReceita = 0;
                    $totalDespesa = 0;
                    $saldo = 0;
                    foreach ($results as $r) {
                        $vencimento = date(('d/m'), strtotime($r->data_lancamento));

                        if ($r->tipo == 1) {
                            $color = 'green';
                            $label = 'success';
                        } else {
                            $color = 'red';
                            $label = 'danger';
                        }

                        echo '<tr>';
                        echo '<td>' . $vencimento . '</td>';
                        //                    echo '<td><span class="badge badge-' . $label . '">' . ucfirst($r->tipo) . '</span></td>';
                        echo '<td>' . strtoupper($r->descricao) . '</td>';
                        echo '<td style=" color: ' . $color . '"> ' . number_format($r->valor, 2, ',', '.') . '</td>';

                        if ($r->valor < 0) {
                            $valor = number_format(abs($r->valor), 2, ',', '.');
                        } else {
                            $valor = number_format($r->valor, 2, ',', '.');
                        }

                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eLancamento')) {
                            echo '<a href="#modalEditar" style="margin-right: 1%" data-toggle="modal" class="btn btn-primary btn-sm editar" title="Detalhes" idLancamento="' .
                                $r->id_lancamentos . '" descricao="' . $r->descricao . '" valor="' . $valor . '" vencimento="' .
                                date('d/m/Y', strtotime($r->data_lancamento)) . '" pagamento="' . date('d/m/Y', strtotime($r->data_pagamento ?? null)) . '" formaPgto="' . $r->forma_pgto . '" tipo="' . $r->tipo . '">
                                <i class="fas fa-search-plus fa-lg fa-fw"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dLancamento')) {
                            echo '<a href="#modalExcluir" data-toggle="modal" idLancamento="' . $r->id_lancamentos . '" class="btn btn-danger btn-sm excluir" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw"></i></a>';
                        }

                        echo '</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>
            <div class="panel-footer">
                <?php echo $this->pagination->create_links(); ?>
            </div>
        </div>
    </div>
<?php
} ?>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            Posição Consolidada
        </h2>
    </div>
    <div class="panel-body panel-no-padding">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <thead>
                <tr role="row">
                    <th colspan="2" style="text-align: left !important;">Descrição</th>
                    <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
                </tr>
            </thead>
            <tr>
                <td colspan="2" style="text-align: left;">(+) SALDO PARCIAL EM CONTA</td>
                <td colspan="1" style="text-align: right;">
                    <?php echo number_format($total_entradas->total, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; font-weight: bold">(=) SALDO LÍQUIDO DISPONÍVEL EM CONTA</td>
                <td colspan="1" style="text-align: right; font-weight: bold">
                    <strong><?php echo number_format($total->total, 2, ',', '.') ?></strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Modal FILTRAR -->
<div class="modal fade" id="modalFiltrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Filtrar lançamentos</h4>
            </div>
            <form action="<?php echo current_url(); ?>" method="get" id="form_filtro">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-6" style="margin-left: 0">
                            <label class="tooltips font-weight-bold" title="Filtrar lançamentos por período específico">Período <i class="fa fa-info-circle fa-fw"></i></label>
                            <select name="periodo" id="select_periodos" class="form-control">
                                <option value="">Selecione o período</option>
                                <option value="3dias" <?php if ($periodo == '3dias') {
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
                            <label class="tip-top font-weight-bold" title="Filtrar lançamentos por status">Status <i class="fa fa-info-circle fa-fw"></i></label>
                            <select class="form-control" id="select_status" name="situacao">
                                <option value="todos">Todos</option>
                                <option value="previsto" <?php if ($situacao == 'previsto') {
                                                                echo 'selected';
                                                            } ?>>Previsto
                                </option>
                                <option value="atrasado" <?php if ($situacao == 'atrasado') {
                                                                echo 'selected';
                                                            } ?>>Atrasado
                                </option>
                                <option value="realizado" <?php if ($situacao == 'realizado') {
                                                                echo 'selected';
                                                            } ?>>Efetivado
                                </option>
                                <option value="pendente" <?php if ($situacao == 'pendente') {
                                                                echo 'selected';
                                                            } ?>>Pendente
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

<!-- Modal NOVA APLICACAO -->
<div class="modal fade" id="modalAplicacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Registrar nova aplicação</h4>
            </div>
            <form id="formAplicacao" action="<?php echo base_url('financeiro/investimentos/aplicacao') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição</label>
                            <input class="form-control" id="descricao" type="text" name="descricao" />
                            <input id="urlEntrada" type="hidden" name="urlAtual" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valor" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" id="valor" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="vencimento" class="font-weight-bold">Data de Aplicação</label>
                            <input class="form-control datepicker" id="vencimento" type="text" name="vencimento" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="debito_conta_aplicacao" name="debito_conta" value="1">
                            </div>
                            <label for="debito_conta_aplicacao" class="font-weight-bold">Debitar em Conta Corrente?</label>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="formaPgto" class="font-weight-bold">Forma Aplicação *</label>
                            <select name="formaPgto" id="formaPgto" class="form-control">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $f) { ?>
                                        <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal NOVO RESGATE -->
<div class="modal fade" id="modalResgate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Registrar novo resgate</h4>
            </div>
            <form id="formResgate" action="<?php echo base_url('financeiro/investimentos/resgate') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição</label>
                            <input class="form-control" id="descricao" type="text" name="descricao" />
                            <input id="urlSaida" type="hidden" name="urlAtual" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valor" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" id="valor" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="vencimento" class="font-weight-bold">Data de Resgate</label>
                            <input class="form-control datepicker" id="vencimento" type="text" name="vencimento" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="debito_conta_resgate" name="debito_conta" value="1">
                            </div>
                            <label for="debito_conta_resgate" class="font-weight-bold">Creditar em Conta Corrente?</label>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="formaPgto" class="font-weight-bold">Forma Resgate *</label>
                            <select name="formaPgto" id="formaPgto" class="form-control">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $f) { ?>
                                        <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
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
            <form id="formEditar" action="<?php echo base_url('financeiro/investimentos/editar') ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricaoEditar">Descrição</label>
                            <input class="form-control" id="descricaoEditar" type="text" name="descricao" />
                            <input id="urlAtualEditar" type="hidden" name="urlAtual" value="" />
                            <input type="hidden" id="idEditar" name="id" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="valorEditar" class="font-weight-bold">Valor *</label>
                            <input class="form-control money" id="valorEditar" type="text" name="valor" />
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="vencimentoEditar" class="font-weight-bold">Data de Lançamento</label>
                            <input class="form-control datepicker" id="vencimentoEditar" type="text" name="vencimento" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="tipoEditar" class="font-weight-bold">Tipo</label>
                            <select class="form-control" name="tipo" id="tipoEditar">
                                <option value="1">APLICAÇÃO</option>
                                <option value="2">RESGATE</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="formaPgto" class="font-weight-bold">Forma Transação *</label>
                            <select name="formaPgto" id="formaPgtoEditar" class="form-control">
                                <option value="">
                                    << Selecione >>
                                </option>
                                <?php if ($formasPagamento) {
                                    foreach ($formasPagamento as $f) { ?>
                                        <option value="<?= $f->id_forma ?>"><?= $f->nome ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EXCLUIR-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir lançamento</h4>
            </div>
            <form id="formExcluir" action="<?php echo base_url('financeiro/investimentos/excluir'); ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir este lançamento?</p>
                    <input id="idExcluir" type="hidden" name="id" value="" />
                    <input id="urlExcluir" type="hidden" name="urlAtual" value="" />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on('change', '#select_periodo, #select_situacao', function() {
        $("#form_filtro").submit();
    });

    jQuery(document).ready(function($) {

        $('#modalAplicacao, #modalResgate').on('hidden.bs.modal', function() {
            $('#formAplicacao, #formResgate').trigger("reset");
        });

        $("#formAplicacao").validate({
            rules: {
                descricao: {
                    required: false
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
                    required: 'Selecione a forma de aplicação'
                }
            }
        });

        $("#formResgate").validate({
            rules: {
                descricao: {
                    required: false
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
                    required: 'Selecione a forma de resgate'
                }
            }
        });

        $("#formEditar").validate({
            rules: {
                descricao: {
                    required: false
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
                    required: 'Selecione a forma de transação'
                }
            }
        });


        $(document).on('click', '.excluir', function(event) {
            $("#idExcluir").val($(this).attr('idLancamento'));
            $("#urlExcluir").val($(location).attr('href'));
        });

        $(document).on('click', '#entrada', function() {
            $("#urlEntrada").val($(location).attr('href'));
        });

        $(document).on('click', '#saida', function() {
            $("#urlSaida").val($(location).attr('href'));
        });

        $(document).on('click', '.editar', function(event) {
            $("#idEditar").val($(this).attr('idLancamento'));
            $("#descricaoEditar").val($(this).attr('descricao'));
            $("#fornecedorEditar").val($(this).attr('fornecedor'));
            $("#valorEditar").val($(this).attr('valor'));
            $("#vencimentoEditar").val($(this).attr('vencimento'));
            $("#pagamentoEditar").val($(this).attr('pagamento'));
            $("#formaPgtoEditar").val($(this).attr('formaPgto'));
            $("#tipoEditar").val($(this).attr('tipo'));
            $("#urlAtualEditar").val($(location).attr('href'));
            var baixado = $(this).attr('baixado');
            if (baixado == 1) {
                $("#pagoEditar").iCheck('check');
            } else {
                $("#pagoEditar").iCheck('uncheck');
            }

        });

    });
</script>