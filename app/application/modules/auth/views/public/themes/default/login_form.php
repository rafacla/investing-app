<div class="container" style="margin-top: 50px;">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Digite seu usário e senha</h3>
                </div>
                <div class="panel-body">
                    <?php if ($this->session->flashdata('message')): ?>
                        <div class="alert alert-danger fade in">
                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                            <?= $this->session->flashdata('message') ?>
                        </div>
                    <?php endif; ?>
                    <form role="form" method="POST">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="e-mail" name="email" type="email" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Senha" name="password" type="password" value="">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me">Lembrar de mim
                                </label>
                            </div>
							
                            <!-- Change this to a button or input when using this as a form -->
                            <button class="btn btn-lg btn-success btn-block" type="submit" formaction="<?= base_url('auth/login') ?>">Login</button>
                        </fieldset>
                    
					<br />
					<a class="btn btn-info" href="novousuario" style="width: 49%;">Novo usuário</a>
					<button class="btn btn-warning" type="submit" formaction="<?= base_url('esqueciasenha') ?>" style="width: 49%;">Esqueci minha senha</button>
					
					</form>
					
                </div>
            </div>
        </div>
    </div>
</div>