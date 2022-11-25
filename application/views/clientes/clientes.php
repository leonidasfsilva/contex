<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-users fa-lg fa-fw"></i>
            Cadastro de Clientes
        </h3>
        <div class="panel-ctrls">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                <a href="<?= base_url('clientes/adicionar'); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus fa-fw"></i> Adicionar Cliente
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="panel-body panel-no-padding table-responsive">
        <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <?php if ($results) { ?>
            <thead>
            <tr role="row">
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th style="width: 150px">Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php foreach ($results as $r) {
                    echo '<td><a href="' . base_url('clientes/visualizar/') . $r->id_clientes . '">' . $r->nome . '</a></td>';
                    echo '<td>' . $r->email . '</td>';
                    echo '<td>' . $r->telefone . '</td>';
                    echo '<td style="text-align: center">';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) {
                        echo '<a href="' . base_url('clientes/visualizar/') . $r->id_clientes . '" style="margin-right: 1%" class="btn btn-info btn-sm" title="Ver detalhes"><i class="fas fa-search-plus fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
                        echo '<a href="' . base_url() . 'clientes/editar/' . $r->id_clientes . '" style="margin-right: 1%" class="btn btn-primary btn-sm" title="Editar"><i class="fas fa-edit fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCliente')) {
                        echo '<a href="#modalExcluir" role="button" data-toggle="modal" cliente="' . $r->id_clientes . '" style="margin-right: 1%" class="btn btn-danger btn-sm" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw" ></i></a>';
                    }

                    echo '</td>';
                    echo '</tr>';
                }
                } else { ?>
                    <td colspan="5">Nenhum cliente cadastrado</td>
                <?php } ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal EXCLUIR-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir cliente</h4>
            </div>
            <form action="<?php echo base_url() ?>clientes/excluir" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir este cliente?</p>
                    <input type="hidden" id="idCliente" name="id" value=""/>
                    <input id="urlExcluir" type="hidden" name="urlAtual" value=""/>
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
    $(document).ready(function () {
        $('a').click(function (event) {

            var cliente = $(this).attr('cliente');
            $('#idCliente').val(cliente);
        });
    });
</script>
