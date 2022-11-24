<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-cogs fa-lg fa-fw"></i>
            Configurações do Sistema
        </h3>
        <div class="panel-ctrls">
            <button onclick="getVersion()" role="button" class="btn btn-primary btn-sm" type="button">
                <i class="fas fa-info-circle fa-fw"></i> Versão do Sistema
            </button>
            <!--            <a href="#" class="button-icon close-panel">-->
            <!--                <i class="fas fa-times"></i>-->
            <!--            </a>-->
        </div>
    </div>
</div>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-wrench fa-lg fa-fw"></i>
            Lista de Opções
        </h3>
        <div class="panel-ctrls">
            <button href="#modalNova" role="button" data-toggle="modal" class="btn btn-primary btn-sm" type="button">
                <i class="fas fa-plus fa-fw"></i> Nova Opção
            </button>
            <!--            <a href="#" class="button-icon close-panel">-->
            <!--                <i class="fas fa-times"></i>-->
            <!--            </a>-->
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
                <th style="width: 30px">ID</th>
                <th>Descrição</th>
                <th>Setor</th>
                <th>Status</th>
                <th style="width: 135px">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$results) { ?>
                <tr>
                    <td colspan="5">Nenhum opção de configuração cadastrada</td>
                </tr>
            <?php } else {
                foreach ($results as $r) {

                    if ($r->status == 1) {
                        $status = 'Ativo';
                        $label_status = 'success';
                        $btn_status = '<a href="#modalDesativar" role="button" data-toggle="modal" id_opcao="' . $r->id_opcao . '" style="margin-right: 2%" class="btn btn-warning btn-sm" title="Desativar"><i class="fas fa-minus-circle fa-lg fa-fw" ></i></a>';
                    } else {
                        $status = 'Inativo';
                        $label_status = 'warning';
                        $btn_status = '<a href="#modalAtivar" role="button" data-toggle="modal" id_opcao="' . $r->id_opcao . '" style="margin-right: 2%" class="btn btn-success btn-sm" title="Ativar"><i class="fas fa-check-circle fa-lg fa-fw" ></i></a>';
                    }

                    echo '<tr>';
                    echo '<td>' . $r->id_opcao . '</td>';
                    echo '<td>' . $r->descricao . '</td>';
                    echo '<td>' . $r->setor . '</td>';
                    echo '<td><span class="label label-' . $label_status . '">' . strtoupper($status) . '</span></td>';
                    echo '<td style="text-align: center">';
                    echo '<a href="#modalEditar" role="button" data-toggle="modal" id_opcao="' . $r->id_opcao . '" descricao="' . $r->descricao . '" setor="' . $r->setor . '" style="margin-right: 2%" class="btn btn-primary btn-sm editar" title="Editar"><i class="fas fa-edit fa-lg fa-fw" ></i></a>';
                    echo $btn_status;
                    echo '<a href="#modalExcluir" role="button" data-toggle="modal" id_opcao="' . $r->id_opcao . '" style="margin-right: 2%" class="btn btn-danger btn-sm excluir" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw" ></i></a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal DESATIVAR-->
<div class="modal fade" id="modalDesativar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Desativar opção de configuração</h4>
            </div>
            <form action="<?php echo base_url('configuracoes/desativar') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente desativar esta opção de configuração?</p>
                    <input type="hidden" id="id_desativar" name="id" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-warning btn-sm"><i class="fa fa-check fa-fw"></i> Desativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ATIVAR-->
<div class="modal fade" id="modalAtivar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Ativar opção de configuração</h4>
            </div>
            <form action="<?php echo base_url('configuracoes/ativar') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente ativar esta opção de configuração?</p>
                    <input type="hidden" id="id_ativar" name="id" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-success btn-sm"><i class="fa fa-check fa-fw"></i> Ativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDITAR-->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Editar opção de configuração</h4>
            </div>
            <form id="formEditar" action="<?php echo base_url('configuracoes/editar') ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricao" type="text" name="descricao" value=""/>
                            <input type="hidden" name="id_opcao" id="id_opcao">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label for="setor" class="font-weight-bold">Setor *</label>
                            <select name="setor" id="setor" class="form-control">
                                <option value=""><< Selecione >></option>
                                <?php if ($setores) {
                                    foreach ($setores as $k => $v) { ?>
                                        <option value="<?= $v ?>"><?= $v ?></option>
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

<!-- Modal NOVA OPÇAO-->
<div class="modal fade" id="modalNova" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Adicionar nova opção de configuração</h4>
            </div>
            <form id="formNova" action="<?php echo base_url('configuracoes/adicionar') ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricao" type="text" name="descricao" value=""/>
                            <input type="hidden" name="id_opcao" id="id_opcao">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label for="setor" class="font-weight-bold">Setor *</label>
                            <select name="setor" id="setor" class="form-control">
                                <option value=""><< Selecione >></option>
                                <?php if ($setores) {
                                    foreach ($setores as $k => $v) { ?>
                                        <option value="<?= $v ?>"><?= $v ?></option>
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
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir opção de configuração</h4>
            </div>
            <form action="<?php echo base_url('configuracoes/excluir') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir esta opção de configuração?</p>
                    <span class="note note-danger block font-weight-bold">Atenção! Isto irá excluir permanentemente este registro.</span>
                    <input type="hidden" id="id_excluir" name="id_opcao" value=""/>
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

<script type="text/javascript">
    function getVersion() {
        const Toast = Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 5000,
            timerProgressBar: true,
            icon: 'info',
            title: 'Versão do Sistema: <?= versionApp() ?><br>Versão do PHP: <?= phpversion() ?>',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    }

    $(document).ready(function () {
        $('.excluir').click(function () {
            let id_opcao = $(this).attr('id_opcao');

            $('#id_excluir').val(id_opcao);
        })

        $('.editar').click(function () {
            let descricao = $(this).attr('descricao');
            let setor = $(this).attr('setor');
            let id_opcao = $(this).attr('id_opcao');

            $('#descricao').val(descricao);
            $('#setor').val(setor);
            $('#id_opcao').val(id_opcao);
        })

        $("#formLogo").validate({
            rules: {
                userfile: {required: true}
            },
            messages: {
                userfile: {required: 'Arquivo não informado'}
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

        $("#formNova").validate({
            rules: {
                descricao: {required: true},
                setor: {required: true},
            },
            messages: {
                descricao: {required: 'Informe a descrição da opção'},
                setor: {required: 'Selecione o setor'},
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
                descricao: {required: true},
                setor: {required: true},
            },
            messages: {
                descricao: {required: 'Informe a descrição da opção'},
                setor: {required: 'Selecione o setor'},
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

    });

</script>
