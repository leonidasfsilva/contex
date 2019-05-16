<script>
    <?php
    $url = current_url();
    $segments = explode("/", $url);
    $bloqueados = array(
        'login',
        'redefinirsenha',
    );

    if (!count(array_intersect($segments, $bloqueados)) > 0) { ?>
    window.onload = function () {
        var wrapper = document.body;
        wrapper.className += " page-loading";
        setTimeout(function () {
            wrapper.classList.remove('page-loading');
        }, 2000);
    };
    <?php
    } ?>

    $(document).ready(function () {

        function limpa_formulário_cep() {
            // Limpa valores do formulário de cep.
            $("#logradouro").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#uf").val("");
            $("#ibge").val("");
        }

        //Quando o campo cep perde o foco.
        $("#cep").blur(function() {

            //Nova variável "cep" somente com dígitos.
            var cep = $(this).val().replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if(validacep.test(cep)) {

                    //Preenche os campos com "..." enquanto consulta webservice.
                    $("#logradouro").val("aguarde...");
                    $("#bairro").val("aguarde...");
                    $("#cidade").val("aguarde...");
                    $("#uf").val("aguarde...");
                    $("#ibge").val("aguarde...");

                    //Consulta o webservice viacep.com.br/
                    $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                        if (!("erro" in dados)) {
                            //Atualiza os campos com os valores da consulta.
                            $("#logradouro").val(dados.logradouro);
                            $("#bairro").val(dados.bairro);
                            $("#cidade").val(dados.localidade);
                            $("#uf").val(dados.uf);
                            $("#ibge").val(dados.ibge);
                        } //end if.
                        else {
                            //CEP pesquisado não foi encontrado.
                            limpa_formulário_cep();
                            // alert("CEP não encontrado.");
                            Swal.fire({
                                position: 'top',
                                type: 'error',
                                timer: 3000,
                                title: 'Erro!',
                                html: 'CEP não encontrado!',
                                showConfirmButton: false,
                                showCancelButton: false,
                                showCloseButton: true,
                                reverseButtons: true,
                                confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
                                cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
                            })
                        }
                    });
                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulário_cep();
                    // alert("Formato de CEP inválido.");
                    Swal.fire({
                        position: 'top',
                        type: 'error',
                        timer: 3000,
                        title: 'Erro!',
                        html: 'Formato de CEP inválido!',
                        showConfirmButton: false,
                        showCancelButton: false,
                        showCloseButton: true,
                        reverseButtons: true,
                        confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
                        cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
                    })
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
        });

        $('.datepicker').datepicker({
            language: 'pt-BR',
            autoclose: true,
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            todayBtn: true
        });

        $('.tooltips').tooltip();

        $('.poupanca').click(function () {
            Swal.fire({
                position: 'top',
                type: 'info',
                // timer: 5000,
                title: 'Em breve',
                html: 'O módulo de Poupança encontra-se em desenvolvimento.',
                showConfirmButton: false,
                showCancelButton: false,
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

            // PNotify.info({
            //     title: 'Em breve',
            //     text: 'O módulo de Poupança encontra-se em desenvolvimento.',
            //     styling: 'bootstrap3',
            //     icon: 'fa fa-warning fa-lg fa-fw',
            //     delay: 5000,
            //     addClass: 'pnotify-shadow',
            //     hide: true,
            //     stack: {
            //         'dir1': 'down',
            //         'firstpos1': 25
            //     },
            //     modules: {
            //         Animate: {
            //             animate: true,
            //             inClass: 'slideInDown',
            //             outClass: 'slideOutUp'
            //         },
            //         Buttons: {
            //             sticker: false,
            //             closerHover: false,
            //         },
            //         Mobile: {
            //             styling: true
            //         }
            //     }
            // });
        });

        <?php if ($this->session->flashdata('erro') != null) { ?>
        Swal.fire({
            position: 'top',
            type: 'error',
            timer: 3000,
            title: 'Erro!',
            html: '<?= $this->session->flashdata('erro') ?>',
            showConfirmButton: false,
            showCancelButton: false,
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

        //PNotify.error({
        //    title: 'Erro!',
        //    text: '<?//= $this->session->flashdata('erro') ?>//',
        //    styling: 'bootstrap3',
        //    icon: 'fa fa-times-circle fa-lg fa-fw',
        //    delay: 3000,
        //    addClass: 'pnotify-center',
        //    hide: true,
        //    stack: {
        //        'dir1': 'down',
        //        'firstpos1': 25
        //    },
        //    modules: {
        //        Animate: {
        //            animate: true,
        //            inClass: 'slideInDown',
        //            outClass: 'slideOutUp'
        //        },
        //        Buttons: {
        //            sticker: false,
        //            closerHover: false,
        //        },
        //        Mobile: {
        //            styling: true
        //        }
        //    }
        //});
        <?php } ?>

        <?php if ($this->session->flashdata('sucesso') != null) { ?>
        Swal.fire({
            position: 'top',
            type: 'success',
            title: 'Feito!',
            timer: 3000,
            html: '<?= $this->session->flashdata('sucesso') ?>',
            showConfirmButton: false,
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

        //PNotify.success({
        //    title: 'Feito!',
        //    text: '<?//= $this->session->flashdata('sucesso') ?>//',
        //    styling: 'bootstrap3',
        //    icon: 'fa fa-check-circle fa-lg fa-fw',
        //    delay: 3000,
        //    addClass: 'pnotify-center',
        //    hide: true,
        //    stack: {
        //        'dir1': 'down',
        //        'firstpos1': 25
        //    },
        //    modules: {
        //        Animate: {
        //            animate: true,
        //            inClass: 'slideInDown',
        //            outClass: 'slideOutUp'
        //        },
        //        Buttons: {
        //            sticker: false,
        //            closerHover: false,
        //        },
        //        Mobile: {
        //            styling: true
        //        }
        //    }
        //});
        <?php } ?>
    });
</script>
