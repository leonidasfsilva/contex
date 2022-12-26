<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            <ul class="nav nav-tabs" id="tabUsuario">
                <li class="active"><a href="#dados" data-toggle="tab">Dados do Usuário</a></li>
                <li><a href="#logs" data-toggle="tab">Logs</a></li>
                <li><a href="#dev" data-toggle="tab">Em breve</a></li>
            </ul>
        </h2>
        <div class="panel-ctrls">
            <a href="<?php echo base_url() ?>usuarios" class="btn btn-default btn-sm "><i
                        class="fas fa-arrow-left fa-fw"></i> Usuários</a>
            <a title="Editar dados do usuário" class="btn btn-primary btn-sm "
               href=" <?= base_url() . 'usuarios/editar/' . $result->id_usuarios ?>"><i class="fas fa-edit fa-fw"></i>
                Editar</a>
        </div>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <!--            TAB DADOS DO USUARIO-->
            <div class="tab-pane active" id="dados">
                <div class="accordion-group" id="accordionB">
                    <div class="panel accordion-item">
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB"
                           href="#collapseaOne">
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
                                                <td><?php echo $result->cpf ?></td>
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
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB"
                           href="#collapseaTwo">
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
                        <a class="accordion-title" data-toggle="collapse" data-parent="#accordionB"
                           href="#collapseaThree">
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
            <!--            TAB LOGS DO USUARIO-->
            <div class="tab-pane" id="logs">
                <?php if (!$logs) { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-body panel-no-padding">
                            <table class="table table-striped">
                                <thead>
                                <tr class="bg-inverse">
                                    <th>#</th>
                                    <th>Registro</th>
                                    <th>IP Origem</th>
                                    <th>Data Ocorrência</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="6">Este usuário não possui registros de logs</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-body panel-no-padding">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr class="bg-inverse">
                                        <th>#</th>
                                        <th>Registro</th>
                                        <th>IP Origem</th>
                                        <th>Data Ocorrência</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($logs as $r) {
                                        $data = date(('d/m/Y - H:i:s'), strtotime($r->data_registro));

                                        echo '<tr>';
                                        echo '<td>' . $r->id_log . '</td>';
                                        echo '<td>' . $r->descricao . '</td>';
                                        echo "<td><a target='_blank' href='https://whatismyipaddress.com/ip/$r->ip'>" . $r->ip . "</a></td>";
                                        echo '<td>' . $data . '</td>';
                                        echo '</tr>';
                                    } ?>
                                    <tr>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel-footer">
                                <?php echo $this->pagination->create_links(); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!--            TAB EM BREVE-->
            <div class="tab-pane" id="dev">
                <div class="alert alert-danger">
                    Esta aba encontra-se em desenvolvimento
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#tabUsuario a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // store the currently selected tab in the hash value
    $("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
        var id = $(e.target).attr("href").substr(1);
        window.location.hash = id;
    });

    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    $('#tabUsuario a[href="' + hash + '"]').tab('show');

</script>