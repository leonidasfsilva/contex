<?php $this->load->view('includes/css'); ?>

<?php $this->load->view('includes/js'); ?>

<body class="focused-form" style="background-color: #37474f">

<div class="container" id="login-form">
    <a href="<?php echo base_url() ?>" class="login-logo"><img id="logo" src="<?php echo base_url() ?>assets/img/logo_contex.png"></a>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h2>Efetue seu login</h2></div>
                <div class="panel-body">
                    <form class="form-horizontal" id="formLogin" method="post" action="<?php echo base_url() ?>mxcode/verificarLogin">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-user fa-fw"></i>
									</span>
                                    <input type="email" class="form-control" placeholder="E-mail" name="email" required/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-12">
                                <div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-lock fa-fw"></i>
									</span>
                                    <input type="password" class="form-control" placeholder="Senha" name="senha" id="senha" required/>
                                    <span id="eye_span" class="input-group-addon" style="cursor: pointer" title="Pré-visualizar senha">
                                        <i style="cursor:pointer;" id="eye" class="fa fa-eye-slash fa-fw"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <a href="javascript:" onclick="recuperar_senha()" id="reset-password" class="pull-left">Esqueci minha senha</a>
                                <div class="checkbox-inline icheck pull-right pt0">
                                    <label for="">
                                        <input type="checkbox">Manter conectado</input>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="clearfix">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <a href="#" id="registro" class="btn btn-default btn-block">Registrar-se <i class="fa fa-user-plus fa-fw"></i></a>
                                    </div>
                                    <div class="col-xs-6">
                                        <button id="btn-acessar" class="btn btn-success btn-block">Acessar <i class="fa fa-sign-in fa-fw"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div id="progress-acessar" class="progress progress-lg progress-striped active hidden">
                <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0%">
                    <span class="sr-only">45% Complete</span>
                </div>
            </div>

        </div>
    </div>
</div>
<div id="form_notice" class="hidden">
    <form id="form_recuperar_senha" method="post" autocomplete="on">
        <div>
            <h4>Esqueceu sua senha?</h4>
            <p>Informe seu e-mail de cadastro e lhe enviaremos instruções para alterar sua senha:</p>
        </div>
        <div class="form-group">
            <div class="input-group"><span class="input-group-addon"> <i class="fa fa-user fa-fw"></i> </span>
                <input type="email" class="form-control" placeholder="E-mail" name="email_usuario" id="email_usuario" required>
            </div>
        </div>
        <div class="mt-3 ">
            <div class="row">
                <div class="col-lg-6">
                    <button class="btn btn-default btn-block" type="button" id="cancel"><i class="fa fa-times fa-fw"></i> Cancelar</button>
                </div>
                <div class="col-lg-6">
                    <button class="btn btn-primary btn-block" type="submit" id="submit_form"><i class="fa fa-send fa-fw"></i> Enviar</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- End loading site level scripts -->


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

        PNotify.info({
            title: 'Indisponível',
            text: 'Lamentamos o inconveniente, mas o registro de usuários está temporariamente indisponível.',
            styling: 'bootstrap3',
            icon: 'fa fa-times-circle fa-lg fa-fw',
            delay: 5000,
            addClass: 'pnotify-center',
            hide: true,
            stack: {
                'dir1': 'down',
                'firstpos1': 25,
            },
            modules: {
                Animate: {
                    animate: true,
                    inClass: 'slideInDown',
                    outClass: 'slideOutUp'
                },
                Buttons: {
                    sticker: false,
                    closerHover: false,
                },
                Mobile: {
                    styling: true
                }
            }
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
                                    title: 'Link enviado!',
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