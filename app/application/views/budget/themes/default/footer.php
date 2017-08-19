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

<!-- typeahead -->
<script src="<?= base_url() ?>assets/budget/js/typeahead.bundle.js"></script>

<!-- File Uploader -->
<script src="<?= base_url() ?>assets/budget/js/jquery.filer.min.js"></script>

<!-- JQuery Date Formatter -->
<script src="<?= base_url() ?>assets/budget/js/jquery-dateFormat.min.js"></script>

<!-- JQuery Currency Formatter -->
<script src="<?= base_url() ?>assets/budget/js/autoNumeric.js"></script>

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
	
	$('#corretora').select2({
		placeholder: "Corretora"
	});
	
	$('#side-menu').metisMenu({ toggle: false });

</script>
</body>

</html>
