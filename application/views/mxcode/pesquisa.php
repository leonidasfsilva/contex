<?php
$termo       = $this->input->get('termo');
$formatMoney = function ($value) {
    return number_format((float)$value, 2, ',', '.');
};
$formatDate = function ($date) {
    return $date ? date('d/m/Y', strtotime($date)) : '';
};
$escape = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2 class="pb0">
            <span class="pr20">Resultados de busca para: </span><span class="pr20"><?= $escape($termo) ?></span>
        </h2>
        <div class="panel-ctrls">
            <a href="<?= base_url('mxcode') ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Painel Inicial</a>
        </div>
    </div>
    <div class="panel-body">
        <form action="<?= current_url() ?>" method="get" autocomplete="off" class="mb20">
            <div class="row">
                <div class="form-group col-lg-10 col-md-9 col-sm-8">
                    <input type="text" class="form-control" name="termo" value="<?= $escape($termo) ?>" placeholder="Digite o termo a pesquisar">
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
                        <h2><i class="fas fa-money-bill-transfer fa-fw"></i> Lançamentos</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição<br>Fornecedor</th>
                                <th>Valor (R$)</th>
                                <th>Tipo<br>Status</th>
                                <th style="width: 80px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$lancamentos) { ?>
                                <tr><td colspan="5">Nenhum lançamento foi encontrado.</td></tr>
                            <?php } ?>
                            <?php foreach ($lancamentos as $r) {
                                $tipoClasse   = $r->tipo == 1 ? 'green' : 'alizarin';
                                $tipoTexto     = $r->tipo == 1 ? 'ENTRADA' : 'SAÍDA';
                                $statusClasse = $r->baixado == 1 ? 'primary' : 'orange';
                                $statusTexto   = $r->baixado == 1 ? 'EFETIVADO' : 'PENDENTE';
                                ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $formatDate($r->data_lancamento) ?></td>
                                    <td class="font-weight-bold">
                                        <?= strtoupper($escape($r->descricao)) ?><br>
                                        <span class="small text-muted"><?= $escape($r->cliente_fornecedor) ?></span>
                                    </td>
                                    <td class="font-weight-bold"><?= $formatMoney($r->valor) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $tipoClasse ?>"><?= $tipoTexto ?></span><br>
                                        <span class="badge badge-<?= $statusClasse ?>"><?= $statusTexto ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('financeiro/lancamentos?search=' . urlencode($r->descricao)) ?>" class="btn btn-info btn-sm" title="Ver no módulo Lançamentos">
                                            <i class="fas fa-search-plus fa-lg fa-fw"></i>
                                        </a>
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
                        <h2><i class="fas fa-file-invoice-dollar fa-fw"></i> Faturas</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>Data Compra</th>
                                <th>Descrição<br>Terceiro</th>
                                <th>Fatura<br>Cartão</th>
                                <th>Valor (R$)</th>
                                <th style="width: 80px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$faturas) { ?>
                                <tr><td colspan="5">Nenhuma compra em fatura foi encontrada.</td></tr>
                            <?php } ?>
                            <?php foreach ($faturas as $r) { ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $formatDate($r->data_compra) ?></td>
                                    <td class="font-weight-bold">
                                        <?= strtoupper($escape($r->descricao)) ?><br>
                                        <span class="small text-muted"><?= strtoupper($escape($r->nome_cliente)) ?></span>
                                    </td>
                                    <td class="font-weight-bold">
                                        <?= translateMonth($r->mes_referencia, true) ?>/<?= $r->ano_referencia ?><br>
                                        <span class="small text-muted"><?= strtoupper($escape($r->cartao_apelido)) ?></span>
                                    </td>
                                    <td class="font-weight-bold"><?= $formatMoney($r->valor_total) ?></td>
                                    <td>
                                        <a href="<?= base_url('financeiro/faturas/detalhes/' . $r->id_fatura . '/' . $r->id_cartao) ?>" class="btn btn-info btn-sm" title="Visualizar fatura desta compra">
                                            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
                                        </a>
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
                        <h2><i class="fas fa-money-check-dollar-pen fa-fw"></i> Despesas</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>Descrição<br>Fornecedor</th>
                                <th>Valor (R$)</th>
                                <th>Parcelamento<br>Terceiro</th>
                                <th>Tipo</th>
                                <th style="width: 80px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$despesas) { ?>
                                <tr><td colspan="5">Nenhuma despesa foi encontrada.</td></tr>
                            <?php } ?>
                            <?php foreach ($despesas as $r) {
                                $tipo = $r->tipo_despesa == 1 ? 'ÚNICA' : 'RECORRENTE';
                                ?>
                                <tr>
                                    <td class="font-weight-bold">
                                        <?= strtoupper($escape($r->descricao)) ?><br>
                                        <span class="small text-muted"><?= $escape($r->fornecedor) ?></span>
                                    </td>
                                    <td class="font-weight-bold"><?= $formatMoney($r->valor_total) ?></td>
                                    <td class="font-weight-bold">
                                        <?php if ($r->despesa_parcelada) { ?>
                                            <span class="text-orange"><?= abs($r->total_parcelas) ?>x <?= $formatMoney($r->valor_parcela) ?></span>
                                        <?php } ?>
                                        <br><span class="small text-muted"><?= strtoupper($escape($r->nome_terceiro)) ?></span>
                                    </td>
                                    <td><span class="badge badge-primary"><?= $tipo ?></span></td>
                                    <td>
                                        <a href="<?= base_url('financeiro/despesas/detalhes/' . $r->id) ?>" class="btn btn-info btn-sm" title="Acessar vínculos da despesa">
                                            <i class="fas fa-search-plus fa-lg fa-fw"></i>
                                        </a>
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
                                <th>Telefone</th>
                                <th style="width: 100px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$clientes) { ?>
                                <tr><td colspan="5">Nenhum cliente foi encontrado.</td></tr>
                            <?php } ?>
                            <?php foreach ($clientes as $r) { ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $r->id_clientes ?></td>
                                    <td class="font-weight-bold"><?= strtoupper($escape($r->nome)) ?></td>
                                    <td><?= $escape($r->cpf) ?></td>
                                    <td><?= $escape($r->telefone) ?></td>
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
                        <h2><i class="fas fa-credit-card fa-fw"></i> Cartões</h2>
                    </div>
                    <div class="panel-body panel-no-padding table-responsive">
                        <table class="table table-condensed table-striped table-bordeless table-hover no-footer">
                            <thead>
                            <tr>
                                <th>Apelido</th>
                                <th>Nome impresso</th>
                                <th>Bandeira</th>
                                <th>Status</th>
                                <th style="width: 80px !important;">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$cartoes) { ?>
                                <tr><td colspan="5">Nenhum cartão foi encontrado.</td></tr>
                            <?php } ?>
                            <?php foreach ($cartoes as $r) { ?>
                                <tr>
                                    <td class="font-weight-bold"><?= strtoupper($escape($r->apelido)) ?></td>
                                    <td class="font-weight-bold"><?= strtoupper($escape($r->nome)) ?></td>
                                    <td><?= strtoupper($escape($r->bandeira)) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $r->ativo == 1 ? 'green' : 'orange' ?>">
                                            <?= $r->ativo == 1 ? 'ATIVO' : 'INATIVO' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('financeiro/cartoes/editar/' . $r->id_cartao) ?>" class="btn btn-primary btn-sm" title="Editar cartão">
                                            <i class="fas fa-edit fa-lg fa-fw"></i>
                                        </a>
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
