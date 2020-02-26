<style>
    label {
        cursor: pointer;
    }
</style>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-bullhorn fa-lg fa-fw"></i>
            Gestão de Anúncios
        </h3>
        <div class="panel-ctrls">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                <a href="<?php echo base_url('anuncios/adicionar'); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus fa-fw"></i> Novo Anúncio
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <?php if ($results) { ?>
            <thead>
            <tr role="row">
                <th style="width: 30px">ID</th>
                <th>Cabeçalho</th>
                <th>Título</th>
                <th>Status</th>
                <th style="width: 180px">Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php foreach ($results as $r) {
                    if ($r->data_expiracao) {
                        $data_expiracao = date(('d/m/Y'), strtotime($r->data_expiracao));
                    } else {
                        $data_expiracao = null;
                    }

                    $format = "d-m-Y";
                    $hoje = DateTime::createFromFormat($format, date('d-m-Y'));
                    $expiracao = DateTime::createFromFormat($format, date(('d-m-Y'), strtotime($r->data_expiracao)));
                    $validade = $expiracao > $hoje;

                    if ($r->habilitado == 1) {
                        $label_tipo = 'success';
                        $habilitado = 'HABILITADO';

                        if ($data_expiracao != null && $validade == false) {
                            $label_tipo = 'warning';
                            $habilitado = 'EXPIRADO';
                        }
                    } else {
                        $label_tipo = 'danger';
                        $habilitado = 'DESABILITADO';
                    }

                    echo '<td>' . $r->id_anuncio . '</td>';
                    echo '<td>' . $r->cabecalho . '</td>';
                    echo '<td>' . $r->titulo . '</td>';
                    echo '<td><span class="label label-' . $label_tipo . '">' . strtoupper($habilitado) . '</span></td>';
                    echo '<td style="text-align: center">';
                    echo '<a href="#modalConfigurar" data-toggle="modal" id_anuncio="' . $r->id_anuncio . '" data_expiracao="' . $data_expiracao . '" habilitado="' . $r->habilitado . '" nome_usuario="' . $r->nome_usuario . '" direcionado="' . $r->direcionado . '" style="margin-right: 1%" class="btn btn-inverse btn-sm configurar" title="Configurações"><i class="fas fa-cog fa-lg fa-fw"></i></a>';
                    echo '<a href="' . base_url('anuncios/editar/') . $r->id_anuncio . '" style="margin-right: 1%" class="btn btn-info btn-sm" title="Editar"><i class="fas fa-edit fa-lg fa-fw"></i></a>';
                    echo '<a href="#modalCopiar" role="button" data-toggle="modal" id_anuncio="' . $r->id_anuncio . '" style="margin-right: 1%" class="btn btn-primary btn-sm" title="Copiar"><i class="fas fa-copy fa-lg fa-fw" ></i></a>';
                    echo '<a href="#modalExcluir" role="button" data-toggle="modal" id_anuncio="' . $r->id_anuncio . '" style="margin-right: 1%" class="btn btn-danger btn-sm" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw" ></i></a>';
                    echo '</td>';
                    echo '</tr>';
                }
                } else { ?>
                    <td colspan="5">Nenhum anúncio cadastrado</td>
                <?php } ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal CONFIGURAR-->
<div class="modal fade" id="modalConfigurar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Configurações do anúncio</h4>
            </div>
            <form id="formConfigurar" action="<?php echo base_url('anuncios/configurar') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group col-sm-12">
                                <div class="checkbox icheck">
                                    <input type="checkbox" class="form-control" id="habilitado" name="habilitado" value="1">
                                </div>
                                <label for="habilitado" class="font-weight-bold">Habilitar anúncio</label>
                            </div>
                            <div id="div_data">
                                <div class="form-group col-sm-12">
                                    <label for="data_expiracao" class="font-weight-bold ">Data de expiração *</label>
                                    <input class="form-control datepicker" type="text" id="data_expiracao" name="data_expiracao">
                                    <?php if ($data_expiracao != null && $validade == false) { ?>
                                        <span style="color: red" class="font-weight-bold">Anúncio expirado!</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group col-sm-12">
                                <div id="div_botao_link">
                                    <div class="checkbox icheck">
                                        <input type="checkbox" class="form-control" name="direcionado" value="1" id="direcionado">
                                    </div>
                                    <label for="direcionado" class="font-weight-bold">Exibir para um usuário específico</label>
                                </div>
                            </div>
                            <div id="div_usuarios" class="hidden">
                                <div class="form-group col-sm-12">
                                    <label for="nome_usuario" class="font-weight-bold">Nome do usuário *</label>
                                    <input class="form-control" id="nome_usuario" type="text" name="nome_usuario"/>
                                    <input id="id_usuario" type="hidden" name="id_usuario"/>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="id_configurar" name="id_anuncio" value=""/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-inverse btn-sm" id="btn_submit"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal COPIAR-->
<div class="modal fade" id="modalCopiar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Copiar anúncio</h4>
            </div>
            <form action="<?php echo base_url('anuncios/copiar') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente copiar este anúncio?</p>
                    <input type="hidden" id="id_copiar" name="id_anuncio" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-info btn-sm"><i class="fa fa-check fa-fw"></i> Copiar</button>
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
                <h4 class="modal-title text-white ">Excluir anúncio</h4>
            </div>
            <form action="<?php echo base_url('anuncios/excluir') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir este anúncio?</p>
                    <input type="hidden" id="id_excluir" name="id_anuncio" value=""/>
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
    $("#nome_usuario").autocomplete({
        source: "<?php echo base_url('anuncios/autoCompleteUsuario'); ?>",
        minLength: 1,
        select: function (event, ui) {
            $("#id_usuario").val(ui.item.id);
        }
    });

    $('a').click(function (event) {
        var id_anuncio = $(this).attr('id_anuncio');
        $('#id_configurar').val(id_anuncio);
        $('#id_excluir').val(id_anuncio);
        $('#id_copiar').val(id_anuncio);
    });

    $('#habilitado').on('ifChanged', function (event) {
        const checked = event.target.checked;
        if (checked == true) {
            $('#div_data').removeClass('hidden');
        } else {
            $('#div_data').addClass('hidden');
        }
    });

    $('#direcionado').on('ifChanged', function (event) {
        const checked = event.target.checked;
        if (checked == true) {
            $('#div_usuarios').removeClass('hidden');
        } else {
            $('#div_usuarios').addClass('hidden');
            $('#nome_usuario').val('');
        }
    });

    $('.configurar').click(function () {
        $("#nome_usuario").val($(this).attr('nome_usuario'));
        $("#data_expiracao").val($(this).attr('data_expiracao'));
        var direcionado = $(this).attr('direcionado');
        var habilitado = $(this).attr('habilitado');
        if (direcionado == 1) {
            $('#direcionado').iCheck('check');
            $("#div_usuarios").removeClass('hidden');
        } else {
            $('#direcionado').iCheck('uncheck');
            $("#div_usuarios").addClass('hidden');
        }
        if (habilitado == 1) {
            $('#habilitado').iCheck('check');
            $('#div_data').removeClass('hidden');
        } else {
            $('#habilitado').iCheck('uncheck');
            $('#div_data').addClass('hidden');
        }
    });

    $("#formConfigurar").validate({
        rules: {
            data_expiracao: {required: true},
            nome_usuario: {required: true},
            data_compra: {required: false},
            qnt_parcelas: {required: true},
            valor_parcela: {required: true},

        },
        messages: {
            data_expiracao: {required: 'Informe a data de expiração do anúncio'},
            nome_usuario: {required: 'Informe o nome do usuário'},
            data_compra: {required: 'Informe a data da compra'},
            qnt_parcelas: {required: 'Informe o número de parcelas'},
            valor_parcela: {required: 'Informe o valor das parcelas'},
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

</script>
