</div>
<!-- /#wrapper -->

<!-- Bootstrap Core JavaScript -->
<script src="<?= base_url() ?>assets/admin/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="<?= base_url() ?>assets/admin/js/metisMenu.min.js"></script>


<!-- Custom Theme JavaScript -->
<script src="<?= base_url() ?>assets/admin/js/sb-admin-2.js"></script>

<!-- Alertas modais -->
<script src="<?= base_url() ?>assets/admin/js/eModal.min.js"></script>

<!-- Script para Datepicker -->
<script src="<?= base_url() ?>assets/budget/js/bootstrap-datepicker.min.js"></script>
<script src="<?= base_url() ?>assets/budget/locales/bootstrap-datepicker.pt-BR.min.js"></script>

<!-- Script para Select -->
<script src="<?= base_url() ?>assets/budget/js/select2.min.js"></script>

<!-- Script Bootstrap Tables -->
<script src="<?= base_url() ?>assets/budget/js/bootstrap-table.min.js"></script>
<script src="<?= base_url() ?>assets/budget/js/bootstrap-table-locale-all.min.js"></script>

<!-- typeahead -->
<script src="<?= base_url() ?>assets/budget/js/typeahead.bundle.js"></script>

<!-- File Uploader -->
<script src="<?= base_url() ?>assets/budget/js/jquery.filer.min.js"></script>

<!-- JQuery Date Formatter -->
<script src="<?= base_url() ?>assets/budget/js/jquery-dateFormat.min.js"></script>

<!-- JQuery Currency Formatter -->
<script src="<?= base_url() ?>assets/budget/js/autoNumeric.js"></script>

<!-- Script do APP -->
<script src="<?= base_url() ?>assets/budget/js/account_lists.js"></script>

<!-- Script da pag budgets -->
<script src="<?= base_url() ?>assets/budget/js/budgets_lists.js"></script>


<script>
    $('.dropdown-toggle').dropdown();
	
	$('#listaContasOrcamento').on('shown.bs.collapse', function () {
		$('#agrupador').removeClass("fa-caret-right").addClass("fa-caret-down");
	});

	$('#listaContasOrcamento').on('hidden.bs.collapse', function () {
		$('#agrupador').removeClass("fa-caret-down").addClass("fa-caret-right");
	});
	
	var base_url = <?php echo json_encode(base_url()); ?>;

  	jQuery(function($) {
      	$('input.valor').autoNumeric('init',{aSep: '', aSign: '', vMin: '-999999999.99'});    
  	});
	
	$('#side-menu').metisMenu({ toggle: false });
	
	$('#corretora').select2({
		placeholder: "Corretora"
	});

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
</body>

</html>
