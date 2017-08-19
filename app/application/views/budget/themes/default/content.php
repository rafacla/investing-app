<?php
	//Aqui verificamos se está setado o módulo do site ativo,
	//se nao estiver, definimos o padrao:
	if (!isset($modulo)) {
		$modulo = "dashboard";
	}
?>
<div id="page-wrapper">
	<div id="titulo-pagina" class="<?=$modulo?>-title">
		<span class="titulo-pagina"><?=(isset($title) ? $title : "No title")?></span>
	</div>
	<div id="conteudo">
		<?php
		if (isset($page)) {
			if (isset($module)) {
				$this->load->view("$module/$page");
			} else {
				$this->load->view($page);
			}
		}
		?>
	</div>
</div>	
<!-- /#page-wrapper -->