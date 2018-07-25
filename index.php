<?php
echo 'Login Api Data <br/>';
//$url = "http://localhost/pamt/api/login?user=admin&password=12345678";
$url = "http://localhost/pamt/api/login?user=crdp_user1&password=123456";
//$url = "http://localhost/pamt/api/login?user=suser1_lged&password=12345678";

$value = file_get_contents($url);
$data = json_decode($value, true);
echo '<pre>'; print_r($data ); echo '</pre>';

//echo $data['result']['token_id'];
echo '<br/>';
echo 'Report list Api Data <br/>';
//http://localhost/pamt/api/report_list?token_id=".$data['result']['token_id']."&role_id=38325f5f5f31
$url1 = "http://localhost/pamt/api/report_list?token_id=".$data['result']['token_id']."&role_id=".$data['result']['role_id']."";
$value1 = file_get_contents($url1);
$data1 = json_decode($value1, true);
//echo '<pre>'; print_r($data1 ); echo '</pre>';

//echo $data['result']['project_id'];
echo '<br/>';
echo 'Dashboard Report <br/>';

// Dashboard Report
//http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=dashborad&step=ajax&project=3133305f5f5f31&agency=3133305f5f5f3435
//$url2 = "http://bdpamt.org/api/report_view?token_id=637264705f75736572315f5f5f313330&report=dashborad&step=ajax&project=3133305f5f5f31&agency=3133305f5f5f3435";
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=dashborad&step=ajax&project=".$data['result']['project_id']."&agency=".$data['result']['agency_id']."";

// O Report
//http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=report_o&step=ajax&project=3133305f5f5f31&agency=3133305f5f5f3435&from_date=01/01/2012&to_date=24/06/2018&bidding=1S1E
//$url2 = "http://bdpamt.org/api/report_view?token_id=637264705f75736572315f5f5f313330&report=report_o&step=ajax&project=3133305f5f5f31&agency=3133305f5f5f3435&from_date=01/01/2012&to_date=24/06/2018&bidding=1S1E";
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=report_o&step=ajax&project=".$data['result']['project_id']."&agency=".$data['result']['agency_id']."&from_date=24/06/2011&to_date=24/06/2018&bidding=1S1E";


//report c
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=report_c&step=ajax&from_date=24/06/2011&to_date=24/06/2018&currency=dolar&from_usd=0&to_usd=0";
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=report_c&step=srs&from_date=538050817&to_date=538445348&currency=dolar&from_usd=0&to_usd=0&sector=6";
//http://localhost/pamt/api/report_view?token_id=61646d696e5f5f5f3832&report=report_c&step=prs&from_date=538050817&to_date=538445348&currency=dolar&from_usd=0&to_usd=0&sector=6&project=1&report_type=Agency
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=report_c&step=prs&from_date=538050817&to_date=538445348&currency=dolar&from_usd=0&to_usd=0&sector=6&project=1&report_type=Agency";
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=report_c&step=ars&from_date=538050817&to_date=538445348&currency=dolar&from_usd=0&to_usd=0&sector=6&project=1&agency=45&report_type=Supervising";


//Graph report B
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=graph_b&step=ajax&method=ICB&from_usd=0&to_usd=0&sector=6&project=1&agency=45";

//Report PP
$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=pp&step=ajax&type=2&from_date=24/06/2011&to_date=24/06/2018";

//Report PAMS
//$url2 = "http://localhost/pamt/api/search_package?token_id=".$data['result']['token_id']."&project=1&agency=45&type=&method=&bidding=";
//$url2 = "http://localhost/pamt/api/search_package_lot?token_id=".$data['result']['token_id']."&package=1";
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=pams&step=ajax&package=1&lot=1";
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=pams&step=ajax&package=274&lot=337"; 
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=pams&step=ajax&package=274&lot=338"; // 1s1e
//$url2 = "http://localhost/pamt/api/report_view?token_id=".$data['result']['token_id']."&report=pams&step=ajax&package=109&lot=137";


$value2 = file_get_contents($url2);
$data2 = json_decode($value2, true);
echo '<pre>'; print_r($data2 ); echo '</pre>';

?>