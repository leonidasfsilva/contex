<script>
    $(document).on('ready', function (event) {
        $("#urlAtual").val($(location).attr('href'));

        $('.modal_anuncio').each(function (key, value) {
            $('.modal_anuncio').modal('show');
        });

        $('[disabled="disabled"]').click(function (e) {
            e.preventDefault();
        });

        $(".preloader").show();
        setTimeout(function () {
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
        if ($(".preloader").fadeOut()) {
        }
    }

    $(function () {
        $('.datepicker').inputmask('date', {placeholder: '__/__/____'});
    });

    $(function () {
        $(".money").maskMoney({thousands: '.', decimal: ',', allowZero: true});
        $('.money').prop('type', 'tel');
    });
    $(function () {
        $('.popover-btn').popover()
    });

    $(document).on('click', 'a:not([href="javascript:"],' +
        '[disabled="disabled"], ' +
        '[class="js:"], ' +
        '[class="ui-corner-all"], ' +
        '[href="#"], ' +
        '[data-toggle="modal"], ' +
        '[data-toggle="collapse"], ' +
        '[data-toggle="tab"])', function () {
        $(".subconteudo-principal").hide();
        $(".preloader").show();

    });

    $(document).on('submit', 'form', function (event) {
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

            setTimeout(function () {
                form.submit();
            }, 1000);
        }
    });

    $(document).on('click', '#teste-btn', function () {
        // console.log('teste ok!');
        $(".preloader").fadeIn();
        setTimeout(function () {
            $(".preloader").fadeOut();
        }, 1000);
    });

    $('#telefone').mask("(99) 9999-99990");
    $('#cep').mask("99999-999");
    $('#cpf').mask("999.999.999-99");
    $('#rg').mask("99.999.999-9");

    <?php
    $url = current_url();
    $segments = explode("/", $url);
    $bloqueados = array(
        'login',
        'redefinirsenha',
    );

    if (!count(array_intersect($segments, $bloqueados)) > 0) { ?>
    window.onload = function () {
        // var wrapper = document.body;
        // wrapper.className += " page-loading";
        // setTimeout(function () {
        //     wrapper.classList.remove('page-loading');
        // }, 2000);
    };
    <?php
    } ?>

    $(document).ready(function () {

        $('#body').each(function () {
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

        $('#menu-switcher').click(function () {
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
        function limpa_formulário_cep() {
            // Limpa valores do formulário de cep.
            $("#logradouro").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#uf").val("");
            $("#ibge").val("");
        }

        //Quando o campo cep possui algum caracter digitado.
        $("#cep").keyup(function () {

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
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/", function (dados) {

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
        // FIM API CEP CORREIOS

        $('.datepicker').datepicker({
            language: 'pt-BR',
            autoclose: true,
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            todayBtn: 'linked'
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
        });

        <?php if ($this->session->flashdata('erro') != null) { ?>
        Swal.fire({
            position: 'top',
            type: 'error',
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
        <?php } ?>

        $.fn.extend({
            toggleText: function (a, b) {
                return this.text(this.text() == b ? a : b);
            }
        });

        $.each($('.expand-icon'), function (key, value) {
            $(this).attr('class', 'fas fa-expand expand-icon');
            $(this).attr('title', 'Expandir');
        });

        $(".panel .expand").click(function () {
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

        $(".panel .close-panel").click(function () {
            $(this).closest(".panel").hide();
        });

        // -------------------------------
        // Panel Collapses
        // -------------------------------
        $('.panel-collapse').click(function () {
            $(this).children().toggleClass("fa-minus fa-plus");
            $(this).closest(".panel-heading").next().slideToggle({duration: 200});
            $(this).closest(".panel-heading").toggleClass('rounded-bottom');
            return false;
        });
    });
</script>
