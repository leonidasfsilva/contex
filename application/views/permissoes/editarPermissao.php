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
        <form id="formEditarPermissao" action="<?= base_url('permissoes/editar/' . $permissao->id_permissao) ?>" method="post">
            <div class="row">
                <div class="col-lg-6 mb30">
                    <div class="input-group">
                        <span class="input-group-addon font-weight-bold">Permissão:</span>
                        <input class="form-control" placeholder="Informe o nome da permissão" type="text" name="permissao" value="<?= $permissao->nome ?>">
                    </div>
                </div>
                <div class="col-lg-6 mb30">
                    <div class="input-group">
                        <span class="input-group-addon font-weight-bold">Situação:</span>
                        <input class="form-control font-weight-bold" style="<?= $permissao->ativo == 1 ? 'color: green' : 'color: red' ?>" type="text" name="situacao" value="<?= $permissao->ativo == 1 ? 'ATIVO' : 'INATIVO' ?>" disabled>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" value="<?= $permissao->id_permissao ?>">
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vCliente" <?= in_array('vCliente', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aCliente" <?= in_array('aCliente', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eCliente" <?= in_array('eCliente', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dCliente" <?= in_array('dCliente', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Cliente</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vConsumo" <?= in_array('vConsumo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Consumo</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aConsumo" <?= in_array('aConsumo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Consumo</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eConsumo" <?= in_array('eConsumo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Consumo</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dConsumo" <?= in_array('dConsumo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Consumo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vProduto" <?= in_array('vProduto', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Produto</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aProduto" <?= in_array('aProduto', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Produto</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eProduto" <?= in_array('eProduto', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Produto</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dProduto" <?= in_array('dProduto', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Produto</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vServico" <?= in_array('vServico', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aServico" <?= in_array('aServico', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eServico" <?= in_array('eServico', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dServico" <?= in_array('dServico', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Serviço</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vOs" <?= in_array('vOs', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aOs" <?= in_array('aOs', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eOs" <?= in_array('eOs', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dOs" <?= in_array('dOs', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir OS</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vVenda" <?= in_array('vVenda', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Venda</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aVenda" <?= in_array('aVenda', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Venda</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eVenda" <?= in_array('eVenda', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Venda</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dVenda" <?= in_array('dVenda', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Venda</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vArquivo" <?= in_array('vArquivo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Arquivos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aArquivo" <?= in_array('aArquivo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Arquivos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eArquivo" <?= in_array('eArquivo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Arquivos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dArquivo" <?= in_array('dArquivo', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Arquivos</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vLancamentos" <?= in_array('vLancamentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aLancamentos" <?= in_array('aLancamentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eLancamentos" <?= in_array('eLancamentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dLancamentos" <?= in_array('dLancamentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Lançamentos</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vFaturas" <?= in_array('vFaturas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Faturas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aFaturas" <?= in_array('aFaturas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Faturas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eFaturas" <?= in_array('eFaturas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Faturas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dFaturas" <?= in_array('dFaturas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Faturas</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vDespesas" <?= in_array('vDespesas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Despesas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aDespesas" <?= in_array('aDespesas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Despesas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eDespesas" <?= in_array('eDespesas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Despesas</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dDespesas" <?= in_array('dDespesas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Despesas</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vPendencias" <?= in_array('vPendencias', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Pendências</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aPendencias" <?= in_array('aPendencias', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Pendências</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="ePendencias" <?= in_array('ePendencias', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Pendências</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dPendencias" <?= in_array('dPendencias', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Pendências</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="vInvestimentos" <?= in_array('vInvestimentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Visualizar Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="aInvestimentos" <?= in_array('aInvestimentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Adicionar Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="eInvestimentos" <?= in_array('eInvestimentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Editar Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="dInvestimentos" <?= in_array('dInvestimentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Excluir Investimentos</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rCliente" <?= in_array('rCliente', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Relatório Cliente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rServico" <?= in_array('rServico', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Relatório Serviço</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rOs" <?= in_array('rOs', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Relatório OS</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rProduto" <?= in_array('rProduto', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Relatório Produto</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rVenda" <?= in_array('rVenda', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Relatório Venda</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rLancamentos" <?= in_array('rLancamentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Relatório Lançamentos</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rInvestimentos" <?= in_array('rInvestimentos', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Relatório Investimentos</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rFaturas" <?= in_array('rFaturas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Relatório Faturas</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="rDespesas" <?= in_array('rDespesas', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Relatório Despesas</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cUsuario" <?= in_array('cUsuario', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="vCliente" class="font-weight-bold">Configurar Usuários</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cEmitente" <?= in_array('cEmitente', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="aCliente" class="font-weight-bold">Configurar Emitente</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cPermissao" <?= in_array('cPermissao', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="eCliente" class="font-weight-bold">Configurar Permissões</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cBackup" <?= in_array('cBackup', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Backup</label>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="panel pl20">
                        <div class="checkbox icheck">
                            <input type="checkbox" class="form-control" name="atividades[]" value="cManutencao" <?= in_array('cManutencao', $atividades) ? 'checked' : '' ?>>
                        </div>
                        <label for="dCliente" class="font-weight-bold">Modo Manutenção</label>
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
            $('#formEditarPermissao').submit();
        });

        $("#formEditarPermissao").validate({
            rules: {
                nome: {required: true},
            },
            messages: {
                nome: {required: 'Informe o nome da permissão'},
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
