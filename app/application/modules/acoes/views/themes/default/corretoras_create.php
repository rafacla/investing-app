<!-- #page-wrapper -->
<div id="page-wrapper" style="width:100%;">
	<div class="conteudo">
		<div class="row">
			<div class="col-md-12">
				<div class="page-header users-header">
					<h2>
						Corretoras
						<a  href="<?=base_url($this->profile->uniqueid.'/corretoras') ?>" class="btn btn-success">Voltar para lista de Corretoras</a>
					</h2>
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
					<div class="panel-heading">
						Adicionar nova corretora
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<form role="form" method="POST" action="<?=base_url($this->profile->uniqueid.'/corretoras/add')?>">
									<div class="form-group">
										<label>Nome da Corretora</label>
										<input class="form-control" placeholder="Nome da Corretora" id="nome" name="nome" value="<?php echo set_value('nome'); ?>" required>
									</div>
									<div class="form-group">
										<label>Site da Corretora</label>
										<input class="form-control" placeholder="Site da Corretora" id="site" name="site" value="<?php echo set_value('site'); ?>" required>
									</div>
									<div style="float:right;">
										<button id="btRegistrar" type="submit" class="btn btn-primary">Cadastrar</button>
										<button type="reset" class="btn btn-default">Cancelar</button>
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
