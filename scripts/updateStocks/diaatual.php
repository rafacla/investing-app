<?php 

include_once('AtualizaAcoes.php');
$date = new DateTime();
$date = $date->format("Y-m-d h:i:s");
file_put_contents (__DIR__ ."/log.txt","[" . $date."] Atualizando ações atuais (-15min)...\r\n",FILE_APPEND);
$acoes = new AtualizaAcoes();

$resultado = $acoes->AtualizaAcoes_DiaAtual();
$date = new DateTime();
$date = $date->format("Y-m-d h:i:s");
if ($resultado) {
	file_put_contents (__DIR__ ."/log.txt","[" . $date."] Ações atuais (-15min) atualizas com sucesso.\r\n",FILE_APPEND);
} else {
	file_put_contents (__DIR__ ."/log.txt","[" . $date."] FALHA ao atualizar Ações atuais (-15min).\r\n",FILE_APPEND);
}
?>