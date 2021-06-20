<div class="row">
    <div class="col-md-12">
        <div class="panel panel-midnightblue">
            <div class="panel-heading">
                <h3>
                    <i class="fas fa-bell fa-lg fa-fw"></i>
                    Notificações do Sistema
                </h3>
                <?php if (getUserPermission() == 1) { ?>
                    <div class="pull-right">
                        <button href="#modalNovaNotificacao" role="button" data-toggle="modal" class="btn btn-primary btn-sm" type="button">
                            <i class="fas fa-plus fa-fw"></i> Nova Notificação
                        </button>
                    </div>
                <?php } ?>
            </div>
            <div class="panel-body panel-no-padding">
                <?php if ($notificacoes != null) {
                    foreach ($notificacoes as $notificacao) {
                        switch ($notificacao->prioridade) {
                            case 2:
                                $prioridade = 'MÉDIA';
                                $corPrioridade = 'primary';
                                break;
                            case 3:
                                $prioridade = 'ALTA';
                                $corPrioridade = 'warning';
                                break;
                            case 4:
                                $prioridade = 'URGENTE';
                                $corPrioridade = 'danger';
                                break;
                            default:
                                $prioridade = 'NORMAL';
                                $corPrioridade = 'info';
                                break;
                        }

                        if ($notificacao->lida == 0) {
//                            $cor = 'background-color: #00c5de2e;';
                        } else {
                            $cor = '';
                        }

                        $date_time = new DateTime($notificacao->data_abertura);
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
                            <li style="<?= $cor ?>">
                                <a href="<?= base_url($notificacao->link) ?>" onclick="lerNotificacao(<?= $notificacao->id ?>)" class="mailbox-msg-list-item">
                                    <span class="time"><?= ($intervalo) ?>
                                    <small style="display: block;" class="label label-<?= ($corPrioridade) ?>"><?= ($prioridade) ?></small>
                                    </span>
                                    <span class="icon"><i class="<?= $notificacao->icone ?>"></i></span>
                                    <div>
                                        <span class="name"><?= capsLock($notificacao->titulo) ?></span>
                                        <span class="msg"><?= $notificacao->descricao ?></span>
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
                                    <span class="name ml20">Nenhuma notificação encontrada</span>
                                    <?php if (getUserPermission() == 1) { ?>
                                        <span class="msg ml20">Clique no botão acima para registrar uma nova notificação</span>
                                    <?php } ?>
                                </div>
                            </a>
                        </li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal NOVA NOTIFICAÇÃO-->
<div class="modal fade" id="modalNovaNotificacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Adicionar nova notificação</h4>
            </div>
            <form id="formNova" action="<?php echo base_url('notificacoes/adicionar') ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="titulo">Título *</label>
                            <input class="form-control" id="titulo" type="text" name="titulo" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="font-weight-bold" for="descricao">Descrição *</label>
                            <input class="form-control" id="descricao" type="text" name="descricao" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="link">Link</label>
                            <input class="form-control" id="link" type="text" name="link" value=""/>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="prioridade">Prioridade</label>
                            <select class="form-control" name="prioridade">
                                <option value="1">NORMAL</option>
                                <option value="2">MÉDIA</option>
                                <option value="3">ALTA</option>
                                <option value="4">URGENTE</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label class="font-weight-bold" for="icone">Ícone</label>
                            <input class="form-control" id="icone" type="text" name="icone" value=""/>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="nome_usuario" class="font-weight-bold">Nome do usuário</label>
                            <input class="form-control" id="nome_usuario" type="text" name="nome_usuario"/>
                            <span style="color: red" class="font-weight-bold hidden" id="icon_wait">
                                        <i class="fas fa-spinner fa-fw fa-pulse"></i> Buscando, aguarde...
                                    </span>
                            <input id="id_usuario" type="hidden" name="id_usuario"/>
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

<!-- Modal EXCLUIR-->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-white ">Excluir opção de configuração</h4>
            </div>
            <form action="<?php echo base_url('configuracoes/excluir') ?>" method="post">
                <div class="modal-body">
                    <p>Deseja realmente excluir esta opção de configuração?</p>
                    <span class="note note-danger block font-weight-bold">Atenção! Isto irá excluir permanentemente este registro.</span>
                    <input type="hidden" id="id_excluir" name="id_opcao" value=""/>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times fa-fw"></i>
                        Cancelar
                    </button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt fa-fw"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getVersion() {
        const Toast = Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 5000,
            timerProgressBar: true,
            icon: 'info',
            title: 'Versão do Sistema: <?= versionApp() ?>',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    }

    $(document).ready(function () {

        $("#nome_usuario").autocomplete({
            source: "<?php echo base_url('anuncios/autoCompleteUsuario'); ?>",
            minLength: 1,
            select: function (event, ui) {
                $("#id_usuario").val(ui.item.id);
            }
        });

        $('.excluir').click(function () {
            let id_opcao = $(this).attr('id_opcao');

            $('#id_excluir').val(id_opcao);
        })

        $('.editar').click(function () {
            let descricao = $(this).attr('descricao');
            let setor = $(this).attr('setor');
            let id_opcao = $(this).attr('id_opcao');

            $('#descricao').val(descricao);
            $('#setor').val(setor);
            $('#id_opcao').val(id_opcao);
        })

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

        $("#formNova").validate({
            rules: {
                descricao: {required: true},
                titulo: {required: true},
            },
            messages: {
                titulo: {required: 'Informe o título da notificação'},
                descricao: {required: 'Informe a descrição da notificação'},
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

        $("#formEditar").validate({
            rules: {
                descricao: {required: true},
                setor: {required: true},
            },
            messages: {
                descricao: {required: 'Informe a descrição da opção'},
                setor: {required: 'Selecione o setor'},
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
