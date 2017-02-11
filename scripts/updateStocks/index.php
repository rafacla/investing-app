<?php

include_once('AtualizaAcoes.php');

$blacklist = array();

function Atualizar() {
	global $blacklist;
	
	$acoes = new AtualizaAcoes();
	$date = new DateTime();
	$date = $date->format("Y-m-d h:i:s");

	if ($acoes->AcaoAleatoriaDesatualizada('yahoo',$blacklist)) {
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		$acao = $acoes->AcaoAleatoriaDesatualizada('yahoo',$blacklist);
		file_put_contents (__DIR__ ."/log.txt","[" . $date."] Atualizando " . $acao . "...\r\n",FILE_APPEND);
		$resultado = $acoes->AtualizaAcaoYahoo($acao);
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		
		
		if ($resultado) {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ".$acao . " foi atualizado.\r\n",FILE_APPEND);
			array_push($blacklist,$acao);
		} else {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ".$acao . " não foi encontrada no Yahoo...\r\n",FILE_APPEND);
			array_push($blacklist,$acao);
		}
		sleep(10);
		Atualizar();
	} else {
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		
		if (sizeof($blacklist)>0) {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ". "Não há ações a atualizar. Encerrando.\r\n",FILE_APPEND);
			die();
		} else {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ". "Não há ações a atualizar. Encerrando.\r\n",FILE_APPEND);
			die();
		}
	}
}
file_put_contents(__DIR__ ."/log.txt","Iniciando atualização.\r\n");

Atualizar();
?>