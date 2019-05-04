<!--Action boxes-->
<div class="panel panel-default">
    <div class="panel-heading"><h2>Painel Inicial</h2></div>
    <div class="panel-body">
        <div class="row">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')): ?>
                <div class="col-md-2">
                    <a href="<?= base_url() ?>clientes" class="shortcut-tile tile-primary">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-group fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">2</span></div>
                        </div>
                        <div class="tile-footer">
                            Clientes
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')): ?>
                <div class="col-md-2">
                    <a href="<?= base_url() ?>produtos" class="shortcut-tile tile-indigo">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-barcode fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">5</span></div>
                        </div>
                        <div class="tile-footer">
                            Produtos
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) : ?>
                <div class="col-md-2">
                    <a href="<?= base_url() ?>servicos" class="shortcut-tile tile-orange">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-wrench fa-fw"></i></div>

                        </div>
                        <div class="tile-footer">
                            Serviços
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) : ?>
                <div class="col-md-2">
                    <a href="<?= base_url() ?>os" class="shortcut-tile tile-danger">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-tags fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">3</span></div>
                        </div>
                        <div class="tile-footer">
                            OS
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) : ?>
                <div class="col-md-2">
                    <a href="<?= base_url() ?>vendas" class="shortcut-tile tile-magenta">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-shopping-cart fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">10</span></div>
                        </div>
                        <div class="tile-footer">
                            Vendas
                        </div>
                    </a>
                </div>
            <?php endif ?>

            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) : ?>
                <div class="col-md-2">
                    <a href="<?= base_url() ?>financeiro" class="shortcut-tile tile-green">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fa fa-dollar fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">16</span></div>
                        </div>
                        <div class="tile-footer">
                            Financeiro
                        </div>
                    </a>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h2>Default Button Variants</h2>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <h3 class="mt0">Default Buttons</h3>
                <p>Basic buttons come in 7 different colors <code>.btn .btn-default</code></p>
                <ul class="demo-btns">
                    <input type="text" class="form-control datepicker">
                    <br>
                    <li><a href="#" class="btn btn-default">Default</a></li>
                    <li><a href="#" class="btn btn-primary">Primary</a></li>
                    <li><a href="#" class="btn btn-success">Success</a></li>
                    <li><a href="#" class="btn btn-info">Info</a></li>
                    <li><a href="#" class="btn btn-warning">Warning</a></li>
                    <li><a href="#" class="btn btn-danger">Danger</a></li>
                    <li><a href="#" class="btn btn-inverse">Inverse</a></li>
                </ul>
                <ul class="demo-btns">
                    <li><a href="#" class="btn btn-default"><i class="fa fa-cog"></i></a></li>
                    <li><a href="#" class="btn btn-primary"><i class="fa fa-cloud"></i></a></li>
                    <li><a href="#" class="btn btn-success"><i class="fa fa-check"></i></a></li>
                    <li><a href="#" class="btn btn-info"><i class="fa fa-info-circle"></i></a></li>
                    <li><a href="#" class="btn btn-warning"><i class="fa fa-warning"></i></a></li>
                    <li><a href="#" class="btn btn-danger"><i class="fa fa-times"></i></a></li>
                    <li><a href="#" class="btn btn-inverse"><i class="fa fa-wrench"></i></a></li>
                </ul>

                <p>Basic buttons in disabled states <code>.btn .disabled</code></p>
                <ul class="demo-btns">
                    <li><a href="#" class="btn disabled btn-default">Default</a></li>
                    <li><a href="#" class="btn disabled btn-primary">Primary</a></li>
                    <li><a href="#" class="btn disabled btn-success">Success</a></li>
                    <li><a href="#" class="btn disabled btn-info">Info</a></li>
                    <li><a href="#" class="btn disabled btn-warning">Warning</a></li>
                    <li><a href="#" class="btn disabled btn-danger">Danger</a></li>
                    <li><a href="#" class="btn  btn-inverse" disabled>Inverse</a></li>
                </ul>
                <ul class="demo-btns">
                    <li><a href="#" class="btn disabled btn-default"><i class="fa fa-cog"></i></a></li>
                    <li><a href="#" class="btn disabled btn-primary"><i class="fa fa-cloud"></i></a></li>
                    <li><a href="#" class="btn disabled btn-success"><i class="fa fa-check"></i></a></li>
                    <li><a href="#" class="btn disabled btn-info"><i class="fa fa-info-circle"></i></a></li>
                    <li><a href="#" class="btn disabled btn-warning"><i class="fa fa-warning"></i></a></li>
                    <li><a href="#" class="btn disabled btn-danger"><i class="fa fa-times"></i></a></li>
                    <li><a href="#" class="btn disabled btn-inverse"><i class="fa fa-wrench"></i></a></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3 class="mt0">Alternate Buttons</h3>
                <p>Alternate buttons come in 7 different colors as well <code>.btn .btn-default-alt</code></p>
                <ul class="demo-btns">
                    <li><a href="#" class="btn btn-default-alt">Default</a></li>
                    <li><a href="#" class="btn btn-primary-alt">Primary</a></li>
                    <li><a href="#" class="btn btn-success-alt">Success</a></li>
                    <li><a href="#" class="btn btn-info-alt">Info</a></li>
                    <li><a href="#" class="btn btn-warning-alt">Warning</a></li>
                    <li><a href="#" class="btn btn-danger-alt">Danger</a></li>
                    <li><a href="#" class="btn btn-inverse-alt">Inverse</a></li>
                </ul>
                <ul class="demo-btns">
                    <li><a href="#" class="btn btn-default-alt"><i class="fa fa-cog"></i></a></li>
                    <li><a href="#" class="btn btn-primary-alt"><i class="fa fa-cloud"></i></a></li>
                    <li><a href="#" class="btn btn-success-alt"><i class="fa fa-check"></i></a></li>
                    <li><a href="#" class="btn btn-info-alt"><i class="fa fa-info-circle"></i></a></li>
                    <li><a href="#" class="btn btn-warning-alt"><i class="fa fa-warning"></i></a></li>
                    <li><a href="#" class="btn btn-danger-alt"><i class="fa fa-times"></i></a></li>
                    <li><a href="#" class="btn btn-inverse-alt"><i class="fa fa-wrench"></i></a></li>
                </ul>

                <p>Alternate buttons in disabled states <code>.btn .disabled</code></p>
                <ul class="demo-btns">
                    <li><a href="#" class="btn disabled btn-default-alt">Default</a></li>
                    <li><a href="#" class="btn disabled btn-primary-alt">Primary</a></li>
                    <li><a href="#" class="btn disabled btn-success-alt">Success</a></li>
                    <li><a href="#" class="btn disabled btn-info-alt">Info</a></li>
                    <li><a href="#" class="btn disabled btn-warning-alt">Warning</a></li>
                    <li><a href="#" class="btn disabled btn-danger-alt">Danger</a></li>
                    <li><a href="#" class="btn disabled btn-inverse-alt">Inverse</a></li>
                </ul>
                <ul class="demo-btns">
                    <li><a href="#" class="btn disabled btn-default-alt"><i class="fa fa-cog"></i></a></li>
                    <li><a href="#" class="btn disabled btn-primary-alt"><i class="fa fa-cloud"></i></a></li>
                    <li><a href="#" class="btn disabled btn-success-alt"><i class="fa fa-check"></i></a></li>
                    <li><a href="#" class="btn disabled btn-info-alt"><i class="fa fa-info-circle"></i></a></li>
                    <li><a href="#" class="btn disabled btn-warning-alt"><i class="fa fa-warning"></i></a></li>
                    <li><a href="#" class="btn disabled btn-danger-alt"><i class="fa fa-times"></i></a></li>
                    <li><a href="#" class="btn disabled btn-inverse-alt"><i class="fa fa-wrench"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--End-Action boxes-->

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fa fa-cubes fa-lg fa-fw"></i></span>
                <h5>Produtos Com Estoque Mínimo</h5>
            </div>
            <div class="widget-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Produto</th>
                        <th>Preço de Venda</th>
                        <th>Estoque</th>
                        <th>Estoque Mínimo</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($produtos != null): ?>
                        <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td><?= $p->idProdutos ?></td>
                                <td><?= $p->descricao ?></td>
                                <td>R$ <?= $p->precoVenda ?></td>
                                <td><?= $p->estoque ?></td>
                                <td><?= $p->estoqueMinimo ?></td>
                                <td>
                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')): ?>
                                        <a
                                                href="<?= base_url() ?>produtos/editar/<?= $p->idProdutos ?>"
                                                class="btn btn-info">
                                            <i class="icon-pencil"></i>
                                        </a>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nenhum produto com estoque baixo.</td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="span12" style="margin-left: 0">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fa fa-exclamation-triangle fa-lg fa-fw"></i></span>
                <h5>Ordens de Serviço Em Aberto</h5>
            </div>
            <div class="widget-content">
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Cliente</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($ordens != null): ?>
                        <?php foreach ($ordens as $o): ?>
                            <tr>
                                <td><?= $o->idOs ?></td>
                                <td><?= date('d/m/Y', strtotime($o->dataInicial)) ?></td>
                                <td><?= date('d/m/Y', strtotime($o->dataFinal)) ?></td>
                                <td><?= $o->nomeCliente ?></td>
                                <td>
                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')): ?>
                                        <a href="<?= base_url() ?>os/visualizar/<?= $o->idOs ?>" class="btn btn-primary btn-xs tip-top" title="Ver detalhes">
                                            <i class="fa fa-search-plus fa-fw"></i> Ver detalhes</a>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nenhuma OS em aberto.</td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php if ($estatisticas_financeiro != null) {
    if ($estatisticas_financeiro->total_receita != null || $estatisticas_financeiro->total_despesa != null || $estatisticas_financeiro->total_receita_pendente != null || $estatisticas_financeiro->total_despesa_pendente != null) { ?>
        <div class="row-fluid" style="margin-top: 0">
            <div class="span4">
                <div class="widget-box">
                    <div class="widget-title"><span class="icon"><i class="fa fa-bar-chart-o fa-lg fa-fw"></i>
                        </span><h5>Estatísticas financeiras - Realizado</h5></div>
                    <div class="widget-content">
                        <div class="row-fluid">
                            <div class="span12">
                                <div id="chart-financeiro" style=""></div>
                                <div id="chart-financeiro" style=""></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="span4">

                <div class="widget-box">
                    <div class="widget-title"><span class="icon"><i class="icon-signal"></i></span><h5>Estatísticas financeiras - Pendente</h5></div>
                    <div class="widget-content">
                        <div class="row-fluid">
                            <div class="span12">
                                <div id="chart-financeiro2" style=""></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div class="span4">

                <div class="widget-box">
                    <div class="widget-title"><span class="icon"><i class="icon-signal"></i></span><h5>Total em caixa / Previsto</h5></div>
                    <div class="widget-content">
                        <div class="row-fluid">
                            <div class="span12">
                                <div id="chart-financeiro-caixa" style=""></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php }
} ?>

<?php if ($os != null) { ?>
    <div class="row-fluid" style="margin-top: 0">

        <div class="span12">

            <div class="widget-box">
                <div class="widget-title"><span class="icon"><i class="fa fa-bar-chart fa-lg fa-fw""></i></span>
                    <h5>Estatísticas de OS</h5></div>
                <div class="widget-content">
                    <div class="row-fluid">
                        <div class="span12">
                            <div id="chart-os" style=""></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>


<div class="row-fluid" style="margin-top: 0">

    <div class="span12">

        <div class="widget-box">
            <div class="widget-title"><span class="icon"><i class="fa fa-bar-chart-o fa-lg fa-fw""></i></span>
                <h5>Estatísticas do Sistema</h5></div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span12">
                        <ul class="site-stats">
                            <li class="bg_lh"><i class="icon-group"></i> <strong><?= $this->db->count_all('clientes'); ?></strong>
                                <small>Clientes</small>
                            </li>
                            <li class="bg_lh"><i class="icon-barcode"></i> <strong><?= $this->db->count_all('produtos'); ?></strong>
                                <small>Produtos</small>
                            </li>
                            <li class="bg_lh"><i class="icon-tags"></i> <strong><?= $this->db->count_all('os'); ?></strong>
                                <small>Ordens de Serviço</small>
                            </li>
                            <li class="bg_lh"><i class="icon-wrench"></i> <strong><?= $this->db->count_all('servicos'); ?></strong>
                                <small>Serviços</small>
                            </li>

                        </ul>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($os != null) { ?>

<?php } ?>


<script type="text/javascript">

    $('.datepicker').datepicker({
        language: 'pt-BR',
        autoclose: true,
        minViewMode: 1,
        format: 'mm/yyyy'
    });
    $(document).on('ready', function () {

    });

</script>
