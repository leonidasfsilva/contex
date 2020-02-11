<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-user-cog fa-lg fa-fw"></i>
            Configurações de Usuário
        </h3>
    </div>
</div>
<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-file-invoice-dollar fa-lg fa-fw"></i>
            Faturas
        </h3>
        <div class="panel-ctrls">
            <!--            <a href="#" class="button-icon close-panel">-->
            <!--                <i class="fas fa-times"></i>-->
            <!--            </a>-->
            <a href="#" class="button-icon expand">
                <i class="fas fa-expand-arrows-alt expand-icon"></i>
            </a>
            <a href="#" class="button-icon panel-collapse">
                <i class="fas fa-minus"></i>
            </a>
        </div>
    </div>
    <div class="panel-body">
        <form action="#" class="form-horizontal row-border">
            <div class="form-group">
                <input type="checkbox" id="faturas_usuario" name="set-name" class="switch-input faturas" checked>
                <label for="faturas_usuario" class="switch-label">Notificar usuário sobre vencimento das faturas</label>
            </div>
            <div class="form-group">
                <input type="checkbox" id="faturas_clientes" name="set-name" class="switch-input faturas" checked>
                <label for="faturas_clientes" class="switch-label">Notificar clientes sobre vencimento das faturas</label>
            </div>
        </form>
    </div>
</div>

<?php if (!isset($dados) || $dados == null) { ?>

<?php } else { ?>

<?php } ?>

<script type="text/javascript">

    $(document).ready(function () {

        $("#formLogo").validate({
            rules: {
                userfile: {required: true}
            },
            messages: {
                userfile: {required: 'Arquivo não informado'}
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

        $("#formCadastrar").validate({
            rules: {
                emitente: {required: true},
                cnpj: {required: true},
                cep: {required: true},
                ie: {required: false},
                logradouro: {required: true},
                numero: {required: false},
                bairro: {required: true},
                cidade: {required: true},
                uf: {required: true},
                telefone: {required: true},
                email: {required: true},
                userfile: {required: false},
            },
            messages: {
                emitente: {required: 'Informe a razão social'},
                cnpj: {required: 'Informe o CNPJ'},
                cep: {required: 'Informe o CEP'},
                ie: {required: 'Informe a I.E.'},
                logradouro: {required: 'Informe o logradouro'},
                numero: {required: 'Nao obrigatorio'},
                bairro: {required: 'Informe o bairro'},
                cidade: {required: 'Informe a cidade'},
                uf: {required: 'Informe a UF'},
                telefone: {required: 'Informe o telefone'},
                email: {required: 'Informe o email'},
                userfile: {required: 'Selecione a logomarca'},
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

        $("#formAlterar").validate({
            rules: {
                emitente: {required: true},
                cnpj: {required: true},
                cep: {required: true},
                ie: {required: false},
                logradouro: {required: true},
                numero: {required: false},
                bairro: {required: true},
                cidade: {required: true},
                uf: {required: true},
                telefone: {required: true},
                email: {required: true}
            },
            messages: {
                emitente: {required: 'Informe a razão social'},
                cnpj: {required: 'Informe o CNPJ'},
                cep: {required: 'Informe o CEP'},
                ie: {required: 'Informe a I.E.'},
                logradouro: {required: 'Informe o logradouro'},
                numero: {required: 'Nao obrigatorio'},
                bairro: {required: 'Informe o bairro'},
                cidade: {required: 'Informe a cidade'},
                uf: {required: 'Informe a UF'},
                telefone: {required: 'Informe o telefone'},
                email: {required: 'Informe o email'}
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
