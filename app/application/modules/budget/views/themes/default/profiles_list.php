<div class="container">
	<h2><?=lang('profiles_select_heading');?></h2> 
	<p><?=lang('profiles_select_description');?></p> 
   <h3><?=lang('profiles_select_heading2');?></h3> 
	<?php if (count($profiles)): ?>
		<div class="row">
		<?php foreach ($profiles as $key => $list): ?>
			
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-tags fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<a style="font-size: 40px;color: #fff;" href="<?= base_url('/profiles/deletaProfile/'.$list['uniqueid']) ?>">
										<span class="glyphicon glyphicon-trash"></span>
									</a>
								</div>
							</div>
						</div>
						<a href="<?= base_url('/profiles/abreProfile/'.$list['uniqueid']) ?>">
							<div class="panel-footer">
								<span class="pull-left"><?=$list['nome']?></span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
			
		<?php endforeach; ?>
		</div>
	<?php else: ?>
		<strong style="padding-left: 20px"><?=lang('profiles_select_noresults');?></strong>
	<?php endif; ?>		
	<br />
	<h3><?=lang('profiles_create_heading');?></h3> 
	<form action="profiles/create" method="post">
		<div class="form-group">
			<input class="form-control" placeholder="<?=lang('profiles_create_placeholder');?>" name="nome">
		</div>
		<button type="submit" class="btn btn-primary">
			<?=lang('profiles_create_submit');?>
		</button>
	</form>
</div>