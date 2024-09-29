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

    <link href="<?php echo base_url(); ?>assets/css/bootstrap-agile.css?v=<?= getAppVersion() ?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/agile-style.css?v=<?= getAppVersion() ?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/custom.css?v=<?= getAppVersion() ?>" type="text/css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.css" type="text/css" rel="stylesheet">

    <!--    Font Awesome 6 -->
    <link href="<?php echo base_url(); ?>assets/font-awesome-6/css/all.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome-6/css/sharp-duotone-solid.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome-6/css/sharp-light.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome-6/css/sharp-regular.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome-6/css/sharp-solid.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome-6/css/sharp-thin.css" rel="stylesheet">

    <script src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.min.js"></script>                            <!-- Load jQuery -->
    <script src="<?php echo base_url(); ?>assets/js/jqueryui-1.9.2.min.js"></script>                            <!-- Load jQueryUI -->
    <script src="<?php echo base_url(); ?>assets/js/bootstrap3.3.7.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-waves.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-style.switcher.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
</head>
<body class="focused-form" style="background-color: #37474f">

<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="login-register">
    <div class="login-box login-sidebar">
        <div class="text-center m-t-40">
            <img class="contex-words" src="<?php echo base_url('assets/img/contex_brand.png') ?>" alt="CONTEX - Sistema de Gestão"/>
        </div>
        <div class="white-box box-login">
            <!--SPINNER LOADER-->
            <div class="preloader-login" style="display: none">
                <i class="fas fa-duotone fa-spinner-third fa-spin cssload-speeding-wheel"></i>
                <!--<i class="fas fa-spinner fa-spin-pulse fa-2x cssload-speeding-wheel"></i>-->
                <p class="preloader-text font-weight-bold text-gray">
                    Aguarde...
                </p>
            </div>
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
                        <input class="form-control" type="text" id="email2" name="email2" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="email">Confirme seu email</label>
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
                        <button class="btn btn-login btn-block waves-effect waves-light" type="submit">Cadastrar <i class="fas fa-user-plus fa-fw"></i></button>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Já possui uma conta? <a href="<?php echo base_url('mxcode/login') ?>" id="login" class="text-primary"><b>Acesse sua conta</b></a>.</p>
                        </div>
                    </div>
                </div>
            </form>
            <div class="footer-login">
                <div class="container-fluid pb0">
                    <span class="font-12"><?= sprintf('&copy; 2019 - %s %s', date('Y'), 'CONTEX • Sistema de Gestão', '• Powered by <a href="https://mxcode.net" >MX CODE SISTEMAS</a>') ?></span>
                    <p class="font-12"><?= sprintf('%s', 'Powered by <a href="https://mxcode.net" >MX CODE SISTEMAS</a>') ?></p>

                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $('#formCadastro').validate({
        rules: {
            nome: {required: true},
            sobrenome: {required: true},
            email: {required: true},
            email2: {
                required: true,
                equalTo: '#email'
            },
            novaSenha: {
                required: true,
                minlength: 6
            },
            confirmarSenha: {
                required: true,
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
            email2: {
                required: 'Confirme seu email',
                email: 'Por favor, informe um email válido',
                equalTo: 'Os emails não correspondem'
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
            html: '<div>Informe o email utilizado em seu cadastro e lhe enviaremos um novo email para confirmar sua conta:</div>',
            input: 'email',
            inputPlaceholder: 'Digite seu email',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-send fa-fw"></i> Enviar ',
            cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
            customClass: {
                cancelButton: 'col-lg-5',
                confirmButton: 'col-lg-5'
            },
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
                        url: "<?= site_url('cadastro/reenviarVerificacao'); ?>",
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
                            } else if (res.validacao == 'ja_validado') {
                                Swal.fire({
                                    position: 'top',
                                    type: 'success',
                                    title: 'Conta já verificada!',
                                    html: 'A conta registrada com o email <strong class="text-success"> ' + res.email +
                                        '</strong> já foi verificada, acesse sua conta utilizando seu email e senha cadastrados.',
                                    showConfirmButton: true,
                                    showCancelButton: true,
                                    reverseButtons: true,
                                    showCloseButton: true,
                                    confirmButtonText: '<i class="fas fa-sign-in-alt fa-fw"></i> Acessar ',
                                    cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
                                }).then((result) => {
                                    if (result.value) {
                                        //$('body').load('<?//= site_url() ?>//' + 'mxcode/login', function () {
                                        //    recuperar_senha();
                                        //});
                                        window.location.replace('<?= site_url() ?>' + 'mxcode/login');
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
                                        verificar_conta();
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