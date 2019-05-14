<style type="text/css">

    label.error {
        color: #b94a48;
    }

    input.error {
        border-color: #b94a48;
    }

    input.valid {
        border-color: #5bb75b;
    }
</style>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css"/>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fa fa-cart-plus fa-lg fa-fw"></i>
                </span>
                <h5>Cadastro de venda</h5>
            </div>
            <div class="widget-content nopadding">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes da venda</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divCadastrarOs">
                                <?php if ($custom_error == true) { ?>
                                    <div class="span12 alert alert-danger" id="divInfo" style="padding: 1%;">Dados incompletos, verifique os campos
                                        com asterisco ou se selecionou corretamente cliente e responsável.
                                    </div>
                                <?php } ?>
                                <form action="<?php echo current_url(); ?>" method="post" id="formVendas">
                                    <div class="span12" style="padding: 1%">
                                        <div class="span2">
                                            <label for="dataInicial">Data da Venda<span class="required"> *</span></label>
                                            <input id="dataVenda" class="span12 datepicker" type="text" name="dataVenda"
                                                   value="<?php echo date('d/m/Y'); ?>"/>
                                        </div>
                                        <div class="span5">
                                            <label for="cliente">Cliente<span class="required"> *</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value=""/>
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value=""/>
                                        </div>
                                        <div class="span5">
                                            <label for="tecnico">Vendedor<span class="required"> *</span></label>
                                            <input id="tecnico" class="span12" type="text" name="tecnico" value="<?php echo $usuario->nome ?>" readonly/>
                                            <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id" value="<?php echo $usuario->id_usuarios ?>"/>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6 offset3" style="text-align: center">
                                            <a href="<?php echo base_url() ?>index.php/vendas" class="btn btn-default btn-sm"><i
                                                        class="fa fa-arrow-left fa-fw"></i> Voltar</a>
                                            <button class="btn btn-primary btn-sm" id="btnContinuar"><i class="fa fa-arrow-circle-right fa-fw"></i> Prosseguir
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                &nbsp;
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/vendas/autoCompleteCliente",
            minLength: 1,
            select: function (event, ui) {

                $("#clientes_id").val(ui.item.id);

            }
        });

        //$("#tecnico").autocomplete({
        //    source: "<?php //echo base_url(); ?>//index.php/vendas/autoCompleteUsuario",
        //    minLength: 1,
        //    select: function (event, ui) {
        //
        //        $("#usuarios_id").val(ui.item.id);
        //
        //
        //    }
        //});


        $("#formVendas").validate({
            rules: {
                cliente: {required: true},
                tecnico: {required: true},
                dataVenda: {required: true}
            },
            messages: {
                cliente: {required: 'Selecione o cliente'},
                tecnico: {required: 'Selecione o vendedor'},
                dataVenda: {required: 'Informe a data da venda'}
            },

            // errorClass: "help-inline",
            // errorElement: "span",
            // highlight: function (element, errorClass, validClass) {
            //     $(element).parents('.control-group').addClass('error');
            // },
            // unhighlight: function (element, errorClass, validClass) {
            //     $(element).parents('.control-group').removeClass('error');
            //     $(element).parents('.control-group').addClass('success');
            // }
        });

        $(".datepicker").datepicker({dateFormat: 'dd/mm/yy'});

    });

</script>

