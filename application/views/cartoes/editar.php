<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-credit-card fa-lg fa-fw"></i>
            Editar Dados do Cartão
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
        <?php if ($cartao->adicional) { ?>
            <div class="note note-info mb40">
                <p class="font-weight-bold" style="font-size: 16px">
                    USUÁRIO ASSOCIADO AO CARTÃO
                </p>
                <p>
                    <span class="font-weight-bold">Nome do Usuário:</span> <?= $usuario ?>
                </p>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-6">
                <form id="formEditarCartao" action="<?php echo base_url('financeiro/cartoes/editar/' . $cartao->id_cartao) ?>" method="post" class="card">
                    <input type="hidden" name="adicional" value="<?php echo $cartao->adicional ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $cartao->id_usuario ?>">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <input class="form-control" id="number" name="number" placeholder="Número do cartão *" type="text" value="<?= trim(decriptar($cartao->numero)) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <input class="form-control" id="name" name="name" placeholder="Nome impresso no cartão" type="text" value="<?= $cartao->nome ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-6">
                            <input class="form-control" id="expiry" name="expiry" placeholder="Validade" type="text" value="<?= $cartao->validade ?>">
                        </div>
                        <div class="form-group col-xs-6">
                            <input class="form-control" id="cvc" name="cvc" placeholder="Código de segurança" type="text" value="<?= trim(decriptar($cartao->cvc)) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="principal" name="principal" value="1" <?php echo $cartao->principal == 1 ? 'checked' : '' ?>>
                            </div>
                            <label for="principal" class="font-weight-bold">Cartão Principal</label>
                        </div>
                    </div>
                    <input type="hidden" name="id_cartao" id="id_cartao" value="<?= $cartao->id_cartao ?>">
                    <input type="hidden" name="bandeira" id="bandeira">
                </form>
            </div>
            <div class="col-md-6">
                <div class="card-wrapper"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        setTimeout(function () {
            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('keyup', false, true);
            document.getElementById('number').dispatchEvent(evt);
            document.getElementById('name').dispatchEvent(evt);
            document.getElementById('expiry').dispatchEvent(evt);
            document.getElementById('cvc').dispatchEvent(evt);
            $('#bandeira').val(bandeira.cardType);
        }, 1100);

        let bandeira;
        //Credit Card
        setTimeout(function () {
            bandeira = mountCard('#formEditarCartao', '.card-wrapper', 1000);
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
            $('#formEditarCartao').submit();
        });

        $("#formEditarCartao").validate({
            rules: {
                number: {
                    required: true,
                    minlength: 19
                },
            },
            messages: {
                number: {
                    required: 'Informe o número do cartão',
                    minlength: 'O número do cartão deve conter 16 dígitos',
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