<!DOCTYPE html>
<html lang="pt-BR">

    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>MIMV - Meus Investimentos</title>

        <!-- Bootstrap Core CSS -->
        <link href="<?= base_url() ?>assets/admin/css/bootstrap.min.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="<?= base_url() ?>assets/admin/css/metisMenu.min.css" rel="stylesheet">

		<!-- CSS do APP -->
        <link href="<?= base_url() ?>assets/budget/css/app-budget.css" rel="stylesheet">
		
        <!-- Custom Fonts -->
        <link href="<?= base_url() ?>assets/admin/css/font-awesome.min.css" rel="stylesheet" type="text/css">
		<link href="<?= base_url() ?>assets/budget/css/select2.min.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">

		<!-- Bootstrap Tables -->
		<link href="<?= base_url() ?>assets/budget/css/bootstrap-table.min.css" rel="stylesheet">
		
		<!-- Bootstrap Date-->
		<link href="<?= base_url() ?>assets/budget/css/bootstrap-datepicker.css" rel="stylesheet">
		
		<!-- jquery-filer -->
		<link href="<?= base_url() ?>assets/budget/css/jquery.filer.css" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

	</head>

    <body>
        <div id="wrapper">
		    <!-- Navigation -->
				<nav class="navbar navbar-inverse navbar-fixed-top">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="/">MIMV: Meus Investimentos</a>
					</div>
					<!-- /.navbar-header -->
					
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="navbar-links dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$this->logged_in_email?><span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li style="display:none;"><a href="<?= base_url('profiles') ?>"><i class="fa fa-exchange fa-fw"></i><?=lang('sidemenu_profile_change');?></a></li>
								<li style="display:none;"><a href="<?= base_url('profiles/novo/'.$this->profile->uniqueid) ?>"><i class="fa fa-leaf fa-fw"></i><?=lang('sidemenu_profile_newbegin');?></a></li>
								<li role="separator" class="divider"></li>
								<li><a href="<?= base_url('user') ?>"><i class="fa fa-cog fa-fw"></i><?=lang('sidemenu_profile_edit');?></a></li>
								<li><a href="<?= base_url('auth/logout') ?>"><i class="fa fa-sign-out fa-fw"></i><?=lang('sidemenu_profile_logout');?></a></li>
							</ul>
						</li>
					</ul>
					<!-- /.navbar-top-links -->
					
					<div class="navbar-inverse sidebar" role="navigation">
						<div class="sidebar-nav navbar-collapse">
						<ul class="nav metismenu">
						<li class="dropdown" style="padding:5px;">
									<a href="#" class="navbar-links dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-address-card-o fa-lg"></i> <?=$this->profile->nome?><span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="<?= base_url('profiles') ?>" style="color:#000;"><i class="fa fa-exchange fa-fw"></i><?=lang('sidemenu_profile_change');?></a></li>
										<li style="display:none;"><a href="<?= base_url('profiles/novo/'.$this->profile->uniqueid) ?>"><i class="fa fa-leaf fa-fw"></i><?=lang('sidemenu_profile_newbegin');?></a></li>
									</ul>
								</li>
								</ul>
							<ul class="nav metismenu" id="side-menu">
								<li>
									<a href="#"><i class="fa fa-tachometer fa-fw"></i> Posição consolidada</span></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-university fa-fw"></i> Contas e cartões<span class="fa arrow"></span></a>
									<ul class="nav nav-second-level collapse in">
										<li><a href="<?= base_url($this->profile->uniqueid.'/accounts') ?>"><?=lang('sidemenu_links_accounts');?></a></li>
										<li>
											<?php $this->load->view($this->config->item('ci_budget_template_dir_admin') . 'sidemenu_exibeContas'); ?>
										</li>
									</ul>
									<!-- /.nav-second-level -->
								</li>
								<li class="nav-divider"></li>
								<li>
									<a href="#"><i class="fa fa-pie-chart fa-fw"></i> Orçamentos e Gastos<span class="fa arrow"></span></a>
									<ul class="nav nav-second-level collapse in">
										<li><a href="<?= base_url($this->profile->uniqueid.'/budget') ?>"><?=lang('sidemenu_links_budgets');?></a></li>
										<li><a href="<?= base_url($this->profile->uniqueid.'/budget') ?>">Gráfico: orçado vs gasto (mês)</a></li>
									</ul>
									<!-- /.nav-second-level -->
								</li>
								<li class="nav-divider"></li>
								<li>
									<a href="#"><i class="fa fa-line-chart fa-fw"></i> Investimentos<span class="fa arrow"></span></a>
									<ul class="nav nav-second-level collapse in">
										<li><a href="<?=base_url($this->profile->uniqueid.'/desenvolvimento') ?>">Tesouro direto</a></li>
										<li><a href="<?=base_url($this->profile->uniqueid.'/desenvolvimento') ?>">Ações</a></li>
										<li><a href="<?=base_url($this->profile->uniqueid.'/desenvolvimento') ?>">Opções</a></li>
										<li><a href="<?=base_url($this->profile->uniqueid.'/desenvolvimento') ?>">Fundos de investimentos</a></li>
										<li><a href="<?=base_url($this->profile->uniqueid.'/desenvolvimento') ?>">Poupança</a></li>
										<li><a href="<?=base_url($this->profile->uniqueid.'/desenvolvimento') ?>">Outros investimentos</a></li>
									</ul>
								</li>
								<?php if ($this->is_admin): ?>
								<li class="nav-divider"></li>
								<li><a href="<?= base_url('budget/users') ?>"><i class="fa fa-edit fa-fw"></i> Users</a></li>
								<?php endif; ?>
							</ul>
						</div>
						<!-- /.sidebar-collapse -->
					</div>
				</nav>				
                <!-- /.navbar-static-side -->