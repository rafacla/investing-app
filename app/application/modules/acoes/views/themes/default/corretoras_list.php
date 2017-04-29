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
				<h2>Corretoras
					<a href="<?=base_url($this->profile->uniqueid.'/corretoras/add') ?>" class="btn btn-success">Adicionar Corretora</a>
				</h2>
			</div>
		</div>
		<!-- /.row -->
		<div class="row">
			<div class="dataTable_wrapper">
				<table class="table table-striped table-bordered table-hover" id="dataTables-example">
					<thead>
						<tr>
							<th class="col-xs-1">ID</th>
							<th class="col-xs-4">Nome da Corretora</th>
							<th class="col-xs-5">Site da Corretora</th>
							<th class="col-xs-2"></th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($corretoras)): ?>
							<?php foreach ($corretoras as $corretora): ?>
								<tr class="odd gradeX">
									<td><?=$corretora["id"]?></td>
									<td><?=$corretora["nome"]?></td>
									<td><?=$corretora["site"]?></td>
									<td>
										<a href="<?= base_url($this->profile->uniqueid.'/corretoras/edit/'.$corretora["id"])?>" class="btn btn-info">editar</a>  
										<a href="<?= base_url($this->profile->uniqueid.'/corretoras/delete/'.$corretora["id"]) ?>" class="btn btn-danger">deletar</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr class="even gradeC">
								<td colspan="4">Sem corretoras cadastrada!</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>
<!-- /#page-wrapper -->
