<!-- #page-wrapper -->
<div id="page-wrapper" style="width:100%;">
    <div class="conteudo">
		<?php if (isset($deleta_corretora_nome)) : ?>
			<div class="alert alert-warning alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<div>
					<div style="display:inline-block;">
						<span style="font-size: 30px;" class="glyphicon glyphicon-question-sign"></span>
					</div>
					<div style="display:inline-block;">
						<strong>Tem certeza que deseja deletar a corretora <?=$deleta_corretora_nome?>?</strong>
						<br>
						<p>Não será possível desfazer esta ação e todo os investimentos associados a esta corretora serão excluídos juntos!</p>
					</div>				
					<br>
						<form role="form" method="POST" action="<?=base_url($this->profile->uniqueid.'/corretoras/delete/'.$deleta_corretora_id) ?>">
							<button type="submit" value="<?=$deleta_corretora_id?>" class="btn btn-danger" name="deletaCorretora">Confirmar</button> <button type="button" class="btn btn-seconday" data-dismiss="alert">Cancelar</button>
						</form>
					
				</div>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="page-header users-header">
				<h2>Notas de corretagem de <?=$corretora_nome?>
					<a href="<?=base_url($this->profile->uniqueid.'/acoes/notas/add') ?>" class="btn btn-success">Adicionar Nota de Corretagem</a>
				</h2>
			</div>
		</div>
		<!-- /.row -->
		<div class="row">
			<div class="dataTable_wrapper">
				<?php if (count($lista)): ?>
				<table class="table table-striped table-bordered table-hover table-condensed" id="dataTables-example">
					<thead>
						<tr>
							<th class="col-xs-1">Data</th>
							<th class="col-xs-1">Número</th>
							<th class="col-xs-2">Corretora</th>
							<th class="col-xs-1"># Ordens</th>
							<th class="col-xs-1">Valores</th>
							<th class="col-xs-1">IRPF</th>
							<th class="col-xs-1">Corretagem</th>
							<th class="col-xs-1">Outr.taxas</th>
							<th class="col-xs-2"></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($lista as $item): ?>
						<tr class="odd gradeX">
							<td><?=date_format(date_create($item["nota_data"]),'d/m/Y')?></td>
							<td><?=$item["nota_numero"]?></td>
							<td><a href="<?=$item["site"]?>"><?=$item["nome"]?></a></td>
							<td><?=$item["numero_ordens"]?></td>
							<td><?=$item["valor_transacoes"]?></td>
							<td><?=$item["irpf_n"]+$item["irpf_dt"]?></td>
							<td><?=$item["taxas_corretagem"]?></td>
							<td><?=$item["taxas_cblc"]+$item["taxas_bovespa"]?></td>
							<td>
								<a href="<?= base_url($this->profile->uniqueid.'/acoes/notas/edit/'.$item["nota_id"])?>" class="btn btn-info btn-xs">ver nota</a>  
								<a href="<?= base_url($this->profile->uniqueid.'/acoes/nota/delete/'.$item["nota_id"]) ?>" class="btn btn-danger btn-xs">deletar</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
				<p>Nenhuma nota encontrada!</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

</div>
<!-- /#page-wrapper -->
