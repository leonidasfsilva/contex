<style type="text/css">

    label.error {
        color: #b94a48;
    }

    input.error {
        border-color: #b94a48;
    }

    input.valid {
        border-color: #5bb75b;
    }

    table {
        font-family: Arial;
        font-size: 11px;
    }

</style>
<?php if (!$results) { ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>
                <i class="fa fa-group fa-lg fa-fw"></i>
                Lista de Clientes
            </h2>
            <div class="panel-ctrls">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                    <a href="<?php echo base_url(); ?>clientes/adicionar" class="btn btn-primary btn-sm"><i class="fa fa-plus-square fa-fw"></i> Adicionar Cliente</a>
                <?php } ?>
            </div>
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>
                <i class="fa fa-group fa-lg fa-fw"></i>
                Lista de Clientes
            </h2>
            <div class="panel-ctrls">
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aLancamento')) { ?>
                    <a href="<?php echo base_url(); ?>clientes/adicionar" class="btn btn-primary btn-sm"><i class="fa fa-plus-square fa-fw"></i> Adicionar Cliente</a>
                <?php } ?>
            </div>
        </div>
        <div class="panel-body panel-no-padding table-responsive">
            <table id="example" class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
                <thead>
                <tr role="row">
                    <th style="width: 30px">ID</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th style="width: 150px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $r) {
                    echo '<tr>';
                    echo '<td>' . $r->idClientes . '</td>';
                    echo '<td>' . $r->nomeCliente . '</td>';
                    echo '<td>' . $r->telefone . '</td>';
                    echo '<td style="text-align: center">';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) {
                        echo '<a href="' . base_url() . 'clientes/visualizar/' . $r->idClientes . '" style="margin-right: 1%" class="btn btn-info btn-sm" title="Ver detalhes"><i class="fa fa-search-plus fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
                        echo '<a href="' . base_url() . 'clientes/editar/' . $r->idClientes . '" style="margin-right: 1%" class="btn btn-primary btn-sm" title="Editar cliente"><i class="fa fa-edit fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dCliente')) {
                        echo '<a href="#modal-excluir" role="button" data-toggle="modal" cliente="' . $r->idClientes . '" style="margin-right: 1%" class="btn btn-danger btn-sm" title="Excluir cliente"><i class="fa fa-trash-o fa-lg fa-fw" ></i></a>';
                    }


                    echo '</td>';
                    echo '</tr>';
                } ?>
                <tr>

                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->pagination->create_links();
} ?>

<!-- Modal -->
<div id="modal-excluir" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/clientes/excluir" method="post">
        <div class="modal-header bg_danger" style="color: white">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">CONFIRMAR EXCLUSÃO</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idCliente" name="id" value=""/>
            <span style="font-size: 11pt">Deseja realmente excluir este cliente e os dados associados a ele (OS, Vendas, Receitas)?</span>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-fw"></i> Cancelar</button>
            <button class="btn btn-danger btn-sm"><i class="fa fa-trash-o fa-fw"></i> Excluir</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {


        $(document).on('click', 'a', function (event) {

            var cliente = $(this).attr('cliente');
            $('#idCliente').val(cliente);

        });

    });

</script>
