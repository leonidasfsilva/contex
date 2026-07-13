<?php if ($this->session->flashdata('api_token')) { ?>
    <div class="note note-success mb20" id="api-token-note" style="position: relative;">
        <button type="button" class="close" aria-label="Fechar" onclick="$('#api-token-note').fadeOut();">&times;</button>
        <strong><?= $this->session->flashdata('api_token_message') ?></strong>
        <div class="mt10">
            <code style="white-space: normal; word-break: break-all; display: block;">
                <?= $this->session->flashdata('api_token') ?>
            </code>
        </div>
    </div>
<?php } ?>

<style>
    .cliente-api-scope-toggle {
        display: inline-block;
        width: 135px;
    }

    @media (max-width: 767px) {
        .cliente-api-scope-toggle {
            float: left;
            width: 50%;
        }
    }
</style>

<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fal fa-plug fa-lg fa-fw"></i>
            Clientes API
        </h3>
        <div class="panel-ctrls">
            <a href="#modalAdicionar" role="button" data-toggle="modal" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-fw"></i> Novo Cliente API
            </a>
        </div>
    </div>

    <div class="panel-body panel-no-padding table-responsive">
        <table class="table table-condensed table-striped table-bordeless table-hover no-footer" role="grid" style="width: 100%;">
            <thead>
                <tr role="row">
                    <th style="width: 60px">ID</th>
                    <th>Username</th>
                    <th>Descrição</th>
                    <th>Escopos</th>
                    <th>Token</th>
                    <th>Status</th>
                    <th style="width: 170px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$results) { ?>
                    <tr>
                        <td colspan="7">Nenhum cliente API cadastrado</td>
                    </tr>
                <?php } else {
                    foreach ($results as $r) {
                        $token = $r->token ? substr($r->token, 0, 8) . '...' . substr($r->token, -8) : '-';
                        $scopes = $r->scopes ? str_replace(',', ', ', $r->scopes) : '-';
                        $status = $r->active == 1 ? 'ATIVO' : 'INATIVO';
                        $labelStatus = $r->active == 1 ? 'success' : 'warning';
                        $clienteApiLabel = htmlspecialchars($r->username . ' (' . ($r->description ?: '-') . ')', ENT_QUOTES, 'UTF-8');
                        $clienteApiUsername = htmlspecialchars($r->username, ENT_QUOTES, 'UTF-8');
                        $clienteApiDescription = htmlspecialchars($r->description, ENT_QUOTES, 'UTF-8');
                        $clienteApiScopes = htmlspecialchars($r->scopes ?: '', ENT_QUOTES, 'UTF-8');
                        $btnStatus = $r->active == 1
                            ? '<a href="#modalDesativar" role="button" data-toggle="modal" cliente-api="' . $r->id . '" cliente-api-label="' . $clienteApiLabel . '" class="btn btn-warning btn-sm" title="Desativar"><i class="fas fa-minus-circle fa-lg fa-fw"></i></a>'
                            : '<a href="#modalAtivar" role="button" data-toggle="modal" cliente-api="' . $r->id . '" cliente-api-label="' . $clienteApiLabel . '" class="btn btn-success btn-sm" title="Ativar"><i class="fas fa-check-circle fa-lg fa-fw"></i></a>';

                        echo '<tr>';
                        echo '<td>' . $r->id . '</td>';
                        echo '<td><a href="#modalEditar" data-toggle="modal" class="editar font-weight-bold" cliente-api="' . $r->id . '" cliente-api-username="' . $clienteApiUsername . '" cliente-api-description="' . $clienteApiDescription . '" cliente-api-scopes="' . $clienteApiScopes . '" cliente-api-active="' . $r->active . '">' . $r->username . '</a></td>';
                        echo '<td>' . ($r->description ?: '-') . '</td>';
                        echo '<td>' . $scopes . '</td>';
                        echo '<td><code>' . $token . '</code></td>';
                        echo '<td><span class="badge badge-' . $labelStatus . '">' . $status . '</span></td>';
                        echo '<td style="text-align: center">';
                        echo '<a href="#modalEditar" role="button" data-toggle="modal" cliente-api="' . $r->id . '" cliente-api-username="' . $clienteApiUsername . '" cliente-api-description="' . $clienteApiDescription . '" cliente-api-scopes="' . $clienteApiScopes . '" cliente-api-active="' . $r->active . '" class="btn btn-primary btn-sm editar" title="Editar"><i class="fas fa-edit fa-lg fa-fw"></i></a> ';
                        echo '<a href="#modalRegenerar" role="button" data-toggle="modal" cliente-api="' . $r->id . '" cliente-api-label="' . $clienteApiLabel . '" class="btn btn-info btn-sm" title="Regenerar token"><i class="fas fa-sync-alt fa-lg fa-fw"></i></a> ';
                        echo $btnStatus . ' ';
                        echo '<a href="#modalExcluir" role="button" data-toggle="modal" cliente-api="' . $r->id . '" cliente-api-label="' . $clienteApiLabel . '" class="btn btn-danger btn-sm" title="Excluir"><i class="fas fa-trash-alt fa-lg fa-fw"></i></a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalAdicionar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title text-white">Novo cliente API</h4>
            </div>
            <form action="<?= base_url('clientesapi/adicionar') ?>" method="post" id="formAdicionarClienteApi">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12 col-xs-12">
                            <label for="adicionar_username" class="font-weight-bold">Username *</label>
                            <input type="text" class="form-control" name="username" id="adicionar_username" maxlength="50" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12 col-xs-12">
                            <label for="adicionar_description" class="font-weight-bold">Descrição</label>
                            <input type="text" class="form-control" name="description" id="adicionar_description" maxlength="50">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12 col-xs-12">
                            <label class="font-weight-bold">Escopos</label>
                            <div class="row">
                                <?php foreach ($availableScopes as $scope => $label) { ?>
                                    <?php $scopeId = 'adicionar_scope_' . preg_replace('/[^a-z0-9_]/i', '_', $scope); ?>
                                    <span class="cliente-api-scope-toggle">
                                        <input type="checkbox" class="switch-input primary adicionar-scope-toggle <?= $scope === '*' ? 'adicionar-scope-all' : 'adicionar-scope-item' ?>" id="<?= $scopeId ?>" name="scopes[]" value="<?= $scope ?>">
                                        <label for="<?= $scopeId ?>" class="switch-label primary font-weight-bold"><?= $label ?></label>
                                    </span>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4">
                            <div class="row">
                                <input type="checkbox" class="switch-input primary" id="adicionar_active" name="active" value="1" checked>
                                <label for="adicionar_active" class="switch-label primary font-weight-bold">Ativo</label>
                            </div>
                        </div>
                        <div class="col-xs-8 modal-form-buttons">
                            <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-check fa-fw"></i> Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title text-white">Editar cliente API</h4>
            </div>
            <form action="<?= base_url('clientesapi/editar') ?>" method="post" id="formEditarClienteApi">
                <div class="modal-body">
                    <input type="hidden" id="editar_id" name="id" value="">

                    <div class="row">
                        <div class="form-group col-lg-12 col-xs-12">
                            <label for="editar_username" class="font-weight-bold">Username *</label>
                            <input type="text" class="form-control" name="username" id="editar_username" maxlength="50" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12 col-xs-12">
                            <label for="editar_description" class="font-weight-bold">Descrição</label>
                            <input type="text" class="form-control" name="description" id="editar_description" maxlength="50">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12 col-xs-12">
                            <label class="font-weight-bold">Escopos</label>
                            <div class="row">
                                <?php foreach ($availableScopes as $scope => $label) { ?>
                                    <?php $scopeId = 'editar_scope_' . preg_replace('/[^a-z0-9_]/i', '_', $scope); ?>
                                    <span class="cliente-api-scope-toggle">
                                        <input type="checkbox" class="switch-input primary editar-scope-toggle <?= $scope === '*' ? 'editar-scope-all' : 'editar-scope-item' ?>" id="<?= $scopeId ?>" name="scopes[]" value="<?= $scope ?>">
                                        <label for="<?= $scopeId ?>" class="switch-label primary font-weight-bold"><?= $label ?></label>
                                    </span>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="text-left col-xs-4">
                            <div class="row">
                                <input type="checkbox" class="switch-input primary" id="editar_active" name="active" value="1">
                                <label for="editar_active" class="switch-label primary font-weight-bold">Ativo</label>
                            </div>
                        </div>
                        <div class="col-xs-8 modal-form-buttons">
                            <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                            <button class="btn btn-primary btn-sm"><i class="fas fa-check fa-fw"></i> Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRegenerar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title text-white">Regenerar token</h4>
            </div>
            <form action="<?= base_url('clientesapi/regenerar') ?>" method="post">
                <div class="modal-body">
                    <p><strong>Deseja realmente regenerar o token deste cliente API?</strong></p>
                    <div class="note note-info cliente-api-confirmacao"><strong></strong></div>
                    <input type="hidden" id="id_regenerar" name="id" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-info btn-sm"><i class="fas fa-sync-alt fa-fw"></i> Regenerar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAtivar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title text-white">Ativar cliente API</h4>
            </div>
            <form action="<?= base_url('clientesapi/ativar') ?>" method="post">
                <div class="modal-body">
                    <p><strong>Deseja realmente ativar este cliente API?</strong></p>
                    <div class="note note-success cliente-api-confirmacao"><strong></strong></div>
                    <input type="hidden" id="id_ativar" name="id" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-success btn-sm"><i class="fa fa-check fa-fw"></i> Ativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDesativar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title text-white">Desativar cliente API</h4>
            </div>
            <form action="<?= base_url('clientesapi/desativar') ?>" method="post">
                <div class="modal-body">
                    <p><strong>Deseja realmente desativar este cliente API?</strong></p>
                    <div class="note note-warning cliente-api-confirmacao"><strong></strong></div>
                    <input type="hidden" id="id_desativar" name="id" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-warning btn-sm"><i class="fa fa-check fa-fw"></i> Desativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title text-white">Excluir cliente API</h4>
            </div>
            <form action="<?= base_url('clientesapi/excluir') ?>" method="post">
                <div class="modal-body">
                    <p><strong>Deseja realmente excluir este cliente API?</strong></p>
                    <div class="note note-danger cliente-api-confirmacao"><strong></strong></div>
                    <input type="hidden" id="id_excluir" name="id" value="">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.editar').click(function() {
            var scopes = ($(this).attr('cliente-api-scopes') || '').split(',');
            $('#editar_id').val($(this).attr('cliente-api'));
            $('#editar_username').val($(this).attr('cliente-api-username'));
            $('#editar_description').val($(this).attr('cliente-api-description'));
            $('#editar_active').prop('checked', $(this).attr('cliente-api-active') == '1');

            $('.editar-scope-toggle').prop('checked', false);
            $.each(scopes, function(index, scope) {
                $('.editar-scope-toggle[value="' + scope + '"]').prop('checked', true);
            });
        });

        $('.editar-scope-all').change(function() {
            $('.editar-scope-item').prop('checked', $(this).prop('checked'));
        });

        $('.editar-scope-item').change(function() {
            $('.editar-scope-all').prop('checked', $('.editar-scope-item').length === $('.editar-scope-item:checked').length);
        });

        $('.adicionar-scope-all').change(function() {
            $('.adicionar-scope-item').prop('checked', $(this).prop('checked'));
        });

        $('.adicionar-scope-item').change(function() {
            $('.adicionar-scope-all').prop('checked', $('.adicionar-scope-item').length === $('.adicionar-scope-item:checked').length);
        });

        $('#modalAdicionar').on('shown.bs.modal', function() {
            $('#adicionar_username').focus();
        });

        $('a[cliente-api]').click(function() {
            var id = $(this).attr('cliente-api');
            var label = $(this).attr('cliente-api-label');
            $('#id_regenerar, #id_ativar, #id_desativar, #id_excluir').val(id);
            $('.cliente-api-confirmacao strong').text(label);
        });
    });
</script>
