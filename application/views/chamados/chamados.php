<div class="row">
    <div class="col-md-12">
        <div class="panel panel-midnightblue">
            <div class="panel-heading">
                <div class="col-md-4 col-xs-7 pull-left pl0">
                    <div class="input-icon right">
                        <i class="fas fa-fw fa-search" style="cursor: pointer"></i>
                        <input type="text" class="form-control mt10" placeholder="Pesquisar chamados" name="pesquisa">
                    </div>
                </div>
                <div class="pull-right pr0">
                    <button type="button" class="btn btn-primary" data-toggle="modal" href="#modalAbrirChamado">
                        <i class="fas fa-plus fa-fw"></i> Abrir Chamado
                    </button>
                </div>
            </div>
            <div class="panel-body panel-no-padding">
                <?php if ($chamados != null) {
                    foreach ($chamados as $c) {

                        $date_time = new DateTime($c->data_abertura);
                        $hoje = new DateTime('now');
                        $interval = $hoje->diff($date_time);
                        $dataformatada = $date_time->format('d/m/Y H:i:s');

                        if ($interval->m < 1) {
                            if ($interval->d < 2) {
                                if ($interval->h < 1) {
                                    if ($interval->i < 01) {
                                        $intervalo = $interval->s . 's';
                                    } else {
                                        $intervalo = $interval->i . 'm';
                                    }
                                } else {
                                    $intervalo = $interval->h . 'h';
                                }
                            } else {
                                $intervalo = $interval->d . 'd';
                            }
                        } else {
                            $intervalo = $dataformatada;
                        } ?>
                        <ul class="mailbox-msg-list">
                            <li>
                                <a href="<?= base_url('chamados/detalhes/' . $c->id_chamado) ?>" class="mailbox-msg-list-item">
                                    <span class="time"><?= ($intervalo) ?></span>
                                    <img src="<?php echo $this->chamados_model->getAvatarUsuario($c->id_usuario) != null ? base_url() . 'assets/uploads/avatars/' .
                                        $this->chamados_model->getAvatarUsuario($c->id_usuario) : base_url() . 'assets/img/avatars/padrao.png'; ?>"
                                         alt="avatar" title="" style="">
                                    <div>
                                        <span class="name"><?= $this->chamados_model->getNomeUsuario($c->id_usuario) ?></span>
                                        <span class="msg"><strong>(<?= $c->assunto ?>)</strong> - <?= $c->descricao ?></span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    <?php }
                } else { ?>
                    <ul class="mailbox-msg-list">
                        <li>
                            <a href="javascript:" class="mailbox-msg-list-item" style="cursor: unset;">
                                <!--                                <span class="time">30s</span>-->
                                <!--                                <img src="assets/demo/avatar/avatar_09.png" alt="avatar" title="" style="">-->
                                <div>
                                    <span class="name">Nenhum chamado encontrado.</span>
                                    <span class="msg">Clique no botão acima para abrir um novo chamado.</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="panel panel-midnightblue" id="painel_chamado" hidden>
            <div class="panel-heading">
                <h2 class="pull-left mt0 mb0">Chamado ID: # 37</h2>
                <div class="panel-ctrls">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-cog fa-fw"></i> Ações <i class="fa fa-angle-down fa-sm "></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="fas fa-check-circle fa-fw"></i> Marcar como Resolvido</a></li>
                            <li><a href="#"><i class="fas fa-trash-alt fa-fw"></i> Excluir Chamado</a></li>
                        </ul>
                    </div>
                    <a href="#" class="button-icon close-panel">
                        <i class="fas fa-times"></i>
                    </a>
                    <a href="#" class="button-icon expand">
                        <i class="fas expand-icon"></i>
                    </a>
                    <a href="#" class="button-icon panel-collapse">
                        <i class="fas fa-minus"></i>
                    </a>
                </div>
            </div>
            <div class="panel-body mailbox-panel">
                <section class="tabular">
                    <div class="message tabular-row">
                        <div class="tabular-cell avatar">
                            <img src="assets/demo/avatar/avatar_09.png" alt="avatar" class="">
                        </div>
                        <div class="tabular-cell msg">
                            <a href="#" class="msgee">Kenneth Ross</a>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ullam odit odio soluta numquam aliquam tempore eius dolor hic!</p>
                            <div class="well mb0 attachment">
                                <div class="clearfix">
									<span class="pull-left">
										<i class="fa fa-paperclip"></i>&nbsp;
										<a href="#">screenshot.jpg</a>
									</span>
                                    <span class="pull-right">
										<a href="#" class="btn btn-xs btn-default">Open</a>
										<a href="#" class="btn btn-xs btn-primary">Save</a>
									</span>
                                </div>
                            </div>
                        </div>
                        <div class="tabular-cell time">
                            <small>2h</small>
                        </div>
                    </div>

                    <div class="message tabular-row">
                        <div class="tabular-cell avatar">
                            <img src="assets/demo/avatar/avatar_10.png" alt="avatar"/>
                        </div>
                        <div class="tabular-cell msg">
                            <a href="#" class="msgee">Skyler Freeman</a>
                            <p>Ullam odit odio soluta numquam aliquam tempore eius dolor hic! Illo, animi, debitis, earum, perferendis voluptate fuga velit rem neque temporibus
                                iure praesentium quas eos eum aliquam odio nihil porro. Adipisci, sit.</p>
                            <p>Provident, omnis, aliquam culpa odit fuga maiores corrupti atque explicabo quis id cumque perspiciatis voluptates labore! Nostrum modi voluptatem
                                qui ipsa accusamus!</p>
                        </div>
                        <div class="tabular-cell time">
                            <small>11h</small>
                        </div>
                    </div>
                    <div class="message tabular-row">
                        <div class="tabular-cell avatar">
                            <img src="assets/demo/avatar/avatar_09.png" alt="avatar" class="">
                        </div>
                        <div class="tabular-cell msg">
                            <a href="#" class="msgee">Kenneth Ross</a>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, sunt!</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente, ipsa molestiae modi nisi assumenda accusamus nihil tempora accusantium. Iste,
                                accusantium, alias, blanditiis eius consequuntur facilis ut eaque modi maxime similique ratione ipsum beatae. Magni, consequuntur, labore
                                molestias quidem ab animi unde delectus tenetur nihil neque maxime recusandae sunt nulla saepe.</p>
                        </div>
                        <div class="tabular-cell time">
                            <small>15h</small>
                        </div>
                    </div>
                </section>
                <div class="panel-footer">
                    <textarea class="form-control" rows="4" placeholder="Escreva uma resposta" style="resize: none; "></textarea>
                    <div class="msg-composer">
                        <div class="pull-left">
                            <button type="button" class="btn btn-default"><i class="fas fa-paperclip fa-fw"></i> Anexar</button>
                            <!--                            <a href="#" class="btn btn-default"><i class="fa fa-camera"></i> Add Photos</a>-->
                        </div>
                        <div class="pull-right clearfix">
                            <a href="#" class="btn btn-primary send-btn pull-right"><i class="fas fa-send fa-fw"></i> Enviar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal ABRIR CHAMADO -->
<div class="modal fade" id="modalAbrirChamado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Abrir novo chamado</h4>
            </div>
            <form id="formAbrirChamado" action="<?php echo base_url() ?>chamados/abrirChamado" method="post" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="assunto" class="font-weight-bold">Assunto *</label>
                            <select name="assunto" id="assunto" class="form-control">
                                <option value=""><< Selecione >></option>
                                <?php if ($assuntos) {
                                    foreach ($assuntos as $a) { ?>
                                        <option value="<?= $a->assunto ?>"><?= $a->assunto ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="cpf" class="font-weight-bold">Descrição *</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelLancamento" class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check fa-fw"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#formAbrirChamado').validate({
        rules: {
            assunto: {required: true},
            descricao: {required: true},
        },
        messages: {
            assunto: {required: 'Selecione o assunto'},
            descricao: {required: 'Descreva o ocorrido'},
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

</script>


