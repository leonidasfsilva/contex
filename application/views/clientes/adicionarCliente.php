<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-user-plus fa-fw"></i>
                </span>
                <h5>Cadastrar Novo Cliente</h5>
            </div>
            <div class="widget-content nopadding">
                <div class="control-group alert alert-info font_bold"> * Preenchimento obrigatório
                </div>
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>

                <form action="<?php echo current_url(); ?>" id="formCliente" method="post" class="form-horizontal">

                    <div class="control-group">
                        <label for="nomeCliente" class="control-label">Nome <span class="required">*</span></label>
                        <div class="controls">
                            <input id="nomeCliente" type="text" class="span4" name="nomeCliente" value="<?php echo set_value('nomeCliente'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="telefone" class="control-label">Telefone <span class="required">*</span></label>
                        <div class="controls">
                            <input id="telefone" type="text" class="span4" name="telefone" value="<?php echo set_value('telefone'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="telefone2" class="control-label">Telefone 2 </label>
                        <div class="controls">
                            <input id="celular" type="text" class="span4" name="celular" value="<?php echo set_value('celular'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="documento" class="control-label">CPF/CNPJ</label>
                        <div class="controls">
                            <input id="documento" type="text" class="span4" name="documento" value="<?php echo set_value('documento'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="email" class="control-label">Email</label>
                        <div class="controls">
                            <input id="email" type="text" class="span4" name="email" value="<?php echo set_value('email'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group" class="control-label">
                        <label for="cep" class="control-label">CEP</label>
                        <div class="controls">
                            <input id="cep" type="text" class="span4" name="cep" value="<?php echo set_value('cep'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group" class="control-label">
                        <label for="rua" class="control-label">Logradouro</label>
                        <div class="controls">
                            <input id="rua" type="text" class="span4" name="rua" value="<?php echo set_value('rua'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="numero" class="control-label">Número</label>
                        <div class="controls">
                            <input id="numero" type="text" class="span4" name="numero" value="<?php echo set_value('numero'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group" class="control-label">
                        <label for="bairro" class="control-label">Bairro</label>
                        <div class="controls">
                            <input id="bairro" type="text" class="span4" name="bairro" value="<?php echo set_value('bairro'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group" class="control-label">
                        <label for="cidade" class="control-label">Cidade</label>
                        <div class="controls">
                            <input id="cidade" type="text" class="span4" name="cidade" value="<?php echo set_value('cidade'); ?>"/>
                        </div>
                    </div>

                    <div class="control-group" class="control-label">
                        <label for="estado" class="control-label">UF</label>
                        <div class="controls">
                            <input id="estado" type="text" class="span4" name="estado" value="<?php echo set_value('estado'); ?>"/>
                        </div>
                    </div>


                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset2">
                                <a href="<?php echo base_url() ?>index.php/clientes" id="" class="btn btn-default btn-sm"><i class="fa fa-arrow-left fa-fw"></i> Voltar</a>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Adicionar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#telefone').mask("(99) 99999-999?9");

        $('#formCliente').validate({
            rules: {
                nomeCliente: {required: true},
                documento: {required: false},
                telefone: {required: true},
                email: {required: false},
                rua: {required: false},
                numero: {required: false},
                bairro: {required: false},
                cidade: {required: false},
                estado: {required: false},
                cep: {required: false}
            },
            messages: {
                nomeCliente: {required: 'Campo obrigatório'},
                documento: {required: 'Campo Requerido.'},
                telefone: {required: 'Campo obrigatório'},
                email: {required: 'Campo Requerido.'},
                rua: {required: 'Campo Requerido.'},
                numero: {required: 'Campo Requerido.'},
                bairro: {required: 'Campo Requerido.'},
                cidade: {required: 'Campo Requerido.'},
                estado: {required: 'Campo Requerido.'},
                cep: {required: 'Campo Requerido.'}

            },

            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script>
<script type="text/javascript">

    $(document).ready(function () {

        function limpa_formulario_cep() {
            // Limpa valores do formulário de cep.
            $("#rua").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#estado").val("");
        }

        //Quando o campo cep perde o foco.
        $("#cep").blur(function () {

            //Nova variável "cep" somente com dígitos.
            var cep = $(this).val().replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if (validacep.test(cep)) {

                    //Preenche os campos com "..." enquanto consulta webservice.
                    $("#rua").val("...");
                    $("#bairro").val("...");
                    $("#cidade").val("...");
                    $("#estado").val("...");

                    //Consulta o webservice viacep.com.br/
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                        if (!("erro" in dados)) {
                            //Atualiza os campos com os valores da consulta.
                            $("#rua").val(dados.logradouro);
                            $("#bairro").val(dados.bairro);
                            $("#cidade").val(dados.localidade);
                            $("#estado").val(dados.uf);
                            document.getElementById("numero").focus();
                        } //end if.
                        else {
                            //CEP pesquisado não foi encontrado.
                            limpa_formulario_cep();
                            alert("CEP não encontrado.");
                        }
                    });
                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulario_cep();
                    alert("Formato de CEP inválido.");
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulario_cep();
            }
        });
    });

</script>
