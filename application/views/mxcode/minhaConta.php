<div class="row">
    <div class="col-md-12">
        <div class="panel panel-profile panel-midnightblue">
            <div class="panel-heading">
                <h2 style="font-size: 12pt">
                    <i class="fa fa-user-circle fa-lg fa-fw"></i>
                    Minha Conta
                </h2>
                <div class="panel-ctrls">
                    <button href="#modalAlterarSenha" class="btn btn-primary btn-sm" id="alterar_senha" data-toggle="modal" title="Alterar senha da conta">
                        <i class="fa fa-lock fa-fw"></i>
                        Alterar Senha
                    </button>
                    <button href="#modalEditarPerfil" class="btn btn-primary btn-sm" id="editar_perfil" data-toggle="modal" title="Editar perfil de usuário">
                        <i class="fa fa-user-circle fa-fw"></i>
                        Editar Perfil
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <div class="user-card">
                    <div class="avatar">
                        <img src="<?php echo base_url(); ?>assets/img/avatars/padrao.png" class="img-responsive img-circle">
                    </div>
                    <div class="contact-name"><?php echo $usuario->nome ?></div>
                    <div class="contact-status"><?php echo $usuario->permissao; ?></div>
                    <ul class="details">
                        <li><a href="javascript:"><?php echo $usuario->email ?></a></li>
                        <li><?php echo $usuario->telefone ?></li>
                        <li>
                            <?php echo $usuario->bairro ?>,
                            <?php echo $usuario->cidade . '/' . $usuario->uf ?>
                        </li>
                    </ul>
                    <hr class="">
                    <div class="text-center">
                        <a href="javascript:" class="btn btn-social btn-facebook"><i class="fa fa-facebook"></i></a>
                        <a href="javascript:" class="btn btn-social btn-twitter"><i class="fa fa-twitter"></i></a>
                        <a href="javascript:" class="btn btn-social btn-github"><i class="fa fa-github"></i></a>
                        <a href="javascript:" class="btn btn-social btn-flickr"><i class="fa fa-flickr"></i></a>
                        <a href="javascript:" class="btn btn-social btn-instagram"><i class="fa fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ATUALIZAR PERFIL DO USUÁRIO -->
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Editar perfil de usuário</h4>
            </div>
            <form id="formAlterar" action="<?php echo base_url() ?>mxcode/atualizarPerfil" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="font-weight-bold" for="nome">Nome *</label>
                            <input id="nome" class="form-control" type="text" name="nome" value="<?php echo $dados->nome; ?>"/>
                            <input id="id_usuarios" type="hidden" name="id_usuarios" value="<?php echo $dados->id_usuarios; ?>"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label for="cpf" class="font-weight-bold">CPF</label>
                            <input class="form-control" type="text" id="cpf" name="cpf" value="<?php echo $dados->cpf; ?>"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="rg" class="font-weight-bold">RG</label>
                            <input type="text" class="form-control" id="rg" name="rg" value="<?php echo $dados->rg; ?>"/>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cep" class="font-weight-bold">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" value="<?php echo $dados->cep; ?>"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-8">
                            <label for="logradouro" class="font-weight-bold">Logradouro</label>
                            <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?php echo $dados->logradouro; ?>"/>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="numero" class="font-weight-bold">Nº</label>
                            <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $dados->numero; ?>"/>
                        </div>
                        <div class="form-group col-md-2 mt30">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="s_n" name="s_n" value="1" <?= $dados->s_n == 1 ? 'checked' : '' ?>>
                            </div>
                            <label for="s_n" class="font-weight-bold">S/N</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label for="bairro" class="font-weight-bold">Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo $dados->bairro; ?>"/>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="cidade" class="font-weight-bold">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo $dados->cidade; ?>"/>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="uf" class="font-weight-bold">UF</label>
                            <input type="text" class="form-control" id="uf" name="uf" value="<?php echo $dados->uf; ?>"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="email" class="font-weight-bold">Email *</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $dados->email; ?>" disabled/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="telefone" class="font-weight-bold">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $dados->telefone; ?>"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ALTERAR SENHA DO USUÁRIO -->
<div class="modal fade" id="modalAlterarSenha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Alterar senha</h4>
            </div>
            <form id="formSenha" action="<?php echo base_url() ?>mxcode/alterarSenha" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="oldSenha" class="font-weight-bold">Senha Atual *</label>
                            <input class="form-control" type="password" id="oldSenha" name="oldSenha"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="novaSenha" class="font-weight-bold">Nova Senha *</label>
                            <input type="password" class="form-control" id="novaSenha" name="novaSenha"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="confirmarSenha" class="font-weight-bold">Confirme Nova Senha *</label>
                            <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#s_n').on('ifChanged', function (event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#numero').attr('disabled', true);
                $('#numero').val('');
            } else {
                $('#numero').removeAttr('disabled');
            }
        });

        var s_n = $('#s_n').iCheck('update')[0].checked;
        $.each($(s_n), function (key, value) {
            if (s_n == true) {
                $('#numero').attr('disabled', true);
                $('#numero').val('');
            } else {
                $('#numero').removeAttr('disabled');
            }
        });


        $('#formSenha').validate({
            rules: {
                oldSenha: {required: true},
                novaSenha: {
                    required: true,
                    minlength: 6
                },
                confirmarSenha: {
                    required: "#novaSenha",
                    equalTo: "#novaSenha",
                },
            },
            messages: {
                oldSenha: {required: 'Digite sua senha atual'},
                novaSenha: {
                    required: 'Digite sua nova senha',
                    minlength: 'A nova senha deve conter no mínimo 6 caracteres'
                },
                confirmarSenha: {
                    required: 'Repita sua nova senha',
                    equalTo: 'As senhas não correspondem'
                }
            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }
        });
    });
</script>