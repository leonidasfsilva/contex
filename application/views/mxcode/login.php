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

    <link href="<?php echo base_url(); ?>assets/css/bootstrap-agile.css?v=<?= versionApp() ?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/agile-style.css?v=<?= versionApp() ?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/custom.css?v=<?= versionApp() ?>" type="text/css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,600,700' rel='stylesheet' type='text/css'>
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome5/css/fontawesome5.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.css" type="text/css" rel="stylesheet">


    <script src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.min.js"></script> <!-- Load jQuery -->
    <script src="<?php echo base_url(); ?>assets/js/jqueryui-1.9.2.min.js"></script> <!-- Load jQueryUI -->
    <script src="<?php echo base_url(); ?>assets/js/bootstrap3.3.7.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-waves.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/agile-style.switcher.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
</head>
<style>
</style>

<body class="focused-form" style="background-color: #37474f">

<section id="wrapper" class="login-register">
    <div class="login-box login-sidebar">
        <div class="text-center m-t-40">
            <img class="contex-words" src="<?php echo base_url() ?>assets/img/contex_brand.png" alt="CONTEX - Sistema de Gestão"/>
        </div>
        <div class="white-box box-login">
            <form class="form-horizontal floating-labels" id="formLogin" method="post" action="<?php echo base_url() ?>mxcode/verificarLogin">
                <div class="preloader-login">
                    <div class="cssload-speeding-wheel"></div>
                    <h4 class="preloader-text font-weight-bold text-gray">
                        Acessando...
                    </h4>
                </div>
                <div class="before-loading">
					<?php if ($this->session->flashdata('error') != null) { ?>
                        <div class="row">
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
								<?php echo $this->session->flashdata('error'); ?>
                            </div>
                        </div>
					<?php } ?>

                    <div class="form-group">
                        <h3 class="font-bold m-b-40">Acesse sua conta</h3>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="email" name="email" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" id="senha" name="senha" required/>
                        <span class="highlight"></span> <span class="bar"></span>
                        <label for="email">Senha</label>
                    </div>
                    <div class="form-group">
                        <a href="javascript:" onclick="recuperar_senha()" class="text-primary pull-right">Esqueci minha senha</a>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-login btn-block waves-effect waves-light" type="submit"><i class="fas fa-user-lock fa-fw"></i> Acessar</button>
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
                            <p>Não tem uma conta? <a href="<?php echo base_url() ?>cadastro" class="text-primary"><b>Crie sua conta</b></a>.</p>
                            <!--                            <p><a href="--><?php //echo base_url()
							?>
                            <!--conecte" class="text-primary"><b>Acesso para clientes</b></a>.</p>-->
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <footer role="contentinfo" class="footer">
            <div class="clearfix text-center">
                <ul class="list-unstyled list-inline pl-sm">
                    <li>
                        <h6 style="margin: 0; text-transform: none"><?= sprintf('&copy; 2019 - %s %s', date('Y'), '• Powered by <a href="https://mxcode.net" >MX CODE SISTEMAS</a>') ?></h6>
                    </li>
                </ul>
            </div>
        </footer>
    </div>
</section>

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

    $('#formLogin').validate({
        rules: {
            email: {
                required: true
            },
            senha: {
                required: true
            },
        },
        messages: {
            email: {
                required: 'Digite seu email'
            },
            senha: {
                required: 'Digite sua senha'
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
        confirmButtonText: '<i class="fas fa-redo fa-fw"></i> Tentar de novo ',
        cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
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
        confirmButtonText: '<i class="fas fa-check fa-fw"></i> OK ',
        cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
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
            showConfirmButton: true,
            showCancelButton: false,
            showCloseButton: true,
            reverseButtons: true,
            confirmButtonText: '<i class="fas fa-check fa-fw"></i> Entendi ',
            cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
        });
    });

    function recuperar_senha() {
        Swal.fire({
            position: 'top',
            title: 'Esqueceu sua senha?',
            html: '<div>Informe seu email de cadastro e lhe enviaremos instruções para alterar sua senha:</div>',
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
                                    confirmButtonText: '<i class="fas fa-check fa-fw"></i> OK ',
                                    cancelButtonText: '<i class="fas fa-times fa-fw"></i> Fechar ',
                                    reverseButtons: true,
                                    html: '<p class="">Enviamos um e-mail para <strong class="text-success">' +
                                        res.email +
                                        '</strong>, ' +
                                        'verifique sua caixa de entrada ou pasta de <i>spam</i> e siga as instruções de recuperação.</p>',
                                }).then((result) => {

                                })
                            }
                            if (res.validacao == false) {
                                Swal.fire({
                                    position: 'top',
                                    type: 'error',
                                    title: 'Conta inexistente!',
                                    html: 'Não encontramos nenhuma conta cadastrada com o email <strong class="text-danger"> ' + res.email + '</strong>. Caso queira criar uma conta com este ' +
                                        'email,<br> <a href="<?php echo base_url() ?>cadastro">clique aqui para se cadastrar</a>.',
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
</script>
</body>

</html>