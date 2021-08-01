<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-credit-card fa-lg fa-fw"></i>
            Novo Cartão de Crédito
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
        <div class="row">
            <div class="col-md-6">
                <form id="formAdicionarCartao" action="<?php echo base_url('financeiro/cartoes/cadastrar') ?>" method="post" class="card">
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
            },
            messages: {
                number: {
                    required: 'Informe o número do cartão',
                    minlength: 'O número do cartão deve conter 12 dígitos',
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