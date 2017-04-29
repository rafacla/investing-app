<!-- #page-wrapper -->
<div id="page-wrapper" style="width:100%;">
	<div class="conteudo">
		<div class="row">
			<div class="col-md-12">
				<div class="page-header users-header">
					<h2><?=$titulo?></h2>
					<h5><a href="<?=base_url($this->profile->uniqueid."/acoes/notas/")?>">Voltar a lista de notas</a> | <a href="<?=base_url($this->profile->uniqueid."/acoes/notas/edit/new")?>">Adicionar nova nota</a></h5>
				</div>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<div class="row">
			<div class="col-md-12">
			<?php
			
				if (strlen(validation_errors())>0) {
					echo "<div class=\"alert alert-danger\" role=\"alert\">";
					echo validation_errors(); 
					echo "</div>";
				}
				?>
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<form method="post" enctype="multipart/form-data">
									<input name="nota_id" id="nota_id" value="<?=($nota_id == 'new') ? '' : $nota_id?>" style="display:none">
									<div>
										<div class="form-group" style="display:inline-block; width:160px;">
											<label>Data</label>
											<input type="date" class="form-control" placeholder="dd/mm/aaaa" id="data" name="data" value="<?=($nota_id == 'new') ? '' : date_format(date_create($nota->nota_data),"Y-m-d")?>" required>
										</div>
										<div class="form-group" style="display:inline-block; width:calc(100% - 160px - 180px - 170px);vertical-align:top;">
											<label style="display:block;">Corretora</label>											
											<select id="corretora_id" name="corretora_id" class="form-control">
												<option disabled selected></option>
											<?php 
												foreach ($corretoras as $corretora) {
													echo "<option value=\"".$corretora["id"]."\" ".(($nota->corretora_id==$corretora['id']) ? 'selected' : '').">".$corretora['nome']."</option>";
												}
											?>
											</select>
										</div>
										<div class="form-group" style="display:inline-block; width:180px">
											<label>Número da Nota</label>
											<input class="form-control" placeholder="ABC2015201" id="nota_numero" name="nota_numero" value="<?=($nota_id == 'new') ? '' : $nota->nota_numero?>" required>
										</div>
										<div class="form-group" style="display:inline-block;width:150px;vertical-align:top;">
											<label>Valores na nota</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input class="form-control" placeholder="0,00" id="totalNota" value="<?=($nota_id == 'new') ? '' : $nota->valor_transacoes?>" disabled>
											</div>
										</div>
									</div>
									<div>
										<div class="form-group" style="display:inline-block; min-width:150px; width:19.5%">
											<label>IRPF (Normal)</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="irpf_n" name="irpf_n" value="<?=($nota_id == 'new') ? '' : $nota->irpf_n?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block; min-width:150px; width:19.5%">
											<label>IRPF (Day-Trade)</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="irpf_dt" name="irpf_dt" value="<?=($nota_id == 'new') ? '' : $nota->irpf_dt?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block;min-width:150px; width:19.5%">
											<label>Taxas CBLC</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="cblc" name="cblc" value="<?=($nota_id == 'new') ? '' : $nota->taxas_cblc?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block;min-width:150px; width:19.5%">
											<label>Taxas Bovespa</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="bovespa" name="bovespa" value="<?=($nota_id == 'new') ? '' : $nota->taxas_bovespa?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block;min-width:150px; width:19.5%">
											<label>Corretagem Total</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="corretagem" name="corretagem" value="<?=($nota_id == 'new') ? '' : $nota->taxas_corretagem?>" required>
											</div>
										</div>
									</div>
									<div style="float:right;">
										<button id="btRegistrar" type="submit" class="btn btn-primary" name="salvaNota" value="0" default>Salvar</button>
										<a href="<?=base_url($this->profile->uniqueid."/acoes/notas/")?>" class="btn btn-default">Cancelar e voltar a lista</a>
									</div>
								</form>
								<br><br>
								<h4>Lista de ordens <a <?=($nota_id == 'new') ? 'disabled title="Você deve salvar sua nota antes de acionar uma ordem!"' : ''?> data-toggle="modal" href="#adicionaOrdem" class="btn btn-success btn-xs" id="addOrdem"><i class="fa fa-plus-circle"></i> Adicionar Ordem</a></h4>
								<hr>
								<table class="table table-condensed table-striped table-hover">
									<thead>
										<th class="col-xs-1">Ativo</th>
										<th class="col-xs-1">C/V</th>
										<th class="col-xs-1">Tipo</th>
										<th class="col-xs-1">Quantidade</th>
										<th class="col-xs-1">Preço</th>
										<th class="col-xs-1">Valor</th>
										<th class="col-xs-1">IRPF</th>
										<th class="col-xs-1">CBLC</th>
										<th class="col-xs-1">Bovespa</th>
										<th class="col-xs-1">Corretagem</th>
									</thead>
									<tbody>
										<?php if (isset($ordens)) : ?>
											<?php foreach ($ordens as $item): ?>
												<tr>
													<td id="ativo_nome_<?=$item["ordem_id"]?>"><?=$item["ativo_nome"]?></td>
													<td id="operacao_<?=$item["ordem_id"]?>"><?=$item["operacao"]?></td>
													<td id="tipo_operacao_<?=$item["ordem_id"]?>"><?=$item["tipo_operacao"]?></td>
													<td id="ativo_quantidade_<?=$item["ordem_id"]?>"><?=$item["ativo_quantidade"]?></td>
													<td id="ativo_valor_<?=$item["ordem_id"]?>"><?=number_format($item["ativo_valor"],2)?></td>
													<td><?=number_format($item["ativo_trans_valor"],2)?></td>
													<td><?=number_format(($item["irpf_n"]+$item["irpf_dt"]),2)?></td>
													<td><?=number_format($item["taxa_cblc"],2)?></td>
													<td><?=number_format($item["taxa_bovespa"],2)?></td>
													<td><?=number_format($item["taxa_corretagem"],2)?> 
													<a href="<?=base_url($this->profile->uniqueid."/acoes/notas/deletaOrdem/".$item["ordem_id"])?>"><i class="fa fa-trash-o" aria-hidden="true" style="float:right;padding:1px;"></i></a> 
													<a href="#"><i class="fa fa-pencil" id="eo<?=$item["ordem_id"]?>" data-oid="<?=$item["ordem_id"]?>" aria-hidden="true" style="float:right;padding:1px;"></i></a></td>
												</tr>
											<?php endforeach; ?>
										<?php else : ?>
											<td colspan="9">
												Nenhuma ordem adicionada a esta ordem!
											</td>
										<?php endif ; ?>
									</tbody>
								</table>
							</div>
							
						</div>
						<!-- /.row (nested) -->
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
	</div>
	<script>
		$(function() {
			$(document).on('click', '.fa-pencil', function(event) {
				$('.modal-title').text('Editar ordem');
				$('#adicionaOrdem').modal('show');
				$oid = $('#'+event.target.id).attr('data-oid');
				$('#ordem_id').val($oid);
				$('#ativo_nome').val($('#ativo_nome'+'_'+$oid).text());
				$('#ativo_quantidade').val($('#ativo_quantidade'+'_'+$oid).text());
				$('#operacao').val($('#operacao'+'_'+$oid).text());
				$('#tipo_operacao').val($('#tipo_operacao'+'_'+$oid).text());
				$('#ativo_valor').val($('#ativo_valor'+'_'+$oid).text());
				$('#ativo_nome').focus();
			});
			$(document).on('click', '#addOrdem', function(event) {
				$('#ordem_id').val('');
				$('#ativo_nome').val('');
				$('#ativo_quantidade').val('');
				$('#operacao').val('');
				$('#tipo_operacao').val('');
				$('#ativo_valor').val('');
				$('.modal-title').text('Adicionar nova ordem');
				$('#ativo_nome').focus();
			});
		});
	</script>
	<div id="adicionaOrdem" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Adicionar nova ordem...</h4>
				</div>
				<div id="criaOrdem" class="modal-body">
					<form method="post" enctype="multipart/form-data">
						<input type="text" name="ordem_id" class="form-control" id="ordem_id" style="display:none">
						<div>
							<div class="form-group col-xs-6">
								<strong>Nome do ativo:</strong>
								<input type="text" name="ativo_nome" class="form-control" placeholder="Sigla da Bovespa" id="ativo_nome" style="text-transform:uppercase" required>
							</div>
							<div class="form-group col-xs-6">
								<strong>Quantidade:</strong>
								<input type="number" min="0" name="ativo_quantidade" class="form-control" placeholder="Quantas ações?" id="ativo_quantidade" required>
							</div>
						</div>
						<div>
							<div class="form-group col-xs-6">
								<strong>Compra ou venda?</strong>
								<select class="form-control" id="operacao" name="operacao" required>
									<option value="c">Compra</option>
									<option value="v">Venda</option>
								</select>
							</div>
							<div class="form-group col-xs-6">
								<strong>Tipo de Operação:</strong>
								<select class="form-control" id="tipo_operacao" name="tipo_operacao" placeholder="Normal ou Day-Trade" required>
									<option value="n">Normal</option>
									<option value="dt">Day-Trade</option>
								</select>
							</div>
							<div class="form-group col-xs-6">
								<strong>Preço:</strong>
								<input type="number" step="0.01" min="0" name="ativo_valor" class="form-control" placeholder="Valor da ação" id="ativo_valor" required>
							</div>
						</div>
					<h6 style="color:#fff;">Fim do formulário</h6>					
					<button type="submit" class="btn btn-info btn-sm" id="btSalvar" name="editaOrdem" value="0">Adicionar</button>
						<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" aria-hidden="true">Cancelar</button>
					</form>
					
				</div>
			</div>
		</div>	
	</div>

</div>
<!-- /#page-wrapper -->
