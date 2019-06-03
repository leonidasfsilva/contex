<script>
    $('#telefone').mask("(99) 9999-99990");
    $('#cep').mask("99999-999");
    $('#cpf').mask("999.999.999-99");
    $('#rg').mask("99.999.999-9");

    setTimeout(function () {
        $(".preloader").fadeOut();
    },500);

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
            } else {
                // SIDEBAR EXPOSTO
                $('#menu-toggle-icon').addClass('fa-chevron-left')
            }
        });

        $('#menu-switcher').click(function () {
            if ($('#body').hasClass('sidebar-collapsed')) {
                $('#menu-toggle-icon').toggleClass('fa-chevron-left fa-ellipsis-v')
            } else {
                $('#menu-toggle-icon').toggleClass('fa-chevron-left fa-ellipsis-v')
            }
        });

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
            if ( cep.length > 7 && cep != "" ) {

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
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

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
    });
</script>
