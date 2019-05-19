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
    <script src="<?php echo base_url(); ?>assets/js/agile-custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-waves.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-style.switcher.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.js"></script>
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
        <div class="text-center m-t-20">
            <img class="contex-logo" src="<?php echo base_url() ?>assets/img/contex_logo.png" alt="Home"/>
            <br/>
            <img class="contex-words" src="<?php echo base_url() ?>assets/img/contex_words.png" alt="Home"/>
        </div>
        <div class="white-box box-login">
            <form class="form-horizontal floating-labels" id="formLogin" method="post" action="<?php echo base_url() ?>mxcode/verificarLogin">
                <div class="preloader-login">
                    <div class="cssload-speeding-wheel"></div>
                </div>
                <div class="before-loading">
                    <div class="form-group">
                        <p class="font-bold">Efetue seu login</p>
                    </div>
                    <div class="form-group m-t-40 p-t-40 p-b-20">
                        <input type="text" class="form-control" id="email" name="email" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="email">E-mail</label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="senha" name="senha" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="email">Senha</label>
                    </div>
                    <div class="form-group">
                        <a href="javascript:" onclick="recuperar_senha()" class="text-primary pull-right">Esqueceu sua senha?</a>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-login btn-block waves-effect waves-light" type="submit">Acessar <i class="fa fa-sign-in fa-fw"></i></button>
                            </div>
                        </div>
                    </div>
                    <!--                <div class="row">-->
                    <!--                    <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">-->
                    <!--                        <div class="social">-->
                    <!--                            <a href="javascript:" class="btn btn-facebook" data-toggle="tooltip" title="Login com sua conta do Facebook"> <i aria-hidden="true" class="fa fa-facebook"></i> </a>-->
                    <!--                            <a href="javascript:" class="btn btn-googleplus" data-toggle="tooltip" title="Login com sua conta do Google"> <i aria-hidden="true" class="fa fa-google"></i> </a>-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Não tem uma conta? <a href="javascript:" id="registro" class="text-primary"><b>Registre-se</b></a>.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


<!--<div class="container" id="login-form">-->
<!--    <a href="--><?php //echo base_url() ?><!--" class="login-logo"><img id="logo" src="--><?php //echo base_url() ?><!--assets/img/contex_brand.png"></a>-->
<!--    <div class="row">-->
<!--        <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">-->
<!--            <div class="panel panel-default">-->
<!--                <div class="panel-heading"><h2>Efetue seu login</h2></div>-->
<!--                <div class="panel-body">-->
<!--                    <form class="form-horizontal" id="formLogin" method="post" action="--><?php //echo base_url() ?><!--mxcode/verificarLogin">-->
<!--                        <div class="form-group">-->
<!--                            <div class="col-xs-12">-->
<!--                                <div class="input-group">-->
<!--									<span class="input-group-addon">-->
<!--										<i class="fa fa-user fa-fw"></i>-->
<!--									</span>-->
<!--                                    <input type="email" class="form-control" placeholder="E-mail" name="email" required/>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!---->
<!--                        <div class="form-group">-->
<!--                            <div class="col-lg-12">-->
<!--                                <div class="input-group">-->
<!--									<span class="input-group-addon">-->
<!--										<i class="fa fa-lock fa-fw"></i>-->
<!--									</span>-->
<!--                                    <input type="password" class="form-control" placeholder="Senha" name="senha" id="senha" required/>-->
<!--                                    <span id="eye_span" class="input-group-addon" style="cursor: pointer" title="Pré-visualizar senha">-->
<!--                                        <i style="cursor:pointer;" id="eye" class="fa fa-eye-slash fa-fw"></i>-->
<!--                                    </span>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!---->
<!--                        <div class="form-group">-->
<!--                            <div class="col-xs-12">-->
<!--                                <a href="javascript:" onclick="recuperar_senha()" id="reset-password" class="pull-left">Esqueci minha senha</a>-->
<!--                                <div class="checkbox-inline icheck pull-right pt0">-->
<!--                                    <label for="">-->
<!--                                        <input type="checkbox">Manter conectado</input>-->
<!--                                    </label>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="panel-footer">-->
<!--                            <div class="clearfix">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-xs-6">-->
<!--                                        <a href="#" id="registro" class="btn btn-default btn-block">Registrar-se <i class="fa fa-user-plus fa-fw"></i></a>-->
<!--                                    </div>-->
<!--                                    <div class="col-xs-6">-->
<!--                                        <button id="btn-acessar" class="btn btn-success btn-block">Acessar <i class="fa fa-sign-in fa-fw"></i></button>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </form>-->
<!---->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div id="progress-acessar" class="progress progress-lg progress-striped active hidden">-->
<!--                <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0%">-->
<!--                    <span class="sr-only">45% Complete</span>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--<div id="form_notice" class="hidden">-->
<!--    <form id="form_recuperar_senha" method="post" autocomplete="on">-->
<!--        <div>-->
<!--            <h4>Esqueceu sua senha?</h4>-->
<!--            <p>Informe seu e-mail de cadastro e lhe enviaremos instruções para alterar sua senha:</p>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <div class="input-group"><span class="input-group-addon"> <i class="fa fa-user fa-fw"></i> </span>-->
<!--                <input type="email" class="form-control" placeholder="E-mail" name="email_usuario" id="email_usuario" required>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="mt-3 ">-->
<!--            <div class="row">-->
<!--                <div class="col-lg-6">-->
<!--                    <button class="btn btn-default btn-block" type="button" id="cancel"><i class="fa fa-times fa-fw"></i> Cancelar</button>-->
<!--                </div>-->
<!--                <div class="col-lg-6">-->
<!--                    <button class="btn btn-primary btn-block" type="submit" id="submit_form"><i class="fa fa-send fa-fw"></i> Enviar</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </form>-->
<!--</div>-->


<script type="text/javascript">

    $('#eye_span').click(function (e) {
        e.preventDefault();
        if ($('#senha').attr('type') == 'password') {
            $('#senha').attr('type', 'text');
            $('#eye').attr('class', 'fa fa-eye fa-fw');
        } else {
            $('#senha').attr('type', 'password');
            $('#eye').attr('class', 'fa fa-eye-slash fa-fw');
        }
    });

    $('#eye_span').mouseout(function (e) {
        $('#senha').attr('type', 'password');
        $('#eye').attr('class', 'fa fa-eye-slash fa-fw');

    });

    $('#formLogin').submit(function (event) {
        var form = this;
        $('#btn-acessar').addClass('disabled');
        $('#btn-acessar').html('Acessando... <i class="fa fa-spinner fa-pulse fa-fw"></i>');
        $('#progress-acessar').removeClass('hidden');
        $(".progress-bar").animate({
            width: "100%"
        }, 1000);
        $(".before-loading").fadeOut();
        $(".preloader-login").fadeIn();
        event.preventDefault();

        setTimeout(function () {
            form.submit();
        }, 1000);
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

    $('#registro').click(function () {

        Swal.fire({
            position: 'top',
            type: 'info',
            // timer: 5000,
            title: 'Indisponível',
            html: 'O registro de usuários encontra-se indisponível no momento. Contate o administrador do sistema para obter uma conta.',
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true,
            reverseButtons: true,
            confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
            cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
        });
    });

    function recuperar_senha() {
        Swal.fire({
            position: 'top',
            title: 'Esqueceu sua senha?',
            html: '<div>Informe seu e-mail de cadastro e lhe enviaremos instruções para alterar sua senha:</div>',
            input: 'email',
            inputPlaceholder: 'Digite seu e-mail',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-send fa-fw"></i> Enviar ',
            cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
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
                        url: "<?= site_url('redefinirsenha/gerartoken'); ?>",
                        data: {
                            'email': result.value
                        }, // <--- THIS IS THE CHANGE
                        dataType: 'json',
                        cache: false,
                        success: function (res) {

                            // var res = jQuery.parseJSON(resposta);

                            if (res.validacao == true) {
                                Swal.fire({
                                    position: 'top',
                                    type: 'success',
                                    title: 'Email enviado!',
                                    showConfirmButton: true,
                                    showCancelButton: false,
                                    showCloseButton: true,
                                    confirmButtonText: '<i class="fa fa-check fa-fw"></i> OK ',
                                    cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
                                    reverseButtons: true,
                                    html:
                                        '<p class="">Enviamos um e-mail para <strong class="text-success">' +
                                        res.email +
                                        '</strong>, ' +
                                        'verifique sua caixa de entrada ou pasta de <i>spam</i> e siga as instruções de recuperação.</p>',
                                }).then((result) => {
                                    if (result.value) {
                                        //window.location.replace('<?//= site_url() ?>//' + 'redefinirsenha/verificacao?token=' + res.token + '&id=' + res.id);
                                    } else {

                                    }
                                })
                            }
                            if (res.validacao == false) {
                                Swal.fire({
                                    position: 'top',
                                    type: 'error',
                                    title: 'Conta inexistente!',
                                    html: 'Não encontramos nenhuma conta cadastrada com o email <strong class="text-danger"> ' + res.email + '</strong>.',
                                    showConfirmButton: true,
                                    showCancelButton: true,
                                    reverseButtons: true,
                                    showCloseButton: true,
                                    confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
                                    cancelButtonText: '<i class="fa fa-times fa-fw"></i> Cancelar ',
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
</script>

</body>
</html>