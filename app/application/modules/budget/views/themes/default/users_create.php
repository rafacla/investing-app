<div class="container" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    Criar um novo usuário
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form role="form" method="POST" action="<?=base_url('novousuario')?>">
								<div style="width:100%;">
									<div class="form-group" style="display:inline-block; width:calc(50% - 2px);">
										<label>Nome</label>
										<input class="form-control" placeholder="Nome" id="first_name" name="first_name"  required>
									</div>
									<div class="form-group" style="display:inline-block; width:calc(50% - 2px);">
										<label>Sobrenome</label>
										<input class="form-control" placeholder="Sobrenome" id="last_name" name="last_name"  required>
									</div>
								</div>
								<div style="width:100%;">
									<div class="form-group" style="display:inline-block; width:calc(60% - 2px);">
										<label>E-mail (será seu nome de usuário)</label>
										<input class="form-control" placeholder="e-mail" id="email" name="email"  required>
									</div>
									<div class="form-group" style="display:inline-block; width:calc(40% - 2px);">
										<label>Telefone</label>
										<input class="form-control" placeholder="Telefone" id="phone" name="phone" required>
									</div>
								</div>
                                <div style="width:100%;">
									<div class="form-group" style="display:inline-block; width:calc(35% - 2px);">
										<label>Senha</label>
										<input type="password" class="form-control" placeholder="Senha" id="password" name="password"  required>
									</div>
									<div class="form-group" style="display:inline-block; width:calc(35% - 2px);">
										<label>Confirmar Senha</label>
										<input type="password" class="form-control" placeholder="Confirmação de Senha" id="confirmaPassword" required>
									</div>
									<div style="display:inline-block;">
										<div id="senhaOK" style="display:none">
											<span class="glyphicon glyphicon-ok" style="color:#0a0;padding-left:5px;"></span><span> As senhas coincidem.</span>
										</div>
										<div id="senhaNOK"  style="display:none">
											<span class="glyphicon glyphicon-remove" style="color:#c00;padding-left:5px;"></span><span> As senhas não coincidem</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<input type="hidden" value="2" name="group_id">
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
<!-- /#page-wrapper -->
