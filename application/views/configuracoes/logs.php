<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h2>
            <ul class="nav nav-tabs" id="tabUsuario">
                <li class="active"><a href="#logs" data-toggle="tab">Logs</a></li>
            </ul>
        </h2>
        <div class="panel-ctrls">
            <a href="<?php echo base_url('configuracoes/sistema') ?>" class="btn btn-default btn-sm "><i class="fas fa-arrow-left fa-fw"></i> Configurações do Sistema</a>
        </div>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <!--            TAB LOGS DO SISTEMA-->
            <div class="tab-pane active" id="logs">
                <?php if (!$logs) { ?>
                    <div class="panel panel-midnightblue">
                        <div class="panel-body panel-no-padding">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Registro</th>
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
                                            <th>Nome</th>
                                            <th>Email</th>
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
                                            echo '<td>' . $r->nome . '</td>';
                                            echo '<td>' . $r->email . '</td>';
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