<?php $this->load->view('includes/css'); ?>

<?php $this->load->view('includes/js'); ?>

<body class="infobar-offcanvas">

<!--    MENU SUSPENSO-->
<div id="headerbar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-6 col-sm-2">
                <a href="#" class="shortcut-tile tile-brown">
                    <div class="tile-body">
                        <div class="pull-left"><i class="fa fa-pencil"></i></div>
                    </div>
                    <div class="tile-footer">
                        Create Post
                    </div>
                </a>
            </div>
            <div class="col-xs-6 col-sm-2">
                <a href="#" class="shortcut-tile tile-grape">
                    <div class="tile-body">
                        <div class="pull-left"><i class="fa fa-group"></i></div>
                        <div class="pull-right"><span class="badge">2</span></div>
                    </div>
                    <div class="tile-footer">
                        Contacts
                    </div>
                </a>
            </div>
            <div class="col-xs-6 col-sm-2">
                <a href="#" class="shortcut-tile tile-primary">
                    <div class="tile-body">
                        <div class="pull-left"><i class="fa fa-envelope-o"></i></div>
                        <div class="pull-right"><span class="badge">10</span></div>
                    </div>
                    <div class="tile-footer">
                        Messages
                    </div>
                </a>
            </div>
            <div class="col-xs-6 col-sm-2">
                <a href="#" class="shortcut-tile tile-inverse">
                    <div class="tile-body">
                        <div class="pull-left"><i class="fa fa-camera"></i></div>
                        <div class="pull-right"><span class="badge">3</span></div>
                    </div>
                    <div class="tile-footer">
                        Gallery
                    </div>
                </a>
            </div>

            <div class="col-xs-6 col-sm-2">
                <a href="#" class="shortcut-tile tile-midnightblue">
                    <div class="tile-body">
                        <div class="pull-left"><i class="fa fa-cog"></i></div>
                    </div>
                    <div class="tile-footer">
                        Settings
                    </div>
                </a>
            </div>
            <div class="col-xs-6 col-sm-2">
                <a href="#" class="shortcut-tile tile-orange">
                    <div class="tile-body">
                        <div class="pull-left"><i class="fa fa-wrench"></i></div>
                    </div>
                    <div class="tile-footer">
                        Plugins
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<!--    MENU SUSPENSO-->

<header id="topnav" class="navbar navbar-midnightblue navbar-fixed-top clearfix" role="banner">

	<span id="trigger-sidebar" class="toolbar-trigger toolbar-icon-bg">
		<a data-placement="bottom" title="Exibir/Ocultar Menu">
            <span class="icon-bg">
                <i class="fa fa-fw fa-bars"></i>
            </span>
        </a>
	</span>

    <a class="navbar-brand" href="<?php echo base_url(); ?>">CONTEX</a>

    <!--    MENU DIREITO-->
    <!--    <span id="trigger-infobar" class="toolbar-trigger toolbar-icon-bg">-->
    <!--		<a data-toggle="tooltips" data-placement="left" title="Toggle Infobar"><span class="icon-bg"><i class="fa fa-fw fa-bars"></i></span></a>-->
    <!--	</span>-->
    <!--    MENU DIREITO-->

    <!--    MEGAMENU-->
    <div class="yamm navbar-left navbar-collapse collapse in">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Megamenu<span class="caret"></span></a>
                <ul class="dropdown-menu" style="width: 900px;">
                    <li>
                        <div class="yamm-content container-sm-height">
                            <div class="row row-sm-height yamm-col-bordered">
                                <div class="col-sm-3 col-sm-height yamm-col">

                                    <h3 class="yamm-category">Sidebar</h3>
                                    <ul class="list-unstyled mb20">
                                        <li><a href="layout-fixed-sidebars.html">Stretch Sidebars</a></li>
                                        <li><a href="layout-sidebar-scroll.html">Scroll Sidebar</a></li>
                                        <li><a href="layout-static-leftbar.html">Static Sidebar</a></li>
                                        <li><a href="layout-leftbar-widgets.html">Sidebar Widgets</a></li>
                                    </ul>

                                    <h3 class="yamm-category">Infobar</h3>
                                    <ul class="list-unstyled">
                                        <li><a href="layout-infobar-offcanvas.html">Offcanvas Infobar</a></li>
                                        <li><a href="layout-infobar-overlay.html">Overlay Infobar</a></li>
                                        <li><a href="layout-chatbar-overlay.html">Chatbar</a></li>
                                        <li><a href="layout-rightbar-widgets.html">Infobar Widgets</a></li>
                                    </ul>

                                </div>
                                <div class="col-sm-3 col-sm-height yamm-col">

                                    <h3 class="yamm-category">Page Content</h3>
                                    <ul class="list-unstyled mb20">
                                        <li><a href="layout-breadcrumb-top.html">Breadcrumbs on Top</a></li>
                                        <li><a href="layout-page-tabs.html">Page Tabs</a></li>
                                        <li><a href="layout-fullheight-panel.html">Full-Height Panel</a></li>
                                        <li><a href="layout-fullheight-content.html">Full-Height Content</a></li>
                                    </ul>

                                    <h3 class="yamm-category">Misc</h3>
                                    <ul class="list-unstyled">
                                        <li><a href="layout-topnav-options.html">Topnav Options</a></li>
                                        <li><a href="layout-horizontal-small.html">Horizontal Small</a></li>
                                        <li><a href="layout-horizontal-large.html">Horizontal Large</a></li>
                                        <li><a href="layout-boxed.html">Boxed</a></li>
                                    </ul>

                                </div>
                                <div class="col-sm-3 col-sm-height yamm-col">

                                    <h3 class="yamm-category">Analytics</h3>
                                    <ul class="list-unstyled mb20">
                                        <li><a href="charts-flot.html">Flot</a></li>
                                        <li><a href="charts-sparklines.html">Sparklines</a></li>
                                        <li><a href="charts-morris.html">Morris</a></li>
                                        <li><a href="charts-easypiechart.html">Easy Pie Charts</a></li>
                                    </ul>

                                    <h3 class="yamm-category">Components</h3>
                                    <ul class="list-unstyled">
                                        <li><a href="ui-tiles.html">Tiles</a></li>
                                        <li><a href="custom-knob.html">jQuery Knob</a></li>
                                        <li><a href="custom-jqueryui.html">jQuery Slider</a></li>
                                        <li><a href="custom-ionrange.html">Ion Range Slider</a></li>
                                    </ul>

                                </div>
                                <div class="col-sm-3 col-sm-height yamm-col">
                                    <h3 class="yamm-category">Rem</h3>
                                    <img src="assets/demo/stockphoto/communication_12_carousel.jpg" class="mb20 img-responsive" style="width: 100%;">
                                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. totam rem aperiam eaque ipsa quae ab illo
                                        inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="dropdown" id="widget-classicmenu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown<span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                    <li class="divider"></li>
                    <li><a href="#">One more separated link</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!--    MEGAMENU-->

    <!--    TOP NAVBAR-->
    <ul class="nav navbar-nav toolbar pull-right">
        <li class="dropdown toolbar-icon-bg">
            <a href="#" id="navbar-links-toggle" data-toggle="collapse" data-target="header>.navbar-collapse" data-placement="bottom" title="Exibir Menu Superior">
				<span class="icon-bg">
					<i class="fa fa-fw fa-ellipsis-h"></i>
				</span>
            </a>
        </li>

        <!--        BUSCA-->
        <li class="dropdown toolbar-icon-bg demo-search-hidden">
            <a href="#" class="dropdown-toggle " data-toggle="dropdown" data-placement="bottom" title="Pesquisa">
                <span class="icon-bg"><i class="fa fa-fw fa-search"></i></span></a>
            <div class="dropdown-menu dropdown-alternate arrow search dropdown-menu-form">
                <div class="dd-header">
                    <span>Pesquisa</span>
                    <span><a href="#">Pesquisa avançada</a></span>
                </div>
                <form action="<?php echo base_url() ?>mxcode/pesquisar">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Pesquisar...">
                        <span class="input-group-btn">
						    <button type="submit" class="btn btn-primary"><i class="fa fa-search fa-fw"></i></button>
					    </span>
                    </div>
                </form>
            </div>
        </li>
        <!--        BUSCA-->

        <!--        BOTAO DO MENU SUSPENSO-->
        <li class="toolbar-icon-bg demo-headerdrop-hidden">
            <a href="#" id="headerbardropdown" data-placement="bottom" title="Menu Suspenso">
                <span class="icon-bg">
                    <i class="fa fa-fw fa-level-down"></i>
                </span>
            </a>
        </li>
        <!--        BOTAO DO MENU SUSPENSO-->

        <!--        TELA CHEIA-->
        <li class="toolbar-icon-bg hidden-xs" id="trigger-fullscreen">
            <a href="#" class="toggle-fullscreen" data-toggle="tooltips" data-placement="bottom" title='Tela Cheia'>
                <span class="icon-bg"><i class="fa fa-fw fa-arrows-alt"></i></span></i></a>
        </li>
        <!--        TELA CHEIA-->

        <!--        NOTIFICAÇOES-->
        <li class="dropdown toolbar-icon-bg">
            <a href="#" class="hasnotifications dropdown-toggle " data-toggle="dropdown" data-placement="bottom" title="Notificações">
                <span class="icon-bg">
                    <i class="fa fa-fw fa-bell"></i>
                </span>
                <!--                <span class="badge badge-info">5</span>-->
            </a>
            <div class="dropdown-menu dropdown-alternate notifications arrow">
                <div class="dd-header">
                    <span>Notifications</span>
                    <span><a href="#">Settings</a></span>
                </div>
                <div class="scrollthis scroll-pane">
                    <ul class="scroll-content">

                        <li class="">
                            <a href="#" class="notification-info">
                                <div class="notification-icon"><i class="fa fa-user fa-fw"></i></div>
                                <div class="notification-content">Profile Page has been updated</div>
                                <div class="notification-time">2m</div>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="notification-success">
                                <div class="notification-icon"><i class="fa fa-check fa-fw"></i></div>
                                <div class="notification-content">Updates pushed successfully</div>
                                <div class="notification-time">12m</div>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="notification-primary">
                                <div class="notification-icon"><i class="fa fa-users fa-fw"></i></div>
                                <div class="notification-content">New users request to join</div>
                                <div class="notification-time">35m</div>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="notification-danger">
                                <div class="notification-icon"><i class="fa fa-shopping-cart fa-fw"></i></div>
                                <div class="notification-content">More orders are pending</div>
                                <div class="notification-time">11h</div>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="notification-primary">
                                <div class="notification-icon"><i class="fa fa-arrow-up fa-fw"></i></div>
                                <div class="notification-content">Pending Membership approval</div>
                                <div class="notification-time">2d</div>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="notification-info">
                                <div class="notification-icon"><i class="fa fa-check fa-fw"></i></div>
                                <div class="notification-content">Succesfully updated to version 1.0.1</div>
                                <div class="notification-time">40m</div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dd-footer">
                    <a href="#">View all notifications</a>
                </div>
            </div>
        </li>
        <!--        NOTIFICAÇOES-->

        <!--        MENSAGENS-->
        <li class="dropdown toolbar-icon-bg hidden-xs">
            <a href="#" class="hasnotifications dropdown-toggle " data-toggle='dropdown' data-placement="bottom" title="Mensagens">
                <span class="icon-bg"><i class="fa fa-fw fa-envelope"></i></span></a>
            <div class="dropdown-menu dropdown-alternate messages arrow">
                <div class="dd-header">
                    <span>Messages</span>
                    <span><a href="#">Settings</a></span>
                </div>

                <div class="scrollthis scroll-pane">
                    <ul class="scroll-content">
                        <li class="">
                            <a href="#">
                                <img class="msg-avatar" src="assets/demo/avatar/avatar_09.png" alt="avatar"/>
                                <div class="msg-content">
                                    <span class="name">Steven Shipe</span>
                                    <span class="msg">Nonummy nibh epismod lorem ipsum</span>
                                </div>
                                <span class="msg-time">30s</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img class="msg-avatar" src="assets/demo/avatar/avatar_01.png" alt="avatar"/>
                                <div class="msg-content">
                                    <span class="name">Roxann Hollingworth <i class="fa fa-paperclip attachment"></i></span>
                                    <span class="msg">Lorem ipsum dolor sit amet consectetur adipisicing elit</span>
                                </div>
                                <span class="msg-time">5m</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img class="msg-avatar" src="assets/demo/avatar/avatar_05.png" alt="avatar"/>
                                <div class="msg-content">
                                    <span class="name">Diamond Harlands</span>
                                    <span class="msg">:)</span>
                                </div>
                                <span class="msg-time">3h</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img class="msg-avatar" src="assets/demo/avatar/avatar_02.png" alt="avatar"/>
                                <div class="msg-content">
                                    <span class="name">Michael Serio <i class="fa fa-paperclip attachment"></i></span>
                                    <span class="msg">Sed distinctio dolores fuga molestiae modi?</span>
                                </div>
                                <span class="msg-time">12h</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img class="msg-avatar" src="assets/demo/avatar/avatar_03.png" alt="avatar"/>
                                <div class="msg-content">
                                    <span class="name">Matt Jones</span>
                                    <span class="msg">Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et mole</span>
                                </div>
                                <span class="msg-time">2d</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img class="msg-avatar" src="assets/demo/avatar/avatar_07.png" alt="avatar"/>
                                <div class="msg-content">
                                    <span class="name">John Doe</span>
                                    <span class="msg">Neque porro quisquam est qui dolorem</span>
                                </div>
                                <span class="msg-time">7d</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="dd-footer"><a href="#">View all messages</a></div>
            </div>
        </li>
        <!--        MENSAGENS-->

        <!--MENU USUARIO-->
        <li class="dropdown toolbar-icon-bg">
            <a href="#" class="dropdown-toggle " data-toggle='dropdown' data-placement="bottom" title="<?= $this->session->userdata('nome') ?>">
                <span class="icon-bg"><i class="fa fa-fw fa-user-circle"></i></span></a>
            <ul class="dropdown-menu userinfo arrow">
                <li><a href="javascript:" id="btn_teste"><span class="pull-left">Perfil</span> <span class="badge badge-info">80%</span></a></li>
                <li><a href="<?php echo site_url(); ?>mxcode/minhaConta"><span class="pull-left">Minha Conta</span> <i class="pull-right fa fa-user-circle fa-lg"></i></a></li>
                <li><a href="javascript:"><span class="pull-left">Configurações</span> <i class="pull-right fa fa-cog fa-lg"></i></a></li>
                <!--                <li class="divider"></li>-->
                <!--                <li><a href="#"><span class="pull-left">Earnings</span> <i class="pull-right fa fa-line-chart"></i></a></li>-->
                <!--                <li><a href="#"><span class="pull-left">Statement</span> <i class="pull-right fa fa-list-alt"></i></a></li>-->
                <!--                <li><a href="#"><span class="pull-left">Withdrawals</span> <i class="pull-right fa fa-dollar"></i></a></li>-->
                <li class="divider"></li>
                <li><a href="<?php echo site_url(); ?>mxcode/sair"><span class="pull-left">Sair</span> <i class="pull-right fa fa-sign-out fa-lg"></i></a></li>
            </ul>
        </li>
        <!--MENU USUARIO-->
    </ul>
    <!--    TOP NAVBAR-->

</header>

<div id="wrapper">
    <div id="layout-static">

        <!--                    SIDEBAR-->
        <div class="static-sidebar-wrapper sidebar-midnightblue">
            <div class="static-sidebar">
                <div class="sidebar">
                    <div class="widget stay-on-collapse" id="widget-welcomebox">
                        <div class="widget-body welcome-box tabular">
                            <a href="<?php echo site_url(); ?>mxcode/minhaConta">
                                <div class="tabular-row">
                                    <div class="tabular-cell welcome-avatar">
                                        <img src="<?php echo base_url(); ?>assets/img/avatars/padrao.png" class="avatar">
                                    </div>
                                    <div class="tabular-cell welcome-options">
                                        <span class="welcome-text">Bem-vindo,</span>
                                        <a href="<?php echo site_url(); ?>mxcode/minhaConta" class="name"><?= $this->session->userdata('nome') ?></a>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="widget stay-on-collapse" id="widget-sidebar">
                        <nav role="navigation" class="widget-body">
                            <ul class="acc-menu">
                                <li class="nav-separator"></li>

                                <li class="<?= (isset($menuPainel)) ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url() ?>"><i class="fa fa-home fa-fw"></i>
                                        <span>Painel Inicial</span>
                                    </a>
                                </li>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                                    <li class="<?= (isset($menuClientes)) ? 'active' : ''; ?>">
                                        <a href="<?php echo base_url() ?>clientes"><i class="fa fa-group fa-fw"></i>
                                            <span>Clientes</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                                    <li class="<?= (isset($menuProdutos)) ? 'active' : ''; ?>">
                                        <a href="<?php echo base_url() ?>produtos"><i class="fa fa-barcode fa-fw"></i>
                                            <span>Produtos</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
                                    <li class="<?= (isset($menuServicos)) ? 'active' : ''; ?>">
                                        <a href="<?php echo base_url() ?>servicos"><i class="fa fa-wrench fa-fw"></i>
                                            <span>Serviços</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                    <li class="<?= (isset($menuOs)) ? 'active' : ''; ?>">
                                        <a href="<?php echo base_url() ?>os"><i class="fa fa-tags fa-fw"></i>
                                            <span>Ordens de Serviço</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
                                    <li class="<?= (isset($menuVendas)) ? 'active' : ''; ?>">
                                        <a href="<?php echo base_url() ?>vendas"><i class="fa fa-shopping-cart fa-fw"></i>
                                            <span>Vendas</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                                    <li class="<?= (isset($menuArquivos)) ? 'active' : ''; ?>">
                                        <a href="<?php echo base_url() ?>arquivos"><i class="fa fa-hdd-o fa-fw"></i>
                                            <span>Arquivos</span>
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                                    <li class="<?= (isset($menuFinanceiro)) ? 'active' : ''; ?>">
                                        <a href="javascript:;"><i class="fa fa-dollar fa-fw"></i>
                                            <span>Financeiro</span>
                                        </a>
                                        <ul class="acc-menu">
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                                                <li><a href="<?php echo base_url() ?>financeiro/lancamentos">Lançamentos</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) { ?>
                                                <li><a href="<?php echo base_url() ?>financeiro/faturas">Faturas</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPendencias')) { ?>
                                                <li><a href="<?php echo base_url() ?>financeiro/pendencias">Pendências</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPoupanca')) { ?>
                                                <li><a href="javascript:" class="poupanca">Poupança</a></li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rServico') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) { ?>
                                    <li class="<?= (isset($menuRelatorios)) ? 'active' : ''; ?>">
                                        <a href="javascript:;"><i class="fa fa-list-alt fa-fw"></i>
                                            <span>Relatórios</span>
                                        </a>
                                        <ul class="acc-menu">
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente')) { ?>
                                                <li><a href="<?php echo base_url() ?>relatorios/clientes">Clientes</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) { ?>
                                                <li><a href="<?php echo base_url() ?>relatorios/produtos">Produtos</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rServico')) { ?>
                                                <li><a href="<?php echo base_url() ?>relatorios/servicos">Serviços</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) { ?>
                                                <li><a href="<?php echo base_url() ?>relatorios/os">Ordens de Serviço</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) { ?>
                                                <li><a href="<?php echo base_url() ?>relatorios/vendas">Vendas</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) { ?>
                                                <li><a href="<?php echo base_url() ?>relatorios/financeiro">Financeiro</a></li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>

                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao') || $this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>
                                    <li class="<?= (isset($menuConfiguracoes)) ? 'active' : ''; ?>">
                                        <a href="javascript:;"><i class="fa fa-cogs fa-fw"></i>
                                            <span>Config. do Sistema</span>
                                        </a>
                                        <ul class="acc-menu">
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario')) { ?>
                                                <li><a href="<?php echo base_url() ?>usuarios">Usuários</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente')) { ?>
                                                <li><a href="<?php echo base_url() ?>mxcode/emitente">Emitente</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                                <li><a href="<?php echo base_url() ?>permissoes">Permissões</a></li>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>
                                                <li><a href="<?php echo base_url() ?>mxcode/backup">Backup</a></li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>

                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!--                    SIDEBAR-->

        <div class="static-content-wrapper">
            <div class="static-content">
                <!--                DIV PRINCIPAL-->
                <div class="page-content">
                    <!--                    BREADCRUMB-->
                    <ol class="breadcrumb">
                        <li class="">
                            <a href="<?= base_url() ?>">
                                Painel Inicial
                            </a>
                        </li>
                        <?php if ($this->uri->segment(1) != null) { ?>
                            <li class="active">
                                <a href="<?= base_url() . '' . $this->uri->segment(1) ?>" class="tip-bottom"
                                   title="<?php echo ucfirst($this->uri->segment(1)); ?>">
                                    <?= ucfirst($this->uri->segment(1)); ?>
                                </a>
                            </li>
                            <li>
                                <?php if ($this->uri->segment(2) != null) { ?>
                                    <a href="<?php echo base_url() . '' . $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>"
                                       class="current tip-bottom" title="<?php echo ucfirst($this->uri->segment(2)); ?>">
                                        <?php echo ucfirst($this->uri->segment(2)); ?>
                                    </a>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ol>

                    <!--                CONTEUDO-->
                    <div class="container-fluid conteudo-principal">
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
                    <!--                CONTEUDO-->
                </div>
                <!--                FOOTER-->
                <footer role="contentinfo">
                    <div class="clearfix">
                        <ul class="list-unstyled list-inline pull-left pl-sm">
                            <a href="https://mxcode.net" target="_blank">
                                <li><h6 style="margin: 0;"><?php echo date('Y'); ?> &copy; MX Code Sistemas - Leônidas Ferreira</h6></li>
                            </a>
                        </ul>
                        <button class="pull-right btn btn-link btn-xs hidden-print" id="back-to-top"><i class="fa fa-arrow-up"></i></button>
                    </div>
                </footer>
                <!--                FOOTER-->

            </div>
        </div>
    </div>
</body>
</html>