<!-- scripts para os gráficos -->
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>

	<?php 

		$grafico = <<<EOT
		<chart caption="Receitas x Gastos (mês)" pyaxisname="Valores (BRL)" syaxisname="Patrimônio (BRL)" tooltipbordercolor="#444444" tooltipbgcolor="#666666" tooltipbgalpha="80" placevaluesInside="1" valueFontColor="#ffffff" valueFontBold="1" theme="fint">
			<categories>
				<category label="$categorias[0]" />
				<category label="$categorias[1]" />
				<category label="$categorias[2]" />
				<category label="$categorias[3]" />
				<category label="$categorias[4]" />
				<category label="$categorias[5]" />
			</categories>
			<dataset seriesname="Receitas">
				<set tooltext="Mês: $categorias[0]{br}Receitas: $receitas[0]{br}Despesas: $despesas[0]{br}Patrimônio: $patrimonio[0]" value="$receitas[0]" />
				<set tooltext="Mês: $categorias[1]{br}Receitas: $receitas[1]{br}Despesas: $despesas[1]{br}Patrimônio: $patrimonio[1]" value="$receitas[1]" />
				<set tooltext="Mês: $categorias[2]{br}Receitas: $receitas[2]{br}Despesas: $despesas[2]{br}Patrimônio: $patrimonio[2]" value="$receitas[2]" />
				<set tooltext="Mês: $categorias[3]{br}Receitas: $receitas[3]{br}Despesas: $despesas[3]{br}Patrimônio: $patrimonio[3]" value="$receitas[3]" />
				<set tooltext="Mês: $categorias[4]{br}Receitas: $receitas[4]{br}Despesas: $despesas[4]{br}Patrimônio: $patrimonio[4]" value="$receitas[4]" />
				<set tooltext="Mês: $categorias[5]{br}Receitas: $receitas[5]{br}Despesas: $despesas[5]{br}Patrimônio: $patrimonio[5]" value="$receitas[5]" />
			</dataset>
			<dataset seriesname="Despesas" color="#ef2b34">
				<set tooltext="Mês: $categorias[0]{br}Receitas: $receitas[0]{br}Despesas: $despesas[0]{br}Patrimônio: $patrimonio[0]" value="$despesas[0]" />
				<set tooltext="Mês: $categorias[1]{br}Receitas: $receitas[1]{br}Despesas: $despesas[1]{br}Patrimônio: $patrimonio[1]" value="$despesas[1]" />
				<set tooltext="Mês: $categorias[2]{br}Receitas: $receitas[2]{br}Despesas: $despesas[2]{br}Patrimônio: $patrimonio[2]" value="$despesas[2]" />
				<set tooltext="Mês: $categorias[3]{br}Receitas: $receitas[3]{br}Despesas: $despesas[3]{br}Patrimônio: $patrimonio[3]" value="$despesas[3]" />
				<set tooltext="Mês: $categorias[4]{br}Receitas: $receitas[4]{br}Despesas: $despesas[4]{br}Patrimônio: $patrimonio[4]" value="$despesas[4]" />
				<set tooltext="Mês: $categorias[5]{br}Receitas: $receitas[5]{br}Despesas: $despesas[5]{br}Patrimônio: $patrimonio[5]" value="$despesas[5]" />
			</dataset>
			<dataset seriesname="Patrimônio" renderas="Line" parentyaxis="S">
				<set tooltext="Mês: $categorias[0]{br}Receitas: $receitas[0]{br}Despesas: $despesas[0]{br}Patrimônio: $patrimonio[0]" value="$patrimonio[0]" showValue="0"/>
				<set tooltext="Mês: $categorias[1]{br}Receitas: $receitas[1]{br}Despesas: $despesas[1]{br}Patrimônio: $patrimonio[1]" value="$patrimonio[1]" showValue="0"/>
				<set tooltext="Mês: $categorias[2]{br}Receitas: $receitas[2]{br}Despesas: $despesas[2]{br}Patrimônio: $patrimonio[2]" value="$patrimonio[2]" showValue="0"/>
				<set tooltext="Mês: $categorias[3]{br}Receitas: $receitas[3]{br}Despesas: $despesas[3]{br}Patrimônio: $patrimonio[3]" value="$patrimonio[3]" showValue="0"/>
				<set tooltext="Mês: $categorias[4]{br}Receitas: $receitas[4]{br}Despesas: $despesas[4]{br}Patrimônio: $patrimonio[4]" value="$patrimonio[4]" showValue="0"/>
				<set tooltext="Mês: $categorias[5]{br}Receitas: $receitas[5]{br}Despesas: $despesas[5]{br}Patrimônio: $patrimonio[5]" value="$patrimonio[5]" showValue="0"/>
			</dataset>
		</chart>
EOT;
	?>
	<div id="chartContainer">FusionCharts XT will load here!</div>


<script type="text/javascript">
FusionCharts.ready(function(){
      var revenueChart = new FusionCharts({
        "type": "mscombidy2d",
        "renderAt": "chartContainer",
        "width": "800",
        "height": "500",
        "dataFormat": "xml",
        "dataSource": <?php echo json_encode($grafico); ?>   });

    revenueChart.render();
})
</script>
<!-- /#page-wrapper -->
