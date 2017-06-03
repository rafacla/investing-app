<!-- scripts para os gráficos -->
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
<!-- #page-wrapper -->
<div id="page-wrapper">
	<div class="conteudo">
		<div class="row">
			<div class="page-header users-header">
				<h2>Gráfico de Performance</h2>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-body" style="display:block;">
			<?php
				if (count($custodia)) {
					$grafico = <<<EOT
					<chart pyaxisname="Valor da Cota (por 100)" linethickness="2" palettecolors="#0075c2" basefontcolor="#333333"
					showvalues="0" showborder="0" bgcolor="#ffffff" showshadow="0" canvasbgcolor="#ffffff" 
					canvasborderalpha="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" 
					divlineisdashed="1" divlinedashlen="1" divlinegaplen="1" showxaxisline="1" legendPosition="right" 
					xaxislinethickness="1" xaxislinecolor="#999999" showalternatehgridcolor="0" drawAnchors="0" 
					drawcrossline="1" crosslinecolor="#cccccc" crosslinealpha="100">  
EOT;
				}
			?>
			<?php if (count($custodia)) : ?>
				<div>
					<div id="c7d" style="width:300px;display:inline-block">
					
					</div>
					<div id="c30d" style="width:300px;display:inline-block">
					
					</div>
					<div id="c365d" style="width:300px;display:inline-block">
					
					</div>
				</div>
				<div id="chartPerformance" style="display:block;vertical-align:top;">
					
				</div>
			<?php endif; ?>
				<div style="display:block;">
					<table class="table table-striped table-bordered table-hover table-condensed" id="dataTables-example">
						<thead>
							<tr>
								<th class="col-xs-1">Data</th>
								<th class="col-xs-1">Valor Cota</th>
								<th class="col-xs-1">Nº de Cotas</th>
								<th class="col-xs-1">Custódia</th>
								<th class="col-xs-1">Resultados</th>
								<th class="col-xs-1">Compras</th>
								<th class="col-xs-1">Vendas</th>
								<th class="col-xs-1">Ibovespa</th>
								<th class="col-xs-1">CDI</th>
							</tr>
						</thead>
						<tbody>
						<?php if (count($custodia)) : ?>
						<?php 
							$cota_valor = 100;
							$cdi_valor = 100;
							$cota_qtde = 0; 
							$compra_d = 0;
							$venda_d = 0;
							$resultado_d = 0;
							$ibov_d = 0;
							$ibov_u = 0;
							$cdi_d = 0;
							if ($compras) {
								$compras_datas = array_column($compras,"nota_data");
								$compras_valores = array_column($compras,"compras");
							}
							if ($vendas) {
								$vendas_datas = array_column($vendas,"nota_data");
								$vendas_valores = array_column($vendas,"vendas");
							}
							if ($ibovespa) {
								$ibov_datas = array_column($ibovespa,"data");
								$ibov_valores = array_column($ibovespa,"fechamento");
								$ibov_zero = $ibov_valores[0];
							}
							if ($cdi) {
								$cdi_datas = array_column($cdi,"data");
								$cdi_valores = array_column($cdi,"taxa_diaria");
							}
							$categorias = '<categories>';
							$valor_fundo = '<dataset seriesname="Fundo Ações" renderas="Line" >';
							$ibov_fundo= '<dataset seriesname="Ibovespa" renderas="Line" color="FF7F27" >';
							$cdi_fundo= '<dataset seriesname="CDI" renderas="Line" color="FF0000" >';
							$j=0;
						?>
						<?php foreach ($custodia AS $item): ?>
							<?php
								if (is_array($compras_datas)) {
									if (is_numeric(array_search($item["data"],$compras_datas))) {
										$compra_d = $compras_valores[array_search($item["data"],$compras_datas)];
									} else {
										$compra_d = 0;
									}
								}
								if (is_array($vendas_datas)) {
									if (is_numeric(array_search($item["data"],$vendas_datas))) {
										$venda_d = $vendas_valores[array_search($item["data"],$vendas_datas)];
									} else {
										$venda_d = 0;
									}
								}
								if (is_array($res_datas)) {
									if (is_numeric(array_search($item["data"],$res_datas))) {
										$resultado_d = $res_valores[array_search($item["data"],$res_datas)];
									} else {
										$resultado_d = 0;
									}
								}
								if (is_array($ibov_datas)) {
									if (is_numeric(array_search($item["data"],$ibov_datas))) {
										$ibov_d = $ibov_valores[array_search($item["data"],$ibov_datas)];
										$ibov_u = $ibov_d;
									} else {
										$ibov_d = $ibov_u;
									}
								}
								if (is_array($cdi_datas)) {
									if (is_numeric(array_search($item["data"],$cdi_datas))) {
										$cdi_d = $cdi_valores[array_search($item["data"],$cdi_datas)];
									} else {
										$cdi_d = 1;
									}
								}
								//Sempre que há compras, compramos cota com o valor de ontem (ou da abertura)
								if ($compra_d > 0 && $cota_valor <> 0) {
									$cota_qtde = $cota_qtde + ($compra_d / $cota_valor);
								}
								//Agora que já fizemos as compras de cotas do dia
								//Calculamos o valor da cota, ou seja, o patrimonio dentro do fundo
								//Que é a custódia (R$) + Resultados do dia dividido pelo numero de cotas
								if ($cota_qtde <> 0)
									$cota_valor = ($item["custodia"] + $venda_d)/$cota_qtde;
								//Agora, se houve venda, então tiramos dinheiro do fundo, menos cotas:
								if ($venda_d > 0 && $cota_valor <> 0) {
									$cota_qtde = $cota_qtde - ($venda_d / $cota_valor);
								}
								if ($cota_qtde < 0)
									$cota_qtde = 0;
								
								$cdi_valor = $cdi_valor * $cdi_d;
								$aCDI[$j]= $cdi_valor;
								$aCotas[$j]=$cota_valor;
								$j=$j+1;
							?>		
							<tr>
								<td><?=date_format(date_create($item["data"]),'d/m/Y')?></td>
								<td><?=number_format($cota_valor,2)?></td>
								<td><?=number_format($cota_qtde,2)?></td>
								<td><?=number_format($item["custodia"],2)?></td>
								<td><?=number_format($resultado_d,2)?></td>
								<td><?=number_format($compra_d,2)?></td>
								<td><?=number_format($venda_d,2)?></td>
								<td><?=($ibov_zero <> 0) ? number_format(100*$ibov_d/$ibov_zero,2) : 'NA'?></td>
								<td><?=number_format($cdi_valor,2)?></td>
							</tr>
							<?php
								$categorias = $categorias . '<category label="'.date_format(date_create($item["data"]),'d/m/Y').'" />';
								$valor_fundo = $valor_fundo . '<set value="'.number_format($cota_valor,2).'" showValue="0"/>';
								$ibov_fundo = $ibov_fundo . '<set value="'.number_format(100*$ibov_d/$ibov_zero,2).'" showValue="0"/>';
								$cdi_fundo = $cdi_fundo. '<set value="'.number_format($cdi_valor,2).'" showValue="0"/>';
							?>
						<?php endforeach;?>
						<?php
							$categorias = $categorias . '</categories>';
							$valor_fundo = $valor_fundo . '</dataset>';
							$ibov_fundo = $ibov_fundo . '</dataset>';
							$cdi_fundo = $cdi_fundo . '</dataset>';
							$grafico = $grafico . $categorias . $valor_fundo . $ibov_fundo . $cdi_fundo . '</chart>';
						?>
						<?php
							$lFundo_i = sizeof($aCotas);
							$lIbov_i = sizeof($ibov_datas);
							$lCDI_i = sizeof($cdi_datas);	
							$Fundo_v = $aCotas[$lFundo_i-1];
							$Ibov_v = $ibov_valores[$lIbov_i-1];
							$CDI_v = $aCDI[$lCDI_i-1];
							$Fundo7_v =  number_format(100*($Fundo_v/$aCotas[$lFundo_i-min(5+1,$lFundo_i-1)]-1),2);
							$Ibov7_v =	 number_format(100*($Ibov_v/$ibov_valores[$lIbov_i-min(5+1,$lIbov_i-1)]-1),2);
							$CDI7_v = 	 number_format(100*($CDI_v/$aCDI[$lCDI_i-min(5+1,$lCDI_i-1)]-1),2);
							$Fundo30_v = number_format(100*($Fundo_v/$aCotas[$lFundo_i-min(21+1,$lFundo_i-1)]-1),2);
							$Ibov30_v =  number_format(100*($Ibov_v/$ibov_valores[$lIbov_i-min(21+1,$lIbov_i-1)]-1),2);
							$CDI30_v = 	  number_format(100*($CDI_v/$aCDI[$lCDI_i-min(21+1,$lCDI_i-1)]-1),2);
							$Fundo365_v = number_format(100*($Fundo_v/$aCotas[$lFundo_i-min(252,$lFundo_i-1)]-1),2);
							$Ibov365_v =  number_format(100*($Ibov_v/$ibov_valores[$lIbov_i-min(252,$lIbov_i-1)]-1),2);
							$CDI365_v =	  number_format(100*($CDI_v/$aCDI[$lCDI_i-min(252,$lCDI_i-1)]-1),2);
							
							$grafico7d = <<<EOT
							<chart caption="Últ. 7d" numbersuffix="%" plotfillalpha="80" palettecolors="#0075c2,#1aaf5d" 
							basefontcolor="#333333" basefont="Helvetica Neue,Arial" captionfontsize="14" 
							subcaptionfontsize="14" subcaptionfontbold="0" showborder="0" 
							bgcolor="#ffffff" showshadow="0" canvasbgcolor="#ffffff" canvasborderalpha="0" 
							divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlineisdashed="1" 
							divlinedashlen="1" divlinegaplen="1" useplotgradientcolor="0" showplotborder="0" 
							valuefontcolor="#ffffff" placevaluesinside="1" showhovereffect="1" rotatevalues="1" 
							showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" 
							showalternatehgridcolor="0" legendbgalpha="0" legendborderalpha="0" 
							legendshadow="0" legenditemfontsize="10" legenditemfontcolor="#666666"
							drawcrossline="1" crosslinecolor="#cccccc" crosslinealpha="10">
								<categories>
									<category label="" />
								</categories>
								<dataset seriesname="FV">
									<set value="$Fundo7_v" />
								</dataset>
								<dataset seriesname="Ibov" color="FF7F27">
									<set value="$Ibov7_v" />
								</dataset>
								<dataset seriesname="CDI" color="FF0000">
									<set value="$CDI7_v" />
								</dataset>
							</chart>
EOT;
$grafico30d = <<<EOT
							<chart caption="Últ. 30d" numbersuffix="%" plotfillalpha="80" palettecolors="#0075c2,#1aaf5d" 
							basefontcolor="#333333" basefont="Helvetica Neue,Arial" captionfontsize="14" 
							subcaptionfontsize="14" subcaptionfontbold="0" showborder="0" 
							bgcolor="#ffffff" showshadow="0" canvasbgcolor="#ffffff" canvasborderalpha="0" 
							divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlineisdashed="1" 
							divlinedashlen="1" divlinegaplen="1" useplotgradientcolor="0" showplotborder="0" 
							valuefontcolor="#ffffff" placevaluesinside="1" showhovereffect="1" rotatevalues="1" 
							showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" 
							showalternatehgridcolor="0" legendbgalpha="0" legendborderalpha="0" 
							legendshadow="0" legenditemfontsize="10" legenditemfontcolor="#666666"
							drawcrossline="1" crosslinecolor="#cccccc" crosslinealpha="10">
								<categories>
									<category label="" />
								</categories>
								<dataset seriesname="FV">
									<set value="$Fundo30_v" />
								</dataset>
								<dataset seriesname="Ibov" color="FF7F27">
									<set value="$Ibov30_v" />
								</dataset>
								<dataset seriesname="CDI" color="FF0000">
									<set value="$CDI30_v" />
								</dataset>
							</chart>
EOT;
$grafico365d = <<<EOT
							<chart caption="Últ. ano" numbersuffix="%" plotfillalpha="80" palettecolors="#0075c2,#1aaf5d" 
							basefontcolor="#333333" basefont="Helvetica Neue,Arial" captionfontsize="14" 
							subcaptionfontsize="14" subcaptionfontbold="0" showborder="0" 
							bgcolor="#ffffff" showshadow="0" canvasbgcolor="#ffffff" canvasborderalpha="0" 
							divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlineisdashed="1" 
							divlinedashlen="1" divlinegaplen="1" useplotgradientcolor="0" showplotborder="0" 
							valuefontcolor="#ffffff" placevaluesinside="1" showhovereffect="1" rotatevalues="1" 
							showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" 
							showalternatehgridcolor="0" legendbgalpha="0" legendborderalpha="0" 
							legendshadow="0" legenditemfontsize="10" legenditemfontcolor="#666666"
							drawcrossline="1" crosslinecolor="#cccccc" crosslinealpha="10">
								<categories>
									<category label="" />
								</categories>
								<dataset seriesname="FV">
									<set value="$Fundo365_v" />
								</dataset>
								<dataset seriesname="Ibov" color="FF7F27">
									<set value="$Ibov365_v" />
								</dataset>
								<dataset seriesname="CDI" color="FF0000">
									<set value="$CDI365_v" />
								</dataset>
							</chart>
EOT;
						?>
						<script type="text/javascript">
						FusionCharts.ready(function(){
							  var revenueChart = new FusionCharts({
								"type": "msline",
								"renderAt": "chartPerformance",
								"width": "1000",
								"height": "500",
								"dataFormat": "xml",
								"dataSource": <?php echo json_encode($grafico); ?>   });

							revenueChart.render();
						})
						FusionCharts.ready(function(){
							  var revenueChart = new FusionCharts({
								"type": "mscolumn2d",
								"renderAt": "c7d",
								"width": "300",
								"height": "300",
								"dataFormat": "xml",
								"dataSource": <?php echo json_encode($grafico7d); ?>   });

							revenueChart.render();
						})
						FusionCharts.ready(function(){
							  var revenueChart = new FusionCharts({
								"type": "mscolumn2d",
								"renderAt": "c30d",
								"width":  "300",
								"height": "300",
								"dataFormat": "xml",
								"dataSource": <?php echo json_encode($grafico30d); ?>   });

							revenueChart.render();
						})
						FusionCharts.ready(function(){
							  var revenueChart = new FusionCharts({
								"type": "mscolumn2d",
								"renderAt": "c365d",
								"width":  "300",
								"height": "300",
								"dataFormat": "xml",
								"dataSource": <?php echo json_encode($grafico365d); ?>   });

							revenueChart.render();
						})
						</script>
						<?php else: ?>
							<tr>
								<td colspan="9">Sem dados para gerar!</td>
							</tr>
						<?php endif;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /#page-wrapper -->
