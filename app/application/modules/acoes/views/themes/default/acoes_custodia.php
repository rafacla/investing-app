<!-- scripts para os gráficos -->
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
<!-- #page-wrapper -->
<div id="page-wrapper">
	<?php 
		//$categorias = array(0, 1, 2, 3, 4, 5);
		//$resultados = array(0, 1, 2, 3, 4, 5);
		$patrimonio = array(0, 1, 2, 3, 4, 5);
		$despesas = array(0, 1, 2, 3, 4, 5);
		$grafico = <<<EOT
		<chart caption="Posições encerradas" pyaxisname="Valores (BRL)" tooltipbordercolor="#444444" 
		tooltipbgcolor="#666666" tooltipbgalpha="80" placevaluesInside="1" valueFontColor="#ffffff" 
		valueFontSize="10" valueFontBold="1" theme="fint" decimals="2" rotateValues="0"
		negativeColor="ff1c1c" positiveColor="0072ca" showShadow="1" showSumAtEnd="0" labelDisplay="ROTATE" slantLabels="1">
			<dataset seriesname="Resultados">
EOT;
		for ($i=0;$i<$cPontos;$i++) {
			$grafico = $grafico. "<set label=\"".$categorias[$i]."\" tooltext=\"Data: ".$categorias[$i]."{br}". $ativos[$i] . " - " .$quantidades[$i] . "{br}Resultado: ".round($resultados[$i],2)."\" value=\"".round($resultados[$i],2)."\" />";
		}
		$grafico = $grafico . "<set label=\"Total\" issum=\"1\" color=\"7ac13a\" />";
		$grafico = $grafico . "</dataset></chart>";
	?>
	<div id="chartContainer">FusionCharts XT will load here!</div>
</div>

<script type="text/javascript">
FusionCharts.ready(function(){
      var revenueChart = new FusionCharts({
        "type": "waterfall2d",
        "renderAt": "chartContainer",
        "width": "800",
        "height": "500",
        "dataFormat": "xml",
        "dataSource": <?php echo json_encode($grafico); ?>   });

    revenueChart.render();
})
</script>
<!-- /#page-wrapper -->
