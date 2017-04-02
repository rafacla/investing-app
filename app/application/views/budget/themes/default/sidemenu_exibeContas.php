	<div>
		<table class="listaContas">
			<tr>
				<td class="listaContas nome" style="padding:0px;">
					<a href="<?= base_url($this->profile->uniqueid.'/accounts') ?>"><?=lang('sidemenu_accounts_summary');?></a>
				</td>
				<td class="listaContas valor">
					<a href="#" data-toggle="collapse" data-target="#listaContasOrcamento" class="listaContas" id="somaTotal"><?=(array_sum(array_column($this->aContas,'saldo'))>=0 ? "$" : "-$")?><?=number_format(abs(array_sum(array_column($this->aContas,'saldo'))),2,'.',',')?></a>
				</td>
			</tr>
		</table>
		<div id="listaContasOrcamento" class="listaContas">
			<table class="listaContas">
			<?php if (count($this->aContas)): ?>
				<?php foreach ($this->aContas as $key => $list): ?>
					<tr>
						<td class="listaContas "><a href="<?= base_url($list['profile_uid'].'/accounts/'.$list['conta_id']) ?>"><?=$list['conta_nome']?> <span style="background-color: rgb(230,100,0);" class="badge"><?=($list['countNClas']>0) ? $list['countNClas'] : "" ?></span></a></td>
						<td class="listaContas valor"><a href="<?= base_url($list['profile_uid'].'/accounts/'.$list['conta_id']) ?>" id="menu_saldo_<?=$list['conta_nome']?>"><?=($list['saldo']>=0 ? "$" : "-$")?><?=number_format(abs($list['saldo']),2,'.',',')?></a></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</table>
		</div>
		<div width="100%" style="padding:5px" align="right">
			<a data-toggle="modal" href="#adicionaConta" id="btImport" class="btn btn-primary btn-xs" style="color:#fff"><i class="fa fa-plus-circle"></i> <?=lang('sidemenu_accounts_add');?></a>
		</div>
	</div>
	