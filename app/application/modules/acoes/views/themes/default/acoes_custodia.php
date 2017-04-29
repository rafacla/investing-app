<!-- scripts para os gráficos -->
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
<!-- #page-wrapper -->
<div id="page-wrapper">
	<div class="conteudo">
		<div class="row">
			<div class="page-header users-header">
				<h2>Custódia de ações</h2>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-body" style="display:inline-block;">
			<h3>Posições compradas</h3>
			<?php
				if (count($custodia)) {
					$grafico = <<<EOT
					<chart showpercentintooltip="0" showPercentValues="1" decimals="1" showvalues="1" showlabels="0" usedataplotcolorforlabels="1" theme="fint" enableSmartLabels="1" showlegend="1">
EOT;
				}
			?>
			<?php if (count($custodia)) : ?>
				<div id="charComprado" style="width:400px;display:inline-block;vertical-align:top;">
					
				</div>
			<?php endif; ?>
				<div style="width:calc(100% - 450px);display:inline-block;">
					<table class="table table-striped table-bordered table-hover table-condensed" id="dataTables-example">
						<thead>
							<tr>
								<th class="col-xs-1">Ativo</th>
								<th class="col-xs-1">Custódia</th>
								<th class="col-xs-1">P.M. Compra</th>
								<th class="col-xs-1">Últ. Valor</th>
								<th class="col-xs-1">Variação</th>
								<th class="col-xs-1">Últ.Atualização</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($custodia)) : ?>
								<?php $investido = 0.00;
									  $variacao = 0.00;
										$corretora_nome = "";?>
								<?php foreach ($custodia AS $item): ?>
									<?php 
										$investido = $investido + ($item["CMC"]+$item["CMV"])*abs($item["custodia"]);
										$variacao = $variacao + abs($item["custodia"])*(($item["custodia"] > 0) ? ($item["fechamento"]-$item["CMC"]) : ($item["CMV"]-$item["fechamento"]));
										if ($corretora_nome != $item["corretora_nome"]) {
											$corretora_nome = $item["corretora_nome"];
											echo "<tr>";
												echo "<td colspan=\"6\"><strong>".$corretora_nome."</strong></td>";
											echo "</tr>";
										}
									?>
									<tr>
										<td><?=$item["ativo_nome"]?></td>
										<td><?=$item["custodia"]?></td>
										<td><?=($item["custodia"] > 0) ? $item["CMC"] : ''?></td>
										<td><?=$item["fechamento"]?></td>
										<td class="<?=($item["fechamento"]-$item["CMC"]>0) ? 'success' : 'danger'?>">
										<?=($item["custodia"] > 0) ? ($item["fechamento"]-$item["CMC"]).' ('.number_format(($item["fechamento"]/$item["CMC"]-1)*100,2).'%)' : ($item["CMV"]-$item["fechamento"]).' ('.number_format(($item["CMV"]/$item["fechamento"]-1)*100,2).'%)'?></td>
										<td><?=$item["ult_atualizacao"]?></td
										<?php 
										$grafico = $grafico . "<set label=\"".$item["ativo_nome"]."\" value=\"".$item["custodia"]*$item["fechamento"]."\" />";
										?>
									</tr>
								<?php endforeach; 
								$grafico = $grafico . "</chart";
								?>
								
								<tr>
									<td colspan="1"></td>
									<td>Valor investido</td>
									<td><?=number_format($investido,2)?></td>
									<td>Valor atual</td>
									<td><?=number_format($investido+$variacao,2)?></td>
									<td colspan="3"><?=number_format($variacao,2).' ('.number_format(((($investido+$variacao)/$investido)-1)*100,2).'%)'?></td>
								</tr>
							<?php else : ?>
								<tr>
									<td colspan="9">Sem posições abertas</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php if (count($custodia_vendida)) : ?>
		<div class="panel panel-default">
			<div class="panel-body">
			<h3>Posições vendidas</h3>
			<?php
				if (count($custodia_vendida)) {
					$grafico_v = <<<EOT
					<chart showpercentintooltip="0" showPercentValues="1" decimals="1" usedataplotcolorforlabels="1" theme="fint" enableSmartLabels="0">
EOT;
				}
			?>
			<?php if (count($custodia_vendida)) : ?>
				<div id="charVendido" style="width:400px;display:inline-block;vertical-align:top;">
					
				</div>
				<div style="width:calc(100% - 450px);display:inline-block;">
					<table class="table table-striped table-bordered table-hover table-condensed" id="dataTables-example">
						<thead>
							<tr>
								<th class="col-xs-1">Ativo</th>
								<th class="col-xs-1">Custódia</th>
								<th class="col-xs-1">P.M. Venda</th>
								<th class="col-xs-1">Últ. Valor</th>
								<th class="col-xs-1">Variação</th>
								<th class="col-xs-1">Últ.Atualização</th>
							</tr>
						</thead>
						<tbody>
								<?php $investido = 0.00;
									  $variacao = 0.00; 
									  $corretora_nome = "";?>
								<?php foreach ($custodia_vendida AS $item): ?>
									<?php 
										$investido = $investido + ($item["CMC"]+$item["CMV"])*abs($item["custodia"]);
										$variacao = $variacao + abs($item["custodia"])*(($item["custodia"] > 0) ? ($item["fechamento"]-$item["CMC"]) : ($item["CMV"]-$item["fechamento"]));
										if ($corretora_nome != $item["corretora_nome"]) {
											$corretora_nome = $item["corretora_nome"];
											echo "<tr>";
												echo "<td colspan=\"6\"><strong>".$corretora_nome."</strong></td>";
											echo "</tr>";
										}
									?>
									<tr>
										<td><?=$item["ativo_nome"]?></td>
										<td><?=$item["custodia"]?></td>
										<td><?=($item["custodia"] < 0) ? $item["CMV"] : ''?></td>
										<td><?=$item["fechamento"]?></td>
										<td class="<?=($item["CMV"]-$item["fechamento"]>0) ? 'success' : 'danger'?>">
											<?=($item["custodia"] > 0) ? ($item["fechamento"]-$item["CMC"]).' ('.number_format(($item["fechamento"]/$item["CMC"]-1)*100,2).'%)' : ($item["CMV"]-$item["fechamento"]).' ('.number_format(($item["CMV"]/$item["fechamento"]-1)*100,2).'%)'?>
										</td>
										<td><?=$item["ult_atualizacao"]?></td>
										<?php 
										$grafico_v = $grafico_v . "<set label=\"".$item["ativo_nome"]."\" value=\"".abs($item["custodia"]*$item["fechamento"])."\" />";
										?>
									</tr>
								<?php endforeach; 
								$grafico_v = $grafico_v . "</chart>";?>
								<tr>
									<td colspan="1"></td>
									<td>Valor investido</td>
									<td><?=number_format($investido,2)?></td>
									<td>Valor atual</td>
									<td><?=number_format($investido+$variacao,2)?></td>
									<td colspan="3"><?=number_format($variacao,2).' ('.number_format(((($investido+$variacao)/$investido)-1)*100,2).'%)'?></td>
								</tr>
							<?php else : ?>
								<tr>
									<td colspan="9">Sem posições abertas</td>
								</tr>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
		<?php if (count($custodia)) : ?>
			<script type="text/javascript">
				$(function() {
					FusionCharts.ready(function(){
						var comprado = new FusionCharts({
						"type": "pie2d",
						"renderAt": "charComprado",
						"width": 400,
						"height": 400,
						"dataFormat": "xml",
						"dataSource": <?php echo json_encode($grafico); ?>   });

						comprado.render();
					});
				});
			</script>
		<?php endif; ?>
		<?php if (count($custodia_vendida)) : ?>
			<script type="text/javascript">
				$(function() {
					FusionCharts.ready(function(){
						var revenueChart = new FusionCharts({
						"type": "pie2d",
						"renderAt": "charVendido",
						"width": 400,
						"height": 400,
						"dataFormat": "xml",
						"dataSource": <?php echo json_encode($grafico_v); ?>   });

						revenueChart.render();
					});
				});
			</script>
		<?php endif; ?>
	</div>
</div>
<!-- /#page-wrapper -->
