<div class="row">
    <div class="col-md-12">
        <div class="panel panel-midnightblue">
            <div class="panel-heading">
                <h3>
                    <i class="fas fa-bullhorn fa-lg fa-fw"></i>
                    Montar Novo Anúncio
                </h3>
                <div class="panel-ctrls">
                    <a href="<?php echo base_url('anuncios') ?>" class="btn btn-sm btn-default">
                        <i class="fas fa-arrow-left fa-fw"></i> Anúncios
                    </a>
                    <button type="button" id="submit" class="btn btn-primary btn-sm"><i class="fas fa-check fa-fw"></i> Cadastrar</button>
                </div>
            </div>
            <div class="panel-body">
                <div class="container-fluid ">
                    <div class="row border-bottom">
                        <h5 class="mt0 pt0">Opções do anúncio</h5>
                    </div>
                </div>
                <form id="formCadastroAnuncio" action="<?php echo base_url('anuncios/adicionar') ?>" method="post" autocomplete="off">
                    <div class="row mb30">
                        <div class="form-group col-sm-3 pb20">
                            <div class="checkbox icheck">
                                <input type="checkbox" class="form-control" id="rodape" name="exibir_rodape" value="1" checked>
                            </div>
                            <label for="rodape" class="font-weight-bold">Exibir rodapé do modal</label>
                        </div>
                        <div id="div_master_rotulo">
                            <div class="form-group col-sm-3">
                                <div id="div_botao_link">
                                    <div class="checkbox icheck">
                                        <input type="checkbox" class="form-control" name="exibir_botao" value="1" id="botao" checked>
                                    </div>
                                    <label for="botao" class="font-weight-bold">Exibir botão de link</label>
                                </div>
                            </div>
                            <div id="div_rotulo">
                                <div class="form-group col-sm-3 mt10">
                                    <label for="rotulo_botao" class="font-weight-bold">Rótulo do botão *</label>
                                    <input class="form-control" type="text" id="rotulo_botao" name="rotulo_botao" placeholder="Botão de link">
                                </div>
                                <div class="form-group col-sm-3 mt10">
                                    <label for="link_botao" class="font-weight-bold">Link do botão *</label>
                                    <input class="form-control" type="text" id="link_botao" name="link_botao" placeholder="Link do botão">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="estilo" class="font-weight-bold">Estilo do modal</label>
                            <select name="estilo" id="estilo" class="form-control">
                                <option value="bg-default">DEFAULT</option>
                                <option value="bg-primary" class="label-primary">PRIMARY</option>
                                <option value="bg-info" class="label-info">INFO</option>
                                <option value="bg-success" class="label-success">SUCCESS</option>
                                <option value="bg-warning" class="label-warning">WARNING</option>
                                <option value="bg-danger" class="label-danger">DANGER</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="cabecalho" class="font-weight-bold">Cabeçalho do modal</label>
                            <input class="form-control" type="text" id="cabecalho" name="cabecalho">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="titulo" class="font-weight-bold">Título do anúncio</label>
                            <input class="form-control" type="text" id="titulo" name="titulo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="descricao" class="font-weight-bold">Descrição do anúncio *</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="5"></textarea>
                        </div>
                    </div>
                </form>

                <h4 class="mt0">Pré-visualização do anúncio</h4>
                <!--                <p>Informe os dados do anúncio nos campos correspondentes abaixo.</p>-->
                <div class="row">
                    <div class="modal visiblemodal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="cabecalho_modal">Cabeçalho do modal</h4>
                                </div>
                                <div class="modal-body">
                                    <h5 class="mt0 pt0" id="titulo_anuncio">Título do anúncio</h5>
                                    <p class="p0" style="font-size: 13px; border: none; background-color: unset; font-family:'Roboto', sans-serif" id="descricao_anuncio">Descrição do anúncio</p>
                                </div>
                                <div class="modal-footer" id="div_rodape">
                                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                                        <i class="fas fa-times fa-fw"></i> Fechar
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm" id="botao_link"> Botão de link</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function nl2br (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
    }

    $('#estilo').change(function () {
        if($(this).val() != 'bg-default') {
            $('.modal-header').attr('class','modal-header text-white ' + $(this).val());
            $('.modal-title').attr('class','modal-title text-white');
            $('#botao_link').attr('class','btn btn-sm text-white ' + $(this).val());
        } else {
            $('.modal-header').attr('class','modal-header ' + $(this).val());
            $('.modal-title').attr('class','modal-title');
            $('#botao_link').attr('class','btn btn-sm btn-default ' + $(this).val());
        }
    });

    $("#formCadastroAnuncio").validate({
        rules: {
            descricao: {required: true},
            rotulo_botao: {required: true},
            link_botao: {required: true},

        },
        messages: {
            descricao: {required: 'Informe a descrição'},
            rotulo_botao: {required: 'Informe o rótulo'},
            link_botao: {required: 'Informe o link'},
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

    $('#submit').click(function () {
        $('#formCadastroAnuncio').submit();
    });

    $('#rodape').on('ifChanged', function (event) {
        const checked = event.target.checked;
        if (checked == true) {
            $('#div_rodape').removeClass('hidden');
            // $('#div_botao_link').removeClass('hidden');
            $('#div_master_rotulo').removeClass('hidden');
        } else {
            $('#div_rodape').addClass('hidden');
            // $('#div_botao_link').addClass('hidden');
            $('#div_master_rotulo').addClass('hidden');
        }
    });

    $('#botao').on('ifChanged', function (event) {
        const checked = event.target.checked;
        if (checked == true) {
            $('#div_rotulo').removeClass('hidden');
            $('#botao_link').removeClass('hidden');
        } else {
            $('#div_rotulo').addClass('hidden');
            $('#botao_link').addClass('hidden');
        }
    });

    $('#rotulo_botao').keyup(function () {
        if ($(this).val() == '') {
            $('#botao_link').text('Botão de link')
        } else {
            $('#botao_link').text($(this).val())
        }
    });

    $('#cabecalho').keyup(function () {
        if ($(this).val() == '') {
            $('#cabecalho_modal').text('CABEÇALHO DO MODAL')
        } else {
            $('#cabecalho_modal').text($(this).val())
        }
    });

    $('#titulo').keyup(function () {
        if ($(this).val() == '') {
            $('#titulo_anuncio').text('Título do anúncio')
        } else {
            $('#titulo_anuncio').text($(this).val())
        }
    });

    $('#descricao').keyup(function () {
        if ($(this).val() == '') {
            $('#descricao_anuncio').text('Descrição do anúncio')
        } else {
            $('#descricao_anuncio').html(nl2br($(this).val(), null))
        }
    });
</script>
