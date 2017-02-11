<div class="container" style="margin-top: 50px;">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Criar nova senha</h3>
                </div>
                <div class="panel-body">
<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open('auth/reset_password/' . $code);?>
	<fieldset>
		<div class="form-group">
			<input class="form-control" placeholder="Nova senha" type="password" name="new" value="" id="new" pattern="^.{8}.*$">
		</div>
		<div class="form-group">
			<input class="form-control" placeholder="Repetir nova senha" type="password" name="new_confirm" value="" id="new_confirm" pattern="^.{8}.*$">
		</div>
		<?php echo form_input($user_id);?>
		<?php echo form_hidden($csrf); ?>

		<input  class="btn btn-lg btn-success btn-block"  type="submit" name="submit" value="Mudar senha">
	</fieldset>
<?php echo form_close();?>
                </div>
            </div>
        </div>
    </div>
</div>