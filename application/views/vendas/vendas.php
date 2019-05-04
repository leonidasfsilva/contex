<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aVenda')) { ?>
    <label>&nbsp;</label>
    <a href="<?php echo base_url(); ?>index.php/vendas/adicionar" class="btn btn-primary btn-sm">
        <i class="fa fa-plus-square fa-fw"></i> Nova Venda</a>
<?php } ?>

<?php

if (!$results) { ?>
    <div class="widget-box">
        <div class="widget-title">
        <span class="icon">
            <i class="fa fa-shopping-cart fa-lg fa-fw"></i>
         </span>
            <h5>Vendas</h5>
        </div>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr style="backgroud-color: #2D335B">
                    <th>ID</th>
                    <th>Data da Venda</th>
                    <th>Cliente</th>
                    <th>Faturado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="6">Nenhuma venda cadastrada</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>

    <div class="widget-box">
        <div class="widget-title">
        <span class="icon">
            <i class="fa fa-shopping-cart fa-lg fa-fw"></i>
         </span>
            <h5>Vendas</h5>
        </div>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr style="backgroud-color: #2D335B">
                    <th>ID</th>
                    <th>Data da Venda</th>
                    <th>Cliente</th>
                    <th>Faturado</th>
                    <th style="width: 150px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $r) {
                    $dataVenda = date(('d/m/Y'), strtotime($r->dataVenda));
                    if ($r->faturado == 1) {
                        $faturado = 'SIM';
                        $color = 'green';
                        $label = 'success';
                    } else {
                        $faturado = 'NÃO';
                        $color = 'red';
                        $label = 'danger';
                    }
                    echo '<tr>';
                    echo '<td>' . $r->idVendas . '</td>';
                    echo '<td>' . $dataVenda . '</td>';
                    echo '<td><a href="' . base_url() . 'index.php/clientes/visualizar/' . $r->idClientes . '">' . $r->nomeCliente . '</a></td>';
                    echo '<td><span class="label label-' . $label . '">' . strtoupper($faturado) . '</span></td>';

                    echo '<td style="text-align: center">';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/vendas/visualizar/' . $r->idVendas . '" class="btn btn-info btn-xs tip-top" title="Ver mais detalhes"><i class="fa fa-search-plus fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/vendas/editar/' . $r->idVendas . '" class="btn btn-primary btn-xs tip-top" title="Editar venda"><i class="fa fa-edit fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/vendas/imprimir/' . $r->idVendas . '" target="_blank" class="btn btn-inverse btn-xs tip-top" title="Imprimir"><i class="fa fa-print fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dVenda')) {
                        echo '<a href="#modal-excluir" role="button" data-toggle="modal" venda="' . $r->idVendas . '" class="btn btn-danger btn-xs tip-top" title="Excluir"><i class="fa fa-trash-o fa-lg fa-fw"></i></a>';
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
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/vendas/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Venda</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idVenda" name="id" value=""/>
            <h5 style="text-align: center">Deseja realmente excluir esta Venda?</h5>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-danger">Excluir</button>
        </div>
    </form>
</div>


<script type="text/javascript">
    $(document).ready(function () {


        $(document).on('click', 'a', function (event) {

            var venda = $(this).attr('venda');
            $('#idVenda').val(venda);

        });

    });

</script>