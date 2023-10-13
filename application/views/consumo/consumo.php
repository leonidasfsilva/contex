<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-lightbulb fa-lg fa-fw"></i>
            Consumo de Energia
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('consumo/configuracoes') ?>" class="btn btn-default btn-sm tip-bottom" title="Configurar variáveis de consumo de energia">
                <i class="fas fa-cog fa-fw"></i>
                Configurações
            </a>
            <button href="#modalNovoConsumo" data-toggle="modal" class="btn btn-primary btn-sm tip-bottom" title="Registrar novo consumo de energia" <?= $configs ? '' : 'disabled' ?>>
                <i class="fas fa-plus fa-fw"></i>
                Registrar Consumo
            </button>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover" role="grid" style="width: 100%;">
            <thead>
                <tr role="row">
                    <th style="text-align: left !important;">#</th>
                    <th style="text-align: left !important;">Data Leitura</th>
                    <th style="text-align: left !important;">Leitura Anterior</th>
                    <th style="text-align: left !important;">Leitura Atual</th>
                    <th style="text-align: left !important;">Consumo</th>
                    <th style="text-align: left !important;">Valor (R$)</th>
                    <th style="text-align: left !important; width: 100px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$results) { ?>
                    <tr>
                        <td colspan="6">Nenhum registro encontrado</td>
                    </tr>
                    <?php } else {
                    foreach ($results as $r) { ?>
                        <tr>
                            <td><?= $r->id ?></td>
                            <td><?= '<a href="#modalEditar" style="margin-right: 0%" data-toggle="modal" class="action" title="Editar" id_consumo="' .
                                    $r->id . '" leitura_anterior="' . $r->leitura_anterior . '" leitura_atual="' . $r->leitura_atual . '" consumo="' . $r->consumo . '" valor="' . number_format($r->valor, 2, ',', '.') . '" data_leitura="' .
                                    date('d/m/Y', strtotime($r->data_leitura)) . '">' . date('d/m/Y', strtotime($r->data_leitura)) . '</a>' ?>
                            </td>
                            <td><?= $r->leitura_anterior ?></td>
                            <td><?= $r->leitura_atual ?></td>
                            <td style="font-weight: bold"><?= $r->consumo ?></td>
                            <td style="font-weight: bold"><?= number_format($r->valor, 2, ',', '.') ?></td>
                            <td>
                                <?= '
                                <a href="#modalEditar" style="margin-right: 0%" data-toggle="modal" class="btn btn-primary btn-sm action" title="Editar" id_consumo="' .
                                    $r->id . '" leitura_anterior="' . $r->leitura_anterior . '" leitura_atual="' . $r->leitura_atual . '" consumo="' . $r->consumo . '" valor="' . number_format($r->valor, 2, ',', '.') . '" data_leitura="' .
                                    date('d/m/Y', strtotime($r->data_leitura)) . '"><i class="fas fa-edit fa-lg fa-fw"></i></a>
                                <button href="#modalExcluir" role="button" data-toggle="modal" id_consumo="' . $r->id . '" class="btn btn-danger btn-sm action" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw" ></i></button>';
                                ?>
                            </td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
        <?php if ($this->pagination->create_links()) { ?>
            <div class="panel-footer">
                <?= $this->pagination->create_links() ?>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Modal NOVO CONSUMO -->
<div class="modal fade" id="modalNovoConsumo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Registrar novo consumo de energia</h4>
            </div>
            <form id="formRegistrarConsumo" action="<?php echo base_url('consumo/registrar') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <!-- <div class="note note-info">
                        <strong>Referência: </strong><span><?= $referencia ?></span>
                    </div> -->
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <div class="input-icon right">
                                <label class="font-weight-bold" for="medicao">Leitura anterior (kWh)</label>
                                <input class="form-control" name="leitura_anterior" placeholder="Leitura anterior (kWh)" type="text">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-icon right">
                                <label class="font-weight-bold" for="medicao">Leitura atual (kWh) *</label>
                                <input class="form-control" name="leitura_atual" placeholder="Leitura atual (kWh)" type="text">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-icon right">
                                <label class="font-weight-bold" for="inicio_medicao">Data de leitura</label>
                                <input class="form-control datepicker" name="data_leitura" placeholder="Data da leitura" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="pull-left btn btn-sm help" type="button" title="Ajuda sobre Consumo de Energia"><i class="far fa-question-circle fa-lg fa-fw"></i> Ajuda</button>
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fas fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR CONSUMO -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Editar consumo de energia</h4>
            </div>
            <form id="formEditarConsumo" action="<?php echo base_url('consumo/editar') ?>" method="post" autocomplete="off">
                <input id="id_editar" name="id" type="hidden">
                <div class="modal-body">
                    <!--                    <div class="note note-info">-->
                    <!--                        <strong>Referência: </strong><span>--><? //= $referencia 
                                                                                        ?><!--</span>-->
                    <!--                    </div>-->
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <div class="input-icon right">
                                <label class="font-weight-bold" for="medicao">Leitura anterior (kWh) *</label>
                                <input class="form-control" id="leitura_anterior_editar" name="leitura_anterior" placeholder="Leitura anterior (kWh)" type="text">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-icon right">
                                <label class="font-weight-bold" for="leitura">Leitura atual (kWh) *</label>
                                <input class="form-control" id="leitura_atual_editar" name="leitura_atual" placeholder="Leitura atual (kWh)" type="text">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-icon right">
                                <label class="font-weight-bold" for="data_leituira">Data da leitura</label>
                                <input class="form-control datepicker" id="data_leitura_editar" name="data_leitura" placeholder="Data da leitura" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="pull-left btn btn-sm help" type="button" title="Ajuda sobre Consumo de Energia"><i class="far fa-question-circle fa-lg fa-fw"></i> Ajuda</button>
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fas fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check fa-fw"></i> Salvar</button>
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
                <h4 class="modal-title text-white ">Excluir Consumo</h4>
            </div>
            <form action="<?php echo base_url('consumo/excluir') ?>" method="post">
                <div class="modal-body">
                    <div class="note note-danger">
                        <strong>Deseja realmente excluir este registro de consumo?</strong>
                    </div>
                    <input type="hidden" id="id_excluir" name="id" />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Modal de ajuda -->
<div class="modal modal-middle fade" id="modalAjuda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Ajuda sobre Consumo de Energia</h4>
            </div>
            <div class="modal-body">
                <p>
                    No campo <strong>Leitura anterior</strong> você pode informar o último registro de kilowatts consumidos, caso se lembre.
                    Este campo é opcional, caso não preencha este campo, o sistema irá efetuar os cálculos com base no último registro informado (caso exista).
                </p>
                <p>
                    No campo <strong>Leitura atual</strong> você deve informar o valor atual dos kilowatts consumidos até o momento que constam no visor do seu medidor de energia.
                </p>
                <p>
                    No campo <strong>Data da leitura</strong> você pode informar a data da leitura que está cadastrando, caso não informe este dado,
                    a data atual (hoje) será registrada automaticamente pelo sistema.
                </p>
                <!-- <p>
                    Após configurar as variáveis de consumo, o sistema utilizará como referência o mês subsequente ao mês cadastrado no campo
                    <strong>Início da medição</strong>, por exemplo:
                </p>
                <div class="note note-info">
                    <strong>Mês cadastrado: JULHO (07)
                        <br>
                        Mês subsequente: AGOSTO (08)</strong>
                </div>
                <p>
                    A cada novo registro de consumo, o sistema irá atribuir o próximo mês como referência automaticamente.
                </p> -->
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('.action').click(function(event) {
            var id = $(this).attr('id_consumo');
            var leitura_atual = $(this).attr('leitura_atual');
            var leitura_anterior = $(this).attr('leitura_anterior');
            var data = $(this).attr('data_leitura');
            $('#id_editar, #id_excluir').val(id);
            $('#leitura_atual_editar').val(leitura_atual);
            $('#leitura_anterior_editar').val(leitura_anterior);
            $('#data_leitura_editar').val(data);
        });
    });

    $('.help').click(function() {
        $('#modalAjuda').modal('show')
    });

    $("#formRegistrarConsumo").validate({
        rules: {
            leitura_atual: {
                required: true,
                number: true
            },
        },
        messages: {
            leitura_atual: {
                required: 'Informe a leitura atual do medidor',
                number: 'Apenas números são permitidos',
            },
        },
        errorClass: "help-block",
        errorElement: "p",
        highlight: function(element, errorClass, validClass) {
            $(element).parents('.form-group').addClass('has-error');
            $(element).parents('.form-group').removeClass('has-success');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.form-group').removeClass('has-error');
            $(element).parents('.form-group').addClass('has-success');
        }
    });

    $("#formEditarConsumo").validate({
        rules: {
            leitura_anterior: {
                required: true,
                number: true
            },
            leitura_atual: {
                required: true,
                number: true
            },
        },
        messages: {
            leitura_anterior: {
                required: 'Informe a leitura anterior do medidor',
                number: 'Apenas números são permitidos',
            },
            leitura_atual: {
                required: 'Informe a leitura atual do medidor',
                number: 'Apenas números são permitidos',
            },
        },
        errorClass: "help-block",
        errorElement: "p",
        highlight: function(element, errorClass, validClass) {
            $(element).parents('.form-group').addClass('has-error');
            $(element).parents('.form-group').removeClass('has-success');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.form-group').removeClass('has-error');
            $(element).parents('.form-group').addClass('has-success');
        }
    });
</script>