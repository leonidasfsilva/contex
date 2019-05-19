<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <div class="panel-ctrls">
            <a href="<?php echo base_url() ?>clientes" class="btn btn-default btn-sm "><i class="fa fa-arrow-left fa-fw"></i> Voltar</a>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
                echo '<a title="Editar detalhes do cliente" class="btn btn-primary btn-sm " href="' . base_url() . 'clientes/editar/' . $result->id_clientes . '"><i class="fa fa-edit fa-fw"></i> Editar</a>';
            } ?>
        </div>
        <h2>
            <ul class="nav nav-tabs">
                <li class="dropdown pull-right tabdrop hide"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a>
                    <ul class="dropdown-menu"></ul>
                </li>
                <li class="active"><a href="#tab-11-1" data-toggle="tab">Dados do Cliente</a></li>
                <li><a href="#tab-11-2" data-toggle="tab">Ordens de Serviço</a></li>
                <li><a href="#tab-11-3" data-toggle="tab">Pendências</a></li>
            </ul>
        </h2>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <!--            TAB DADOS DO CLIENTE-->
            <div class="tab-pane active" id="tab-11-1">
                <div class="accordion-group" id="accordionB">
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB" href="#collapseaOne">
                            <h2>
                                <i class="fa fa-id-card fa-fw"></i>
                                Dados Pessoais
                            </h2>
                        </a>
                        <div id="collapseaOne" class="collapse in">
                            <div class="accordion-body">
                                <div class="panel panel-midnightblue">
                                    <div class="panel-heading"></div>
                                    <div class="panel-body panel-no-padding">
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                            <tr>
                                                <td style="text-align: right; width: 30%"><strong>Nome</strong></td>
                                                <td><?php echo $result->nome ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>CPF</strong></td>
                                                <td><?php echo $result->cpf ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>Data de Cadastro</strong></td>
                                                <td><?php echo date('d/m/Y', strtotime($result->data_cadastro)) ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB" href="#collapseaTwo">
                            <h2>
                                <i class="fa fa-phone fa-fw"></i>
                                Contatos
                            </h2>
                        </a>
                        <div id="collapseaTwo" class="collapse">
                            <div class="accordion-body">
                                <div class="panel panel-midnightblue">
                                    <div class="panel-heading"></div>
                                    <div class="panel-body panel-no-padding">
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                            <tr>
                                                <td style="text-align: right; width: 30%"><strong>Telefone</strong></td>
                                                <td><?php echo $result->telefone ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>Email</strong></td>
                                                <td><?php echo $result->email ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB" href="#collapseaThree">
                            <h2>
                                <i class="fa fa-map-marker fa-fw"></i>
                                Endereço
                            </h2>
                        </a>
                        <div id="collapseaThree" class="collapse">
                            <div class="accordion-body">
                                <div class="panel panel-midnightblue">
                                    <div class="panel-heading"></div>
                                    <div class="panel-body panel-no-padding">
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                            <tr>
                                                <td style="text-align: right; width: 30%"><strong>Logradouro</strong></td>
                                                <td><?php echo $result->logradouro ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>Número</strong></td>
                                                <td><?php echo $result->numero ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>Bairro</strong></td>
                                                <td><?php echo $result->bairro ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>Cidade</strong></td>
                                                <td><?php echo $result->cidade ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>UF</strong></td>
                                                <td><?php echo $result->uf ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>CEP</strong></td>
                                                <td><?php echo $result->cep ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--            TAB OS DO CLIENTE-->
            <div class="tab-pane" id="tab-11-2">
                <?php if (!$os) { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-heading"></div>
                        <div class="panel-body panel-no-padding">
                            <table class="table table-striped">
                                <thead>
                                <tr class="bg-inverse">
                                    <th>#</th>
                                    <th>Data Inicial</th>
                                    <th>Data Final</th>
                                    <th>Descrição</th>
                                    <th>Defeito</th>
                                    <th style="width: 100px">Ações</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td colspan="6">Nenhuma OS encontrada para este cliente</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-heading"></div>
                        <div class="panel-body panel-no-padding">
                            <table class="table table-striped">
                                <thead>
                                <tr class="bg-inverse">
                                    <th>#</th>
                                    <th>Data Inicial</th>
                                    <th>Data Final</th>
                                    <th>Descrição</th>
                                    <th>Defeito</th>
                                    <th style="width: 100px">Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($os as $r) {
                                    $dataInicial = date(('d/m/Y'), strtotime($r->dataInicial));
                                    $dataFinal = date(('d/m/Y'), strtotime($r->dataFinal));
                                    echo '<tr>';
                                    echo '<td>' . $r->idOs . '</td>';
                                    echo '<td>' . $dataInicial . '</td>';
                                    echo '<td>' . $dataFinal . '</td>';
                                    echo '<td>' . $r->descricaoProduto . '</td>';
                                    echo '<td>' . $r->defeito . '</td>';

                                    echo '<td>';
                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                                        echo '<a href="' . base_url() . 'os/visualizar/' . $r->idOs . '" style="margin-right: 1%" class="btn btn-info btn-sm tip-top" title="Ver mais detalhes"><i class="fa fa-search-plus fa-fw"></i></a>';
                                    }
                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                                        echo '<a href="' . base_url() . 'os/editar/' . $r->idOs . '" class="btn btn-primary btn-sm tip-top" title="Editar OS"><i class="fa fa-edit fa-fw"></i></a>';
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
                <?php } ?>
            </div>
            <!--            TAB PENDENCIAS DO CLIENTE-->
            <div class="tab-pane" id="tab-11-3">
                <?php if (!$pendencias) { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-heading"></div>
                        <div class="panel-body panel-no-padding">
                            <table class="table table-striped">
                                <thead>
                                <tr class="bg-inverse">
                                    <th>#</th>
                                    <th>Data Pendência</th>
                                    <th>Data Pagamento</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th style="width: 100px">Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="6">Nenhuma pendência encontrada para este cliente</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php } else { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-heading"></div>
                        <div class="panel-body panel-no-padding">
                            <table class="table table-striped">
                                <thead>
                                <tr class="bg-inverse">
                                    <th>#</th>
                                    <th>Data Pendência</th>
                                    <th>Data Pagamento</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th style="width: 100px">Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($pendencias as $r) {
                                    $dataInicial = date(('d/m/Y'), strtotime($r->data_pendencia));
                                    if ($r->data_pagamento != null) {
                                        $dataFinal = date(('d/m/Y'), strtotime($r->data_pagamento));
                                    } else {
                                        $dataFinal = null;
                                    }
                                    if ($r->tipo == 1) {
                                        $tipo = 'Credito';
                                        $colorTipo = 'primary';
                                    } else {
                                        $tipo = 'Debito';
                                        $colorTipo = 'warning';
                                    }

                                    echo '<tr>';
                                    echo '<td>' . $r->id_pendencia . '</td>';
                                    echo '<td>' . $dataInicial . '</td>';
                                    echo '<td>' . $dataFinal . '</td>';
                                    echo '<td><span class="label label-' . $colorTipo . '">' . strtoupper($tipo) . '</span></td>';
                                    echo '<td>' . $r->descricao . '</td>';
                                    echo '<td>' . number_format($r->valor, 2, ',', '.') . '</td>';

                                    echo '<td>';
                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
                                        echo '<a href="' . base_url() . 'os/visualizar/' . $r->id_pendencia . '" style="margin-right: 1%" class="btn btn-info btn-sm tip-top" title="Ver mais detalhes"><i class="fa fa-search-plus fa-fw"></i></a>';
                                    }
                                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
                                        echo '<a href="' . base_url() . 'os/editar/' . $r->id_pendencia . '" class="btn btn-primary btn-sm tip-top" title="Editar OS"><i class="fa fa-edit fa-fw"></i></a>';
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
                <?php } ?>
            </div>
        </div>
    </div>
</div>