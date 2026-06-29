<?php
$termo = $this->input->get('termo');
?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2 class="pb0">
            <span class="pr20">Resultados de busca para: </span><span class="pr20"><?= htmlspecialchars($termo, ENT_QUOTES, 'UTF-8') ?></span>
        </h2>
        <div class="panel-ctrls">
            <a href="<?= base_url('mxcode') ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Painel Inicial</a>
        </div>
    </div>
    <div class="panel-body">
        <form action="<?= current_url() ?>" method="get" autocomplete="off" class="mb20">
            <div class="row">
                <div class="form-group col-lg-10 col-md-9 col-sm-8">
                    <input type="text" class="form-control" name="termo" value="<?= htmlspecialchars($termo, ENT_QUOTES, 'UTF-8') ?>" placeholder="Digite o termo a pesquisar">
                </div>
                <div class="form-group col-lg-2 col-md-3 col-sm-4">
                    <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-search fa-fw"></i> Pesquisar</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h2><i class="fas fa-barcode fa-fw"></i> Produtos</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th style="width: 100px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($produtos == null) { ?>
                                <tr><td colspan="4">Nenhum produto foi encontrado.</td></tr>
                            <?php } ?>
                            <?php foreach ($produtos as $r) { ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $r->idProdutos ?></td>
                                    <td class="font-weight-bold"><?= strtoupper($r->descricao) ?></td>
                                    <td><?= number_format($r->precoVenda, 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                                            <a href="<?= base_url('produtos/visualizar/' . $r->idProdutos) ?>" class="btn btn-info btn-sm tip-top" title="Ver mais detalhes">
                                                <i class="fas fa-search-plus fa-lg fa-fw"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) { ?>
                                            <a href="<?= base_url('produtos/editar/' . $r->idProdutos) ?>" class="btn btn-primary btn-sm tip-top" title="Editar Produto">
                                                <i class="fas fa-pencil fa-lg fa-fw"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h2><i class="fas fa-user fa-fw"></i> Clientes</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th style="width: 100px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($clientes == null) { ?>
                                <tr><td colspan="4">Nenhum cliente foi encontrado.</td></tr>
                            <?php } ?>
                            <?php foreach ($clientes as $r) { ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $r->id_clientes ?></td>
                                    <td class="font-weight-bold"><?= strtoupper($r->nome) ?></td>
                                    <td><?= $r->cpf ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                                            <a href="<?= base_url('clientes/visualizar/' . $r->id_clientes) ?>" class="btn btn-info btn-sm tip-top" title="Ver mais detalhes">
                                                <i class="fas fa-search-plus fa-lg fa-fw"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) { ?>
                                            <a href="<?= base_url('clientes/editar/' . $r->id_clientes) ?>" class="btn btn-primary btn-sm tip-top" title="Editar Cliente">
                                                <i class="fas fa-pencil fa-lg fa-fw"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h2><i class="fas fa-wrench fa-fw"></i> Serviços</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th style="width: 100px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($servicos == null) { ?>
                                <tr><td colspan="4">Nenhum serviço foi encontrado.</td></tr>
                            <?php } ?>
                            <?php foreach ($servicos as $r) { ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $r->idServicos ?></td>
                                    <td class="font-weight-bold"><?= strtoupper($r->nome) ?></td>
                                    <td><?= number_format($r->preco, 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eServico')) { ?>
                                            <a href="<?= base_url('servicos/editar/' . $r->idServicos) ?>" class="btn btn-primary btn-sm tip-top" title="Editar Serviço">
                                                <i class="fas fa-pencil fa-lg fa-fw"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="panel panel-midnightblue">
                    <div class="panel-heading">
                        <h2><i class="fas fa-tags fa-fw"></i> Ordens de Serviço</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Data Inicial</th>
                                <th>Defeito</th>
                                <th style="width: 100px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($os == null) { ?>
                                <tr><td colspan="4">Nenhuma OS foi encontrada.</td></tr>
                            <?php } ?>
                            <?php foreach ($os as $r) { ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $r->idOs ?></td>
                                    <td class="font-weight-bold"><?= date('d/m/Y', strtotime($r->dataInicial)) ?></td>
                                    <td><?= strtoupper($r->defeito) ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                            <a href="<?= base_url('os/visualizar/' . $r->idOs) ?>" class="btn btn-info btn-sm tip-top" title="Ver mais detalhes">
                                                <i class="fas fa-search-plus fa-lg fa-fw"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) { ?>
                                            <a href="<?= base_url('os/editar/' . $r->idOs) ?>" class="btn btn-primary btn-sm tip-top" title="Editar OS">
                                                <i class="fas fa-pencil fa-lg fa-fw"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
