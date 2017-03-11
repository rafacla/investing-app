<!DOCTYPE html>
<html lang="pt-BR">

    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>MIMV</title>

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
						<a class="navbar-brand" href="/">MIMV</a>
					</div>
					<!-- /.navbar-header -->
					
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="navbar-links dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$this->logged_in_email?><span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?= base_url('profiles') ?>"><i class="fa fa-exchange fa-fw"></i><?=lang('sidemenu_profile_change');?></a></li>
								<li style="display:none;"><a href="<?= base_url('profiles/novo/'.$this->profile->uniqueid) ?>"><i class="fa fa-leaf fa-fw"></i><?=lang('sidemenu_profile_newbegin');?></a></li>
								<li role="separator" class="divider"></li>
								<li><a href="<?= base_url('user') ?>"><i class="fa fa-cog fa-fw"></i><?=lang('sidemenu_profile_edit');?></a></li>
								<li><a href="<?= base_url('auth/logout') ?>"><i class="fa fa-sign-out fa-fw"></i><?=lang('sidemenu_profile_logout');?></a></li>
							</ul>
						</li>
					</ul>
					<!-- /.navbar-top-links -->
					
					<div class="navbar-default sidebar" role="navigation">
						<div class="sidebar-nav navbar-collapse">
							<ul class="nav in" id="side-menu">							
								<li>
									<a href="#"><i class="fa fa-wrench fa-fw"></i> Budgets<span class="fa arrow"></span></a>
									<ul class="nav nav-second-level collapse in">
										<li><a href="<?= base_url($this->profile->uniqueid.'/budget') ?>"><i class="fa fa-pie-chart fa-fw"></i> <?=lang('sidemenu_links_budgets');?></a></li>
										<li><a href="<?= base_url($this->profile->uniqueid.'/accounts') ?>"><i class="fa fa-university fa-fw"></i> <?=lang('sidemenu_links_accounts');?></a></li>
										<li>
											<?php $this->load->view($this->config->item('ci_budget_template_dir_admin') . 'sidemenu_exibeContas'); ?>
										</li>
									</ul>
									<!-- /.nav-second-level -->
								</li>
								<li>
									<a href="#"><i class="fa fa-edit fa-fw"></i> Investments</a>
								</li>
								<?php if ($this->is_admin): ?>
								<li><a href="<?= base_url('budget/users') ?>"><i class="fa fa-edit fa-fw"></i> Users</a></li>
								<?php endif; ?>
							</ul>
						</div>
						<!-- /.sidebar-collapse -->
					</div>
				</nav>				
                <!-- /.navbar-static-side -->