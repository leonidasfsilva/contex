<div class="panel panel-midnightblue">
    <div class="panel-heading">
        <h3>
            <i class="fas fa-key fa-lg fa-fw"></i>
            Editar Permissão
        </h3>
        <div class="panel-ctrls">
            <a href="<?= base_url('permissoes') ?>" class="btn btn-sm btn-default"><i class="fas fa-arrow-left fa-fw"></i> Permissões</a>
            <button class="btn btn-default btn-sm marcar_todos" id="marcar_todos" title="Marcar todas as opções">
                <i class="fas fa-check-square fa-fw"></i>
                Marcar Todos
            </button>
            <button class="btn btn-default btn-sm marcar_todos hidden" id="desmarcar_todos" title="Desmarcar todas as opções">
                <i class="far fa-square fa-fw"></i>
                Desmarcar Todos
            </button>
            <button type="button" id="submit" class="btn btn-primary btn-sm" title="Salvar">
                <i class="fas fa-check fa-fw"></i>
                Salvar
            </button>
        </div>
    </div>
    <div class="panel-body">
        <form id="formNovaPermissao" action="<?= base_url('permissoes/adicionar') ?>" method="post">
            <div class="row">
                <div class="col-lg-6 mb30">
                    <div class="input-group">
                        <span class="input-group-addon font-weight-bold">Permissão:</span>
                        <input class="form-control" placeholder="Informe o nome da permissão" type="text" name="nome">
                    </div>
                </div>
                <div class="col-lg-6 mb30">
                    <div class="input-group">
                        <span class="input-group-addon font-weight-bold">Situação:</span>
                        <select class="form-control" name="status">
                            <option value=""><< Selecione >></option>
                            <option value="1">ATIVO</option>
                            <option value="0">INATIVO</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vCliente">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aCliente">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eCliente">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dCliente">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Cliente</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vProduto">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Produto</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aProduto">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Produto</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eProduto">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Produto</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dProduto">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Produto</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vServico">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aServico">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eServico">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dServico">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Serviço</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vOs">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aOs">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eOs">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dOs">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir OS</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vVenda">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Vendas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aVenda">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Vendas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eVenda">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Vendas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dVenda">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Vendas</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vArquivo">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Arquivos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aArquivo">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Arquivos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eArquivo">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Arquivos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dArquivo">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Arquivos</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vLancamentos">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aLancamentos">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eLancamentos">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dLancamentos">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Lançamentos</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vFaturas">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Faturas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aFaturas">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Faturas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eFaturas">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Faturas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dFaturas">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Faturas</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vDespesas">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Despesas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aDespesas">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Despesas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eDespesas">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Despesas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dDespesas">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Despesas</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vPendencias">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Pendências</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aPendencias">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Pendências</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="ePendencias">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Pendências</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dPendencias">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Pendências</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vInvestimentos">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aInvestimentos">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eInvestimentos">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dInvestimentos">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Investimentos</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rCliente">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Relatório Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rServico">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Relatório Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rOs">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Relatório OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rProduto">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Relatório Produto</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rVenda">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Relatório Venda</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rLancamentos">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Relatório Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rInvestimentos">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Relatório Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rFaturas">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Relatório Faturas</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cUsuario">
                        </div>
                        <label for="vCliente" class="font-weight-bold">Configurar Usuários</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cEmitente">
                        </div>
                        <label for="aCliente" class="font-weight-bold">Configurar Emitente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cPermissao">
                        </div>
                        <label for="eCliente" class="font-weight-bold">Configurar Permissões</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cBackup">
                        </div>
                        <label for="dCliente" class="font-weight-bold">Backup</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        let marcado = false;
        var checkboxes = $('input[name="atividades[]"]');


        $("#marcarTodos").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });

        $('#marcar_todos, #desmarcar_todos').click(function () {
            $('#marcar_todos').toggleClass('hidden');
            $('#desmarcar_todos').toggleClass('hidden');
            if (marcado == false) {
                marcado = true;
                checkboxes.iCheck('check');
                checkAll.prop('checked', 'checked');
            } else {
                marcado = false;
                checkboxes.iCheck('uncheck');
                checkAll.prop('checked', false);
            }
            checkAll.iCheck('update');
        });


        $('#submit').click(function () {
            $('#formNovaPermissao').submit();
        });

        $("#formNovaPermissao").validate({
            rules: {
                nome: {required: true},
                status: {required: true}
            },
            messages: {
                nome: {required: 'Informe o nome da permissão'},
                status: {required: 'Selecione o status da permissão'}
            },
            errorClass: "help-block",
            errorElement: "p",

            highlight: function (element, errorClass, validClass) {
                console.log(element)
                $(element).parents('.col-lg-6').addClass('has-error');
                $('.validate-error').removeClass('hidden');
                $('.validate-success').addClass('hidden');
                $(element).parents('.col-lg-6').removeClass('has-success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.col-lg-6').removeClass('has-error');
                $('.validate-error').addClass('hidden');
                $('.validate-success').removeClass('hidden');
                $(element).parents('.col-lg-6').addClass('has-success');
            }
        });

    });
</script>