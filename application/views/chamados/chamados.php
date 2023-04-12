<div class="row">
    <div class="col-md-12">
        <div class="panel panel-midnightblue">
            <div class="panel-heading">
                <h3>
                    <i class="fas fa-headset fa-lg fa-fw"></i>
                    Chamados de Suporte
                </h3>
                <div class="pull-right pr0">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" href="#modalAbrirChamado">
                        <i class="fas fa-plus fa-fw"></i> Novo Chamado
                    </button>
                </div>
            </div>
            <div class="panel-body panel-no-padding">
                <?php if ($chamados != null) {
                    foreach ($chamados as $c) {

                        $status_chamado = $this->chamados_model->getStatusChamado($c->id_chamado);
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
                        }

                        if ($this->chamados_model->notificacaoChamado($c->id_chamado) > 0) {
                            $notificacao = 'background-color: #00c5de2e;';
                        } else {
                            $notificacao = '';
                        }

                        $date_time = new DateTime($c->data_abertura);
                        $hoje = new DateTime('now');
                        $interval = $hoje->diff($date_time);
                        $dataformatada = $date_time->format('d/m/Y H:i:s');

                        if ($interval->m < 1) {
                            if ($interval->d < 1) {
                                if ($interval->h < 1) {
                                    if ($interval->i < 1) {
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
                            <li style="<?= $notificacao ?>">
                                <a href="<?= base_url('chamados/detalhes/' . $c->id_chamado) ?>" class="mailbox-msg-list-item">
                                    <span class="time"><?= ($intervalo) ?>
                                    <small style="display: block;" class="label label-<?= ($cor_status) ?>"><?= ($status) ?></small>
                                    </span>
                                    <img src="<?php echo $this->chamados_model->getAvatarUsuario($c->id_usuario) != null ? base_url('assets/uploads/avatars/') .
                                        $this->chamados_model->getAvatarUsuario($c->id_usuario) : base_url('assets/img/avatars/padrao.png'); ?>"
                                         alt="avatar" title="">
                                    <div>
                                        <span class="name"><?= $this->chamados_model->getNomeUsuario($c->id_usuario) ?></span>
                                        <span class="msg"><strong><?= '# '.$c->id_chamado .' - '. $c->assunto ?>:</strong> <?= $c->descricao ?></span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    <?php }
                } else { ?>
                    <ul class="mailbox-msg-list">
                        <li>
                            <a href="javascript:" class="mailbox-msg-list-item pl0" style="cursor: unset;">
                                <!--                                <span class="time">30s</span>-->
                                <!--                                <img src="assets/demo/avatar/avatar_09.png" alt="avatar" title="" style="">-->
                                <div>
                                    <span class="name ml20">Nenhum chamado encontrado.</span>
                                    <span class="msg ml20">Clique no botão acima para abrir um novo chamado.</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                <?php } ?>
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
                <h4 class="modal-title text-white ">Abrir novo chamado de suporte</h4>
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


