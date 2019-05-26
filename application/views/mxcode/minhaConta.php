<div class="row">
    <div class="col-md-6">
        <div class="panel panel-profile">
            <div class="panel-heading">
                <h2 style="font-size: 12pt">
                    <i class="fa fa-user-circle fa-lg fa-fw"></i>
                    Minha Conta
                </h2>
                <div class="panel-ctrls">
                    <button href="#modalFiltrar" class="btn btn-default btn-sm" id="editar_perfil" data-toggle="modal" title="Editar perfil de usuário">
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
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 style="font-size: 12pt">
                    <i class="fa fa-lock fa-lg fa-fw"></i>
                    Alterar Senha
                </h2>
            </div>
            <div class="panel-body ">
                <form id="formSenha" action="<?php echo base_url(); ?>mxcode/alterarSenha" method="post">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Senha Atual *</label>
                            <input class="form-control" type="password" id="oldSenha" name="oldSenha"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Nova Senha *</label>
                            <input class="form-control" type="password" id="novaSenha" name="novaSenha"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Confirme Nova Senha *</label>
                            <input class="form-control" type="password" name="confirmarSenha"/>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-primary btn-sm pull-right"><i class="fa fa-refresh fa-fw"></i> Alterar Senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

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
                    minlength: 6
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