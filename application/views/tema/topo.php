<?php
clearstatcache();
$this->load->view('includes/css');
$this->load->view('includes/custom_css');
$this->load->view('includes/js');
$this->load->view('includes/custom_js');
?>

<body class="infobar-offcanvas infobar-overlay sidebar-hideon-collapse sidebar-scroll" id="body">

    <!--    MENU SUSPENSO-->
    <div id="headerbar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-6 col-sm-2">
                    <a href="#" class="shortcut-tile tile-green">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fas fa-pencil-alt fa-fw"></i></div>
                        </div>
                        <div class="tile-footer">
                            Criar Postagem
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="#" class="shortcut-tile tile-alizarin">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fas fa-group fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">2</span></div>
                        </div>
                        <div class="tile-footer">
                            Contatos
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="#" class="shortcut-tile tile-primary">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fas fa-inbox fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">10</span></div>
                        </div>
                        <div class="tile-footer">
                            Mensagens
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-2">
                    <a href="#" class="shortcut-tile tile-magenta">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fas fa-camera fa-fw"></i></div>
                            <div class="pull-right"><span class="badge">3</span></div>
                        </div>
                        <div class="tile-footer">
                            Galeria
                        </div>
                    </a>
                </div>

                <div class="col-xs-6 col-sm-2">
                    <a href="<?= base_url('notificacoes') ?>" class="shortcut-tile tile-toyo">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fas fa-bell fa-fw"></i></div>
                        </div>
                        <div class="tile-footer">
                            Notificações
                        </div>
                    </a>
                </div>

                <div class="col-xs-6 col-sm-2">
                    <a href="<?= base_url('configuracoes/sistema') ?>" class="shortcut-tile tile-midnightblue">
                        <div class="tile-body">
                            <div class="pull-left"><i class="fas fa-cog fa-fw"></i></div>
                        </div>
                        <div class="tile-footer">
                            Configurações
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--    MENU SUSPENSO-->
    <?php if (ENVIRONMENT == 'development') {
        $topbar_class = 'navbar-development';
        $dev = '[ DEVELOPMENT ]';
    } else {
        $topbar_class = null;
        $dev = '';
    } ?>

    <header id="topnav" class="navbar navbar <?= ($topbar_class ?? 'navbar-midnightblue') ?> navbar-fixed-top clearfix" role="banner">

        <span id="trigger-sidebar" class="toolbar-trigger toolbar-icon-bg">
            <a href="javascript:" data-placement="bottom" title="Exibir/Ocultar Menu" id="menu-switcher">
                <span class="icon-bg menu-toggle">
                    <i id="menu-toggle-icon" class="fas fa-fw"></i>
                </span>
            </a>
        </span>

        <a class="navbar-brand" href="javascript:" style="cursor: unset">CONTEX</a>

        <!--    MENU DIREITO-->
        <!--    <span id="trigger-infobar" class="toolbar-trigger toolbar-icon-bg">-->
        <!--		<a data-toggle="tooltips" data-placement="left" title="Toggle Infobar"><span class="icon-bg"><i class="fa fa-fw fa-bars"></i></span></a>-->
        <!--	</span>-->
        <!--    MENU DIREITO-->

        <div class="yamm navbar-left navbar-collapse collapse in">
            <ul class="nav navbar-nav">
                <!--    MEGAMENU-->
                <li class="dropdown">
                    <!--                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Megamenu<span class="caret"></span></a>-->
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
                                        <img src="#" class="mb20 img-responsive" style="width: 100%;">
                                        <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. totam rem aperiam eaque ipsa quae ab illo
                                            inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
                <!--    MEGAMENU-->

                <!--    DROPDOWN-->
                <li class="dropdown" id="widget-classicmenu">
                    <!--                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown<span class="caret"></span></a>-->
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
                <!--    DROPDOWN-->
            </ul>
        </div>

        <!--    TOP NAVBAR-->
        <ul class="nav navbar-nav toolbar pull-right">
            <li class="dropdown toolbar-icon-bg">
                <!--            <a href="#" id="navbar-links-toggle" data-toggle="collapse" data-target="header>.navbar-collapse" data-placement="bottom" title="Exibir Menu Superior">-->
                <!--				<span class="icon-bg">-->
                <!--					<i class="fa fa-fw fa-chevron-down"></i>-->
                <!--				</span>-->
                <!--            </a>-->
            </li>

            <!--        BUSCA-->
            <li class="dropdown toolbar-icon-bg demo-search-hidden">
                <a href="#" class="dropdown-toggle " data-toggle="dropdown" data-placement="bottom" title="Pesquisa">
                    <span class="icon-bg"><i class="fas fa-fw fa-search"></i></span></a>
                <div class="dropdown-menu dropdown-alternate arrow search dropdown-menu-form">
                    <div class="dd-header">
                        <span>Pesquisa</span>
                        <span><a href="#">Pesquisa avançada</a></span>
                    </div>
                    <form action="<?php echo base_url() ?>mxcode/pesquisar">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Pesquisar...">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search fa-fw"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
            </li>
            <!--        BUSCA-->

            <!--        BOTAO DO MENU SUSPENSO-->
            <li class="toolbar-icon-bg hidden-xs demo-headerdrop-hidden">
                <a href="#" id="headerbardropdown" data-placement="bottom" title="Menu suspenso">
                    <span class="icon-bg">
                        <i class="fas fa-fw fa-level-down-alt"></i>
                    </span>
                </a>
            </li>
            <!--        BOTAO DO MENU SUSPENSO-->

            <!--        TELA CHEIA-->
            <li class="toolbar-icon-bg" id="trigger-fullscreen">
                <a href="#" class="toggle-fullscreen" data-toggle="tooltips" data-placement="bottom" title='Ativar/desativar exibição em tela cheia'>
                    <span class="icon-bg"><i class="fas fa-fw fa-expand-arrows-alt"></i></span></i></a>
            </li>
            <!--        TELA CHEIA-->

            <!--        NOTIFICAÇOES-->
            <li class="dropdown toolbar-icon-bg">
                <a href="#" class="hasnotifications dropdown-toggle" data-toggle="dropdown" data-placement="bottom" title="Notificações">
                    <span class="icon-bg">
                        <i class="fas fa-fw fa-bell"></i>
                    </span>
                    <!--    NUMERO DE NOTIFICACOES-->
                    <span class="badge badge-danger" id="qnt_notificacoes"></span>
                </a>
                <div class="dropdown-menu dropdown-alternate notifications arrow">
                    <div class="dd-header">
                        <span>Notificações</span>
                        <span class=""><a href="<?= base_url('notificacoes') ?>">Acessar Notificações</a></span>
                    </div>
                    <div class="scrollthis scroll-pane" id="scroll-panel">
                        <ul class="scroll-content" id="notifications-panel">
                            <li class="text-center note note-info font-weight-bold" style="border: 0">
                                Usuário não possui notificações
                            </li>
                        </ul>
                    </div>
                    <div class="dd-footer hidden" id="notifications-panel-footer">
                        <a href="#" onclick="lerTodasNotificacoes()">Marcar todas como lidas</a>
                    </div>
                </div>
            </li>
            <!--        NOTIFICAÇOES-->

            <!--        CHAMADOS-->
            <li class="dropdown toolbar-icon-bg">
                <a href="<?= base_url('chamados') ?>" class="hasnotifications" title="Chamados de Suporte">
                    <span class="icon-bg">
                        <i class="fas fa-fw fa-headset"></i>
                    </span>
                    <!--                numero de notificações-->
                    <?php if ($this->session->userdata('permissao') == 1) {
                        if (getAdminTickets() > 0) { ?>
                            <span class="badge badge-danger"><?= getAdminTickets() ?></span>
                        <?php }
                    } else {
                        if (getUserTickets() > 0) { ?>
                            <span class="badge badge-danger"><?= getUserTickets() ?></span>
                    <?php }
                    } ?>
                </a>
            </li>
            <!--        CHAMADOS-->

            <!--MENU USUARIO-->
            <li class="dropdown toolbar-icon-bg">
                <a href="#" class="dropdown-toggle " data-toggle='dropdown' data-placement="bottom" title="<?= $this->session->userdata('nome') ?>">
                    <span class="icon-bg"><i class="fas fa-user-circle fa-fw"></i></span></a>
                <ul class="dropdown-menu userinfo arrow">
                    <!--                <li><a href="javascript:" id="btn_teste"><span class="pull-left">Perfil</span> <span class="badge badge-info">80%</span></a></li>-->
                    <li title="Dados da conta">
                        <a href="<?php echo base_url(); ?>mxcode/minha-conta"><span class="pull-left">Minha Conta</span> <i class="pull-right fas fa-user fa-lg"></i></a>
                    </li>
                    <li title="Configurações da conta">
                        <a href="<?php echo base_url(); ?>configuracoes/usuario"><span class="pull-left">Configurações</span> <i class="pull-right fas fa-cog fa-lg"></i></a>
                    </li>
                    <!--                <li><a href="javascript:"><span class="pull-left">Configurações</span> <i class="pull-right fa fa-cog fa-lg"></i></a></li>-->
                    <!--                <li class="divider"></li>-->
                    <!--                <li><a href="#"><span class="pull-left">Earnings</span> <i class="pull-right fa fa-line-chart"></i></a></li>-->
                    <!--                <li><a href="#"><span class="pull-left">Statement</span> <i class="pull-right fa fa-list-alt"></i></a></li>-->
                    <!--                <li><a href="#"><span class="pull-left">Withdrawals</span> <i class="pull-right fa fa-dollar"></i></a></li>-->
                    <li class="divider"></li>
                    <li title="Encerrar sessão"><a href="<?php echo site_url(); ?>mxcode/sair"><span class="pull-left">Sair</span> <i class="pull-right fas fa-power-off fa-lg"></i></a></li>
                </ul>
            </li>
            <!--MENU USUARIO-->
        </ul>
        <!--    TOP NAVBAR-->

    </header>

    <div id="wrapper">
        <div id="layout-static">
            <!--                    SIDEBAR-->
            <div class="static-sidebar-wrapper <?= (isset($sidebar_color) ? $sidebar_color : 'sidebar-midnightblue') ?>">
                <div class="static-sidebar">
                    <div class="sidebar">
                        <div class="widget stay-on-collapse" id="widget-welcomebox">
                            <div class="widget-body welcome-box tabular">
                                <div class="tabular-row">
                                    <div class="tabular-cell welcome-avatar">
                                        <img src="<?php echo $this->session->userdata('avatar') != null ? base_url('assets/uploads/avatars/') . $this->session->userdata('avatar') : base_url('assets/img/avatars/padrao.png'); ?>" class="avatar">
                                    </div>
                                    <div class="tabular-cell welcome-options">
                                        <!--                                        <span class="welcome-text">Bem-vindo,</span>-->
                                        <span class="name" style="font-size: 13px; font-weight: 600"><?= $this->session->userdata('nome') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget stay-on-collapse" id="widget-sidebar">
                            <nav role="navigation" class="widget-body">
                                <ul class="acc-menu">
                                    <!--                                <li class="nav-separator"></li>-->
                                    <li class="<?= (isset($menuPainel)) ? 'active' : ''; ?>">
                                        <a href="<?php echo base_url() ?>"><i class="fas fa-th-large fa-fw"></i>
                                            <span>Painel Inicial</span>
                                        </a>
                                    </li>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) { ?>
                                        <li class="<?= (isset($menuClientes)) ? 'active' : ''; ?>">
                                            <a href="<?php echo base_url('clientes') ?>"><i class="fas fa-users fa-fw"></i>
                                                <span>Clientes</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vConsumo')) { ?>
                                        <li class="<?= (isset($menuConsumo)) ? 'active' : ''; ?>">
                                            <a href="<?php echo base_url('consumo') ?>"><i class="fas fa-lightbulb fa-fw"></i>
                                                <span>Consumo de Energia</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) { ?>
                                        <li class="<?= (isset($menuProdutos)) ? 'active' : ''; ?>">
                                            <a href="<?php echo base_url('produtos') ?>"><i class="fas fa-barcode fa-fw"></i>
                                                <span>Produtos</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vServico')) { ?>
                                        <li class="<?= (isset($menuServicos)) ? 'active' : ''; ?>">
                                            <a href="<?php echo base_url('servicos') ?>"><i class="fas fa-wrench fa-fw"></i>
                                                <span>Serviços</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) { ?>
                                        <li class="<?= (isset($menuOs)) ? 'active' : ''; ?>">
                                            <a href="<?php echo base_url('os') ?>"><i class="fas fa-tags fa-fw"></i>
                                                <span>Ordens de Serviço</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) { ?>
                                        <li class="<?= (isset($menuVendas)) ? 'active' : ''; ?>">
                                            <a href="<?php echo base_url('vendas') ?>"><i class="fas fa-shopping-cart fa-fw"></i>
                                                <span>Vendas</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vArquivo')) { ?>
                                        <li class="<?= (isset($menuArquivos)) ? 'active' : ''; ?>">
                                            <a href="<?php echo base_url('arquivos') ?>"><i class="fas fa-hdd fa-fw"></i>
                                                <span>Arquivos</span>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                                        <li class="<?= (isset($menuFinanceiro)) ? 'active' : ''; ?>">
                                            <a href="javascript:"><i class="fas fa-dollar-sign fa-fw"></i>
                                                <span>Financeiro</span>
                                            </a>
                                            <ul class="acc-menu">
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vLancamento')) { ?>
                                                    <li><a href="<?php echo base_url('financeiro/lancamentos') ?>">Lançamentos</a></li>
                                                <?php } ?>
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vInvestimentos')) { ?>
                                                    <li><a href="<?php echo base_url('financeiro/investimentos') ?>">Investimentos</a></li>
                                                <?php } ?>
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) { ?>
                                                    <li><a href="<?php echo base_url('financeiro/cartoes') ?>">Cartões</a></li>
                                                <?php } ?>
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturas')) { ?>
                                                    <li><a href="<?php echo base_url('financeiro/faturas') ?>">Faturas</a></li>
                                                <?php } ?>
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPendencias')) { ?>
                                                    <li><a href="<?php echo base_url('financeiro/pendencias') ?>">Pendências</a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>

                                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rServico') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro') || $this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) { ?>
                                        <li class="<?= (isset($menuRelatorios)) ? 'active' : ''; ?>">
                                            <a href="javascript:"><i class="fas fa-file-alt fa-fw"></i>
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

                                    <?php if (
                                        $this->permission->checkPermission($this->session->userdata('permissao'), 'cUsuario') ||
                                        $this->permission->checkPermission($this->session->userdata('permissao'), 'cEmitente') ||
                                        $this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao') ||
                                        $this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')
                                    ) { ?>
                                        <li class="<?= (isset($menuConfiguracoes)) ? 'active' : ''; ?>">
                                            <a href="javascript:"><i class="fas fa-cogs fa-fw"></i>
                                                <span>Config. do Sistema</span>
                                            </a>
                                            <ul class="acc-menu">
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                                    <li><a href="<?php echo base_url() ?>anuncios">Anúncios</a></li>
                                                <?php } ?>
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) { ?>
                                                    <li><a href="<?php echo base_url() ?>configuracoes/sistema">Configurações</a></li>
                                                <?php } ?>
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
            <!--                    SIDEBAR fim-->

            <div class="static-content-wrapper">
                <div class="static-content">
                    <!--                DIV PRINCIPAL-->
                    <div class="page-content">
                        <!--                    BREADCRUMB-->
                        <ol class="breadcrumb">
                        <li class="">
                            <a href=" <?= base_url() ?>" title="Painel Inicial">
                            Painel Inicial
                            </a>
                            </li>
                            <?php if ($this->uri->segment(1)) { ?>
                                <li class="active">
                                    <a href="<?= base_url() . '' . $this->uri->segment(1) ?>" title="<?php echo ucfirst($this->uri->segment(1)); ?>">
                                        <?= ucfirst($this->uri->segment(1)); ?>
                                    </a>
                                </li>
                                <?php if ($this->uri->segment(2)) { ?>
                                    <li>
                                        <a href="<?php echo base_url() . '' . $this->uri->segment(1) . '/' . $this->uri->segment(2) ?>" title="<?php echo ucfirst($this->uri->segment(2)); ?>">
                                            <?php echo ucfirst($this->uri->segment(2)); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->uri->segment(3)) { ?>
                                    <li>
                                        <a href="<?php echo base_url() . '' . $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>" title="<?php echo ucfirst($this->uri->segment(3)); ?>">
                                            <?php echo ucfirst($this->uri->segment(3)); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ($this->uri->segment(4)) { ?>
                                    <li>
                                        ...
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ol>

                        <!--CONTAINER CONTEUDO-->
                        <div class="container-fluid conteudo-principal">
                            <div class="preloader" style="display: none">
                                <div class="cssload-speeding-wheel"></div>
                            </div>
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
                            <div class="subconteudo-principal">
                                <?php if (isset($view)) {
                                    echo $this->load->view($view, null, true);
                                } ?>
                            </div>
                            <!--CONTEUDO PRINCIPAL-->
                        </div>
                        <!--CONTAINER CONTEUDO-->
                    </div>
                    <!--DIV FOOTER-->
                    <footer role="contentinfo">
                        <div class="clearfix">
                            <ul class="list-unstyled list-inline pull-left pl-sm">
                                <li>
                                    <h6 style="margin: 0; text-transform: none"><?= sprintf('&copy; 2019 - %s %s ver.%s', date('Y'), 'CONTEX • Sistema de Gestão •', VERSION_APP, phpversion()) ; ?> </h6>
                                </li>
                            </ul>
                            <button class="pull-right btn btn-link btn-xs hidden-print" id="back-to-top"><i class="fa fa-arrow-up"></i></button>
                        </div>
                    </footer>
                    <!--DIV FOOTER-->
                </div>
            </div>
        </div>
</body>

</html>