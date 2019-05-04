<div class="accordion" id="collapse-group">
    <div class="accordion-group widget-box">
        <div class="accordion-heading">
            <div class="widget-title">
                <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                    <span class="icon">
                        <i class="fa fa-barcode fa-lg fa-fw"></i>
                    </span>
                    <h5>Dados do Produto</h5>
                </a>
                <div class="buttons">
                    <a href="<?= base_url() ?>index.php/produtos" class="btn btn-default btn-sm"><i class="fa fa-arrow-left fa-fw"></i> Voltar</a>
                </div>
            </div>
        </div>
        <div class="collapse in accordion-body">
            <div class="widget-content">
                <table class="table table-bordered table-condensed">
                    <tbody>
                    <tr>
                        <td style="text-align: right; width: 30%"><strong>Descrição</strong></td>
                        <td><?php echo $result->descricao ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><strong>Unidade</strong></td>
                        <td><?php echo $result->unidade ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><strong>Preço de Compra</strong></td>
                        <td>R$ <?php echo number_format($result->precoCompra, 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><strong>Preço de Venda</strong></td>
                        <td>R$ <?php echo number_format($result->precoVenda, 2, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><strong>Estoque</strong></td>
                        <td><?php echo $result->estoque; ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><strong>Estoque Mínimo</strong></td>
                        <td><?php echo $result->estoqueMinimo; ?></td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

