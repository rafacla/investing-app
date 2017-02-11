<script>
	var contas = <?php echo json_encode(array_values($contas)); ?>;
	var categorias = <?php echo json_encode(array_values($categorias)); ?>;
	var contaID = <?php echo json_encode($contaID); ?>;	
	var contaNome = <?php echo json_encode($contaNome); ?>;
	var lang_btSave = <?=json_encode(lang('accounts_button_save'));?>;
	var lang_btCancel = <?=json_encode(lang('accounts_button_cancel'));?>;
	var lang_labelSubtransaction = <?=json_encode(lang('accounts_add_subtransaction'));?>;
	var lang_error_noaccounts = <?=json_encode(lang('accountsJS_error_noaccounts'));?>;
	var lang_remaining_value = <?=json_encode(lang('accounts_value_left'));?>;
	var lang_remaining_error = <?=json_encode(lang('accountsJS_error_remaining_value'));?>;
	var lang_account_head = <?=json_encode(lang('accounts_heads_account'));?>;
	var lang_account_date = <?=json_encode(lang('accounts_heads_date'));?>;
	var lang_account_payee = <?=json_encode(lang('accounts_heads_payee'));?>;
	var lang_account_inflow = <?=json_encode(lang('accounts_heads_inflow'));?>;
	var lang_account_outflow = <?=json_encode(lang('accounts_heads_outflow'));?>;
	var lang_error_account_notfound = <?=json_encode(lang('accountsJS_error_noaccounts_found'));?>;
	var lang_no_category = <?=json_encode(lang('accounts_no_category'));?>;
	var lang_transferto= <?=json_encode(lang('accounts_add_transferto'));?>;
	
	
</script>

<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top fundoNavBar" role="navigation" style="margin: 0">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	<!-- /.navbar-header -->

	<ul class="nav navbar-top-links fundoNavBar-vertical" style="margin-left:260px">
		<div class="tipDireita">
			<span><?php echo $contaNome; ?></span>
		</div>
		<?php 
			$saldo_n_conciliado = $saldo - $saldo_conciliado;
		?>
		<div class="exibeInformacoes"><span class="Titulo"><?=lang('accounts_value_cleared');?></span><br/><span id="saldoConciliado" class="<?=($saldo_conciliado>=0 ? "SaldoPos" : "SaldoNeg")?>"><?=($saldo_conciliado>=0 ? "$" : "-$")?><?=number_format(abs($saldo_conciliado), 2, '.', ',')?></span></div>
		<div class="exibeInformacoes"><span class="Divisor">+</span></div>
		<div class="exibeInformacoes"><span class="Titulo"><?=lang('accounts_value_not_cleared');?></span><br/><span id="saldoNConciliado" class="<?=($saldo_n_conciliado>=0 ? "SaldoPos" : "SaldoNeg")?>"><?=($saldo_n_conciliado>=0 ? "$" : "-$")?><?=number_format(abs($saldo_n_conciliado), 2, '.', ',')?></span></div>
		<div class="exibeInformacoes"><span class="Divisor">=</span></div>
		<div class="exibeInformacoes"><span class="Titulo"><?=lang('accounts_value_working');?></span><br/><span id="saldoGeral" class="<?=($saldo>=0 ? "SaldoPos" : "SaldoNeg")?>"><?=($saldo>=0 ? "$" : "-$")?><?=number_format(abs($saldo), 2, '.', ',')?></span></div>
	</ul>
</nav>

<div id="page-wrapper">

	<div class="alert alert-warning alert-dismissible" role="alert" style="display:none" id="erro">
	<button type="button" class="close" id="fechaErro" aria-label="Close"><span aria-hidden="true">&times;</span></button><?=lang('accounts_errors_subtransaction');?></div>
	<div class="tabela">
		<div class="tabelaLinks">
			<a id="btAddTransacao" href="#" class=""><i class="fa fa-plus-circle fa-fw"></i><?=lang('accounts_list_addtransaction');?></a>
			<div class="dropdown" style="display: inline-block;">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-pencil-square-o fa-fw"></i><span id="btEditar"><?=lang('accounts_list_edittransaction');?></span><span class="caret"></span></a>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="font-size:0.8em;">
					<li><a href="#"><?=lang('accounts_list_edittransaction_markcleared');?></a></li>
					<li><a href="#"><?=lang('accounts_list_edittransaction_marknotcleared');?></a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#" id="btExcluirSel"><?=lang('accounts_list_edittransaction_delete');?></a></li>
				</ul>
			</div>
			<a data-toggle="modal" href="#importaTransacoes" id="btImport"><i class="fa fa-upload fa-fw"></i><?=lang('accounts_list_importOFX');?></a>
		</div>
	<form id="formTransacoes" method="post">	
	<table class="table table-hover table-no-bordered table-condensed table-responsive tabela" id="tbTransacoes" 
		data-toggle="table"
		data-show-export="true"
		data-search="true"
 	    data-show-refresh="true"
	    data-show-columns="false"
		data-click-to-select="false"
		data-locale="pt-BR"
		data-height="100%">
			<thead>
				<tr><th id="thckAll"><input type="checkbox" id="ckbAll" data-indice="todas"></th>
					<th data-sortable="false" class="col-xs-1" data-filed="conta"><?=lang('accounts_heads_account');?></th>
					<th data-sortable="false" class="col-xs-1" data-field="date" data-sort-name="_date_data" data-sorter="monthSorter"><?=lang('accounts_heads_date');?></th>
					<th data-sortable="false" class="col-xs-4" data-field="sacado"><?=lang('accounts_heads_payee');?></th>
					<th data-sortable="false" class="col-xs-2" data-field="categoria"><?=lang('accounts_heads_category');?></th>
					<th data-sortable="false" class="col-xs-1" data-field="memo"><?=lang('accounts_heads_memo');?></th>
					<th data-sortable="false" class="col-xs-1 valores" data-field="saida"><?=lang('accounts_heads_outflow');?></th>
					<th data-sortable="false" class="col-xs-1 valores" data-field="entrada"><?=lang('accounts_heads_inflow');?></th>
					<th data-sortable="false" class="col-xs-1 valores" data-field="saldo"><?=lang('accounts_heads_balance');?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
					$i = 0;
					$tID=0;
					$contaID=0;
					$tIDs=false;
					$saldo = array();
				?>
				<?php if (count($accounts)): ?>
					<?php foreach ($accounts as $key => $list): ?>
						<?php if (($tID != $list['transacao_id']) || ($contaID!=$list['conta_id'])): ?>
							<tr id="r<?=$i?>" data-indice="<?=$i?>" data-tid="<?=$list['transacao_id']?>" data-editavel="<?=$list['editavel']?>" height="40px" >
								<td><input data-indice="<?=$i?>" type="checkbox" id="ck<?=$i?>"></td>
								<td id="col_conta_nome"><?=$list['conta_nome']?></td>
								<td id="col_data" data-month="<?=substr($list['data'],-4).substr($list['data'],3,2).substr($list['data'],0,2)?>"><?=$list['data']?></td>
								<td id="col_sacado_nome"><?=$list['sacado_nome']?></td>
								<td id="col_categoria"><?php
										if($list['count_filhas']>1) {
											echo "<i>".lang('accounts_multiple_transfer')."</i>";
										} else if ($list['conta_para_id']!='') {
											echo "<i>".lang('accounts_multiple_transfer')."</i>";
										} else if (($list['categoria'])=='') {
											echo "<span class=\"label label-warning\" style=\"font-size: 12px;\">".lang('accounts_no_category')."</span>";
										} else {
											echo $list['categoria'];
										} ?></td>
								<td id="col_memo" class="valores"><?=$list['memo']?></td>
								<td id="col_saida" class="valores"><?=($list['valor']<0) ? number_format((-1)*$list['valor'], 2, '.', '') : "0.00"?></td>
								<td id="col_entrada" class="valores"><?=($list['valor']>=0) ? number_format($list['valor'], 2, '.', '') : "0.00"?></td>
								<td id="col_saldo" class="valores"><?php 
										if(!isset($saldo['geral']))
											$saldo['geral']=0;
										$saldo['geral'] = $saldo['geral'] + $list['valor'];
										echo number_format($saldo['geral'],2,'.','');
									?></td>	
								<td><button id="btConciliar" data-conciliado="<?=($list['conciliado']==1) ? "1" : "0" ?>" data-tid="<?=$list['transacao_id']?>" type="button" class="btn <?=($list['conciliado']==1) ? "btn-success" : "btn-secondary" ?> btn-circle btn-xs">C</button></td>
							</tr>
						<?php endif; ?>
						<?php $tID = $list['transacao_id']; $contaID = $list['conta_id'];?>
					<?php $i++; endforeach; ?>
				<?php else: ?>
					
				<?php endif;?>
			</tbody>
		</table>
		</form>
		<?php 
			$i = 0;
			$tID=0;
			$contaID=0;
			$tIDs=false;
			$saldo = array();
			//Gerar o select para reuso
			function geraCategorias($id,$trid,$categorias,$itemSelecionado,$acao) {
				$sHTML = "<select class=\"categorias\" id=\"" . strval($id) . "\" name=\"" . strval($id) . "\" data-trid=\"" . strval($trid) . "\">";
				$sHTML .="<option></option>";
				$grupo = "";
				foreach ($categorias as $key => $item) {
					if ($grupo != $item['categoria_grupo']) {
						if ($grupo !="") {
							$sHTML .= "</optgroup>";
						}
						$grupo = $item['categoria_grupo'];
						$sHTML .= "<optgroup label=\"" . $grupo . "\">"; 
					}
					$sHTML .= "<option value=\"" . $item['id'] . "\"";
					if ($item['id']==$itemSelecionado) {
						$sHTML .= "selected=\"selected\"";
					}
					$sHTML .= ">" . $item['categoria'] . "</option>";
				}			
				$sHTML .= "</optgroup>";
				if ($acao) {
					$sHTML .= "<optgroup label=\"Ação:\"><option value=\"multiplos\"";
					if ($itemSelecionado=="multiplos") {
						$sHTML .= "selected=\"selected\"";
					}
					$sHTML .= ">".lang('accounts_multiple_transfer')."</optgroup>";
				}
				$sHTML .= "</select>";
				return $sHTML;
			}
		?>
		<div style="display: none;">
			<table id="tbEdicao">
			<?php $i=0; $ultLinha=0; $intTr=0; if (count($accounts)): ?>
				<?php foreach ($accounts as $key => $list): ?>
					<?php if ((($tID != $list['transacao_id']) || ($contaID!=$list['conta_id'])) && $tID!=0): ?>
						<tr class="editaTransacao selected">
							<td></td><td><input name="contaID" type="text" id="contaID" value="<?=$contaID?>" style="display:none">
							</td><td><input type="text" name="transacaoID" id="transacaoID" value="<?=$tID?>" style="display:none"></td>
							<td>
								<button type="button" class="btn btn-info btn-sm" aria-label="Adicionar Subtransação"  id="btAddSub">
								  <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> <?=lang('accounts_add_subtransaction');?>
								</button>
							</td>							
							<td style="text-align: right" class="valores"><?=lang('accounts_value_left');?></td><td></td>
							<td><span id="faltandoSaida" class="account valores">0</span></td>
							<td><span id="faltandoEntrada" class="account valores">0</span></td>
							<td></td>
							<td></td>
						</tr>
						<tr class="editaTransacao">
							<td></td><td><input name="countTr" id="countTr" value="<?=$intTr?>"  style="display:none"></td>
							<td></td><td></td><td></td>
							<td></td>
							<td>
								<button type="submit" class="btn btn-success btn-sm" aria-label="Salvar" id="btSalvar">
								  <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> <?=lang('accounts_button_save');?>
								</button>
							</td>
							<td>
								<button type="button" class="btn btn-danger btn-sm" aria-label="Cancelar" id="btCancelar">
								  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span> <?=lang('accounts_button_cancel');?>
								</button>
							</td>
							<td></td>
							<td></td>
						</tr>
						 
						</tbody>
					<?php $intTr=0; endif;?>
					<?php if ((($tID != $list['transacao_id']) || ($contaID!=$list['conta_id']))): ?>
					<tbody id="edita_r<?=$i?>">
					
					<?php endif; ?>
						<?php if ((($tID != $list['transacao_id']) || ($contaID!=$list['conta_id'])))  {
							$ultLinha=$i;
						}?>
						<?php if ((($tID != $list['transacao_id']) || ($contaID!=$list['conta_id']))): ?>
							<tr class="editaTransacao selected" id="main<?=$intTr+1?>" data-parent="<?=$ultLinha?>">
								<td><input name="tritem_id" id="tritem_id" value="<?=$list['tritem_id']?>" style="display:none"></td>
								<td><div id="conta_nome" class="input-group-btn"><input type="text" placeholder="Conta" id="conta" data-formValue="<?=$list['conta_id']?>" value="<?=$list['conta_nome']?>" class="form-control form-inline transacao input-sm typeahead conta"/></div></td>
								<td><input name="dataTr" type="text" data-provide="datepicker" placeholder="Data" id="dataTr" value="<?=$list['data']?>" class="form-control form-inline transacao input-sm"></td>
								<td><input type="text" placeholder="Sacado" data-trid="<?=$list['tritem_id']?>" id="sacado" name="sacado" value="<?=$list['sacado_nome']?>" class="form-control form-inline transacao input-sm"/></td>
								<td><?=($list['count_filhas']<=1) ? geraCategorias("categoria",$list['tritem_id'],$categorias,$list['catitem_id'],true) : geraCategorias("categoria",$list['tritem_id'],$categorias,"multiplos",true)?></td>
								<td><input type="text" placeholder="Memo"  data-trid="<?=$list['tritem_id']?>" id="memo" name="memo" value="<?=$list['memo']?>" class="form-control form-inline transacao input-sm"/></td>
								<td><input type="text" name="totalSaida" placeholder="Saída" id="totalSaida" value="<?=($list['valor']<0) ? (-1)*$list['valor'] : "0"?>" class="form-control form-inline transacao input-sm valor"/></td>
								<td><input type="text" name="totalEntrada" placeholder="Entrada" id="totalEntrada" value="<?=($list['valor']>=0) ? $list['valor'] : "0"?>" class="form-control form-inline transacao input-sm valor"/></td>
								<td><input type="text" name="split" id="split" value="<?=($list['count_filhas']>1 || $list['conta_para_id']!='') ? "true" : "false" ?>" style="display:none"></td>
								<td></td>
							</tr>
						<?php endif; ?>
						<?php if ($list['count_filhas']>1 || $list['conta_para_id']!=''):?>
						<tr class="editaTransacao selected" id="sub<?=$intTr+1?>" data-parent="<?=$ultLinha?>">
							<td><input class="transfpara" type="text" name="transferir_para_id_<?=$intTr?>" id="transferir_para_id_<?=$intTr?>" value="<?=$list['conta_para_id']?>" style="display:none"></td>
							<td><input id="tritem_id_<?=$intTr?>" name="tritem_id_<?=$intTr?>" value="<?=$list['tritem_id']?>" style="display:none"></td>
							<td align="right"><a href="#"><span style="font-size: 22px; padding-top:4px;" class="glyphicon glyphicon-remove-circle" aria-hidden="true" id="remSubt" data-id="<?=$intTr?>"></span></a></td>
							<td><div id="conta_nome" class="input-group-btn"><input type="text" placeholder="Transferir para:" data-trid="<?=$list['tritem_id']?>" id="transferir_<?=$intTr?>" name="transferir_<?=$intTr?>" data-intTr="<?=$intTr?>" value="<?=$list['conta_para_nome']?>" class="form-control form-inline transacao input-sm typeahead transferir_para"/></div></td>
							<td><?=geraCategorias("categoria_".$intTr,$list['tritem_id'],$categorias,$list['catitem_id'],false)?></td>
							<td><input type="text" placeholder="Memo"  data-trid="<?=$list['tritem_id']?>" id="memo_<?=$intTr?>" name="memo_<?=$intTr?>" value="<?=$list['memo']?>" class="form-control form-inline transacao input-sm" disabled/></td>
							<td><input type="text" placeholder="Saída" data-trid="<?=$list['tritem_id']?>"  id="saida_<?=$intTr?>" name="saida_<?=$intTr?>" value="<?=($list['valor_item']<0) ? (-1)*$list['valor_item'] : ''?>" class="form-control form-inline transacao input-sm valor"/></td>
							<td><input type="text" placeholder="Entrada" data-trid="<?=$list['tritem_id']?>"  id="entrada_<?=$intTr?>" name="entrada_<?=$intTr?>" value="<?=($list['valor_item']>=0) ? $list['valor_item'] : ''?>" class="form-control form-inline transacao input-sm valor"/></td>
							<td></td>
							<td></td>
						</tr>
						<?php endif;?>
				<?php $tID = $list['transacao_id']; $contaID = $list['conta_id'];?>
				<?php $i++; $intTr++; endforeach; ?>
				
				<?php if (count($accounts)): ?>			
						<tr class="editaTransacao selected">
							<td></td><td><input name="contaID" type="text" id="contaID" value="<?=$contaID?>" style="display:none">
							</td><td><input type="text" name="transacaoID" id="transacaoID" value="<?=$tID?>" style="display:none"></td>
							<td>
								<button type="button" class="btn btn-info btn-sm" aria-label="Adicionar Transacao"  id="btAddSub">
								  <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> <?=lang('accounts_add_subtransaction');?>
								</button>
							</td>							
							<td style="text-align: right" class="valores"><?=lang('accounts_value_left');?></td><td></td>
							<td><span id="faltandoSaida" class="account valores">0.00</span></td>
							<td><span id="faltandoEntrada" class="account valores">0.00</span></td>
							<td></td>
							<td></td>
						</tr>
						<tr class="editaTransacao">
							<td></td><td><input name="countTr" id="countTr" value="<?=$intTr?>"  style="display:none"></td><td></td><td></td><td></td>
							<td></td>
							<td>
								<button type="submit" class="btn btn-success btn-sm" aria-label="Salvar" id="btSalvar">
								  <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> <?=lang('accounts_button_save');?>
								</button>
							</td>
							<td>
								<button type="button" class="btn btn-danger btn-sm" aria-label="Cancelar" id="btCancelar">
								  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span> <?=lang('accounts_button_cancel');?>
								</button>
							</td>
							<td></td>
							<td></td>
						</tr>
						 
						</tbody>
				
				<?php endif;?>
			<?php endif;?>
			</table>
			<div style="display:none">
				<?=geraCategorias("categoria_".$intTr,$list['tritem_id'],$categorias,$list['catitem_id'],false)?>
			</div>
		</div>
		<!-- Modal -->
			<div id="importaTransacoes" class="modal fade">
			  <div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?=lang('accounts_ofx_heading');?></h4>
					</div>
					<form action="<?=base_url('importaOFX')?>" method="post" enctype="multipart/form-data">
						<div id="OFX_Inicio" class="modal-body">
							<p><?=lang('accounts_ofx_select');?></p>
							<div style="display:none">
								<input type="file" name="files[]" id="filer_input" accept=".ofx">							
							</div>
							<button type="button" class="btn btn-info" id="btFile">
								<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> <?=lang('accounts_ofx_button');?>
							</button>
						</div>
						<div id="OFX_Resultado" class="modal-body">
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('accounts_button_close');?></button>
							<button type="submit" type="button" class="btn btn-primary" id="btImportarFinal" disabled><?=lang('accounts_button_import');?></button>
						</div>
					</form>
				</div>
			</div>	
	</div>	
</div>
</div>

<!-- /#page-wrapper -->