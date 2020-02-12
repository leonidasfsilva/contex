<?php
$status_chamado = $this->chamados_model->getStatusChamado($chamado->id_chamado);
switch ($status_chamado) {
    case 1:
        $status = 'aberto';
        $cor_status = 'danger';
        break;
    case 2:
        $status = 'fechado';
        $cor_status = 'inverse';
        break;
    case 3:
        $status = 'finalizado';
        $cor_status = 'success';
        break;
} ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-midnightblue" id="painel_chamado">
            <div class="panel-heading">
                <h2 class="pull-left mt0 mb0">
                    Chamado: # <?= $chamado->id_chamado . ' - ' . $chamado->assunto ?>
                </h2>
                <div class="panel-ctrls">
                    <a href="<?= base_url('chamados') ?>" class="btn btn-default btn-sm"><i class="fas fa-arrow-left fa-fw"></i> Chamados</a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-cog fa-fw"></i> Ações <i class="fa fa-angle-down fa-sm "></i>
                        </button>
                        <ul class="dropdown-menu">
                            <!--                            <li><a href="#modalResolverChamado" data-toggle="modal"><i class="fas fa-check-circle fa-fw"></i> Marcar como Resolvido</a></li>-->
                            <li><a href="#modalFinalizarChamado" data-toggle="modal"><i class="fas fa-minus-circle fa-fw"></i> Finalizar Chamado</a></li>
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
                <div class="pull-right">
                    <strong>Status: <span class="ml10 pull-right label label-<?= ($cor_status) ?>"> <?= ($status) ?></span></strong>
                </div>
                <section class="tabular">
                    <div class="message tabular-row">
                        <div class="tabular-cell avatar">
                            <img src="<?php echo $this->chamados_model->getAvatarUsuario($chamado->id_usuario) != null ? base_url() . 'assets/uploads/avatars/' .
                                $this->chamados_model->getAvatarUsuario($chamado->id_usuario) : base_url() . 'assets/img/avatars/padrao.png'; ?>"
                                 alt="avatar" class="">
                        </div>
                        <div class="tabular-cell msg">
                            <p class="msgee"><?= $this->chamados_model->getNomeUsuario($chamado->id_usuario) ?></p>
                            <p class="panel chamado p10"><?= nl2br($chamado->descricao) ?></p>
                            <!--                            <div class="well mb0 attachment">-->
                            <!--                                <div class="clearfix">-->
                            <!--									<span class="pull-left">-->
                            <!--										<i class="fa fa-paperclip"></i>&nbsp;-->
                            <!--										<a href="#">screenshot.jpg</a>-->
                            <!--									</span>-->
                            <!--                                    <span class="pull-right">-->
                            <!--										<a href="#" class="btn btn-xs btn-default">Open</a>-->
                            <!--										<a href="#" class="btn btn-xs btn-primary">Save</a>-->
                            <!--									</span>-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                        </div>
                        <div class="tabular-cell time">
                            <small>
                                <?= $intervalo ?>
                            </small>
                        </div>
                    </div>

                    <!--                    RESPOSTAS-->
                    <?php if ($respostas) {
                        foreach ($respostas as $r) {

                            $date_time = new DateTime($r->data_resposta);
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
                            <div class="message tabular-row">
                                <div class="tabular-cell avatar">
                                    <img src="<?php echo $this->chamados_model->getAvatarUsuario($r->id_usuario) != null ? base_url() . 'assets/uploads/avatars/' .
                                        $this->chamados_model->getAvatarUsuario($r->id_usuario) : base_url() . 'assets/img/avatars/padrao.png'; ?>"
                                         alt="avatar" class="">
                                </div>
                                <div class="tabular-cell msg">
                                    <p class="msgee"><?= $this->chamados_model->getNomeUsuario($r->id_usuario) ?></p>
                                    <p class="panel chamado p10"><?= nl2br($r->resposta) ?></p>
                                </div>
                                <div class="tabular-cell time">
                                    <small><?= $intervalo ?></small>
                                </div>
                            </div>
                        <?php }
                    } ?>
                </section>
                <div class="panel-footer">
                    <form id="formResponderChamado" action="<?php echo base_url('chamados/responder') ?>" method="post" autocomplete="off">
                        <div class="form-group mb0">
                            <div class="input-icon right">
                                <i class="fas fa-fw fa-times-circle tooltips validate-error hidden" data-original-title="Preencha este campo!" data-container="body"></i>
                                <i class="fas fa-fw fa-check-circle tooltips validate-success hidden" data-original-title="Tudo certo!" data-container="body"></i>
                                <textarea class="form-control" name="resposta" rows="4" placeholder="Escreva uma resposta" style="resize: none; "></textarea>
                                <input type="hidden" name="id_chamado" value="<?= $chamado->id_chamado ?>">
                            </div>
                        </div>
                        <div class="msg-composer">
                            <div class="pull-left">
                                <div class="fileinput fileinput-new" data-provides="fileinput" style="cursor: pointer !important;">
                                    <span class="btn btn-default btn-file" style="cursor: pointer !important;">
                                        <span class="fileinput-new"><i class="fas fa-paperclip fa-fw"></i> Anexar</span>
                                        <span class="fileinput-exists"><i class="fas fa-refresh fa-fw"></i> Alterar</span>
                                        <input name="arquivo" type="file" style="cursor: pointer !important;">
                                    </span>
                                    <span class="fileinput-filename"></span>
                                    <a class="close fileinput-exists" data-dismiss="fileinput" href="#" style="float: none">&times;</a>
                                </div>
                                <!--                            <a href="#" class="btn btn-default"><i class="fa fa-camera"></i> Add Photos</a>-->
                            </div>
                            <div class="pull-right clearfix">
                                <button type="submit" class="btn btn-primary send-btn pull-right">
                                    <i class="fas fa-send fa-fw"></i> Enviar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal RESOLVER CHAMADO-->
<div class="modal fade" id="modalResolverChamado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Chamado resolvido</h4>
            </div>
            <form id="formPagar" action="<?php echo base_url('chamados/resolver') ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p>Deseja realmente marcar este chamado como RESOLVIDO?</p>
                    <input id="id_chamado" type="hidden" name="id_chamado" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check fa-fw"></i> Resolvido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal FINALIZAR CHAMADO-->
<div class="modal fade" id="modalFinalizarChamado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white">Finalizar chamado</h4>
            </div>
            <form id="formPagar" action="<?php echo base_url('chamados/finalizar/' . $chamado->id_chamado) ?>" method="post" autocomplete="off">
                <div class="modal-body">
                    <p>Deseja realmente finalizar este chamado?</p>
                    <input id="id_chamado" type="hidden" name="id_chamado" value="<?= $chamado->id_chamado ?>"/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times fa-fw"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check fa-fw"></i> Finalizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#formResponderChamado').validate({
        rules: {
            resposta: {required: true},
        },
        messages: {
            resposta: {required: 'Digite uma resposta.'},
        },

        errorClass: "help-block",
        errorElement: "p",
        highlight: function (element, errorClass, validClass) {
            console.log(element)
            $(element).parents('.form-group').addClass('has-error');
            $('.validate-error').removeClass('hidden');
            $('.validate-success').addClass('hidden');
            $(element).parents('.form-group').removeClass('has-success');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parents('.form-group').removeClass('has-error');
            $('.validate-error').addClass('hidden');
            $('.validate-success').removeClass('hidden');
            $(element).parents('.form-group').addClass('has-success');
        }
    });
</script>


