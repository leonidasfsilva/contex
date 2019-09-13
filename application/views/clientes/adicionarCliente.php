<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fa fa-user-plus fa-lg fa-fw"></i>
            Cadastrar Novo Cliente
        </h3>
    </div>
    <div class="panel-body">
        <?php if ($custom_error != '') {
            echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . $custom_error . '</div>';
        } ?>
        <form action="<?php echo current_url(); ?>" id="formCliente" method="post" autocomplete="off">
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="nome" class="control-label font-weight-bold">Nome *</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo set_value('nome'); ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="cpf" class="control-label font-weight-bold">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo set_value('cpf'); ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="telefone" class="control-label font-weight-bold">Telefone *</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo set_value('telefone'); ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="email" class="control-label font-weight-bold">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="cep" class="control-label font-weight-bold">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" value="<?php echo set_value('cep'); ?>">
                </div>
                <div class="form-group col-md-8">
                    <label for="logradouro" class="control-label font-weight-bold">Logradouro</label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?php echo set_value('logradouro'); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="numero" class="control-label font-weight-bold">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero" value="<?php echo set_value('numero'); ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="bairro" class="control-label font-weight-bold">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo set_value('bairro'); ?>">
                </div>
                <div class="form-group col-md-5">
                    <label for="cidade" class="control-label font-weight-bold">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo set_value('cidade'); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="uf" class="control-label font-weight-bold">UF</label>
                    <input type="text" class="form-control" id="uf" name="uf" value="<?php echo set_value('uf'); ?>">
                </div>
            </div>
            <div class="panel-footer">
                <div class="row pull-right">
                    <div class="col-md-12 ">
                        <a href="<?php echo base_url() ?>clientes" id="" class="btn btn-default btn-sm"><i class="fa fa-arrow-left fa-fw"></i> Voltar</a>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#telefone').mask("(99) 9999-99990");

        $('#formCliente').validate({
            rules: {
                nome: {required: true},
                cpf: {required: false},
                telefone: {required: true},
                email: {required: false},
                logradouro: {required: false},
                numero: {required: false},
                bairro: {required: false},
                cidade: {required: false},
                uf: {required: false},
                cep: {required: false}
            },
            messages: {
                nome: {required: 'Informe o nome'},
                cpf: {required: 'Campo Requerido.'},
                telefone: {required: 'Informe o telefone'},
                email: {required: 'Campo Requerido.'},
                logradouro: {required: 'Campo Requerido.'},
                numero: {required: 'Campo Requerido.'},
                bairro: {required: 'Campo Requerido.'},
                cidade: {required: 'Campo Requerido.'},
                uf: {required: 'Campo Requerido.'},
                cep: {required: 'Campo Requerido.'}

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