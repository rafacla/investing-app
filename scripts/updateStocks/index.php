<?php

include_once('AtualizaAcoes.php');

$blacklist = array();

function Atualizar() {
	global $blacklist;
	
	$acoes = new AtualizaAcoes();
	$date = new DateTime();
	$date = $date->format("Y-m-d h:i:s");

	if ($acoes->AcaoAleatoriaDesatualizada($blacklist)) {
		set_time_limit (300);
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		$acao = $acoes->AcaoAleatoriaDesatualizada($blacklist);
		file_put_contents (__DIR__ ."/log.txt","[" . $date."] Atualizando " . $acao . "...\r\n",FILE_APPEND);
		$resultado = $acoes->AtualizaAcaoGoogle($acao);
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		
		
		if ($resultado) {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ".$acao . " foi atualizado.\r\n",FILE_APPEND);
			array_push($blacklist,$acao);
		} else {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ".$acao . " nao foi encontrada no Yahoo...\r\n",FILE_APPEND);
			array_push($blacklist,$acao);
		}
		$number = rand(5,13);
		logMsg("Dormindo ".$number." segundos");
		sleep($number);
		logMsg("Acordei!");
		Atualizar();
	} else {
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		
		if (sizeof($blacklist)>0) {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ". "Nao ha acoes a atualizar. Encerrando.\r\n",FILE_APPEND);
			die();
		} else {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ". "Nao ha acoes a atualizar. Encerrando.\r\n",FILE_APPEND);
			die();
		}
	}
}
file_put_contents(__DIR__ ."/log.txt","[" . date("Y-m-d h:i:s")."] ". "Iniciando atualizacao.\r\n");

Atualizar();
?>