<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-user-edit fa-lg fa-fw"></i>
            Editar Dados do Cliente
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('clientes') ?>" class="btn btn-default btn-sm"><i class="fas fa-arrow-left fa-fw"></i> Clientes</a>
            <button class="btn btn-primary btn-sm submit"><i class="fas fa-check fa-fw"></i> Salvar</button>
        </div>
    </div>
    <div class="panel-body">
        <?php if (isset($custom_error)) {
            echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . $custom_error . '</div>';
        }
        if ($result->dob) {
            $dob = date(('d/m/Y'), strtotime($result->dob));
        }
        ?>
        <form action="<?php echo current_url(); ?>" id="formCliente" method="post" autocomplete="off">
            <div class="row">
                <div class="form-group col-md-8">
                    <label for="nome" class="control-label font-weight-bold">Nome *</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $result->nome; ?>">
                    <input type="hidden" name="id_clientes" value="<?php echo $result->id_clientes; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="cpf" class="control-label font-weight-bold">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo $result->cpf; ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="email" class="control-label font-weight-bold">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $result->email; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="telefone" class="control-label font-weight-bold">Telefone *</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $result->telefone; ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="dob" class="control-label font-weight-bold">Data Nascimento</label>
                    <input type="text" class="form-control datepicker" id="dob" name="dob" value="<?php echo $dob ?? null; ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="cep" class="control-label font-weight-bold">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" value="<?php echo $result->cep; ?>">
                </div>
                <div class="form-group col-md-7">
                    <label for="logradouro" class="control-label font-weight-bold">Logradouro</label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?php echo $result->logradouro; ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="numero" class="control-label font-weight-bold">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $result->numero; ?>">
                </div>
                <div class="form-group col-md-1 mt30">
                    <div class="checkbox icheck">
                        <input type="checkbox" class="form-control" id="s_n" name="s_n" value="1" <?= isset($result->s_n) ? 'checked' : '' ?>>
                    </div>
                    <label for="s_n" class="font-weight-bold">S/N</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="complemento" class="control-label font-weight-bold">Complemento</label>
                    <input type="text" class="form-control" id="complemento" name="complemento" value="<?php echo $result->complemento; ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="bairro" class="control-label font-weight-bold">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo $result->bairro; ?>">
                </div>
                <div class="form-group col-md-5">
                    <label for="cidade" class="control-label font-weight-bold">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo $result->cidade; ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="uf" class="control-label font-weight-bold">UF</label>
                    <input type="text" class="form-control" id="uf" name="uf" value="<?php echo $result->uf; ?>">
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.submit').click(function() {
            $('#formCliente').submit();
        });

        $('#s_n').on('ifChanged', function(event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#numero').val('');
                $('#numero').attr('disabled', true);
            } else {
                $('#numero').removeAttr('disabled');
            }
        });

        var s_n = $('#s_n').iCheck('update')[0].checked;
        $.each($(s_n), function(key, value) {
            if (s_n == true) {
                $('#numero').val('');
                $('#numero').attr('disabled', true);
            } else {
                $('#numero').removeAttr('disabled');
            }
        });

        $('#formCliente').validate({
            rules: {
                nome: {
                    required: true
                },
                cpf: {
                    required: false
                },
                telefone: {
                    required: true
                },
                email: {
                    required: false
                },
                logradouro: {
                    required: false
                },
                numero: {
                    required: false
                },
                bairro: {
                    required: false
                },
                cidade: {
                    required: false
                },
                uf: {
                    required: false
                },
                cep: {
                    required: false
                }
            },
            messages: {
                nome: {
                    required: 'Informe o nome'
                },
                cpf: {
                    required: 'Campo Requerido.'
                },
                telefone: {
                    required: 'Informe o telefone'
                },
                email: {
                    required: 'Campo Requerido.'
                },
                logradouro: {
                    required: 'Campo Requerido.'
                },
                numero: {
                    required: 'Campo Requerido.'
                },
                bairro: {
                    required: 'Campo Requerido.'
                },
                cidade: {
                    required: 'Campo Requerido.'
                },
                uf: {
                    required: 'Campo Requerido.'
                },
                cep: {
                    required: 'Campo Requerido.'
                }

            },

            errorClass: "help-block",
            errorElement: "p",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('has-error');
                $(element).parents('.form-group').removeClass('has-success');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('has-error');
                $(element).parents('.form-group').addClass('has-success');
            }
        });

    });
</script>