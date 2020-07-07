<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-key fa-lg fa-fw"></i>
            Permissões
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('permissoes/adicionar') ?>" class="btn btn-primary btn-sm tip-bottom" title="Cadastrar nova permissão">
                <i class="fas fa-plus fa-fw"></i>
                Nova Permissão
            </a>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover" role="grid" style="width: 100%;">
            <thead>
            <tr role="row">
                <th style="text-align: left !important;">#</th>
                <th style="text-align: left !important;">Permissão</th>
                <th style="text-align: left !important;">Data Cadastro</th>
                <th style="text-align: left !important;">Status</th>
                <th style="text-align: left !important; width: 140px">Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$results) { ?>
                <tr>
                    <td colspan="6">Nenhuma permissão cadastrada</td>
                </tr>
            <?php } else {
                foreach ($results as $r) {
                    if ($r->ativo == 1) {
                        $status = 'ATIVO';
                        $label_status = 'success';
                        $btn_status = '<button href="#modalDesativar" role="button" data-toggle="modal" id_permissao="' . $r->id . '" style="margin-right: 1%" class="btn btn-success action btn-sm" title="Desativar"><i class="fas fa-power-off fa-lg fa-fw" ></i></a>';
                        $title = 'Desativar';
                    } else {
                        $status = 'INATIVO';
                        $label_status = 'warning';
                        $btn_status = '<button href="#modalAtivar" role="button" data-toggle="modal" id_permissao="' . $r->id . '" style="margin-right: 1%" class="btn btn-warning action btn-sm" title="Ativar"><i class="fas fa-power-off fa-lg fa-fw" ></i></a>';
                        $title = 'Ativar';
                    }
                    ?>
                    <tr>
                        <td><?= $r->id ?></td>
                        <td><?= $r->nome ?></td>
                        <td><?= date('d/m/Y', strtotime($r->criado_em)) ?></td>
                        <td><span class="label label-<?= $label_status ?>"><?= $status ?></span></td>
                        <?=
                        '<td>
                            <a href="' . base_url('permissoes/editar/') . $r->id . '" class="btn btn-primary btn-sm" title="Editar"><i class="fas fa-edit fa-lg fa-fw"></i></a>
                            '.$btn_status.'
                            <button href="#modalExcluir" role="button" data-toggle="modal" id_permissao="' . $r->id . '" class="btn btn-danger btn-sm action" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw" ></i></button>
                        </td>'; ?>
                    </tr>
                <?php }
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
                <h4 class="modal-title text-white ">Desativar permissão</h4>
            </div>
            <form action="<?= base_url('permissoes/desativar') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente desativar esta permissão?</p>
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

<!-- Modal EXCLUIR-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir Permissão</h4>
            </div>
            <form action="<?php echo base_url('permissoes/excluir') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir esta permissão?</p>
                    <input type="hidden" id="id_excluir" name="id" value=""/>
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

<!-- Modal ATIVAR-->
<div class="modal fade" id="modalAtivar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Ativar Permissão</h4>
            </div>
            <form action="<?php echo base_url('permissoes/ativar') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente ativar esta permissão?</p>
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


<script type="text/javascript">
    $(document).ready(function () {
        $('.action').click(function (event) {
            var permissao = $(this).attr('id_permissao');
            $('#id_ativar, #id_desativar, #id_excluir').val(permissao);
        });
    });

</script>
