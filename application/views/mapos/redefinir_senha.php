<?php $this->load->view('includes/css'); ?>

<?php $this->load->view('includes/js'); ?>

<body class="focused-form" style="background-color: #37474f">


<div class="container" id="login-form">
    <a href="javascript:" class="login-logo">
        <img alt="Contex - Sistema de Gestão" id="logo" src="<?php echo base_url() ?>assets/img/contex_brand.png">
    </a>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <?php echo validation_errors(); ?>
            <div class="panel panel-default">
                <div class="panel-heading"><h2>Olá, <?= $nome ?>!</h2></div>
                <div class="panel-body">
                    <form class="form-horizontal" id="formLogin" method="post" action="<?= site_url('redefinirsenha/alterarsenha') ?>">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <p class="">Cadastre uma nova senha para sua conta:</p>
                                <div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-lock fa-fw"></i>
									</span>
                                    <input type="hidden" id="token" name="token" value="<?= $token ?>"/>
                                    <input type="hidden" id="id" name="id" value="<?= $id ?>"/>
                                    <input type="password" class="form-control" placeholder="Nova senha" name="novasenha" required autofocus/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-lock fa-fw"></i>
									</span>
                                    <input type="password" class="form-control" placeholder="Confirme nova senha" name="repitasenha" required/>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <div class="clearfix">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <a href="javascript:" id="cancelToken" class="btn btn-default btn-block">
                                            <i class="fa fa-times fa-fw"></i> Cancelar
                                        </a>
                                    </div>
                                    <div class="col-xs-6">
                                        <button id="btn-mudar-senha" class="btn btn-success btn-block">
                                            <i class="fa fa-refresh fa-fw"></i> Mudar Senha
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div id="progress-acessar" class="progress progress-lg progress-striped active hidden">
                <div class="progress-bar progress-bar-primary" role="progressbar" style="width: 0%">
                    <span class="sr-only">Carregando...</span>
                </div>
            </div>

        </div>
    </div>
</div>
<div id="form_notice" class="hidden">
    <form id="form_invalidar_token" method="post" action="<?= site_url('redefinirsenha/invalidatoken') ?>">
        <input type="hidden" id="id" name="id" value="<?= $id ?>"/>
    </form>
</div>
<!-- End loading site level scripts -->


<script type="text/javascript">

    $('#btn_teste').click(function () {
        Swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            position: 'top',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
            }
        });
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

    $('#cancelToken').click(function () {
        var form = $('#form_invalidar_token');
        form.submit();
    });

    <?php if ($this->session->flashdata('erro') != null) { ?>
    Swal.fire({
        position: 'top',
        type: 'error',
        timer: 5000,
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
            esqueci_senha();
        } else {

        }
    });
    <?php } ?>

    <?php if ($this->session->flashdata('sucesso') != null) { ?>
    PNotify.success({
        title: 'Feito!',
        text: '<?= $this->session->flashdata('sucesso') ?>',
        styling: 'bootstrap3',
        icon: 'fa fa-check-circle fa-lg fa-fw',
        delay: 3000,
        addClass: 'pnotify-center',
        hide: true,
        stack: {
            'dir1': 'down',
            'firstpos1': 25
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

    function esqueci_senha() {
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
                                        window.location.replace('<?= site_url() ?>' + 'redefinirsenha/verificacao?token=' + res.token + '&id=' + res.id);
                                        //window.location.replace('<?//= site_url() ?>//');
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
                                    confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
                                    cancelButtonText: '<i class="fa fa-times fa-fw"></i> Cancelar ',
                                }).then((result) => {
                                    if (result.value) {
                                        esqueci_senha();
                                    } else {

                                    }
                                })
                            }
                        },
                        error: function () {
                            alert('Erro na função!');

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