	<!-- jQuery -->
    <script src="<?=base_url()?>assets/admin/js/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?=base_url()?>assets/admin/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=base_url()?>assets/admin/js/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?=base_url()?>assets/admin/js/sb-admin-2.js"></script>

	
	<script>
		$(document).ready(function() {
			$('#password').keyup(function() {
				if ($('#password').val() == $('#confirmaPassword').val()) {
					$('#senhaOK').show();
					$('#senhaNOK').hide();
					$('#btRegistrar').prop('disabled',false);
				} else {
					$('#senhaOK').hide();
					$('#senhaNOK').show();
					$('#btRegistrar').prop('disabled',true);
				}
			});
			$('#confirmaPassword').keyup(function() {
				if ($('#password').val() == $('#confirmaPassword').val()) {
					$('#senhaOK').show();
					$('#senhaNOK').hide();
					$('#btRegistrar').prop('disabled',false);
				} else {
					$('#senhaOK').hide();
					$('#senhaNOK').show();
					$('#btRegistrar').prop('disabled',true);
				}
			});
		});
		
		
	</script>
</body>

</html>