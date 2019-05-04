<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css"/>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>

<style type="text/css">

    label.error {
        color: #b94a48;
    }

    input.error {
        border-color: #b94a48;
    }

    input.valid {
        border-color: #5bb75b;
    }

    .table-bordeless td, .table-bordeless th {
        border: none;
    }

    table {
        font-family: Arial;
        font-size: 11px;
    }

</style>

<div class="span6" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-user-circle fa-lg fa-fw"></i>
		</span>
            <h5>Minha Conta</h5>
        </div>
        <div class="widget-content">
            <div class="row-fluid">
                <div class="span12" style="min-height: 260px">
                    <ul class="site-stats">
                        <li class="bg_ls span12" style="text-align: left; margin-left: 0"><strong>Nome: <?php echo $usuario->nome ?></strong></li>
                        <li class="bg_lb span12" style="text-align: left; margin-left: 0"><strong>Telefone: <?php echo $usuario->telefone ?></strong></li>
                        <li class="bg_lg span12" style="text-align: left; margin-left: 0"><strong>Email: <?php echo $usuario->email ?></strong></li>
                        <li class="bg_lo span12" style="text-align: left; margin-left: 0"><strong>Nível: <?php echo $usuario->permissao; ?></strong></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="span6">
    <div class="widget-box">
        <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-lock fa-lg fa-fw"></i>
		</span>
            <h5>Alterar Minha Senha</h5>
        </div>
        <div class="widget-content">
            <div class="row-fluid">
                <div class="span12" style=" min-height: 260px">
                    <form id="formSenha" action="<?php echo base_url(); ?>index.php/mapos/alterarSenha" method="post">

                        <div class="span12" style="margin-left: 0">
                            <label for="">Senha Atual</label>
                            <input type="password" id="oldSenha" name="oldSenha" class="span12"/>
                        </div>
                        <div class="span12" style="margin-left: 0">
                            <label for="">Nova Senha</label>
                            <input type="password" id="novaSenha" name="novaSenha" class="span12"/>
                        </div>
                        <div class="span12" style="margin-left: 0">
                            <label for="">Confirmar Senha</label>
                            <input type="password" name="confirmarSenha" class="span12"/>
                        </div>
                        <div class="span12" style="margin-left: 0; text-align: center">
                            <button class="btn btn-primary"><i class="fa fa-refresh fa-fw"></i> Alterar Senha</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('#formSenha').validate({
            rules: {
                oldSenha: {required: true},
                novaSenha: {required: true},
                confirmarSenha: {
                    required: "#novaSenha",
                    equalTo: "#novaSenha"
                },
            },
            messages: {
                oldSenha: {required: 'Digite sua senha atual'},
                novaSenha: {required: 'Digite sua nova senha'},
                confirmarSenha: {
                    required: 'Confirme sua nova senha',
                    equalTo: 'As senhas não correspondem'
                }
            },

            // errorClass: "help-inline",
            // errorElement: "span",
            // highlight: function (element, errorClass, validClass) {
            //     $(element).parents('.control-group').addClass('error');
            // },
            // unhighlight: function (element, errorClass, validClass) {
            //     $(element).parents('.control-group').removeClass('error');
            //     $(element).parents('.control-group').addClass('success');
            // }
        });
    });
</script>