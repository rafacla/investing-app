<?php
include_once('AtualizaAcoes.php');
set_time_limit(0);
	$acoes = new AtualizaAcoes();
	$resultado = $acoes->AtualizaAcaoYahoo("^BVSP","","","IBOVESPA");
	var_dump($resultado);
?>