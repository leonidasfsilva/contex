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
            <form class="form-horizontal floating-labels" id="formLogin" method="post" action="<?= site_url('redefinirsenha/alterarsenha') ?>" autocomplete="off">
                <div class="preloader-login">
                    <div class="cssload-speeding-wheel"></div>
                </div>
                <div class="before-loading">
                    <div class="form-group p-b-0 m-t-0">
                        <h4 class="font-bold">Olá, <?= $nome ?>!</h4>
                        <p class="font-bold">Cadastre uma nova senha para sua conta:</p>
                    </div>
                    <div class="form-group p-b-0 m-t-0">
                        <?php echo validation_errors(); ?>
                    </div>
                    <div class="form-group ">
                        <input type="hidden" id="token" name="token" value="<?= $token ?>"/>
                        <input type="hidden" id="id" name="id" value="<?= $id ?>"/>
                        <input type="password" class="form-control" id="novaSenha" name="novaSenha" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="novasenha">Nova senha</label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="confirmarSenha" name="confirmarSenha" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="repitasenha">Confirme nova senha</label>
                    </div>
                    <div class="form-group text-center m-t-40 p-t-20">
                        <div class="row">
                            <div class="col-xs-6">
                                <button id="cancelToken" class="btn btn-default btn-block waves-effect waves-light" type="button"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                            </div>
                            <div class="col-xs-6">
                                <button class="btn btn-login btn-block waves-effect waves-light" type="submit"><i class="fa fa-refresh fa-fw"></i> Alterar Senha</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<div id="form_notice" class="hidden">
    <form id="form_invalidar_token" method="post" action="<?= site_url('redefinirsenha/invalidatoken') ?>">
        <input type="hidden" id="id_token" name="id" value="<?= $id ?>"/>
    </form>
</div>
<!-- End loading site level scripts -->


<script type="text/javascript">

    $('#formLogin').validate({
        rules: {
            novaSenha: {
                required: true,
                minlength: 6,
            },
            confirmarSenha: {
                required: "#novaSenha",
                equalTo: "#novaSenha"
            },
        },
        messages: {
            novaSenha: {
                required: 'Digite sua nova senha',
                minlength: 'A nova senha deve conter no mínimo 6 caracteres',
            },
            confirmarSenha: {
                required: 'Repita sua nova senha',
                equalTo: 'As senhas não correspondem',
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

    $('#formLogin').submit(function (event) {
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