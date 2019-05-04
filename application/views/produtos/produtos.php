<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) { ?>
    <label>&nbsp;</label>
    <a href="<?php echo base_url(); ?>index.php/produtos/adicionar" class="btn btn-primary btn-sm"><i class="fa fa-plus-square fa-fw"></i> Adicionar Produto</a>
<?php } ?>

<?php

if (!$results) { ?>
    <div class="widget-box">
        <div class="widget-title">
        <span class="icon">
            <i class="fa fa-barcode fa-lg fa-fw"></i>
         </span>
            <h5>Produtos</h5>
        </div>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5">Nenhum Produto Cadastrado</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php } else { ?>

    <div class="widget-box">
        <div class="widget-title">
        <span class="icon">
            <i class="fa fa-barcode fa-lg fa-fw"></i>
         </span>
            <h5>Produtos</h5>
        </div>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr style="backgroud-color: #2D335B">
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th style="width: 100px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $r) {
                    echo '<tr>';
                    echo '<td>' . $r->idProdutos . '</td>';
                    echo '<td>' . $r->descricao . '</td>';
                    echo '<td>' . $r->estoque . '</td>';
                    echo '<td>' . number_format($r->precoVenda, 2, ',', '.') . '</td>';

                    echo '<td style="text-align: center">';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/produtos/visualizar/' . $r->idProdutos . '" class="btn btn-info btn-xs tip-top" title="Detalhes"><i class="fa fa-search-plus fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/produtos/editar/' . $r->idProdutos . '" class="btn btn-primary btn-xs tip-top" title="Editar"><i class="fa fa-edit fa-lg fa-fw"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dProduto')) {
                        echo '<a href="#modal-excluir" role="button" data-toggle="modal" produto="' . $r->idProdutos . '" class="btn btn-danger btn-xs tip-top" title="Excluir"><i class="fa fa-trash-o fa-lg fa-fw"></i></a>';
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


<!-- Modal EXCLUIR -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/produtos/excluir" method="post">
        <div class="modal-header bg_danger">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">EXCLUIR PRODUTO</h3>
        </div>
        <div class="modal-body">
            <span style="font-size: 11pt">Deseja realmente excluir esse produto?</span>
            <input type="hidden" id="idProduto" name="id" value=""/>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir"><i
                        class="fa fa-times fa-fw"></i> Cancelar
            </button>
            <button class="btn btn-danger btn-sm" id="btnExcluir"><i class="fa fa-check fa-fw"></i> Excluir</button>
        </div>
    </form>
</div>


<script type="text/javascript">
    $(document).ready(function () {


        $(document).on('click', 'a', function (event) {

            var produto = $(this).attr('produto');
            $('#idProduto').val(produto);

        });

    });

</script>