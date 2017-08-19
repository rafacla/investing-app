<?php

class GoogleFinanceAPI
{
	public function Historico($ticker,$startDate,$endDate) {
		$monthNames = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$s1 = $monthNames[date("m",$startDate)-1];
		$s1 = $s1 . "+" . Trim(date("d",$startDate)) . ",+" . Trim(date("yy",$startDate));
		
		$s2 = $monthNames[date("m",$endDate)-1];
		$s2 = $s2 . "+" . Trim(date("d",$endDate)) . ",+" . Trim(date("yy",$endDate));
		If ($ticker == "IBOVESPA") {
			$ticker = "INDEXBVMF:IBOV";
		} else {
			$ticker = "BVMF:" . $ticker;
		}
		$url = "http://www.google.com/finance/historical?q=" . $ticker . "&startdate=" . $s1 . "&enddate=" . $s2 . "&output=csv";
		logMsg("Acessando URL: ".$url);
		$csv = file_get_contents($url,false);
		if ($csv === false) {
			return false;
		}
		$linhas = explode("\n",$csv);
		
		$data = array();
		
		$data[0]['ativo'] = $ticker;
		$data[0]['dtInicio'] = date('d/m/Y',$startDate);
		$data[0]['dtTermino'] = date('d/m/Y',$endDate);
		$data[0]['nrPregoes'] = sizeof($linhas)-2;
		
		for ($i=1;$i<=$data[0]['nrPregoes'];$i++) {
			$tempArray = explode(",",$linhas[$i]);
			$data[$i]['Data']=date("Y-m-d",strtotime($tempArray[0]));
			$data[$i]['Abertura']=$tempArray[1];
			$data[$i]['Alta']=$tempArray[2];
			$data[$i]['Baixa']=$tempArray[3];
			$data[$i]['Fechamento']=$tempArray[4];
			$data[$i]['Fechamento_Ajustado']=$tempArray[4];
			if ($tempArray[5]!='-') {
				$data[$i]['Volume']=$tempArray[5];
			} else {
				$data[$i]['Volume']=0;
			}
			//$data[$i]['Dividends']=$tempArray[7];
		}
        return $data;
	}
	
	public function DiaAtual($tickers) {
		$splitAcoes = array_chunk($tickers,150);
		$linhasFinal = array();
		foreach ($splitAcoes as $acoes) {
			$tickers_s = implode(',',$acoes);
			$url = "http://download.finance.yahoo.com/d/quotes.csv?s=";
			$url .= $tickers_s;
			$url .= "&f=sd1ohgl1v";
			
			$csv = @file_get_contents($url);
			
			if ($csv === false) {
				return false;
			}
			$linhas = explode("\n",$csv);
			
			$linhasFinal = array_merge($linhasFinal,$linhas);
			sleep(3);
		}
		$arrayFinal = array();
		$i=0;
		
		foreach ($linhasFinal as $linha) {
			if (strlen($linha)>0) {
				++$i;
				$tempArray = explode(",",$linha);
				$arrayFinal[$i]['codigo_yahoo'] = trim(trim($tempArray[0],'"'));
				$arrayFinal[$i]['ativo'] = substr($arrayFinal[$i]['codigo_yahoo'],0,5);
				$arrayFinal[$i]['ultTrade'] = substr(trim(trim($tempArray[1],'"')),6,4)."-".substr(trim(trim($tempArray[1],'"')),0,2)."-".substr(trim(trim($tempArray[1],'"')),3,2);
				$arrayFinal[$i]['abertura'] = $tempArray[2];
				$arrayFinal[$i]['alta'] = $tempArray[3];
				$arrayFinal[$i]['baixa'] = $tempArray[4];
				$arrayFinal[$i]['ultima'] = $tempArray[5];
				$arrayFinal[$i]['volume'] = $tempArray[6];
			}
		}
		
		return $arrayFinal;
	}
	
}

function logMsg ($msg,$verbose=true) {
	if ($verbose) { 
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		echo "[".$date."] " . $msg ."<br>";
		file_put_contents (__DIR__ ."/log.txt","[".$date."] " . $msg."\r\n",FILE_APPEND);
	}
}