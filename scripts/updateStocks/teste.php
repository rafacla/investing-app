<?php
include_once('AtualizaAcoes.php');
include_once('GoogleFinance.php');
$acoes = new GoogleFinanceAPI();
$acoes1 = new AtualizaAcoes();
	
$acao = "IBOVESPA";
$resultado = $acoes1->AtualizaAcaoGoogle($acao);
var_dump($resultado);
?>