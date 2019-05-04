<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>MX CODE - Sistemas Web</title>
    <meta charset="UTF-8"/>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css"/>
    <!--    <link rel="stylesheet" href="--><?php //echo base_url(); ?><!--assets/css/bootstrap3.3.7.css"/>-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-style.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-media.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/sweetalert2.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/animate.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.toast.min.css"/>

    <!--    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">-->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.min.js"></script>
</head>
<body>

<!--Header-part-->
<div id="header">
    <h1><a href="#">MX CODE</a></h1>
</div>
<!--close-Header-part-->

<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
    <ul class="nav">

        <li class=""><a title="" href="<?php echo site_url(); ?>/mapos/minhaConta"><i
                        class="fa fa-user-circle fa-lg fa-fw"></i> <span class="text"><?= $this->session->userdata('nome') ?></span></a></li>
        <li class=""><a title="" href="<?php echo site_url(); ?>/mine"><i class="fa fa-handshake-o fa-lg fa-fw"></i>
                <span class="text"> Área do Cliente</span></a></li>
        <li class="pull-right"><a href="#"><i class="fa fa-info-circle fa-lg fa-fw"></i> <span
                        class="text">Versão: <?php echo $this->config->item('app_version'); ?></span></a></li>
        <li class=""><a title="" href="<?php echo site_url(); ?>/mapos/sair">
                <i class="text-danger fa fa-times fa-rotate-180 fa-lg fa-fw"></i>
                <span class="text"> Sair</span></a></li>
    </ul>
</div>

<!--start-top-serch-->
<div id="search" style="">
    <form action="<?php echo base_url() ?>index.php/mapos/pesquisar">
        <div class="col-lg-6">
            <div class="input-group">
                <input style="background: white" type="text" name="termo" class="form-control" placeholder="Pesquisar...">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search fa-fw"></i></button>
                </span>
            </div><!-- /input-group -->
        </div>
    </form>
</div>
<!--close-top-serch-->

<!--sidebar-menu-->

<div id="sidebar"><a href="#" class="visible-phone"><i class="fa fa-list fa-fw"></i> Menu</a>
    <ul>


        <li class="<?php if (isset($menuPainel)) {
            echo 'active';

        }; ?>"><a href="<?php echo base_url() ?>"><i class="fa fa-dashboard fa-lg fa-fw"></i>
                <span>Painel de Controle</span></a></li>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
            <li class="<?php if (isset($menuClientes)) {
                echo 'active';

            }; ?>"><a href="<?php echo base_url() ?>index.php/clientes"><i class="fa fa-group fa-lg fa-fw"></i> <span>Clientes</span></a>
            </li>
        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
            <li class="<?php if (isset($menuProdutos)) {
                echo 'active';

            }; ?>"><a href="<?php echo base_url() ?>index.php/produtos"><i class="fa fa-barcode fa-lg fa-fw"></i> <span>Produtos</span></a>
            </li>
        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
            <li class="<?php if (isset($menuServicos)) {
                echo 'active';

            }; ?>"><a href="<?php echo base_url() ?>index.php/servicos"><i class="fa fa-wrench fa-lg fa-fw"></i> <span>Serviços</span></a>
            </li>
        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
            <li class="<?php if (isset($menuOs)) {
                echo 'active';

            }; ?>"><a href="<?php echo base_url() ?>index.php/os"><i class="fa fa-tags fa-lg fa-fw"></i> <span>Ordens de Serviço</span></a>
            </li>
        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
            <li class="<?php if (isset($menuVendas)) {
                echo 'active';

            }; ?>"><a href="<?php echo base_url() ?>index.php/vendas"><i class="fa fa-shopping-cart fa-lg fa-fw"></i>
                    <span>Vendas</span></a></li>
        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
            <li class="<?php if (isset($menuArquivos)) {
                echo 'active';

            }; ?>"><a href="<?php echo base_url() ?>index.php/arquivos"><i class="fa fa-hdd-o fa-lg fa-fw"></i> <span>Arquivos</span></a>
            </li>
        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
            <li class="submenu <?php if (isset($menuFinanceiro)) {
                echo 'active open';

            }; ?>">
                <a href="#"><i class="fa fa-dollar fa-lg fa-fw"></i> <span>Financeiro</span>
                    <span class="label"><i class="fa fa-chevron-down fa-fw"></i></span></a>
                <ul>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/financeiro/lancamentos">Lançamentos</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/faturas">Faturas</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPendencias')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/financeiro/pendencias">Pendências</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPoupanca')) { ?>
                        <li><a href="#">Poupança</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rServico') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) { ?>

            <li class="submenu <?php if (isset($menuRelatorios)) {
                echo 'active open';

            }; ?>">
                <a href="#"><i class="fa fa-list-alt fa-fw fa-lg"></i> <span>Relatórios</span> <span class="label"><i
                                class="fa fa-chevron-down fa-fw"></i></span></a>
                <ul>

                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/relatorios/clientes">Clientes</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/relatorios/produtos">Produtos</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rServico')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/relatorios/servicos">Serviços</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/relatorios/os">Ordens de Serviço</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/relatorios/vendas">Vendas</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/relatorios/financeiro">Financeiro</a></li>
                    <?php } ?>

                </ul>
            </li>

        <?php } ?>

        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>
            <li class="submenu <?php if (isset($menuConfiguracoes)) {
                echo 'active open';

            }; ?>">
                <a href="#"><i class="fa fa-cogs fa-lg fa-fw"></i> <span>Configurações</span> <span class="label"><i
                                class="fa fa-chevron-down fa-fw"></i></span></a>
                <ul>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/usuarios">Usuários</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/mapos/emitente">Emitente</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/permissoes">Permissões</a></li>
                    <?php } ?>
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>
                        <li><a href="<?php echo base_url() ?>index.php/mapos/backup">Backup</a></li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>

        <li class="submenu <?php if (isset($minhaConta)) {
            echo 'active open';

        }; ?>">
            <a href="#"><i class="fa fa-user-circle fa-lg fa-fw"></i> <span><?= $this->session->userdata('nome') ?></span> <span class="label">
                    <i class="fa fa-chevron-down fa-fw"></i></span></a>
            <ul>
                <li><a href="<?php echo site_url(); ?>/mapos/minhaConta"><i class="fa fa-id-card fa-fw fa-lg"></i> <span>Meus Dados</span></a></li>
                <li><a href="<?php echo site_url(); ?>/mapos/sair"><i class="fa fa-times fa-fw fa-lg fa-rotate-180"></i> Sair</a></li>

            </ul>
        </li>

    </ul>
</div>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb">
            <a href="<?php echo base_url() ?>" title="Painel de Controle" class="tip-bottom">
                <i class="fa fa-dashboard fa-fw"></i> Painel de Controle</a>
            <?php if ($this->uri->segment(1) != null) { ?>
                <a href="<?php echo base_url() . 'index.php/' . $this->uri->segment(1) ?>" class="tip-bottom"
                   title="<?php echo ucfirst($this->uri->segment(1)); ?>"><?php echo ucfirst($this->uri->segment(1)); ?>
                </a>
                <?php if ($this->uri->segment(2) != null) { ?>
                    <a href="<?php echo base_url() . 'index.php/' . $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>"
                    class="current tip-bottom" title="<?php echo ucfirst($this->uri->segment(2)); ?>">
                    <?php echo ucfirst($this->uri->segment(2));
                } ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <?php if ($this->session->flashdata('error') != null) { ?>
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>

                <?php if ($this->session->flashdata('success') != null) { ?>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php } ?>
                <!--CONTEUDO PRINCIPAL-->
                <?php if (isset($view)) {
                    echo $this->load->view($view, null, true);
                } ?>
                <!--CONTEUDO PRINCIPAL-->

            </div>
        </div>
    </div>
</div>

<!--Footer-part-->
<div class="row-fluid">
    <div id="footer" class="span12"><a href="https://mxcode.net" target="_blank">
            <?php echo date('Y'); ?> &copy; MX Code Sistemas - Leônidas Ferreira
        </a>
    </div>
</div>
<!--end-Footer-part-->

<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<!--<script src="--><?php //echo base_url(); ?><!--assets/js/bootstrap3.3.7.js"></script>-->
<script src="<?php echo base_url(); ?>assets/js/matrix.js"></script>
<script src="<?php echo base_url(); ?>assets/js/sweetalert2.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.toast.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskedinput.js"></script>


</body>
</html>







