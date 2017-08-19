<?php

class YahooFinanceAPI
{    
	
	public function Historico($ticker,$startDate,$endDate) {
		//$url = "http://real-chart.finance.yahoo.com/table.csv?s=".$ticker."&a=".(date('m',$startDate)-1)."&b=".date('d',$startDate)."&c=".date('Y',$startDate)."&d=".(date('m',$endDate)-1)."&e=".date('d',$endDate)."&f=".date('Y',$endDate)."&g=d&ignore=.csv";
		//$sDate = datetoint($startDate - strtotime(1-1-1970)) * 86400;
		$sDate = mktime(0,0,0,date("m",$startDate),date("d",$startDate),date("Y",$startDate));
		//$eDate = cint($endDate - strtotime(1-1-1970)) * 86400;
		$eDate = mktime(0,0,0,date("m",$endDate),date("d",$endDate),date("Y",$endDate));
		//$cookie = "afjuc3tc7aqm8&b=3&s=94"; //retirado da session
		//$crumb =  "dvBvLPVQLg0";// retirada da session do navegador
		$cookie = "aqre859cktb97&b=3&s=mu"; //retirado da session
		$crumb =  "3zzqP1ez3WW";// retirada da session do navegador
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
					"Cookie: B=".$cookie."\r\n"
			)
		);
		$context = stream_context_create($opts); 
		
		$url = "https://query1.finance.yahoo.com/v7/finance/download/".$ticker."?period1=".$sDate."&period2=".$eDate."&interval=1d&events=history&crumb=".$crumb;
		
		$csv = file_get_contents($url,false,$context);
		
		logMsg(serialize($http_response_header));
		
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
			$data[$i]['Data']=$tempArray[0];
			$data[$i]['Abertura']=$tempArray[1];
			$data[$i]['Alta']=$tempArray[2];
			$data[$i]['Baixa']=$tempArray[3];
			$data[$i]['Fechamento']=$tempArray[4];
			$data[$i]['Fechamento_Ajustado']=$tempArray[6];
			$data[$i]['Volume']=$tempArray[5];
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
			//s=nome,d1=data ult trade,o=abertura,h=altadodia,g=baixa do dia,l1=ultima v=volume do dia
			//http://www.jarloo.com/yahoo_finance/
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
	
    public function DiaAtual_dep ($tickers,$fields=true) {
        // set url
		$api_url = 'http://query.yahooapis.com/v1/public/yql';
		
        $url = $api_url;
        $url .= '?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20%28%22'.implode(',',$tickers).'%22%29&env=store://datatables.org/alltableswithkeys';
		
        // set fields
        if ($fields===true || empty($fields)) {
            $fields = array(
                    'Symbol','Name','Change','ChangeRealtime','PERatio',
                    'PERatioRealtime','Volume','PercentChange','DividendYield',
                    'LastTradeRealtimeWithTime','LastTradeWithTime','LastTradePriceOnly','LastTradeTime',
                    'LastTradeDate'
                    );
        }
        // make request
        $ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $resp = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 
        // parse response
        if (!empty($fields)) {
            $xml = new SimpleXMLElement($resp);
            $data = array();
            $row = array();
            $time = time();
            if(is_object($xml)){
                foreach($xml->results->quote as $quote){
                    $row = array();
                    foreach ($fields as $field) {
                        $row[$field] = (string) $quote->$field;
                    }
                    $data[] = $row;
                }
            }
        } else {
            $data = $resp;
        }
        return $data;
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
