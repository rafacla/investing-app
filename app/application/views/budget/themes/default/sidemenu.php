<?php
	//Aqui verificamos se está setado o módulo do site ativo,
	//se nao estiver, definimos o padrao:
	if (!isset($modulo)) {
		$modulo = "dashboard";
	}
?>
	<body>
        <div id="wrapper">				
			<div class="sidebar" role="navigation">
				<div class="sidebar-nav">
					<div class="site-logo">
						<img src="<?= base_url() ?>assets/budget/img/site_logo.png"></img>
					</div>
					<li class="dropdown activeProfile">
						<a href="#" class="navbar-links dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-address-card-o fa-lg"></i> <?=$this->profile->nome?><span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="<?= base_url('profiles') ?>"><i class="fa fa-exchange fa-fw"></i><?=lang('sidemenu_profile_change');?></a></li>
							<li style="display:none;"><a href="<?= base_url('profiles/novo/'.$this->profile->uniqueid) ?>"><i class="fa fa-leaf fa-fw"></i><?=lang('sidemenu_profile_newbegin');?></a></li>
							<li role="separator" class="divider"></li>
							<li><a href="<?= base_url('user') ?>"><i class="fa fa-cog fa-fw"></i><?=lang('sidemenu_profile_edit');?></a></li>
							<li><a href="<?= base_url('auth/logout') ?>"><i class="fa fa-sign-out fa-fw"></i><?=lang('sidemenu_profile_logout');?></a></li>
						</ul>
					</li>
					<ul class="nav" id="side-menu">	
						<li class="dashboard<?=($modulo == "dashboard" ? " active in" : "")?>">
							<a href="#">Posição consolidada</span></a>
						</li>
						<li class="orcamento<?=($modulo == "orcamento" ? " active in" : "")?>">
							<a href="#">Orçamentos e Gastos</span></a>
							<ul class="nav-second-level">
								<h6>Contas, Gastos e Orçamentos</h6>
								<li><a href="<?= base_url($this->profile->uniqueid.'/accountslist') ?>"><?=lang('sidemenu_links_accounts');?></a></li>
								<li><a href="<?= base_url($this->profile->uniqueid.'/accounts') ?>"><?=lang('sidemenu_links_statement');?></a></li>
								<li><a href="<?= base_url($this->profile->uniqueid.'/budget') ?>"><?=lang('sidemenu_links_budgets');?></a></li>
								<!--<li><a href="#"><?=lang('sidemenu_links_reports');?><span class="fa arrow"></span></a>-->
								<li><a href="<?= base_url($this->profile->uniqueid.'/budget/chartMonth') ?>"><?=lang('sidemenu_links_budget_report');?></a></li>
							</ul>
						</li>
						<li class="investimentos<?=($modulo == "investimentos" ? " active in" : "")?>">
							<a href="#">Investimentos</span></a>
							<ul class="nav-second-level">
								<h6>Corretoras de Valores</h6>
								<li><a href="<?=base_url($this->profile->uniqueid.'/corretoras') ?>">Cadastro de Corretoras</a></li>
							</ul>
							<ul class="nav-second-level">
								<h6>Ações</h6>
								<li><a href="<?=base_url($this->profile->uniqueid.'/acoes/cotas') ?>">Gráfico Performance</a></li>
								<li><a href="<?=base_url($this->profile->uniqueid.'/acoes/notas') ?>">Notas de Corretagem</a></li>
								<li><a href="<?=base_url($this->profile->uniqueid.'/acoes/ajustes') ?>">Notas de Ajuste</a></li>
								<li><a href="<?=base_url($this->profile->uniqueid.'/acoes/custodia') ?>">Custódia</a></li>
								<li><a href="<?=base_url($this->profile->uniqueid.'/acoes/resultados') ?>">Posições Encerradas</a></li>
							</ul>											
						</li>
						<li class="adm"<?=($modulo == "adm" ? " active in" : "")?>>
							<a href="https://github.com/rafacla/investing-app/issues">Reportar BUGs</span></a>
						</li>
						<?php if ($this->is_admin): ?>
						<li class="adm"<?=($modulo == "adm" ? " active in" : "")?>>
							<a href="#">Administration</span></a>
							<ul class="nav-second-level">
								<li><a href="<?= base_url('budget/users') ?>">Edit Users</a></li>
							</ul>
						</li>
						<?php endif; ?>
					</ul>
				</div>
				<!-- /.sidebar-collapse -->
			</div>
			<!-- /.navbar-static-side -->