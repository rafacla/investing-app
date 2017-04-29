<!-- #page-wrapper -->
<div id="page-wrapper" style="width:100%;">
	<div class="conteudo">
		<div class="row">
			<div class="col-md-12">
				<div class="page-header users-header">
					<h2><?=$titulo?></h2>
					<p>Esta opção de ajustes só funciona para posições compradas, para posições vendidas você terá de fazer o ajuste manualmente em uma nota de corretagem.
					<br>Certifique-se de primeiro cadastrar as ordens da corretora antes de ajustar, caso contrário o cálculo de preço médio será feito incorretamente.</p>
					<h6><a href="<?=base_url($this->profile->uniqueid."/acoes/ajustes/")?>">(Voltar a lista de notas)</a></h6>
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
										<div class="form-group" style="display:inline-block; width:160px;">
											<label>Ativo</label>
											<input type="text" class="form-control" placeholder="RAFA3" id="ativo" name="ativo" value="<?=($nota_id == 'new') ? '' : $ordens[0]["ativo_nome"]?>" required>
										</div>
										<div class="form-group" style="display:inline-block; width:calc(100% - 160px - 165px - 180px);vertical-align:top;">
											<label style="display:block;">Corretora</label>											
											<select id="corretora_id" name="corretora_id" class="form-control">
												<option readonly selected></option>
											<?php 
												foreach ($corretoras as $corretora) {
													echo "<option value=\"".$corretora["id"]."\" ".(($nota->corretora_id==$corretora['id']) ? 'selected' : '').">".$corretora['nome']."</option>";
												}
											?>
											</select>
										</div>
										<div class="form-group" style="width:170px;display:inline-block;">
											<label style="display:block;">Tipo de Ajuste:</label>				
											<select id="tipo_ajuste" name="tipo_ajuste" class="form-control" readonly>
												<option readonly selected></option>
												<option value="g" <?=((isset($nota) && $nota->tipo_ajuste=='g') ? 'selected' : '')?>>Grupamento</option>
												<option value="d" <?=((isset($nota) && $nota->tipo_ajuste=='d') ? 'selected' : '')?>>Desdobramento</option>
												<option value="c" <?=((isset($nota) && $nota->tipo_ajuste=='c') ? 'selected' : '')?>>Conversão</option>
											</select>
										</div>
										<a href="#" id="atualizaAjuste" class="btn btn-success">Atualizar Dados!</a>
										<br><br>
									</div>
									<div class="panel panel-default" id="pAjuste">
										<div class="panel-heading">
											<strong>Ajuste</strong>
										</div>
										<div class="panel-body">
											<div style="width:40%;display:inline-block;">
												<input type="text" name="ordem_de_id" value="<?=($nota_id == 'new') ? '' : $ordens[0]["ordem_id"]?>" style="display:none">
												<fieldset id="fDe">
													<legend>Ordem "de"</legend>
													<div class="form-group" style="width:30%;display:inline-block;">
														<label style="display:block;">Ativo</label>
														<input type="text" name="ativo_de" class="form-control" readonly id="ativo_de" value="<?=($nota_id == 'new') ? '' : $ordens[0]["ativo_nome"]?>">
													</div>
													<div class="form-group" style="width:30%;display:inline-block;">
														<label style="display:block;">Quantidade</label>
														<input type="number" step="1" min="0" name="qtde_de" class="form-control" readonly id="qtde_de" value="<?=($nota_id == 'new') ? '' : $ordens[0]["ativo_quantidade"]?>">
													</div>
													<div class="form-group" style="width:30%;display:inline-block;">
														<label style="display:block;">CMC</label>
														<input type="text" id="cmc_de" name="cmc_de" class="form-control" readonly>
													</div>
												</fieldset>
											</div>
											<div style="width:19%;display:inline-block;text-align:center;vertical-align:middle;padding-bottom:20px;">
												<i class="fa fa-arrow-right fa-5x" aria-hidden="true"></i>
											</div>
											<div style="width:40%;display:inline-block;">
												<input type="text" name="ordem_para_id" value="<?=($nota_id == 'new') ? '' : $ordens[1]["ordem_id"]?>" style="display:none">
												<fieldset id="fPara">
													<legend>Ordem "para"</legend>
													<div class="form-group" style="width:30%;display:inline-block;">
														<label style="display:block;">Ativo</label>
														<input type="text" name="ativo_para" class="form-control" id="ativo_para" value="<?=($nota_id == 'new') ? '' : $ordens[1]["ativo_nome"]?>">
													</div>
													<div class="form-group" style="width:30%;display:inline-block;">
														<label style="display:block;">Quantidade</label>
														<input type="number" step="1" min="0" type="text" name="qtde_para" class="form-control" id ="qtde_para" value="<?=($nota_id == 'new') ? '' : $ordens[1]["ativo_quantidade"]?>">
													</div>
													<div class="form-group" style="width:30%;display:inline-block;">
														<label style="display:block;">CMC</label>
														<input type="text" id="cmc_para" name="cmc_para" class="form-control" readonly>
													</div>
												</fieldset>
											</div>
										</div>							
									</div>
									<div style="display:none;">
										<div class="form-group" style="display:inline-block; min-width:150px; width:19.5%">
											<label>IRPF (Normal)</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="irpf_n" name="irpf_n" value="<?=($nota_id == 'new') ? '0' : $nota->irpf_n?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block; min-width:150px; width:19.5%">
											<label>IRPF (Day-Trade)</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="irpf_dt" name="irpf_dt" value="<?=($nota_id == 'new') ? '0' : $nota->irpf_dt?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block;min-width:150px; width:19.5%">
											<label>Taxas CBLC</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="cblc" name="cblc" value="<?=($nota_id == 'new') ? '0' : $nota->taxas_cblc?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block;min-width:150px; width:19.5%">
											<label>Taxas Bovespa</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="bovespa" name="bovespa" value="<?=($nota_id == 'new') ? '0' : $nota->taxas_bovespa?>" required>
											</div>
										</div>
										<div class="form-group" style="display:inline-block;min-width:150px; width:19.5%">
											<label>Corretagem Total</label>
											<div class="input-group">
												<span class="input-group-addon">R$</span>
												<input type="number" step="0.01" min="0" class="form-control" placeholder="0,00" id="corretagem" name="corretagem" value="<?=($nota_id == 'new') ? '0' : $nota->taxas_corretagem?>" required>
											</div>
										</div>
									</div>
									<div style="float:right;">
										<button id="btRegistrar" type="submit" class="btn btn-primary" name="salvaNota" value="0" default>Salvar</button>
										<a href="<?=base_url($this->profile->uniqueid."/acoes/ajustes/")?>" class="btn btn-default">Cancelar e voltar a lista</a>
									</div>
								</form>
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
</div>
<!-- /#page-wrapper -->
<script>
	$(function () {
		$(document).on('click', '#atualizaAjuste', function(event) {
			atualizaAtivo();	
		});
		
		$('input').change(function() {
			verificaTipoAjuste();
			calculaNovaPosicao();
			if ($('#ativo_para').val()!='' && $('#qtde_para').val()!='') {
				$('#btRegistrar').prop('readonly',false);
			} else {
				$('#btRegistrar').prop('readonly',true);
				$('#btRegistrar').focus();
			}
		});
		
		atualizaAtivo();
		
		
	});
	function atualizaAtivo() {
		data = $('#data').val();
		ativo = $('#ativo').val();
		corretora_id = $('#corretora_id').val();
		
		$.post(base_url+'acoes/ajustes/getPosicao',
		{
			data: data,
			ativo: ativo,
			corretora_id: corretora_id
		}).done (function (dados) {
			
			posicao = $.parseJSON(dados);
			$('#ativo_de').val(posicao.ativo_nome);
			$('#qtde_de').val(posicao.qt_final);
			$('#cmc_de').val(posicao.CMC);
			calculaNovaPosicao();
		});
	}
	
	function verificaTipoAjuste() {
		if ($('#ativo_de').val()==$('#ativo_para').val()) {
			if ($('#qtde_de').val() > $('#qtde_para').val()) {
				$('#tipo_ajuste').val('g');
			} else {
				$('#tipo_ajuste').val('d');
			}
		} else {
			$('#tipo_ajuste').val('c');
		}
	}
	
	function calculaNovaPosicao() {
		if ($.isNumeric($('#qtde_para').val()) && $('#qtde_para').val() > 0 && $('#qtde_de').val() > 0) {
			fator = $('#qtde_para').val()/$('#qtde_de').val();
			$('#cmc_para').val($('#cmc_de').val()/fator);
		} else {
			$('#cmc_para').val('');
		}
	}
</script>