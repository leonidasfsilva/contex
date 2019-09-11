<?php if (!$results) { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                <i class="fa fa-user-circle fa-lg fa-fw"></i>
                Gestão de Usuários do Sistema
            </h2>
            <div class="panel-ctrls">
                <a href="<?php echo base_url(); ?>usuarios/adicionar" class="btn btn-primary btn-sm"><i
                            class="fa fa-plus-square fa-fw"></i> Adicionar Cliente</a>
            </div>
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer"
                   role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th style="width: 30px">ID</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th style="width: 100px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5">Nenhum cliente cadastrado</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h2>
                <i class="fa fa-user-circle fa-lg fa-fw"></i>
                Gestão de Usuários do Sistema
            </h2>
            <div class="panel-ctrls">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                    <a href="<?php echo base_url(); ?>usuarios/adicionar" class="btn btn-primary btn-sm"><i
                                class="fa fa-plus-square fa-fw"></i> Adicionar Usuário</a>
                <?php } ?>
            </div>
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer"
                   role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th style="width: 30px">ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Permissão</th>
                    <th>Status</th>
                    <th style="width: 150px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $r) {

                    if ($r->status == 1) {
                        $status = 'Ativo';
                        $label_status = 'success';
                        $btn_status = '<a href="#modalDesativar" role="button" data-toggle="modal" usuario="' . $r->id_usuarios . '" style="margin-right: 1%" class="btn btn-danger btn-sm" title="Inativar usuário"><i class="fa fa-times-circle fa-lg fa-fw" ></i></a>';
                    } else {
                        $status = 'Inativo';
                        $label_status = 'danger';
                        $btn_status = '<a href="#modalAtivar" role="button" data-toggle="modal" usuario="' . $r->id_usuarios . '" style="margin-right: 1%" class="btn btn-success btn-sm" title="Ativar usuário"><i class="fa fa-check-circle fa-lg fa-fw" ></i></a>';
                    }

                    echo '<tr>';
                    echo '<td>' . $r->id_usuarios . '</td>';
                    echo '<td>' . $r->nome . '</td>';
                    echo '<td>' . $r->email . '</td>';
                    echo '<td>' . $r->permissao . '</td>';
                    echo '<td><span class="label label-' . $label_status . '">' . strtoupper($status) . '</span></td>';
                    echo '<td style="text-align: center">';
                    echo '<a href="' . base_url() . 'usuarios/visualizar/' . $r->id_usuarios . '" style="margin-right: 1%" class="btn btn-info btn-sm" title="Ver detalhes"><i class="fa fa-search-plus fa-lg fa-fw"></i></a>';
                    echo '<a href="' . base_url() . 'usuarios/editar/' . $r->id_usuarios . '" style="margin-right: 1%" class="btn btn-primary btn-sm" title="Editar usuário"><i class="fa fa-edit fa-lg fa-fw"></i></a>';
                    echo $btn_status;
                    echo '</td>';
                    echo '</tr>';
                } ?>
                <tr>

                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

echo $this->pagination->create_links();
?>

<!-- Modal DESATIVAR-->
<div class="modal fade" id="modalDesativar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Desativar usuário</h4>
            </div>
            <form action="<?php echo base_url() ?>usuarios/desativar" method="post">
                <div class="modal-body">
                    <p>Deseja realmente desativar este usuário?</p>
                    <input type="hidden" id="id_desativar" name="id" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i> Desativar</button>
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
                <h4 class="modal-title text-white ">Ativar usuário</h4>
            </div>
            <form action="<?php echo base_url() ?>usuarios/ativar" method="post">
                <div class="modal-body">
                    <p>Deseja realmente ativar este usuário?</p>
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

        $('a').click(function (event) {

            var usuario = $(this).attr('usuario');
            $('#id_ativar, #id_desativar').val(usuario);

        });

    });

</script>

