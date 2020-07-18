<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-cog fa-lg fa-fw"></i>
            Configurações de Consumo de Energia
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('consumo') ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Consumo</a>
            <button type="button" id="submit" class="btn btn-primary btn-sm" title="Salvar cartão">
                <i class="fas fa-check fa-fw"></i>
                Salvar
            </button>
        </div>
    </div>
    <div class="panel-body">
        <div class="note note-info mb40">
            Precisamos que você forneça abaixo algumas informações iniciais necessárias para o funcionamento do módulo de Consumo de Energia,
            você informará estes dados apenas uma única vez,
            caso tenha alguma dúvida, clique no ícone <i class="fas fa-question-circle fa-fw"></i> de cada campo para obter ajuda.
        </div>
        <form id="formConfig" action="<?php echo base_url('consumo/configuracoes') ?>" method="post">
            <div class="row">
                <div class="form-group col-sm-4">
                    <div class="input-icon right">
                        <label class="font-weight-bold" for="leitura_inicial">Leitura Inicial *</label>
                        <i class="fas fa-fw fa-question-circle" style="cursor: pointer" title="Ajuda" id="ajuda1"></i>
                        <input class="form-control" id="leitura_inicial" name="leitura_inicial" placeholder="Valor inicial da medição" type="text" value="<?= $configs->leitura_inicial ? $configs->leitura_inicial : '' ?>">
                        <p for="medicao_inicial" generated="true" id="help-block-cpf" class="help-block"></p>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <div class="input-icon right">
                        <label class="font-weight-bold" for="data_leitura">Data da Leitura Inicial</label>
                        <i class="fas fa-fw fa-question-circle" style="cursor: pointer" title="Ajuda" id="ajuda2"></i>
                        <input class="form-control datepicker" id="data_leitura" name="data_leitura" placeholder="Data da leitura inicial" type="text" value="<?= $configs->data_leitura ? date('d/m/Y', strtotime($configs->data_leitura)) : '' ?>">
                        <p for="inicio_medicaocpf" generated="true" id="help-block-cpf" class="help-block"></p>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <div class="input-icon right">
                        <label class="font-weight-bold" for="valor_kwh">Custo do kWh (R$)</label>
                        <i class="fas fa-fw fa-question-circle" style="cursor: pointer" title="Ajuda" id="ajuda3"></i>
                        <input class="form-control precision" id="valor_kwh" name="valor_kwh" placeholder="Custo do kWh em R$" type="text" value="<?= $configs->valor_kwh ? number_format(abs($configs->valor_kwh), 4, ',', '.') : '' ?>">
                        <p for="valor_kwh" generated="true" id="help-block-cpf" class="help-block"></p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!--Modal de ajuda 1-->
<div class="modal modal-middle fade" id="modalAjuda1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Leitura Inicial</h4>
            </div>
            <div class="modal-body">
                <p>
                    Informe o valor da leitura atual que consta no visor do seu medidor de energia.
                    Precisamos desta informação para o sistema efetuar os cálculos necessários.
                </p>
            </div>
        </div>
    </div>
</div>
<!--Modal de ajuda 2-->
<div class="modal modal-middle fade" id="modalAjuda2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Data da Leitura Inicial</h4>
            </div>
            <div class="modal-body">
                <p>
                    Informe a data de início das leituras de energia, ou seja, a data em que você começou a utilizar o módulo de Consumo de Energia.
                    Caso não informe este dado, o sistema utilizará a data atual.
                    Precisamos desta informação para o sistema controlar as marcações dos meses subsequentes.
                </p>
            </div>
        </div>
    </div>
</div>
<!--Modal de ajuda 3-->
<div class="modal modal-middle fade" id="modalAjuda3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Valor do kWh</h4>
            </div>
            <div class="modal-body">
                <p>
                    Informe o valor em reais (R$) do kWh (Kilowatt-hora) que consta em sua fatura de energia.
                    Caso esta informação não esteja discriminada em sua fatura, você pode efetuar o cálculo
                    manual para obter o valor aproximado da seguinte forma:
                </p>
                <div class="note note-info">
                    <strong>Valor da fatura do mês (R$) / Consumo do mês (kWh) = Valor aproximado do kWh</strong>
                </div>
                <p>
                    Caso não informe este dado, o sistema utilizará como base de cálculo valores que constam no site oficial da ANEEL.
                    Precisamos desta informação para o sistema calcular os valores aproximados de suas faturas de energia subsequentes.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $(".precision").maskMoney({
            precision: 4
        });

        $('#ajuda1').click(function () {
            $('#modalAjuda1').modal('show')
        });
        $('#ajuda2').click(function () {
            $('#modalAjuda2').modal('show')
        });
        $('#ajuda3').click(function () {
            $('#modalAjuda3').modal('show')
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

        $('#submit').click(function () {
            $('#formConfig').submit();
        });

        $("#formConfig").validate({
            rules: {
                leitura_inicial: {
                    required: true,
                    number: true
                },
            },
            messages: {
                leitura_inicial: {
                    required: 'Informe o valor da leitura do medidor',
                    number: 'Apenas números são permitidos',
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