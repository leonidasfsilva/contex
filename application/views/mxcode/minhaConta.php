<div class="row">
    <div class="col-md-12">
        <div class="panel panel-profile panel-midnightblue">
            <div class="panel-heading">
                <h3>
                    <i class="fas fa-user-cog fa-lg fa-fw"></i>
                    Minha Conta
                </h3>
                <div class="panel-ctrls">
                    <button href="#modalAlterarSenha" class="btn btn-primary btn-sm" id="alterar_senha" data-toggle="modal" title="Alterar senha de acesso">
                        <i class="fas fa-key fa-fw"></i>
                        Alterar Senha
                    </button>
                    <button href="#modalEditarPerfil" class="btn btn-primary btn-sm" id="editar_perfil" data-toggle="modal" title="Alterar dados de usuário">
                        <i class="fas fa-user-edit fa-fw"></i>
                        Alterar Dados
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <div class="user-card">
                    <div class="avatar avatar-overlay">
                        <a href="#modalAlterarFoto" data-toggle="modal" title="Alterar foto de perfil ">
                            <img src="<?php echo $this->session->userdata('avatar') != null ? base_url() . 'assets/uploads/avatars/' . $this->session->userdata('avatar') : base_url() . 'assets/img/avatars/padrao.png'; ?>"
                                 class="img-responsive img-circle avatar-image">
                            <div class="avatar-image-hover">
                                <i class="fas fa-paint-brush fa-2x fa-fw"></i>
                            </div>
                        </a>
                    </div>
                    <div class="contact-name"><?php echo $usuario->nome ?></div>
                    <div class="contact-status"><?php echo $usuario->permissao; ?></div>
                    <ul class="details">
                        <li>
                            <strong>Email:</strong> <?php echo $usuario->email ?>
                        </li>
                        <li>
                            <strong>Telefone:</strong> <?php echo $usuario->telefone ?>
                        </li>
                        <li>
                            <strong>CPF:</strong> <?php echo $usuario->cpf ?>
                        </li>
                        <li>
                            <strong>RG:</strong> <?php echo $usuario->rg ?>
                        </li>
                        <li>
                            <strong>CEP:</strong> <?php echo $usuario->cep ?>
                        </li>
                        <li>
                            <strong>Logradouro:</strong> <?php echo $usuario->logradouro ?>,
                            <?php if ($usuario->s_n) {
                                echo 'S/N';
                            } else {
                                echo $usuario->numero;
                            }
                            ?>
                        </li>
                        <li>
                            <strong>Bairro:</strong> <?php echo $usuario->bairro ?>
                        </li>
                        <li>
                            <strong>Cidade:</strong> <?php echo $usuario->cidade ?>
                        </li>
                        <li>
                            <strong>UF:</strong> <?php echo $usuario->uf ?>
                        </li>
                    </ul>
                    <hr class="">
                    <div class="text-center">
                        <a href="javascript:" class="btn btn-social btn-facebook" title="Facebook"><i class="fab fa-facebook-f fa-fw"></i></a>
                        <a href="javascript:" class="btn btn-social btn-twitter" title="Twitter"><i class="fab fa-twitter fa-fw"></i></a>
                        <a href="javascript:" class="btn btn-social btn-github" title="GitHub"><i class="fab fa-github fa-fw"></i></a>
                        <a href="javascript:" class="btn btn-social btn-flickr" title="Flickr"><i class="fab fa-flickr fa-fw"></i></a>
                        <a href="javascript:" class="btn btn-social btn-instagram" title="Instagram"><i class="fab fa-instagram fa-fw"></i></a>
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
                <h4 class="modal-title text-white ">Alterar dados de usuário</h4>
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
                        <div class="form-group col-md-8" id="div_logradouro">
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
                            <div class="input-group" title="Para alterar seu email de cadastro, contate o administrador do sistema">
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $dados->email; ?>" disabled/>
                                <span class="input-group-addon"><i class="fas fa-info-circle fa-lg fa-fw"></i></span>
                            </div>
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
                <h4 class="modal-title text-white ">Alterar senha de acesso</h4>
            </div>
            <form id="formSenha" action="<?php echo base_url() ?>mxcode/alterarSenha" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="anitgaSenha" class="font-weight-bold">Senha Atual *</label>
                            <input class="form-control" type="password" id="anitgaSenha" name="antigaSenha"/>
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
                    <button id="btnCancel" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ALTERAR FOTO DO USUÁRIO -->
<div class="modal fade" id="modalAlterarFoto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Alterar foto de perfil</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="btn btn-primary btn-upload btn-block" for="inputImage" data-toggle="tooltip" data-animation="false" title="" data-original-title="Selecionar imagem para perfil">
                            <input type="file" class="sr-only" id="inputImage" name="file" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                            <span class="docs-tooltip">
                                  <i class="fas fa-folder-open fa-fw"></i> Selecionar Imagem
                                </span>
                        </label>
                    </div>
                </div>
                <?php if ($this->session->userdata('avatar') != null) { ?>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <button href="#modalExcluirFotoUsuario" class="btn btn-danger btn-block" data-toggle="modal" title="Excluir foto de perfil">
                                <span class="docs-tooltip">
                                      <i class="fas fa-trash-alt fa-fw"></i> Excluir Foto
                                </span>
                            </button>
                        </div>
                    </div>
                <?php } ?>
                <div class="hidden" id="div_img_cropper">
                    <div class="row">
                        <div class="col-md-12 form-group img-cropper">
                            <div class="cropper-container">
                                <img id="img-cropper" alt="Picture" class="cropper-hidden">
                            </div>
                        </div>
                    </div>
                    <!--                        CONTROLS BUTTONS-->
                    <div class="row">
                        <div class="col-xs-4 docs-buttons form-group">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Aumentar zoom">
                                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="" data-original-title="Aumentar zoom">
                                      <i class="fas fa-search-plus fa-lg fa-fw"></i>
                                    </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Diminuir zoom">
                                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="" data-original-title="Diminuir zoom">
                                      <i class="fas fa-search-minus fa-lg fa-fw"></i>
                                    </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4 docs-buttons form-group">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Inverter horizontalmente">
                                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="" data-original-title="Inverter horizontalmente">
                                      <i class="fas fa-exchange-alt fa-lg fa-fw"></i>
                                    </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Inverter verticalmente">
                                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="" data-original-title="Inverter verticalmente">
                                      <i class="fas fa-exchange-alt fa-rotate-90 fa-lg fa-fw"></i>
                                    </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4 docs-buttons form-group">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-45" title="Girar para esquerda">
                                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="" data-original-title="Girar para esquerda">
                                      <i class="fas fa-undo fa-lg fa-fw"></i>
                                    </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="45" title="Girar para direitat">
                                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="" data-original-title="Girar para direita">
                                      <i class="fas fa-redo fa-lg fa-fw"></i>
                                    </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 docs-buttons form-group">
                            <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-method="getCroppedCanvas" data-animation="false" data-original-title="Finalizar edição">
                                    <span class="docs-tooltip">
                                      <span class="fas fa-check fa-fw"></span> Finalizar
                                    </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--                <div class="modal-footer">-->
            <!--                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">-->
            <!--                        <i class="fa fa-times fa-fw"></i> Cancelar-->
            <!--                    </button>-->
            <!--                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>-->
            <!--                </div>-->
        </div>
    </div>
</div>

<!-- Modal CONFIRMAR FOTO DO USUÁRIO -->
<div class="modal fade" id="getCroppedCanvasModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Confirmar foto</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="avatar-cropped">
                        <img id="image-cropped" class="img-responsive img-circle image-cropped">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-times fa-fw"></i> Cancelar
                </button>
                <button type="button" id="send_cropped_avatar" class="btn btn-success btn-sm"><i class="fa fa-check fa-fw"></i> Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal EXCLUIR FOTO DO USUÁRIO -->
<div class="modal fade" id="modalExcluirFotoUsuario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir foto de perfil</h4>
            </div>
            <form id="formExcluirFoto" action="<?php echo base_url() ?>mxcode/excluirFotoUsuario" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir sua foto de perfil de usuário?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm" id="btnExcluir"><i class="fa fa-check fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    // $('#modalAlterarFoto').on('hidden.bs.modal', function () {
    //     cropBoxData = $image.cropper('getCropBoxData');
    //     canvasData = $image.cropper('getCanvasData');
    //     $image.cropper('destroy');
    // });

    $(document).ready(function () {

    });

    $('input[type=file]').change(function () {
        $("#div_img_cropper").removeClass('hidden');
    });

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


        $('#formAlterar').validate({
            rules: {
                nome: {required: true},
            },
            messages: {
                nome: {required: 'Informe seu nome'},
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

        $('#formSenha').validate({
            rules: {
                antigaSenha: {required: true},
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
                antigaSenha: {required: 'Digite sua senha atual'},
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