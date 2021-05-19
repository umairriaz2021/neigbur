<?php
$GOOGLE_MAPS_API_KEY = "AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw";

$cat_arr = [];

if(isset($_POST['location'])){
	unset($_SESSION['sePra']);
    $_SESSION['sePra'] = $_POST;
}else{
	unset($_SESSION['sePra']['search_event']);
}
/* echo "<pre>"; print_r($_POST); print_r($_SESSION['sePra']); die; */

if (isset($_SESSION['sePra']['location']))
{
    $_POST = $_SESSION['sePra'];

    $searchrequesturl = API_URL . 'events?search=' . urlencode($_POST['search_event']);

    if (isset($_POST['radiusfilter']) && $_POST['radiusfilter'] != '' && $_POST['location'] != '')
		{
			//if ($_POST['loclat'] == '' || $_POST['loclong'] == '')
			if ($_POST['loclocation'] != $_POST['location'])
			{
				// we need to first geocode a manually inputted address
				$url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($_POST['location']) . "&key=" . $GOOGLE_MAPS_API_KEY;
				$res = file_get_contents($url);
				$data = json_decode($res);
				if (isset($data->results[0]))
				{
					$_POST['loclat'] = $data->results[0]->geometry->location->lat;
					$_POST['loclong'] = $data->results[0]->geometry->location->lng;
				}
				else
				{
					$_POST['loclat'] = 1;
					$_POST['loclong'] = 1;
				}
			}


        $searchrequesturl .= '&lat=' . urlencode($_POST['loclat']) . '&long=' . urlencode($_POST['loclong']);
        $searchrequesturl .= '&radius=' . urlencode($_POST['radiusfilter']);
    }

    /* if (isset($_POST['pricefilter']) && $_POST['pricefilter'] != '') {
        $searchrequesturl .= ($_POST['pricefilter'] == 1) ? '&priceLow=0' : '&priceHigh=0';
    } */
	if (isset($_POST['pricefilter']) && $_POST['pricefilter'] == 0) {
        $searchrequesturl .= '&priceHigh=0';
    }

    if (isset($_POST['datefilter']) && $_POST['datefilter'] != '') {
        if ($_POST['datefilter'] == 'tod') {
            $searchrequesturl .= '&startDate=' . date('Y-m-d') . urlencode(' 00:00:00') . '&endDate=' . date('Y-m-d') . urlencode(' 23:59:59');
        }
		if ($_POST['datefilter'] == 'lm') {
            $searchrequesturl .= '&startDate=' . date("Y-m-d", strtotime("- 30 day")) . urlencode(' 00:00:00') . '&endDate=' . date('Y-m-d') . urlencode(' 23:59:59');
        }
        if ($_POST['datefilter'] == 'nm') {
            $searchrequesturl .= '&startDate=' . date('Y-m-d') . urlencode(' 00:00:00') . '&endDate=' . date("Y-m-d", strtotime("+ 30 day")) . urlencode(' 23:59:59') . '&sort=ASC&sortType=startDate';
			$stdt = strtotime(date('Y-m-d'));
			$eddt = strtotime(date("Y-m-d", strtotime("+ 30 day")));
        }
		if($_POST['datefilter'] == 'nw') {
            $searchrequesturl .= '&startDate=' . date('Y-m-d') . urlencode(' 00:00:00') . '&endDate=' . date("Y-m-d", strtotime("+ 7 day")) . urlencode(' 23:59:59') . '&sort=ASC&sortType=startDate';
			$stdt = strtotime(date('Y-m-d'));
			$eddt = strtotime(date("Y-m-d", strtotime("+ 7 day")));
        }
		if ($_POST['datefilter'] == 'ad') {
            $searchrequesturl .= '&startDate=' . date('Y-m-d') . urlencode(' 00:00:00') . '&endDate=' . date("Y-m-d", strtotime("+ 100 month")) . urlencode(' 23:59:59') . '&sort=ASC&sortType=startDate';
			$stdt = strtotime(date('Y-m-d'));
			$eddt = strtotime(date("Y-m-d", strtotime("+ 100 month")));
        }
    }


    if (isset($_POST['filter_category']) && count($_POST['filter_category']) > 0 && $_POST['filter_category'][0] != 17) {

        if (!empty($cat_arr)) {

            foreach ($cat_arr as $cat) {
                array_push($_POST['filter_category'], $cat);
            }
        }

        $cat_arr = $_POST['filter_category'];

        $cats = implode(',', $_POST['filter_category']);
        $searchrequesturl .= '&categories=' . urlencode($cats);
    }


    $ch = curl_init($searchrequesturl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        // 'Authorization: ' . $token
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $apirespons = json_decode($result);
    $events = array();

    if ($apirespons->success) {
        $events = $apirespons->events;
        
        // $edate1 = date("Y-m-d", strtotime($events[0]->start. "+ 7 day"));
        // $createDate = new DateTime($events[0]->start);

        // $strip = $createDate->format('Y-m-d');
        //   if($strip <= $edate1 && $edate1 >= $strip ){
        //         $events = $edate;
                     
        //      }
        // if(count($events) > 0){
        //     foreach($events as $key=>$edate ){
        //       $edate1 = date("Y-m-d", strtotime($edate->start. "+ 7 day"));
        //         $createDate = new DateTime($edate->start);

        //         $strip = $createDate->format('Y-m-d');

                
        //         //echo "<pre>"; print_r($edate1); die;
        //       if($strip <= $edate1 && $edate1 >= $strip ){
        //           $events = $edate;
                     
        //       }
                
        //     }
        // }
        
    }
        
   

}

if (isset($_POST['btnReset'])) {
	unset($_SESSION['sePra']);
	$searchrequesturl = API_URL . 'events?startDate=' . date('Y-m-d') . urlencode(' 00:00:00') . '&endDate=' . date("Y-m-d", strtotime("+ 100 month")) . urlencode(' 23:59:59') . '&sort=ASC&sortType=startDate';
    $ch = curl_init($searchrequesturl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        // 'Authorization: ' . $token
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $apirespons = json_decode($result);

    if ($apirespons->success) {
        $events = $apirespons->events;
        $cat_arr = [17];
    }
}

$ch = curl_init(API_URL . 'categories?sort=ASC&sortType=name');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
$result2 = curl_exec($ch);
curl_close($ch);
$categoryList = json_decode($result2);

?>
