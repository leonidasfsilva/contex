<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-credit-card fa-lg fa-fw"></i>
            Novo Cartão Adicional
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('financeiro/cartoes') ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Cartões</a>
            <button type="button" id="submit" class="btn btn-primary btn-sm" title="Salvar cartão">
                <i class="fas fa-check fa-fw"></i>
                Salvar
            </button>
        </div>
    </div>
    <div class="panel-body">
        <div class="note note-warning mb40">
            Todos os dados inseridos neste formulário são armazenados em nosso banco de dados de forma segura.
            Caso não se sinta à vontade para fornecer todos os dados do seu cartão, apenas o número do cartão é obrigatório.
        </div>
        <div class="note note-info mb40">
            <p class="font-weight-bold" style="font-size: 16px">
                Dados do Cartão Titular
            </p>
            <p>
                <span class="font-weight-bold">Número do Cartão:</span> <?= base64_decode($cartao->numero) ?>
            </p>
            <p>
                <span class="font-weight-bold">Bandeira do Cartão:</span> <?= $cartao->bandeira ?>
            </p>
            <p>
                <span class="font-weight-bold">Nome do Titular:</span> <?= $cartao->nome ?>
            </p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form id="formAdicionarCartao" action="<?php echo base_url('financeiro/cartoes/adicional') ?>" method="post" class="card">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <input class="form-control" id="number" name="number" placeholder="Número do cartão *" type="text">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <input class="form-control" id="name" name="name" placeholder="Nome impresso no cartão" type="text">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <input class="form-control" id="expiry" name="expiry" placeholder="Validade" type="text">
                        </div>
                        <div class="form-group col-sm-6">
                            <input class="form-control" id="cvc" name="cvc" placeholder="Código de segurança" type="text">
                        </div>
                    </div>
                    <input type="hidden" name="bandeira" id="bandeira">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div class="input-icon right">
                                <i class="fas fa-fw fa-question-circle" style="cursor: pointer" title="Ajuda" id="ajuda1"></i>
                                <input class="form-control" id="nome_usuario" name="nome_usuario" placeholder="Nome do usuário do cartão adicional *" type="text" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-8">
                            <div class="input-icon right">
                                <i class="fas fa-fw fa-question-circle" style="cursor: pointer" title="Ajuda" id="ajuda2"></i>
                                <input class="form-control" id="cpf" name="cpf" placeholder="CPF do usuário do cartão adicional *" type="text">
                                <p for="cpf" generated="true" id="help-block-cpf" class="help-block"></p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12 mb20">
                            <button type="button" class="btn btn-primary btn-block text-center" title="Consultar Usuário" id="consultar">
                                <i class="fas fa-search fa-fw"></i> Consultar
                            </button>
                        </div>
                    </div>
                    <div class="alert alert-danger alert-result" style="display: none" id="usuario_inexistente">
                        <a href="#" style="font-size: 12px" class="close" onclick="$('#usuario_inexistente').hide()"><i class="fas fa-times fa-fw"></i></a>
                        <i class="fas fa-exclamation-triangle fa-lg fa-fw"></i> Nenhum usuário encontrado para o CPF informado.
                    </div>

                    <div class="alert alert-danger mt0 hidden" id="carregando">
                        <i class="fas fa-spinner fa-pulse fa-lg fa-fw"></i> Consultando, por favor, aguarde...
                    </div>

                    <div class="alert alert-success alert-result" style="display: none" id="usuario_selecionado">
                        <a href="#" style="font-size: 12px" class="pull-right" onclick="$('#usuario_selecionado').hide(); $('#nome_usuario').val(''); $('#id_usuario').val(''); "><span class="label label-default"><i class="fas fa-times fa-fw"></i> Remover</span></a>
                        <p class="font-weight-bold">
                            <i class="fas fa-check-square fa-fw"></i>
                            <span id="alert_usuario"></span>
                        </p>
                    </div>
                    <input type="hidden" name="id_cartao" id="id_cartao" value="<?= $cartao->id_cartao ?>">
                    <input type="hidden" name="id_usuario" id="id_usuario" value="">
                </form>
            </div>
            <div class="col-md-6">
                <div class="card-wrapper"></div>
            </div>
        </div>
    </div>
</div>

<!--Modal de ajuda sobre CPF do usuario-->
<div class="modal modal-middle fade" id="modalAjuda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Usuário do Cartão Adicional</h4>
            </div>
            <div class="modal-body">
                <p>O cartão adicional deve estar vinculado a um usuário do sistema, para atribuir o cartão adicional a um usuário, informe o CPF do usuário desejado.</p>
                <p>Caso o CPF informado corresponda a algum usuário, o sistema irá preencher o nome do usuário selecionado automaticamente.</p>
                <p>Caso o sistema não encontre nenhum usuário para o CPF informado, verifique o CPF informado e tente novamente ou verifique se o usuário para qual você quer atribuir o cartão possui o CPF cadastrado corretamente no sistema.</p>
                <p class="note note-danger font-weight-bold"><i style="color: #f44336" class="fas fa-exclamation-circle fa-lg fa-fw"></i> Não é possível gerar um cartão adicional sem nenhum usuário atribuído.</p>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('#ajuda1, #ajuda2').click(function () {
            $('#modalAjuda').modal('show')
        });

        $('#consultar').click(function () {
            let cpf = $('#cpf').val();

            if (cpf == '') {
                $('#help-block-cpf').text('Informe o CPF a ser consultado').parents('.form-group').addClass('has-error');
                return;
            } else {
                if (cpf.length < 14) {
                    $('#help-block-cpf').text('O CPF deve conter 11 dígitos').parents('.form-group').addClass('has-error');
                    return;
                }
            }

            $.each($('.alert-result'), function (key, value) {
                $(this).hide();
            });

            $('#nome_usuario').val('');
            $('#id_usuario').val('');
            $('#carregando').removeClass('hidden');

            setTimeout(function () {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url();?>financeiro/cartoes/consultarUsuarioCPF",
                    data: {
                        cpf: cpf
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.result === true) {
                            $('#carregando').addClass('hidden');
                            $('#usuario_selecionado').show();
                            $('#alert_usuario').text(data.retorno.nome);
                            $('#nome_usuario').val(data.retorno.nome);
                            $('#id_usuario').val(data.retorno.id_usuarios);
                        } else {
                            $('#carregando').addClass('hidden');
                            $('#usuario_inexistente').show();
                        }
                    },
                    error: function (data) {
                        alert('Erro na função consultarUsuarioCPF()')
                    }
                });
            }, 1000);
        });

        let bandeira;
        //Credit Card
        setTimeout(function () {
            bandeira = mountCard('#formAdicionarCartao', '.card-wrapper', 1000);
        }, 1000);

        $('#number').keyup(function () {
            if ($(this).val().length >= 4) {
                $('#bandeira').val(bandeira.cardType);
            }
        });

        $('#name').keyup(function () {
            if ($(this).val().length >= 1) {
                $(this).css({
                    'text-transform': 'uppercase'
                })
            } else {
                $(this).css({
                    'text-transform': 'unset'
                })
            }
        });

        $('#submit').click(function () {
            $('#formAdicionarCartao').submit();
        });

        $("#formAdicionarCartao").validate({
            rules: {
                number: {
                    required: true,
                    minlength: 19
                },
                cpf: {
                    required: true,
                    minlength: 14
                },
                nome_usuario: {
                    required: true,
                },
            },
            messages: {
                number: {
                    required: 'Informe o número do cartão',
                    minlength: 'O número do cartão deve conter 12 dígitos',
                },
                cpf: {
                    required: 'Informe o CPF do usuário do cartão',
                    minlength: 'O CPF deve conter 11 dígitos',
                },
                nome_usuario: {
                    required: 'Informe o CPF para selecionar o usuário',
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
    });
</script>