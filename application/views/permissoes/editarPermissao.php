<?php $permissoes = unserialize($result->permissoes); ?>
<div class="span12" style="margin-left: 0">
    <form action="<?php echo base_url(); ?>index.php/permissoes/editar" id="formPermissao" method="post">
        <div class="span12" style="margin-left: 0">
            <div class="widget-box">
                <div class="widget-title">
                <span class="icon">
                <i class="fa fa-lock fa-lg fa-fw"></i>
                </span>
                    <h5>Editar Permissão</h5>
                </div>
                <div class="widget-content">
                    <div class="span4">
                        <label>Nome da Permissão</label>
                        <input name="nome" type="text" id="nome" class="span12" value="<?php echo $result->nome; ?>"/>
                        <input type="hidden" name="idPermissao" value="<?php echo $result->idPermissao; ?>">
                    </div>
                    <div class="span3">
                        <label>Situação</label>
                        <select name="situacao" id="situacao" class="span12">
                            <?php if ($result->situacao == 1) {
                                $sim = 'selected';
                                $nao = '';

                            } else {
                                $sim = '';
                                $nao = 'selected';
                            } ?>
                            <option value="1" <?php echo $sim; ?>>Ativo</option>
                            <option value="0" <?php echo $nao; ?>>Inativo</option>
                        </select>

                    </div>
                    <div class="span4">
                        <br/>
                        <div class="checkbox">
                            <label>
                                <input name="" type="checkbox" value="1" id="marcarTodos"/>
                                <span class="lbl"> Marcar Todos</span>

                            </label>
                        </div>
                        <br/>
                    </div>
                    <div class="control-group">
                        <label for="documento" class="control-label"></label>
                        <div class="controls">
                            <table class="table table-bordered table-condensed">
                                <tbody>
                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vCliente'])) {
                                                    if ($permissoes['vCliente'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vCliente" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Cliente</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aCliente'])) {
                                                    if ($permissoes['aCliente'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aCliente" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Cliente</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eCliente'])) {
                                                    if ($permissoes['eCliente'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eCliente" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Cliente</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dCliente'])) {
                                                    if ($permissoes['dCliente'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dCliente" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Cliente</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vProduto'])) {
                                                    if ($permissoes['vProduto'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vProduto" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Produto</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aProduto'])) {
                                                    if ($permissoes['aProduto'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aProduto" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Produto</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eProduto'])) {
                                                    if ($permissoes['eProduto'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eProduto" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Produto</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dProduto'])) {
                                                    if ($permissoes['dProduto'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dProduto" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Produto</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                </tr>

                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vServico'])) {
                                                    if ($permissoes['vServico'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vServico" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Serviço</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aServico'])) {
                                                    if ($permissoes['aServico'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aServico" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Serviço</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eServico'])) {
                                                    if ($permissoes['eServico'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eServico" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Serviço</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dServico'])) {
                                                    if ($permissoes['dServico'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dServico" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Serviço</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vOs'])) {
                                                    if ($permissoes['vOs'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vOs" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar OS</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aOs'])) {
                                                    if ($permissoes['aOs'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aOs" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar OS</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eOs'])) {
                                                    if ($permissoes['eOs'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eOs" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar OS</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dOs'])) {
                                                    if ($permissoes['dOs'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dOs" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir OS</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                </tr>

                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vVenda'])) {
                                                    if ($permissoes['vVenda'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vVenda" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Venda</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aVenda'])) {
                                                    if ($permissoes['aVenda'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aVenda" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Venda</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eVenda'])) {
                                                    if ($permissoes['eVenda'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eVenda" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Venda</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dVenda'])) {
                                                    if ($permissoes['dVenda'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dVenda" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Venda</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="4"></td>
                                </tr>

                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vArquivo'])) {
                                                    if ($permissoes['vArquivo'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vArquivo" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Arquivo</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aArquivo'])) {
                                                    if ($permissoes['aArquivo'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aArquivo" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Arquivo</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eArquivo'])) {
                                                    if ($permissoes['eArquivo'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eArquivo" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Arquivo</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dArquivo'])) {
                                                    if ($permissoes['dArquivo'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dArquivo" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Arquivo</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="4"></td>
                                </tr>
<!--                                LANÇAMENTOS-->
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vLancamento'])) {
                                                    if ($permissoes['vLancamento'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vLancamento" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Lançamento</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aLancamento'])) {
                                                    if ($permissoes['aLancamento'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aLancamento" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Lançamento</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eLancamento'])) {
                                                    if ($permissoes['eLancamento'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eLancamento" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Lançamento</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dLancamento'])) {
                                                    if ($permissoes['dLancamento'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dLancamento" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Lançamento</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
<!--                                fim LANÇAMENTOS-->
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
<!--                                FATURAS-->
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vFaturas'])) {
                                                    if ($permissoes['vFaturas'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vFaturas" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Faturas</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aFaturas'])) {
                                                    if ($permissoes['aFaturas'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aFaturas" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Faturas</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['eFaturas'])) {
                                                    if ($permissoes['eFaturas'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="eFaturas" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Faturas</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dFaturas'])) {
                                                    if ($permissoes['dFaturas'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dFaturas" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Faturas</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
<!--                                fim FATURAS-->
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
<!--                                PENDENCIAS-->
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vPendencias'])) {
                                                    if ($permissoes['vPendencias'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vPendencias" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Pendências</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aPendencias'])) {
                                                    if ($permissoes['aPendencias'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aPendencias" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Pendências</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['ePendencias'])) {
                                                    if ($permissoes['ePendencias'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="ePendencias" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Pendências</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dPendencias'])) {
                                                    if ($permissoes['dPendencias'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dPendencias" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Pendências</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
<!--                                fim PENDENCIAS-->
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
<!--                                POUPANÇA-->
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['vPoupanca'])) {
                                                    if ($permissoes['vPoupanca'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="vPoupanca" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Visualizar Poupança</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['aPoupanca'])) {
                                                    if ($permissoes['aPoupanca'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="aPoupanca" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Adicionar Poupança</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['ePoupanca'])) {
                                                    if ($permissoes['ePoupanca'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="ePoupanca" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Editar Poupança</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['dPoupanca'])) {
                                                    if ($permissoes['dPoupanca'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="dPoupanca" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Excluir Poupança</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
<!--                                fim POUPANÇA-->
                                <tr>
                                    <td colspan="4"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['rCliente'])) {
                                                    if ($permissoes['rCliente'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="rCliente" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Relatório Cliente</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['rServico'])) {
                                                    if ($permissoes['rServico'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="rServico" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Relatório Serviço</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['rOs'])) {
                                                    if ($permissoes['rOs'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="rOs" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Relatório OS</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['rProduto'])) {
                                                    if ($permissoes['rProduto'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="rProduto" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Relatório Produto</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>

                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['rVenda'])) {
                                                    if ($permissoes['rVenda'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="rVenda" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Relatório Venda</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['rFinanceiro'])) {
                                                    if ($permissoes['rFinanceiro'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="rFinanceiro" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Relatório Financeiro</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td colspan="2"></td>

                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                </tr>

                                <tr>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['cUsuario'])) {
                                                    if ($permissoes['cUsuario'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="cUsuario" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Configurar Usuário</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['cEmitente'])) {
                                                    if ($permissoes['cEmitente'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="cEmitente" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Configurar Emitente</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['cPermissao'])) {
                                                    if ($permissoes['cPermissao'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="cPermissao" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Configurar Permissão</span>
                                            </label>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input <?php if (isset($permissoes['cBackup'])) {
                                                    if ($permissoes['cBackup'] == '1') {
                                                        echo 'checked';
                                                    }
                                                } ?> name="cBackup" class="marcar" type="checkbox" value="1"/>
                                                <span class="lbl"> Backup</span>
                                            </label>
                                        </div>
                                    </td>

                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3">
                                <a href="<?php echo base_url() ?>index.php/permissoes" id="" class="btn btn-default btn-sm">
                                    <i class="fa fa-arrow-left fa-fw"></i> Voltar</a>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Alterar</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>

    </form>

</div>


<script type="text/javascript" src="<?php echo base_url() ?>assets/js/validate.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#marcarTodos").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        });


        $("#formPermissao").validate({
            rules: {
                nome: {required: true}
            },
            messages: {
                nome: {required: 'Campo obrigatório'}
            }
        });


    });
</script>
