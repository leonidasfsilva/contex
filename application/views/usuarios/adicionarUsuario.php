<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-user-plus fa-lg fa-fw"></i>
            Adicionar Novo Usuário
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('usuarios') ?>" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left fa-fw"></i> Usuários</a>
            <button class="btn btn-primary btn-sm submit"><i class="fas fa-check fa-fw"></i> Salvar</button>
        </div>
    </div>
    <div class="panel-body">
        <?php if (isset($custom_error)) {
            echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . $custom_error . '</div>';
        } ?>
        <form action="<?php echo current_url(); ?>" id="formUsuario" method="post" autocomplete="off">
            <div class="row">
                <div class="form-group col-md-8">
                    <label for="nome" class="control-label font-weight-bold">Nome *</label>
                    <input type="text" class="form-control" id="nome" name="nome"">
                </div>
                <div class="form-group col-md-4">
                    <label for="cpf" class="control-label font-weight-bold">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="telefone" class="control-label font-weight-bold">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone">
                </div>
                <div class="form-group col-md-4">
                    <label for="email" class="control-label font-weight-bold">Email *</label>
                    <input type="text" class="form-control" id="email" name="email">
                </div>
                <div class="form-group col-md-4">
                    <label for="senha" class="control-label font-weight-bold">Senha *</label>
                    <input type="password" class="form-control" id="senha" name="senha">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="cep" class="control-label font-weight-bold">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep">
                </div>
                <div class="form-group col-md-7">
                    <label for="logradouro" class="control-label font-weight-bold">Logradouro</label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro">
                </div>
                <div class="form-group col-md-2">
                    <label for="numero" class="control-label font-weight-bold">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero">
                </div>
                <div class="form-group col-md-1 mt30">
                    <div class="checkbox icheck">
                        <input type="checkbox" class="form-control" id="s_n" name="s_n" value="1">
                    </div>
                    <label for="s_n" class="font-weight-bold">S/N</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="complemento" class="control-label font-weight-bold">Complemento</label>
                    <input type="text" class="form-control" id="complemento" name="complemento">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="bairro" class="control-label font-weight-bold">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro">
                </div>
                <div class="form-group col-md-5">
                    <label for="cidade" class="control-label font-weight-bold">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade">
                </div>
                <div class="form-group col-md-2">
                    <label for="uf" class="control-label font-weight-bold">UF</label>
                    <input type="text" class="form-control" id="uf" name="uf">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="permissoes_id" class="control-label font-weight-bold">Permissão *</label>
                    <select class="form-control" name="permissoes_id" id="permissoes_id">
                        <option value=""><< Selecione >></option>
                        <?php foreach ($permissoes as $p) {
                            echo '<option value="' . $p->id_permissao . '">' . $p->nome . '</option>';
                        } ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="situacao" class="control-label font-weight-bold">Status *</label>
                    <select class="form-control" name="situacao" id="situacao">
                        <option value=""><< Selecione >></option>
                        <option value="1">ATIVO</option>
                        <option value="0">INATIVO</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.submit').click(function () {
            $('#formUsuario').submit();
        });

        $('#s_n').on('ifChanged', function (event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#numero').val('');
                $('#numero').attr('disabled', true);
            } else {
                $('#numero').removeAttr('disabled');
            }
        });

        var s_n = $('#s_n').iCheck('update')[0].checked;
        $.each($(s_n), function (key, value) {
            if (s_n == true) {
                $('#numero').val('');
                $('#numero').attr('disabled', true);
            } else {
                $('#numero').removeAttr('disabled');
            }
        });


        $('#formUsuario').validate({
            rules: {
                nome: {required: true},
                permissoes_id: {required: true},
                situacao: {required: true},
                cpf: {required: false},
                telefone: {required: false},
                email: {required: true},
                senha: {required: true},
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
                email: {required: 'Informe o email'},
                senha: {required: 'Defina uma senha'},
                logradouro: {required: 'Campo Requerido.'},
                numero: {required: 'Campo Requerido.'},
                bairro: {required: 'Campo Requerido.'},
                cidade: {required: 'Campo Requerido.'},
                uf: {required: 'Campo Requerido.'},
                cep: {required: 'Campo Requerido.'},
                permissoes_id: {required: 'Selecione o nível de permissão'},
                situacao: {required: 'Selecione o status'},

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



