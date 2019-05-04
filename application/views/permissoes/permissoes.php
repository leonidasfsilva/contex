<label>&nbsp;</label>
<a href="<?php echo base_url(); ?>index.php/permissoes/adicionar" class="btn btn-primary btn-sm">
    <i class="fa fa-plus-square fa-fw"></i> Adicionar Permissão</a>
<?php
if (!$results) { ?>
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon">
                <i class="fa fa-lock fa-lg fa-fw"></i>
            </span>
            <h5>Permissões</h5>
        </div>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Data de Criação</th>
                    <th>Situação</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5">Nenhuma Permissão foi cadastrada</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php } else {


    ?>
    <div class="widget-box">
        <div class="widget-title">
        <span class="icon">
                <i class="fa fa-lock fa-lg fa-fw"></i>
         </span>
            <h5>Permissões</h5>
        </div>
        <div class="widget-content nopadding">
            <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Data de Criação</th>
                    <th>Situação</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $r) {
                    if ($r->situacao == 1) {
                        $situacao = 'Ativo';

                    } else {
                        $situacao = 'Inativo';
                    }
                    echo '<tr>';
                    echo '<td>' . $r->idPermissao . '</td>';
                    echo '<td>' . $r->nome . '</td>';
                    echo '<td>' . date('d/m/Y', strtotime($r->data)) . '</td>';
                    echo '<td>' . $situacao . '</td>';
                    echo '<td>
                      <a href="' . base_url() . 'index.php/permissoes/editar/' . $r->idPermissao . '" class="btn btn-primary btn-xs tip-top" title="Editar Permissão"><i class="fa fa-edit fa-lg fa-fw"></i></a>
                      <a href="#modal-desativar" role="button" data-toggle="modal" permissao="' . $r->idPermissao . '" class="btn btn-inverse btn-xs tip-top desativar" title="Desativar Permissão"><i class="fa fa-power-off fa-lg fa-fw"></i></a>
                      <a href="#modal-excluir" role="button" data-toggle="modal" permissao="' . $r->idPermissao . '" class="btn btn-danger btn-xs tip-top excluir" title="Excluir Permissão"><i class="fa fa-trash-o fa-lg fa-fw"></i></a>
                  </td>';
                    echo '</tr>';
                } ?>
                <tr>

                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->pagination->create_links();
} ?>


<!-- Modal DESATIVAR-->
<div id="modal-desativar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/permissoes/desativar" method="post">
        <div class="modal-header bg_inverse">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">DESATIVAR PERMISSÃO</h3>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_permissao_desativar" name="id" value=""/>
            <span style="font-size: 11pt">Deseja realmente desativar esta permissão?</span>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-fw"></i> Cancelar</button>
            <button class="btn btn-inverse btn-sm"><i class="fa fa-check fa-fw"></i> Desativar</button>
        </div>
    </form>
</div>

<!-- Modal EXCLUIR-->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/permissoes/excluir" method="post">
        <div class="modal-header bg_danger">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">EXCLUIR PERMISSÃO</h3>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_permissao_excluir" name="id" value=""/>
            <span style="font-size: 11pt">Deseja realmente excluir esta permissão?</span>
            <h5 style="font-size: 11pt">Atenção! Esta ação não poderá ser desfeita.</h5>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default btn-sm" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-fw"></i> Cancelar</button>
            <button class="btn btn-danger btn-sm"><i class="fa fa-check fa-fw"></i> Excluir</button>
        </div>
    </form>
</div>


<script type="text/javascript">
    $(document).ready(function () {


        $(document).on('click', '.desativar', function (event) {
            var permissao = $(this).attr('permissao');
            $('#id_permissao_desativar').val(permissao);
        });
        $(document).on('click', '.excluir', function (event) {
            var permissao = $(this).attr('permissao');
            $('#id_permissao_excluir').val(permissao);
        });

    });

</script>
