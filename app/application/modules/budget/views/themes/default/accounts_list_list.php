<!-- scripts para os gráficos -->
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>
<style>
.divcont {
	display:inline-block;
	vertical-align: top;
}
.divcont.contas {
	width: 400px;
}
.divcont-fluxo {
	min-width: 300px;
	width: calc(100vw - 650px);
	min-height: 400px;
}
</style>
<!-- Navigation -->
	<div id="contas" class="divcont contas">
		<h3>Lista de Contas
			<a data-toggle="modal" href="#adicionaConta" id="btImport" class="btn btn-success btn-xs"><?=lang('sidemenu_accounts_add');?></a>
		</h3>
		<?php if(isset($saldos)): ?>
		<table class="table table-condensed">
			<thead>
				<tr>
					<td><strong>Conta</strong></td>
					<td align="right"><strong>Saldo</strong></td>
				</tr>
			</thead>
			<tbody>
				<?php $totalSaldo = 0; ?>
				<?php foreach($saldos as $key => $valor){?>
				<?php $totalSaldo += $valor;?>
				<tr>
					<td><a href="<?=base_url().$profile_uid."/accounts/".$links[$key]?>"><?=$key;?></a></td>
					<td align="right"><?=number_format($valor,2,",",".");?></td>
				</tr>
					<?php };?>
			</tbody>
			<tfoot>
				<tr>
					<td><strong>Total</strong></td>
					<td align="right"><strong><?=number_format($totalSaldo,2,",",".");?></strong></td>
				</tr>
			</tfoot>
		</table>
		<?php endif;?>
	</div>
	<div id="fluxo" class="divcont divcont-fluxo">
		<h3>Fluxo de Caixa</h3>
		<div id="chartFluxo">
		</div>		
	</div>
<?php
	if (count($fluxo)) {
		$grafico = <<<EOT
			<chart pyaxisname="Valor da Cota (por 100)" linethickness="2" palettecolors="#0075c2" basefontcolor="#333333"
			showvalues="0" showborder="0" bgcolor="#ffffff" showshadow="1" canvasbgcolor="#ffffff" 
			canvasborderalpha="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" 
			divlineisdashed="1" divlinedashlen="1" divlinegaplen="1" showxaxisline="1" showLegend="0" 
			xaxislinethickness="1" xaxislinecolor="#999999" showalternatehgridcolor="0" drawAnchors="0" 
			drawcrossline="1" crosslinecolor="#cccccc" crosslinealpha="100">
EOT;
		}
	$categorias = "<categories>";
	$valor = '<dataset seriesname="Fluxo de Caixa" renderas="Line" >';
	$vAcum = 0.00;
	foreach ($fluxo as $ponto => $valores)  {
		//echo number_format(array_sum($valores),2,".","")."<br>";
		$categorias = $categorias . '<category label="'.$ponto.'" />';
		$vAcum+=array_sum($valores);
		$valor = $valor . '<set value="'.number_format($vAcum,2,".","").'" showValue="0"/>';
	}
	$categorias = $categorias . '</categories>';
	$valor = $valor . '</dataset>';
	$grafico = $grafico . $categorias . $valor . '</chart>';
?>
<script type="text/javascript">
	FusionCharts.ready(function(){
		  var revenueChart = new FusionCharts({
			"type": "msline",
			"renderAt": "chartFluxo",
			"width": $('#fluxo').width(),
			"height": $('#fluxo').height(),
			"dataFormat": "xml",
			"dataSource": <?php echo json_encode($grafico); ?>   });

		revenueChart.render();
	})
</script>
<!-- Modal -->
<div id="adicionaConta" class="modal fade">
  <div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Adicionar nova conta...</h4>
		</div>
		<form action="/criaConta" method="post" enctype="multipart/form-data">
			<div id="criaConta" class="modal-body">
				<strong>Nome da conta:</strong>
				<input type="text" name="conta_nome" class="form-control" placeholder="Nome da Conta">
				<strong>Descrição da conta:</strong>
				<textarea col="10" rows="4" name="conta_descricao" class="form-control"></textarea>
				<strong>Saldo na data de hoje:</strong>
				<input type="text" name="valor_reconciliado" class="form-control" placeholder="Valor atual">
				<input type="text" style="display:none" name="url" value="<?=base_url($profile_uid.'/accounts')?>"></li>
				<br />
				<div style="text-align:right">
					<button type="submit" class="btn btn-info" id="btSalvaConta">
						<span class="glyphicon glyphicon-piggy-bank" aria-hidden="true"></span> Criar
					</button>
				</div>
			</div>
		</form>
	</div>
	</div>
</div>