<?php
	if ($_GET['ativos'] == "" || $_GET['dataI'] == "" || $_GET['dataF'] == "") {
		die("Faltando parametros");
	}
	$ativos = explode("+",$_GET['ativos']);
	$dataI = $_GET['dataI'];
	$dataF = $_GET['dataF'];
	
	
?>