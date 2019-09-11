<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-11-1" data-toggle="tab">Dados do Usuário</a></li>
                <li><a href="#tab-11-3" data-toggle="tab">Em breve</a></li>
            </ul>
        </h2>
        <div class="panel-ctrls">
            <a href="<?php echo base_url() ?>usuarios" class="btn btn-default btn-sm "><i
                        class="fa fa-arrow-left fa-fw"></i> Voltar</a>
            <a title="Editar detalhes do cliente" class="btn btn-primary btn-sm "
               href=" <?= base_url() . 'usuarios/editar/' . $result->id_usuarios ?>"><i class="fa fa-edit fa-fw"></i>
                Editar</a>
        </div>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <!--            TAB DADOS DO USUARIO-->
            <div class="tab-pane active" id="tab-11-1">
                <div class="accordion-group" id="accordionB">
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB"
                           href="#collapseaOne">
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
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB"
                           href="#collapseaTwo">
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
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB"
                           href="#collapseaThree">
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
                                                <td style="text-align: right; width: 30%"><strong>Logradouro</strong>
                                                </td>
                                                <td><?php echo $result->logradouro ?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right"><strong>Número</strong></td>
                                                <td><?php if ($result->s_n == 1) {
                                                        echo 'S/N';
                                                    } else {
                                                        echo $result->numero;
                                                    } ?></td>
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
            <!--            TAB DESENVOLVIMENTO DO USUARIO-->
            <div class="tab-pane" id="tab-11-3">
                <?php if (!$pendencias) { ?>
                    <div class="panel panel-midnightblue" data-widget="{&quot;id&quot; : &quot;wiget9&quot;}">
                        <div class="panel-heading">
                            <div class="panel-ctrls button-icon-bg" data-actions-container=""
                                 data-action-collapse="{&quot;target&quot;: &quot;.panel-body&quot;}"
                                 data-action-expand="" data-action-colorpicker="" data-action-edit=""
                                 data-action-refresh="" data-action-close="">
                                <span class="button-icon has-bg"><i class="fa fa-minus"></i></span><span
                                        class="button-icon has-bg"><i class="fa fa-expand"></i></span><span
                                        class="button-icon"><i class="fa fa-tint"></i></span><span
                                        class="button-icon"><i class="fa fa-pencil"></i></span><span
                                        class="button-icon"><i class="fa fa-refresh"></i></span><span
                                        class="button-icon"><i class="fa fa-times"></i></span></div>
                            <h2>Panel</h2>
                        </div>
                        <div class="panel-editbox" data-widget-controls=""></div>
                        <div class="panel-body">
                            <p>
                                Esta aba encontra-se em desenvolvimento.
                            </p>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-heading"></div>
                        <div class="panel-body panel-no-padding">
                            <table class="table table-striped">
                                <thead>
                                <tr class="bg-inverse">
                                    <th colspan="2" style="text-align: left !important;">Descrição</th>
                                    <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
                                </tr>
                                </thead>
                                <tr>
                                    <td colspan="2" style="text-align: left; color: green">(+) SALDO TOTAL DE PENDÊNCIAS
                                        CRÉDITO
                                    </td>
                                    <td colspan="1" style="text-align: right; color: green">
                                        <?php echo number_format($total_credito->total, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: left; color: red">(-) SALDO TOTAL DE PENDÊNCIAS
                                        DÉBITO
                                    </td>
                                    <td colspan="1" style="text-align: right; color: red">
                                        <?php echo number_format($total_debito->total, 2, ',', '.') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="panel panel-midnightblue">
                        <div class="panel-heading"></div>
                        <div class="panel-body panel-no-padding">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr class="bg-inverse">
                                        <th>#</th>
                                        <th>Data Pendência</th>
                                        <th>Descrição</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th>Data Pagamento</th>
                                        <th>Valor</th>
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
                                        if ($r->quitado == 0) {
                                            $status = 'Pendente';
                                            $color = 'red';
                                            $label = 'danger';
                                            $icon = 'fa fa-check-square-o';
                                        } else {
                                            $status = 'Pago';
                                            $color = 'green';
                                            $label = 'success';
                                            $icon = 'fa fa-check-square';
                                        }

                                        echo '<tr>';
                                        echo '<td>' . $r->id_pendencia . '</td>';
                                        echo '<td>' . $dataInicial . '</td>';
                                        echo '<td>' . $r->descricao . '</td>';
                                        echo '<td><span class="label label-' . $colorTipo . '">' . strtoupper($tipo) . '</span></td>';
                                        echo '<td><span class="label label-' . $label . '">' . strtoupper($status) . '</span></td>';
                                        echo '<td>' . $dataFinal . '</td>';
                                        echo '<td>' . number_format($r->valor, 2, ',', '.') . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>

                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>