<!-- Navigation -->
	<div class="fundoNavBar-vertical fixed">
		<div class="mesAno">
			<?php if (!isset($mesano) || !is_numeric($mesano)) { $mesano = date("Ym"); } ?>
			<br />
			<a href="<?= base_url() . $profile_uid ."/budget/" . ((intval(substr($mesano,4,2))-1)<1 ? (substr($mesano,0,4)-1) . "12" : (substr($mesano,0,4)) . sprintf('%02d',intval(substr($mesano,4,2)-1)))?>"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a href="#">
				<input type="text" id="mesano" value="<?=$mesano?>" readonly />
			</a>
			<a href="<?= base_url() . $profile_uid ."/budget/" . ((intval(substr($mesano,4,2))+1)>12 ? (substr($mesano,0,4)+1) . "01" : (substr($mesano,0,4)) .sprintf('%02d',intval(substr($mesano,4,2)+1)))?>"><span class="glyphicon glyphicon-chevron-right"></span></a>
			<br /><br />
		</div>
		<div class="exibeBudget">
			<div class="valorBudget <?=($ReceitaMes-$sobreGastoMesAnterior-$budgetMes)>=0 ? "positivo" : "negativo"?>">
				<span class="valor"><?php echo number_format($ReceitaBruta-$sobreGastoMesAnterior-$budgetMes, 2, '.', ''); ?></span>
				<span class="descricao">A ser distribuído</span>
			</div>
			<div class="informacoesBudget">
				<table>
					<tbody>
						<tr class="excedente"><td class="valor"><?=number_format($sobreGastoMesAnterior, 2, '.', '')?></td><td class="descricao">Excedentes (mês passado)</td></tr>
						<tr class="receitas"><td class="valor"><?=number_format($ReceitaBruta, 2, '.', '')?></td><td class="descricao">Receitas (mês atual)</td></tr>
						<tr class="orcado"><td class="valor"><?=number_format($budgetMes, 2, '.', '')?></td><td class="descricao">Orçado (mês atual)</td></tr>
						<?php if (isset($countNClas) && $countNClas>0) : ?>
						<tr>
							<td colspan="2">
								<form method="post" action="<?= base_url() . $profile_uid ."/accounts/all/all/all"?>">
									<input type="hidden" name="search" value="classifique" />
									<input class="btn btn-warning btn-xs" type="submit" data-toggle="tooltip" title="Essas transações não entram no orçamento! Verifique!" value="<?=$countNClas?> transa<?=($countNClas == 1 ? "ção" : "ções")?> não classificada<?=($countNClas == 1 ? "" : "s")?>">
								</form>								
							</td>
						</tr>
						<?php endif;?>
					</tbody>
				</table>	
			</div>
		</div>
	</div>
	<div class="budgetDisplay">
		<div class="budgetLinks">
			<div class="dropdown meuBudget" style="padding: 3px 20px; margin-left:0px;">
				<a class="dropdown-toggle" data-toggle="dropdown">
					<span class="glyphicon glyphicon-plus-sign"></span> Grupo de Categorias
				</a>
				<ul class="dropdown-menu dropCima">
					<form action="<?="budget/adicionaCategoriaGrupo"?>" method="post">
						<li><input type="text" name="categoria" placeholder="Novo Grupo de Categorias" class="form-control">
						<input type="text" style="display:none" name="url" value="<?=base_url() . $profile_uid ."/budget/".$mesano?>"></li>
						<li role="separator" class="divider"></li>
						<li><button class="btn btn-primary" aria-label="Criar grupo" type="submit">
							  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Salvar
							</button>
						</li>
					</form>
				</ul>
			</div>
		</div>
		<div class="budgetConteudo">
			<div class="budgetTabela">
				<form id="formOrcamentos">	
				<table class="table table-hover table-no-bordered tabela" id="tbBudgets" 
				data-toggle="table"
				data-click-to-select="false"
				data-locale="pt-BR">
					<thead>
						<tr><th class="col-md-1" id="thckAll" data-checkbox="true"></th>
							<th class="col-md-4">CATEGORIA</th>
							<th class="col-md-2 orcado">ORÇADO</th>
							<th class="col-md-2 gasto">GASTO</th>
							<th class="col-md-2 disponivel">DISPONÍVEL</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($budgets)): ?>
							<?php $grupo=""; ?>
							<?php foreach ($budgets as $key => $list): ?>
							<?php if ($grupo!=$list['categoria_grupo']): ($grupo=$list['categoria_grupo'])?>
								<tr class="sumaria" id="<?=$list['categoria_grupo']?>">
									<td id="thckAll" data-checkbox="true"></td>
									<td class="categoria">
										<a href="#" data-target=".f<?=$list['categoria_grupo_id']?>" data-toggle="collapse" class="grupoSumario"><?=$list['categoria_grupo']?></a>
										<a href="#"><span id="cat_grupo_id" data-grupo-id="<?=$list['categoria_grupo_id']?>" class="glyphicon glyphicon-plus-sign linkOculto"></span></a>
										<a href="#"><span id="cat_grupo_edit" data-valor="<?=$list['categoria_grupo']?>" data-grupo-id="<?=$list['categoria_grupo_id']?>" class="glyphicon glyphicon-pencil linkOculto"></span></a>
									</td>
									<td class="orcado"></td>
									<td class="gasto"></td>
									<td class="disponivel"></td>
								</tr>
							<?php endif; ?>
							<?php if ($list['categoriaitem']!=""): ?>
							<tr class="filha collapse in f<?=$list['categoria_grupo_id']?>" data-parent="<?=$list['categoria_grupo']?>" data-catid="<?=$list['categoriaitem_id']?>">
								<td id="thckAll" data-checkbox="true"></td>
								<td class="categoria"><?=$list['categoriaitem']?><a href="#"><span id="cat_edit" data-valor="<?=$list['categoriaitem']?>" data-catid="<?=$list['categoriaitem_id']?>" data-grupo-id="<?=$list['categoria_grupo_id']?>" class="glyphicon glyphicon-pencil linkOculto"></span></a></td>
								<td class="orcado"><input type="text" class="valor" name="orcado" id="orcado" data-budgetID="<?=$list['categoriaitem_id']?>" value="<?=($list['budgetMes']=="") ? 0 : $list['budgetMes'] ?>"></td>
								<td class="gasto"><?=number_format(($list['gastoMes']=="" ? 0 : -$list['gastoMes']), 2, '.', ''); ?></td>
								<?php
									$valDisp=floatval($list['Disponivel']);
								?>
								<td class="disponivel"><span class="<?=( $valDisp < 0 ? "menorZero" : ($valDisp  > 0 ? "maiorZero" : "zero"))?>" id="disp_<?=$list['categoriaitem_id']?>"><?=number_format($valDisp, 2, '.', '')?></span></td>
							</tr>
							<?php endif; ?>
							<?php endforeach; ?>
						<?php else: ?>
							
						<?php endif;?>
					</tbody>
				</table>
				</form>
			</div>	
			<div class="budgetMenuDireito">
				<div id="panelResumo" class="panel panel-default" style="margin:0;">
				  <div class="panel-heading">Resumo para o mês</div>
				  <div class="panel-body">
					<strong>Perfil de gastos do mês:</strong>
					<div class="progress">
						<?php
							if ($ReceitaMes == 0) {
								$budget_ratio = floatval(1);
							} else {
								$budget_ratio = floatval($budgetMes/$ReceitaMes);
							}
							$gasto_ratio = $ReceitaMes == 0 ? ($budgetMes == 0 ? 1 : ($gastosMes*(-1))/$budgetMes) : ($gastosMes*(-1))/$ReceitaMes;
							$pb_budget = $budget_ratio > 1 ? 1 : $budget_ratio;
							$pb_gasto = $gasto_ratio > 1 ? 1 : $gasto_ratio;
							if ($pb_gasto > $pb_budget) {
								$pb_budget_s="0";
								$pb_gasto_s = $pb_gasto*100;
							} else {
								$pb_budget_s = ($pb_budget - $pb_gasto)*100;
								$pb_gasto_s = $pb_gasto*100;
							}
						?>
					  <div id="pgb-gasto" class="progress-bar progress-bar-striped <?=(($gastosMes*(-1))>$budgetMes) ? "progress-bar-danger" : "progress-bar-success"?> active" role="progressbar" aria-valuenow="<?=$pb_gasto_s?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$pb_gasto_s?>%">
					  </div>
					  <div id="pgb-orcado" class="progress-bar progress-bar-striped <?=($budgetMes>$ReceitaMes) ? "progress-bar-info" : ""?> active" role="progressbar" aria-valuenow="<?=$pb_budget_s?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$pb_budget_s?>%">
					  </div>
					</div>
					<strong>Legenda:</strong>
					<div id="legendaReceita" class="legendaBudget">
						<div class="barra">
							<div class="progress">
							</div>
						</div>
						<div class="descricao">Receita:
						</div>
						<div class="valor"><?=number_format($ReceitaMes, 2, '.', '')?>
						</div>
					</div>
					<div id="legendaOrcado" class="legendaBudget">
						<div class="barra">
							<div class="progress">
								<div id="pgb-orcado_l" class="progress-bar progress-bar-striped <?=($budgetMes>$ReceitaMes) ? "progress-bar-info" : ""?> active" role="progressbar" aria-valuenow="<?=$pb_budget_s?>" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
								</div>
							</div>
						</div>
						<div class="descricao"><?=($budgetMes>$ReceitaMes) ? "Sobreorçado:" : "Orçado:" ?>
						</div>
						<div class="valor"><?=number_format($budgetMes, 2, '.', '')?>
						</div>
					</div>
					<div id="legendaGastos" class="legendaBudget">
						<div class="barra">
							<div class="progress">
								<div id="pgb-gasto_l" class="progress-bar progress-bar-striped <?=(($gastosMes*(-1))>$budgetMes) ? "progress-bar-danger" : "progress-bar-success"?> active" role="progressbar" aria-valuenow="<?=$pb_gasto_s?>" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
								</div>
							</div>
						</div>
						<div class="descricao"><?=(($gastosMes*(-1))>$budgetMes) ? "Sobregastos:" : "Gastos:"?>
						</div>
						<div class="valor"><?=number_format($gastosMes*(-1), 2, '.', '')?>
						</div>
					</div>
				  </div>
				</div>
				<div id="panelGastos" class="panel panel-default">
				  <div class="panel-heading">Gastos na categoria</div>
				  <div class="panel-body">
					<div id="listaGastos">
						<div id="semGastos">
							<span id="emoticon">=)</span><br/>
							<span>Selecione uma categoria!</span>
						</div>
						<div id="resultados">
						</div>
					</div>
				  </div>
				</div>
			</div>
		</div>
	</div>

<div id="adicionaCatItem" class="dropdown clearfix pontaEsquerda">
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;">
		<form action="<?=base_url() . "/" . $profile_uid ."/budget/adicionaCategoriaItem"?>" method="post">
			<li><strong id="label">Adicionar item a categoria:</strong></li>
			<li><input type="text" id="novaCategoria" name="categoria" placeholder="Nome da Categoria" class="form-control">
			<input type="text" style="display:none" name="url" value="<?=base_url() . $profile_uid ."/budget/".$mesano?>"></li>
			<input type="text" style="display:none" id="categoriagrupo_id" name="categoriagrupo_id" class="form-control" value="0">
			<input type="text" style="display:none" id="categoriaitem_id" name="categoriaitem_id" class="form-control" value="0">
			<div class="checkbox">
			  <label><input type="checkbox" value="" name="carryNegValues" id="carryNegValues">Valores negativos persistem na categoria.</label>
			</div>
			<li role="separator" class="divider"></li>
			<li><button class="btn btn-primary" aria-label="Criar categoria" type="submit">
				  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Salvar
				</button>
				<a href="#" class="apagar" id="apagarCategoria"><span class="glyphicon glyphicon-trash"></span></a>
			</li>
		</form>
	</ul>
</div>
<div id="editaCatGrupo" class="dropdown clearfix pontaEsquerda">
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;">
		<form action="<?=base_url() . "/" . $profile_uid ."/budget/editaCategoriaGrupo"?>" method="post">
			<li><strong id="label">Editar o grupo:</strong></li>
			<li><input type="text" id="categoria" name="categoria" placeholder="Nome da Categoria" class="form-control">
			<input type="text" style="display:none" name="url" value="<?=base_url() . $profile_uid ."/budget/".$mesano?>"></li>
			<input type="text" style="display:none" id="categoriagrupo_id" name="categoriagrupo_id" class="form-control" value="0">
			<select name="ordem_grupo">
				
			</select>
			<li role="separator" class="divider"></li>
			<li><button class="btn btn-primary" aria-label="Criar categoria" type="submit">
				  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Salvar
				</button>
				<a href="#" class="apagar" id="apagarCategoria"><span class="glyphicon glyphicon-trash"></span></a>
			</li>
		</form>
	</ul>
</div>
<!-- /#page-wrapper -->
<!-- Script Bootstrap Tables -->
<script src="<?= base_url() ?>assets/budget/js/bootstrap-table.min.js"></script>
<script src="<?= base_url() ?>assets/budget/js/bootstrap-table-locale-all.min.js"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip({placement: 'right'});
});
</script>