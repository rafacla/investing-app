<div class="container" style="margin-top: 50px;">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
				<div class="panel-heading">
                    <h3 class="panel-title"><?=lang('user_profile_heading');?></h3>
                </div>
				<div class="panel-body">
					<p><?=lang('user_select_language');?></p>
					
					<?php echo form_open("change_language");?>
					<select class="form-control" name="language">
						<option value="portuguese-brazilian">Português brasileiro</option>
						<option value="english">English</option>
					</select>
					<br/>
					<?php echo form_submit('submit', lang('user_select_language_button'),"class=\"btn btn-lg btn-primary btn-block\"");?></p>

					<?php echo form_close();?>

				</div>
				<div class="panel-body">
                    <p><?=lang('reset_password_heading');?></p>
					<?php if ($this->session->flashdata('message')): ?>
                        <div class="alert alert-danger fade in">
                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                            <?= $this->session->flashdata('message') ?>
                        </div>
                    <?php endif; ?>
					<?php echo form_open("user");?>
					<div class="form-group">
						<?php echo form_input($old_password, "","class=\"form-control\" placeholder=\""."".lang('change_password_old_password_label')."\"");?>
					</div>
					<div class="form-group">
						<?php echo form_input($new_password,"","class=\"form-control\" placeholder=\"".sprintf(lang('change_password_new_password_label'), $min_password_length)."\"");?>
					</div>
					<div class="form-group">
						<?php echo form_input($new_password_confirm, "","class=\"form-control\" placeholder=\""."".lang('change_password_new_password_confirm_label')."\"");?>
					</div>

					<?php echo form_input($user_id);?>
					<?php echo form_submit('submit', lang('change_password_submit_btn'),"class=\"btn btn-lg btn-success btn-block\"");?></p>

					<?php echo form_close();?>
					<a href="<?= base_url('/') ?>"><i class="fa fa-undo fa-fw"></i><?=lang('user_back_to_home');?></a>
				</div>
				
			</div>
		</div>
    </div>
</div>