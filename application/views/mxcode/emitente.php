<?php if (!isset($dados) || $dados == null) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>
                <i class="fas fa-id-card fa-lg fa-fw"></i>
                Dados do Emitente
            </h3>
            <div class="panel-ctrls">
                <!--                <button href="#modalFiltrar" class="btn btn-default btn-sm" id="editar_perfil" data-toggle="modal" title="Editar perfil de usuário">-->
                <!--                    <i class="fa fa-user-circle fa-fw"></i>-->
                <!--                    Editar Perfil-->
                <!--                </button>-->
            </div>
        </div>
        <div class="panel-body">
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle fa-lg fa-fw"></i>
                Nenhuma informação referente ao emitente foi cadastrada até o momento.
            </div>
            <div class="panel-footer">
                <a href="#modalCadastrar" data-toggle="modal" role="button" class="btn btn-primary pull-right">
                    <i class="fas fa-plus fa-fw"></i> Cadastrar Dados
                </a>

            </div>
        </div>
    </div>

    <!-- Modal CADASTRAR DADOS EMITENTE -->
    <div class="modal fade" id="modalCadastrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white ">Cadastrar dados do emitente</h4>
                </div>
                <form id="formCadastrar" action="<?php echo base_url() ?>mxcode/cadastrarEmitente" method="post" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="font-weight-bold" for="emitente">Razão Social *</label>
                                <input id="emitente" class="form-control" type="text" name="emitente" value=""/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label for="cnpj" class="font-weight-bold">CNPJ *</label>
                                <input class="form-control" type="text" id="cnpj" name="cnpj" value=""/>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ie" class="font-weight-bold">IE </label>
                                <input type="text" class="form-control" id="ie" name="ie" value=""/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="cep" class="font-weight-bold">CEP *</label>
                                <input type="text" class="form-control" id="cep" name="cep" value=""/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="logradouro" class="font-weight-bold">Logradouro *</label>
                                <input type="text" class="form-control" id="logradouro" name="logradouro" value=""/>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="numero" class="font-weight-bold">Nº</label>
                                <input type="text" class="form-control" id="numero" name="numero" value=""/>
                            </div>
                            <div class="form-group col-md-2 mt30">
                                <div class="checkbox icheck">
                                    <input type="checkbox" class="form-control" id="s_n" name="s_n" value="1">
                                </div>
                                <label for="s_n" class="font-weight-bold">S/N</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label for="bairro" class="font-weight-bold">Bairro *</label>
                                <input type="text" class="form-control" id="bairro" name="bairro" value=""/>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="cidade" class="font-weight-bold">Cidade *</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value=""/>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="uf" class="font-weight-bold">UF *</label>
                                <input type="text" class="form-control" id="uf" name="uf" value=""/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="email" class="font-weight-bold">Email *</label>
                                <input type="text" class="form-control" id="email" name="email" value=""/>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="telefone" class="font-weight-bold">Telefone *</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value=""/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="font-weight-bold" for="emitente">Logomarca </label>
                                <input id="userfile" class="form-control" type="file" name="userfile" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times fa-fw"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php } else { ?>
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h3>
                <i class="fas fa-id-card fa-lg fa-fw"></i>
                Dados do Emitente
            </h3>
            <div class="panel-ctrls">
                <a href="#modalAlterar" data-toggle="modal" class="btn btn-primary btn-sm" title="Alterar dados de emitente">
                    <i class="fas fa-edit fa-fw"></i> Alterar Dados
                </a>
                <a href="#modalLogo" data-toggle="modal" class="btn btn-primary btn-sm" title="Alterar logotipo">
                    <i class="fas fa-image fa-fw"></i> Alterar Logo
                </a>
            </div>
        </div>
        <div class="panel-body panel-no-padding">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="width: 30%; padding: 15px">
                            <?php if ($dados->logomarca) { ?>
                                <img style="width: 150px" src="<?php echo base_url() . 'assets/uploads/logomarcas/' . $dados->logomarca; ?>">
                            <?php } else { ?>
                                <p class="alert alert-inverse">
                                    Nenhuma logomarca cadastrada
                                </p>
                            <?php } ?>
                        </td>
                        <td style="padding: 15px">
                            <span style="font-size: 18px; "><?php echo $dados->emitente; ?></span>
                            <br>
                            <span>CNPJ: <?php echo $dados->cnpj; ?></span>
                            <br>
                            <span><?php echo $dados->logradouro . ', '
                                    . ($dados->s_n == 1 ? 'S/N' : $dados->numero)
                                    . ($dados->complemento == null ? : ', '.$dados->complemento); ?>
                            </span>
                            <br>
                            <span><?php echo $dados->bairro . ' - '
                                    . $dados->cidade . ' / '
                                    . $dados->uf; ?>
                            </span>
                            <br>
                            <span> Email: <?php echo $dados->email . ' - Fone: ' . $dados->telefone; ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!--            </div>-->
                <!--            <div class="panel-footer">-->
                <p class="alert alert-info">
                    <i class="fas fa-info-circle fa-lg fa-fw"></i>
                    Os dados acima serão utilizados no cabeçalho das telas de impressão.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal ALTERAR DADOS EMITENTE -->
    <div class="modal fade" id="modalAlterar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white ">Editar dados do emitente</h4>
                </div>
                <form id="formAlterar" action="<?php echo base_url() ?>mxcode/editarEmitente" method="post" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="font-weight-bold" for="emitente">Razão Social *</label>
                                <input id="emitente" class="form-control" type="text" name="emitente" value="<?php echo $dados->emitente; ?>"/>
                                <input id="id_emitente" type="hidden" name="id_emitente" value="<?php echo $dados->id_emitente; ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label for="cnpj" class="font-weight-bold">CNPJ *</label>
                                <input class="form-control" type="text" id="cnpj" name="cnpj" value="<?php echo $dados->cnpj; ?>"/>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ie" class="font-weight-bold">IE </label>
                                <input type="text" class="form-control" id="ie" name="ie" value="<?php echo $dados->ie; ?>"/>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="cep" class="font-weight-bold">CEP *</label>
                                <input type="text" class="form-control" id="cep" name="cep" value="<?php echo $dados->cep; ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="logradouro" class="font-weight-bold">Logradouro *</label>
                                <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?php echo $dados->logradouro; ?>"/>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="numero" class="font-weight-bold">Nº</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $dados->numero; ?>"/>
                            </div>
                            <div class="form-group col-md-2 mt30">
                                <div class="checkbox icheck">
                                    <input type="checkbox" class="form-control" id="s_n" name="s_n" value="1" <?= $dados->s_n == 1 ? 'checked' : '' ?>>
                                </div>
                                <label for="s_n" class="font-weight-bold">S/N</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12" id="div_complemento">
                                <label for="complemento" class="font-weight-bold">Complemento</label>
                                <input type="text" class="form-control" id="complemento" name="complemento" value="<?php echo $dados->complemento; ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label for="bairro" class="font-weight-bold">Bairro *</label>
                                <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo $dados->bairro; ?>"/>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="cidade" class="font-weight-bold">Cidade *</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo $dados->cidade; ?>"/>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="uf" class="font-weight-bold">UF *</label>
                                <input type="text" class="form-control" id="uf" name="uf" value="<?php echo $dados->uf; ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="email" class="font-weight-bold">Email *</label>
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $dados->email; ?>"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="telefone" class="font-weight-bold">Telefone *</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $dados->telefone; ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times fa-fw"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal ALTERAR LOGO-->
    <div class="modal fade" id="modalLogo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white">Alterar logomarca</h4>
                </div>
                <form action="<?php echo base_url(); ?>mxcode/editarLogo" id="formLogo" enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <p>Selecione uma imagem para a logomarca.</p>
                        <input id="id_emitente" type="hidden" name="id_emitente" value="<?php echo $dados->id_emitente; ?>"/>
                        <input id="url" type="hidden" name="urlAtual" value=""/>
                        <div class="row">
                            <div class="form-group col-lg-7">
                                <label class="font-weight-bold control-label" for="data_pagamento"><span>Logotipo *</span></label>
                                <input type="file" class="btn btn-default form-control" name="userfile" value=""/>
                            </div>
                        </div>
                        <span class="badge badge-info">Tamanho máximo permitido para upload: 2 MB</span>
                    </div>
                    <div class="modal-footer">
                        <?php if ($dados->logomarca) { ?>
                            <a href="#modalExcluirLogo" data-toggle="modal" class="btn btn-danger btn-sm pull-left">
                                <i class="fas fa-trash-alt fa-fw"></i> Excluir Logo
                            </a>
                        <?php } ?>
                        <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times fa-fw"></i> Cancelar
                        </button>
                        <button class="btn btn-primary btn-sm">
                            <i class="fa fa-check fa-fw"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal EXCLUIR LOGO-->
    <div class="modal fade" id="modalExcluirLogo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-white">Excluir logomarca</h4>
                </div>
                <form action="<?php echo base_url(); ?>mxcode/excluirLogo" id="formLogo" enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <p>Deseja realmente excluir a logomarca cadastrada?</p>
                        <input id="id_emitente" type="hidden" name="id_emitente" value="<?php echo $dados->id_emitente; ?>"/>
                        <input id="url" type="hidden" name="urlAtual" value=""/>
                        <p class="alert alert-danger">
                            <i class="fa fa-exclamation-circle fa-lg fa-fw"></i>
                            ATENÇÃO: esta ação não poderá ser desfeita!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times fa-fw"></i> Cancelar
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <i class="fa fa-check fa-fw"></i> Excluir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">

    $(document).ready(function () {

        $('#s_n').on('ifChanged', function (event) {
            const checked = event.target.checked;
            if (checked == true) {
                $('#numero').attr('disabled', true);
                $('#numero').val('');
            } else {
                $('#numero').removeAttr('disabled');
            }
        });

        var s_n = $('#s_n').iCheck('update')[0].checked;
        $.each($(s_n), function (key, value) {
            if (s_n == true) {
                $('#numero').attr('disabled', true);
                $('#numero').val('');
            } else {
                $('#numero').removeAttr('disabled');
            }
        });

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
