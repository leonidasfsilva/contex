<script>
    $(document).ready(function() {
        // method to close and open one modal after another one
        $('.modal-copy').click(function(e) {
            var parentModal = null
            var parentElement = $(this)

            for (var i = 0; i < 20; i++) {
                if (parentModal != null && parentModal.attr('id') == "modalEditar") {
                    toggleModals(parentModal, parentElement, true)
                    return false;
                }

                if (parentModal != null) {
                    parentModal = parentModal.parent()
                } else {
                    parentModal = parentElement.parent()
                }
            }
        })
    })

    function toggleModals(discardModal, targetModal, trigger = false) {
        $(discardModal).modal('hide')
        $(discardModal).on('hidden.bs.modal', function() {
            // get the ID button clicked to invoke the target modal
            if (trigger) {
                $('#' + targetModal.attr('id')).modal('show')
                trigger = false
            }
        })
    }

    let conectado = false;

    function lerTodasNotificacoes() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('notificacoes/lerTodasNotificacoes'); ?>",
            data: {
                post: true
            },
            dataType: 'html',
            success: function() {
                atualizaNotificacoesUsuario()
            }
        });
    }

    function lerNotificacao(id) {
        $.ajax({
            type: "POST",
            url: "<?= base_url('notificacoes/lerNotificacao'); ?>",
            data: {
                id: id
            },
            dataType: 'html',
            success: function() {
                atualizaNotificacoesUsuario()
                window.location.replace('<?= base_url('notificacoes'); ?>')
            }
        });
    }

    $(document).ready(function() {
        setInterval(function() {
            if (conectado === true) {
                // atualizaNotificacoesUsuario();
            }
        }, 10000);
    });

    function atualizaNotificacoesUsuario() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('notificacoes/atualizaNotificacoesUsuario'); ?>",
            dataType: 'json',
            data: {
                request: true
            },
            success: function(data) {
                if (data.result === true) {
                    let link;
                    let icone;
                    let height = 0;
                    $('#notifications-panel').html('');
                    $('#qnt_notificacoes').html('');
                    if (data.qnt === 0) {
                        $('#qnt_notificacoes').html('');
                    } else {
                        $('#qnt_notificacoes').html(data.qnt);
                    }

                    if (data.logado == true) {
                        conectado = true;
                        // console.log('usuario logado: ' + data.logado)
                    } else {
                        conectado = false;
                        Swal.fire({
                            position: 'top',
                            icon: 'error',
                            // timer: 3000,
                            title: 'Sessão Expirada',
                            html: 'Por favor, efetue seu login novamente para continuar.',
                            showConfirmButton: true,
                            showCancelButton: false,
                            showCloseButton: false,
                            reverseButtons: true,
                            allowOutsideClick: false,
                            confirmButtonText: '<i class="fas fa-user-lock"></i> Efetuar Login ',
                            cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
                        }).then((result) => {
                            if (result.value) {
                                window.location.reload()
                            } else {

                            }
                        });
                    }

                    if (data.retorno !== null) {
                        // console.log('atualizaNotificacoesUsuario(): usuario possui novas notificacoes')
                        $('#notifications-panel-footer').removeClass('hidden')
                        $(data.retorno).each(function(index, item) {
                            if (height < 210) {
                                height = height + 72;
                            }

                            if (item.link) {
                                base_url = '<?= base_url() ?>'
                                link = base_url + item.link
                                if (item.link.includes('http')) {
                                    link = item.link
                                }
                            } else {
                                link = 'notificacoes'
                            }
                            if (item.icone) {
                                icone = item.icone
                            } else {
                                icone = 'fas fa-bell fa-fw'
                            }
                            $('#notifications-panel').append(
                                '<li>' +
                                '<a href="' + link + '" class="notification-info">' +
                                '<div class="notification-icon"><i class="' + icone + '"></i></div>' +
                                '<div class="notification-content ajax-notification ">' + (item.descricao) + '</div>' +
                                '</a>' +
                                '</li>'
                            )
                        })
                    } else {
                        // console.log('atualizaNotificacoesUsuario(): usuario não possui novas notificacoes')
                        height = 73
                        $('#notifications-panel-footer').addClass('hidden')
                        $('#notifications-panel').append(
                            '<li class="text-center note note-info font-weight-bold" style="border: 0">' +
                            '    Sem novas notificações' +
                            '</li>'
                        )
                    }
                    $('#scroll-panel').css('height', height)
                } else {
                    // console.error('ERRO: atualizaNotificacoesUsuario()')
                }
            }
        });
    }

    $(document).on('ready', function(event) {
        atualizaNotificacoesUsuario();

        $("#urlAtual, .urlAtual").val($(location).attr('href'));

        $('.modal_anuncio').each(function(key, value) {
            $('.modal_anuncio').modal('show');
        });

        $('[disabled="disabled"]').click(function(e) {
            e.preventDefault();
        });

        $(".preloader").show();
        setTimeout(function() {
            hidePreLoader();
        }, 500);
    });

    function mountCard(form, container, timeout) {
        let card = new Card({
            // a selector or DOM element for the form where users will
            // be entering their information
            form: form, // *required*
            // a selector or DOM element for the container
            // where you want the card to appear
            container: container, // *required*
            formSelectors: {
                numberInput: 'input[name="number"]', // optional — default input[name="number"]
                expiryInput: 'input[name="expiry"]', // optional — default input[name="expiry"]
                cvcInput: 'input[name="cvc"]', // optional — default input[name="cvc"]
                nameInput: 'input[name="name"]' // optional - defaults input[name="name"]
            },
            width: 350, // optional — default 350px
            formatting: true, // optional - default true
            // Strings for translation - optional
            messages: {
                validDate: '', // optional - default 'valid\nthru'
                monthYear: 'validade', // optional - default 'month/year'
            },
            // Default placeholders for rendered fields - optional
            placeholders: {
                number: '•••• •••• •••• ••••',
                name: 'Nome e sobrenome',
                expiry: 'MM / AA',
                cvc: '•••'
            },
            masks: {
                cardNumber: '•' // optional - mask card number
            },
            // if true, will log helpful messages for setting up Card
            debug: true, // optional - default false
        });
        return card;
    }

    function hidePreLoader() {
        if ($(".preloader").fadeOut()) {}
    }

    $(function() {
        $('.datepicker').inputmask('date', {
            placeholder: '__/__/____'
        });
    });

    $(function() {
        $(".money").maskMoney({
            thousands: '.',
            decimal: ',',
            allowZero: true
        });
        $('.money').prop('type', 'tel');
    });

    $(function() {
        $('.popover-btn').popover()
    });

    $(document).on('click', 'a:not([href="javascript:"],' +
        '[target="_blank"], ' +
        '[disabled="disabled"], ' +
        '[class="js:"], ' +
        '[class="ui-corner-all"], ' +
        '[href="#"], ' +
        '[data-toggle="modal"], ' +
        '[data-toggle="collapse"], ' +
        '[data-toggle="tab"])',
        function() {
            $(".subconteudo-principal").hide();
            $(".preloader").show();
        });

    $(document).on('submit', 'form', function(event) {
        var form = this;
        event.preventDefault();
        // $('#btn-acessar').addClass('disabled');
        // $('#btn-acessar').html('Acessando... <i class="fa fa-spinner fa-pulse fa-fw"></i>');
        // $('#progress-acessar').removeClass('hidden');
        // $(".progress-bar").animate({
        //     width: "100%"
        // }, 1000);

        if ($(form).valid()) {
            // console.log('executou preloader');
            $(".subconteudo-principal").fadeOut();
            $(".preloader").fadeIn();

            setTimeout(function() {
                form.submit();
            }, 1000);
        }
    });

    $(document).on('click', '#teste-btn', function() {
        // console.log('teste ok!');
        $(".preloader").fadeIn();
        setTimeout(function() {
            $(".preloader").fadeOut();
        }, 1000);
    });

    $('#telefone').mask("(99) 9999-99990");
    $('#cep').mask("99999-999");
    $('#cpf').mask("999.999.999-99");
    $('#rg').mask("99.999.999-9");
    $('#expiry').mask("99 / 99");

    <?php
    $url = current_url();
    $segments = explode("/", $url);
    $bloqueados = array(
        'login',
        'redefinirsenha',
    );

    if (!count(array_intersect($segments, $bloqueados)) > 0) { ?>
        window.onload = function() {
            // var wrapper = document.body;
            // wrapper.className += " page-loading";
            // setTimeout(function () {
            //     wrapper.classList.remove('page-loading');
            // }, 2000);
        };
    <?php
    } ?>

    $(document).ready(function() {

        $('#body').each(function() {
            if ($(this).hasClass('sidebar-collapsed')) {
                // SIDEBAR OCULTO
                $('#menu-toggle-icon').addClass('fa-ellipsis-v')
                $('#menu-switcher').attr('title', 'Exibir menu')
            } else {
                // SIDEBAR EXPOSTO
                $('#menu-toggle-icon').addClass('fa-chevron-left')
                $('#menu-switcher').attr('title', 'Ocultar menu')
            }
        });

        $('#menu-switcher').click(function() {
            if ($('#body').hasClass('sidebar-collapsed')) {
                // SIDEBAR OCULTO
                $('#menu-toggle-icon').toggleClass('fa-chevron-left fa-ellipsis-v')
                $('#menu-switcher').attr('title', 'Exibir menu')
            } else {
                // SIDEBAR EXPOSTO
                $('#menu-toggle-icon').toggleClass('fa-chevron-left fa-ellipsis-v')
                $('#menu-switcher').attr('title', 'Ocultar menu')
            }
        });

        // API CEP DOS CORREIOS (viacep.com.br)
        function limpaFormCep() {
            // Limpa valores do formulário de cep.
            $("#logradouro").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#uf").val("");
            $("#ibge").val("");
        }

        //Quando o campo cep possui algum caracter digitado.
        $("#cep").keyup(function() {

            //Nova variável "cep" somente com dígitos.
            var cep = $(this).val().replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep.length > 7 && cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if (validacep.test(cep)) {


                    //Preenche os campos com "..." enquanto consulta webservice.
                    $("#logradouro").val("aguarde...");
                    $("#bairro").val("aguarde...");
                    $("#cidade").val("aguarde...");
                    $("#uf").val("aguarde...");
                    $("#ibge").val("aguarde...");

                    //Consulta o webservice viacep.com.br/
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/", function(dados) {

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
                            limpaFormCep();
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
                    limpaFormCep();
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
                limpaFormCep();
            }
        });
        // FIM API CEP CORREIOS

        $('.datepicker').datepicker({
            language: 'pt-BR',
            autoclose: true,
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            todayBtn: 'linked'
        });

        $('.tooltips').tooltip();

        $('.poupanca').click(function() {
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
        });

        <?php if ($this->session->flashdata('erro') != null) { ?>
            Swal.fire({
                position: 'top',
                icon: 'error',
                // timer: 3000,
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
        <?php } ?>

        <?php if ($this->session->flashdata('sucesso') != null) { ?>
            Swal.fire({
                position: 'top',
                icon: 'success',
                title: 'Feito!',
                timer: 1200,
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
        <?php } ?>

        $.fn.extend({
            toggleText: function(a, b) {
                return this.text(this.text() == b ? a : b);
            }
        });

        $.each($('.expand-icon'), function(key, value) {
            $(this).attr('class', 'fas fa-expand expand-icon');
            $(this).attr('title', 'Expandir');
        });

        $(".panel .expand").click(function() {
            var n = $(this).closest(".panel");
            var m = $(this).find(".expand-icon");
            n.toggleClass("widget-fullscreen");
            m.toggleClass("fa-expand fa-compress");
            $("body").toggleClass("fullscreen-widget-active")

            if ($('body').hasClass("fullscreen-widget-active")) {
                m.attr('title', 'Recolher')
            } else {
                m.attr('title', 'Expandir')
            }
        });

        $(".panel .close-panel").click(function() {
            $(this).closest(".panel").hide();
        });

        // -------------------------------
        // Panel Collapses
        // -------------------------------
        $('.panel-collapse').click(function() {
            if ($(this).children().hasClass('fa-chevron-up') || $(this).children().hasClass('fa-chevron-down')) {
                $(this).children().toggleClass("fa-chevron-up fa-chevron-down");
            } else {
                $(this).children().toggleClass("fa-minus fa-plus");
            }
            $(this).closest(".panel-heading").next().slideToggle({
                duration: 200
            });
            $(this).closest(".panel-heading").toggleClass('rounded-bottom');
            return false;
        });
    });

    // Function to make the dropdown shows on mouse hover - 27/11/2022
    $(function() {
        function is_touch_device() {
            return 'ontouchstart' in window // works on most browsers 
                ||
                navigator.maxTouchPoints; // works on IE10/11 and Surface
        };

        if (!is_touch_device() && $('.navbar-toggle:hidden')) {
            $('.dropdown-menu-hover', this).css('margin-top', 0);
            $('.dropdown-hover').hover(function() {
                $('.dropdown-toggle', this).trigger('click');
                // uncomment below to make the parent item clickable
                // $('.dropdown-toggle', this).toggleClass("disabled");
            });
        }
    })

    // JS to controller the select mounths modals - 25/06/2023
    $(document).ready(function() {
        $('.selectMonth').click(function() {
            var value = $(this).val()
            var target = $(':input.selectedMonth')
            var form = $(this).parent()

            target.val(value)
            form.submit()
        })
    })
</script>