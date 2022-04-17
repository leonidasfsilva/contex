<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_dados_cliente" data-toggle="tab">Dados do Cliente</a></li>
                <li><a href="#tab_pendencias" data-toggle="tab">Pendências</a></li>
            </ul>
        </h2>
        <div class="panel-ctrls">
            <a href="<?= base_url('clientes') ?>" class="btn btn-default btn-sm "><i class="fas fa-arrow-left fa-fw"></i> Clientes</a>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eCliente')) {
                echo '<a title="Editar dados do cliente" class="btn btn-primary btn-sm " href="' . base_url() . 'clientes/editar/' . $result->id_clientes . '"><i class="fas fa-edit fa-fw"></i> Editar</a>';
            }
            if ($result->dob) {
                $dob = date(('d/m/Y'), strtotime($result->dob));
            } 
            ?>
        </div>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <!--            TAB DADOS DO CLIENTE-->
            <div class="tab-pane active" id="tab_dados_cliente">
                <div class="accordion-group" id="accordionB">
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB" href="#collapseaOne">
                            <h2>
                                <i class="fas fa-id-card fa-lg fa-fw"></i>
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
                                                    <td style="cursor: pointer;" id="copy-cpf"><?php echo $result->cpf ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right"><strong>Data Nascimento</strong></td>
                                                    <td><?php echo $dob ?? null ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: right"><strong>Data de Cadastro</strong></td>
                                                    <td><?php echo date('d/m/Y H:i:s', strtotime($result->data_cadastro)) ?></td>
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
                                <i class="fas fa-phone-alt fa-lg fa-fw"></i>
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
                                <i class="fas fa-map-marker-alt fa-lg fa-fw"></i>
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
                                                    <td style="text-align: right; width: 30%"><strong>Complemento</strong>
                                                    </td>
                                                    <td><?php echo $result->complemento ?></td>
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
            <!--            TAB PENDENCIAS DO CLIENTE-->
            <div class="tab-pane" id="tab_pendencias">
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
                                        <th colspan="2" style="text-align: left !important;">Descrição</th>
                                        <th colspan="1" style="text-align: right !important;">Valor (R$)</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td colspan="2" style="text-align: left; color: green">(+) SALDO TOTAL DE PENDÊNCIAS CRÉDITO</td>
                                    <td colspan="1" style="text-align: right; color: green">
                                        <?php echo number_format($total_credito->total, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: left; color: red">(-) SALDO TOTAL DE PENDÊNCIAS DÉBITO</td>
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
                                            $dataInicial = date(('d/m/Y'), strtotime($r->data_vencimento));
                                            if ($r->data_pagamento != null) {
                                                $dataFinal = date(('d/m/Y'), strtotime($r->data_pagamento));
                                            } else {
                                                $dataFinal = null;
                                            }
                                            if ($r->tipo == 1) {
                                                $tipo = 'Entrada';
                                                $colorTipo = 'primary';
                                            } else {
                                                $tipo = 'Saída';
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
<script>
    $("#copy-cpf").hover(function() {
        $('#copy-cpf').toggleClass('font-weight-bold')
    });

    $("#copy-cpf").click(function() {
        var copyText = document.getElementById("copy-cpf");
        var textArea = document.createElement("textarea");
        value = copyText.textContent.trim();
        valueNew = value.toString().split('.').join('');
        valueNew = valueNew.toString().split('-').join('');
        textArea.value = valueNew;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();

        Swal.fire({
            toast: true,
            position: "top-right",
            showConfirmButton: false,
            showCloseButton: true,
            timer: 2000,
            timerProgressBar: true,
            icon: 'success',
            title: 'Valor copiado!',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    })
</script>