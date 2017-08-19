<!-- scripts para os gráficos -->
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
<!-- #page-wrapper -->
<div class="conteudo">
	<div class="row">
		<div class="page-header users-header">
			<h2>Posições Encerradas</h2>
		</div>
	</div>
	<?php 
		if (sizeof($categorias) > 0) {
			$grafico = <<<EOT
			<chart pyaxisname="Valores (BRL)" tooltipbordercolor="#444444" 
			tooltipbgcolor="#666666" tooltipbgalpha="80" placevaluesInside="1" valueFontColor="#ffffff" 
			valueFontSize="10" valueFontBold="1" theme="fint" decimals="2" rotateValues="1"
			negativeColor="ff1c1c" positiveColor="0072ca" showShadow="1" showSumAtEnd="0" labelDisplay="ROTATE" slantLabels="1" bgColor="#fcfcfc" bgAlpha="100">
				<dataset seriesname="Resultados">
EOT;
			for ($i=0;$i<$cPontos;$i++) {
				$grafico = $grafico. "<set label=\"".$categorias[$i]."\" tooltext=\"Data: ".$categorias[$i]."{br}". $ativos[$i] . " - " .$quantidades[$i] . "{br}Resultado: ".round($resultados[$i],2)."\" value=\"".round($resultados[$i],2)."\" />";
			}
			$grafico = $grafico . "<set label=\"Total\" issum=\"1\" color=\"7ac13a\" />";
			$grafico = $grafico . "</dataset></chart>";
		}
	?>
	
	<div id="chartContainer">Não há ordens suficientes para compor um gráfico.</div>
</div>
<?php if (sizeof($categorias) > 0) : ?>
<script type="text/javascript">
	$(function() {
		maxW = $('.conteudo').width()-10;
		maxH = $('.conteudo').height() - $('.page-header.users-header').height() - 70;
		console.log(maxW);
		FusionCharts.ready(function(){
			var revenueChart = new FusionCharts({
			"type": "waterfall2d",
			"renderAt": "chartContainer",
			"width": maxW,
			"height": maxH,
			"dataFormat": "xml",
			"dataSource": <?php echo json_encode($grafico); ?>   });

			revenueChart.render();
		});
	});
</script>
<?php endif; ?>
<!-- /#page-wrapper -->
