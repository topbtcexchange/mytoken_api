<?php
$coin_name = [
    'BTC' => 'BitCoin',
    'ETH' => 'Ethereum',
    'ETP' => 'Metaverse ETP',
    'LTC' => 'Litecoin',
    'BCH' => 'Bitcoin Cash',
    'INK' => 'INK',
    'LEO' => 'LEOcoin',
    'CHC' => 'CHC',
    'FOTA' => 'FORTUNA',
    'NGOT' => 'NGO Token',
    'SGCC' => 'SUPER GAME CHAIN',
    'SNT' => 'Status',
    'OMG' => 'OmiseGO',
    'BICC' => 'Bitclassic Coin',
    'EOS' => 'EOSIO',
    'HSR' => 'Hshare',
    'ATB' => 'ATBCoin',
    'OLE' => 'Olive',
    'CIG' => 'CIorigin',
    'CFC' => 'Coffee gold chain',
    'ETER' => 'ETERNAL',
    'SV' => 'SV',
    'MVT' => 'MVT',
    'WJC' => 'WujinCoin',
    'MANA' => 'Decentraland',
    'YTC' => 'YiBitcoin',
    'ORME' => 'OrmeusCoin',
    'EXCC' => 'Exchange Coin',
    'BGC' => 'Bagcoin',
    'UQC' => 'UquidCoin',
    'VOISE' => 'VOISE',
    'BLT' => 'Bloom',
    'CNY' => 'China Yuan',
    'QQC' => 'QQC'
];
$token = 'token';

function curl($url,$request,$data='',$header_info){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $request,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $header_info,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
$curl = curl_init();

$url = "api";
$header_info = array(
    "Cache-Control: no-cache",
    "Content-Type: application/json",
    "X-API-key: {$token}"
);
$tickers_info = json_decode(curl($url,'GET','',$header_info),true);

//
$mysqli = new mysqli("localhost", "root", "root", "btc");
$sql = "SELECT * FROM btc_currency";
$result = $mysqli->query($sql);
$coin = [];
while ($row=$result->fetch_assoc())
{
    $coin[] = $row['symbol'];
    //echo ($row['symbol'])."<br>";//这里的name是标的列名。
}
//var_dump($coin);
//die;
$pearm = [];
date_default_timezone_set("PRC");
foreach($tickers_info as $k => $v ){
    if(in_array($v['coin'],$coin)){
        $pearm['tickers'][] = array(
            "symbol_key" => $v['coin'],
            "symbol_name" => $coin_name[$v['coin']],
            "anchor_key" => $v['market'],
            "anchor_name" => $coin_name[$v['market']],
            "price" => $v['ticker']['last'],
            "price_updated_at" => date('Y-m-d H:i:s', $v['date']),
            "volume_24h" => $v['ticker']['vol'],
            "volume_anchor_24h" => $v['ticker']['last']*$v['ticker']['vol']
        );
    }
}
//var_dump($pearm);die;
$pearm = json_encode($pearm,true);

$url = "http://matrix.api.mytoken.io/api/v1/tickers/batch_create";

$response = curl($url,'POST',$pearm,$header_info);
echo $response;


