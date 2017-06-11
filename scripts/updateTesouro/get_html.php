<?php 
function getPage ($url) {


$useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
$timeout= 120;
$dir            = dirname(__FILE__);
$cookie_file    = $dir . '/cookies/' . md5($_SERVER['REMOTE_ADDR']) . '.txt';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt($ch, CURLOPT_ENCODING, "" );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch, CURLOPT_AUTOREFERER, true );
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout );
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout );
curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
$content = curl_exec($ch);
if(curl_errno($ch))
{
    echo 'error:' . curl_error($ch);
}
else
{
    return $content;        
}
    curl_close($ch);

}

?>