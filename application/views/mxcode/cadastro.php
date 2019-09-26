<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>CONTEX - Sistema de Gestão</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="description" content="Contex Sistema de Gestão">
    <meta name="author" content="Leônidas Ferreira">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/contex_logo.png" type="image/x-icon"/>

    <link href="<?php echo base_url(); ?>assets/css/bootstrap-agile.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/agile-style.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,600,700' rel='stylesheet' type='text/css'>
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome5/css/fontawesome5.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.css" type="text/css" rel="stylesheet">


    <script src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.min.js"></script>                            <!-- Load jQuery -->
    <script src="<?php echo base_url(); ?>assets/js/jqueryui-1.9.2.min.js"></script>                            <!-- Load jQueryUI -->
    <script src="<?php echo base_url(); ?>assets/js/bootstrap3.3.7.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-waves.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-style.switcher.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
</head>
<style>
    .preloader-login {
        display: none;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: static;
        z-index: 1;
        background: #EEEEEE;
    }

    .preloader-login .cssload-speeding-wheel {
        position: absolute;
        top: calc(30%);
        left: calc(50% - 4%);
    }

    @media screen and (min-width: 1024px) {
        .box-login {
            position: relative;
            right: 0px;
            padding-top: calc(30%);
            height: 100%;
        }
    }
</style>
<body class="focused-form" style="background-color: #37474f">

<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="login-register">
    <div class="login-box login-sidebar">
        <div class="text-center m-t-40">
            <img class="contex-words" src="<?php echo base_url() ?>assets/img/contex_brand.png" alt="CONTEX - Sistema de Gestão"/>
        </div>
        <div class="white-box box-login">
            <form class="form-horizontal floating-labels" id="formCadastro" method="post" action="<?= site_url('cadastro/cadastrar') ?>" autocomplete="off">
                <div class="preloader-login">
                    <div class="cssload-speeding-wheel"></div>
                </div>
                <div class="before-loading">
                    <div class="form-group">
                        <h3 class="font-bold">Crie sua conta</h3>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6">
                                <input type="text" class="form-control" id="nome" name="nome" required/>
                                <span class="highlight"></span> <span class="bar"></span>
                                <label for="nome">Nome</label>
                            </div>
                            <div class="col-xs-6">
                                <input type="text" class="form-control" id="sobrenome" name="sobrenome" required/>
                                <span class="highlight"></span> <span class="bar"></span>
                                <label for="sobrenome">Sobrenome</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="email" id="email" name="email" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6">
                                <input type="password" class="form-control" id="novaSenha" name="novaSenha" required/>
                                <span class="highlight"></span> <span class="bar"></span>
                                <label for="novaSenha">Defina sua senha</label>
                            </div>
                            <div class="col-xs-6">
                                <input class="form-control" type="password" id="confirmarSenha" name="confirmarSenha" required/>
                                <span class="highlight"></span> <span class="bar"></span>
                                <label for="confirmarSenha">Confirme sua senha</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="javascript:" onclick="verificar_conta()" class="text-primary pull-right">Não recebi o email de verificação</a>
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-login btn-block waves-effect waves-light" type="submit"><i class="far fa-check-circle fa-fw"></i> Cadastrar</button>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Já possui uma conta? <a href="<?php echo base_url() ?>mxcode/login" id="login" class="text-primary"><b>Efetue seu login</b></a>.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- End loading site level scripts -->


<script type="text/javascript">
    $('#formCadastro').validate({
        rules: {
            nome: {required: true},
            sobrenome: {required: true},
            email: {required: true},
            novaSenha: {
                required: true,
                minlength: 6
            },
            confirmarSenha: {
                required: "#novaSenha",
                equalTo: "#novaSenha"
            },
        },
        messages: {
            nome: {
                required: 'Informe seu nome',
            },
            sobrenome: {
                required: 'Informe seu sobrenome',
            },
            email: {
                required: 'Informe seu email',
                email: 'Por favor, informe um email válido'
            },
            novaSenha: {
                required: 'Crie uma senha de acesso',
                minlength: 'A senha deve conter no mínimo 6 caracteres',
            },
            confirmarSenha: {
                required: 'Repita sua senha de acesso',
                equalTo: 'As senhas não correspondem',
            },
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

    $('#formCadastro').submit(function (event) {
        var form = this;
        event.preventDefault();
        $('#btn-acessar').addClass('disabled');
        $('#btn-acessar').html('Acessando... <i class="fa fa-spinner fa-pulse fa-fw"></i>');
        $('#progress-acessar').removeClass('hidden');
        $(".progress-bar").animate({
            width: "100%"
        }, 1000);
        if ($(form).valid()) {
            $(".before-loading").fadeOut();
            $(".preloader-login").fadeIn();

            setTimeout(function () {
                form.submit();
            }, 1000);
        }
    });

    $('#cancelToken').click(function () {
        var form = $('#form_invalidar_token');
        form.submit();
    });

    function verificar_conta() {
        Swal.fire({
            position: 'top',
            title: 'Não recebeu o email de verificação?',
            html: '<div>Informe o email que utilizou em seu cadastro e lhe enviaremos um novo email para confirmar sua conta:</div>',
            input: 'email',
            inputPlaceholder: 'Digite seu email',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-send fa-fw"></i> Enviar ',
            cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
            reverseButtons: true,
            showCloseButton: true,
            showLoaderOnConfirm: true,
            preConfirm: (email) => {
                return new Promise((resolve) => {
                    setTimeout(() => {
                        // if (email === 'taken@example.com') {
                        //     swal.showValidationError(
                        //         'This email is already taken.'
                        //     )
                        // }
                        resolve();
                    }, 2000)
                })
            },
            allowOutsideClick: () => !swal.isLoading()
        }).then((result) => {
            if (result.value) {
                $(function () {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('cadastro/reenviarverificacao'); ?>",
                        data: {
                            'email': result.value
                        }, // <--- THIS IS THE CHANGE
                        dataType: 'json',
                        cache: false,
                        success: function (res) {
                            // var res = jQuery.parseJSON(resposta);
                            if (res.validacao == 'ok') {
                                Swal.fire({
                                    position: 'top',
                                    type: 'success',
                                    title: 'Email enviado!',
                                    showConfirmButton: true,
                                    showCancelButton: false,
                                    showCloseButton: true,
                                    confirmButtonText: '<i class="fas fa-check fa-fw"></i> OK ',
                                    cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
                                    reverseButtons: true,
                                    html:
                                        '<p class="">Enviamos um e-mail para <strong class="text-success">' +
                                        res.email +
                                        '</strong>, ' +
                                        'verifique sua caixa de entrada ou pasta de <i>spam</i> e siga as instruções para validar sua conta.</p>',
                                }).then((result) => {
                                    if (result.value) {
                                        //window.location.replace('<?//= site_url() ?>//' + 'redefinirsenha/verificacao?token=' + res.token + '&id=' + res.id);
                                    } else {

                                    }
                                })
                            }
                            else if (res.validacao == 'ja_validado') {
                                Swal.fire({
                                    position: 'top',
                                    type: 'error',
                                    title: 'Conta inexistente!',
                                    html: 'A conta registrada com o email <strong class="text-success"> ' + res.email + '</strong> já foi verificada, caso tenha esquecido sua senha, clique no botão',
                                    showConfirmButton: true,
                                    showCancelButton: true,
                                    reverseButtons: true,
                                    showCloseButton: true,
                                    confirmButtonText: '<i class="fas fa-user-lock fa-fw"></i> Esqueci minha senha ',
                                    cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
                                }).then((result) => {
                                    if (result.value) {
                                        recuperar_senha();
                                    } else {

                                    }
                                })
                            } else {
                                Swal.fire({
                                    position: 'top',
                                    type: 'error',
                                    title: 'Conta inexistente!',
                                    html: 'Não encontramos nenhuma conta cadastrada com o email <strong class="text-danger"> ' + res.email + '</strong>.',
                                    showConfirmButton: true,
                                    showCancelButton: true,
                                    reverseButtons: true,
                                    showCloseButton: true,
                                    confirmButtonText: '<i class="fas fa-redo fa-fw"></i> Tentar de novo ',
                                    cancelButtonText: '<i class="fas fa-times fa-fw"></i> Cancelar ',
                                }).then((result) => {
                                    if (result.value) {
                                        recuperar_senha();
                                    } else {

                                    }
                                })

                            }
                        },
                        error: function () {
                            alert('Erro na função');

                        }
                    });

                });

            } else {

            }
        })
    }


    <?php if ($this->session->flashdata('erro') != null) { ?>
    Swal.fire({
        position: 'top',
        type: 'error',
        // timer: 5000,
        title: 'Erro!',
        html: '<?= $this->session->flashdata('erro') ?>',
        showConfirmButton: false,
        showCancelButton: true,
        showCloseButton: true,
        reverseButtons: true,
        confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
        cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
    }).then((result) => {
        if (result.value) {
            recuperar_senha();
        } else {

        }
    });
    <?php } ?>

    <?php if ($this->session->flashdata('sucesso') != null) { ?>
    Swal.fire({
        position: 'top',
        type: 'success',
        title: 'Feito!',
        // timer: 5000,
        html: '<?= $this->session->flashdata('sucesso') ?>',
        showConfirmButton: true,
        showCancelButton: false,
        showCloseButton: true,
        confirmButtonText: '<i class="fa fa-check fa-fw"></i> OK ',
        cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
        reverseButtons: true,
    }).then((result) => {
        if (result.value) {

        } else {

        }
    });
    <?php } ?>

</script>
</body>
</html>