<?php
/** トップ画面*/
$app->get ( '/', function () use ($app, $twig) {
	$params = $app->request()->get();
	$daterange=null;
	if(isset($params['d'])) {
		$daterange_array = explode("-", $params['d']);
		$daterange = array("start" => $daterange_array[0], "end" => $daterange_array[1]);
	}

	$result = \lib\db\LogGroup::getList ( 1, $params, $pager_info );
	print $twig->render ( 'log_groups/list.twig', array (
			'result' => $result,
			'pager_info' => $pager_info,
			'daterange' => $daterange,
			'query_string' => '?'.http_build_query($params)
	) );
} );

$app->get ( '/page/:page', function ($page) use ($app, $twig) {
	if(!is_numeric($page)){
		$page = 1;
	}
	
	$params = $app->request()->get();
	$daterange=null;
	if(isset($params['d'])) {
		$daterange_array = explode("-", $params['d']);
		$daterange = array("start" => $daterange_array[0], "end" => $daterange_array[1]);
	}	
	
	$result = \lib\db\LogGroup::getList ( $page, $params, $pager_info );
	
	print $twig->render ( 'log_groups/list.twig', array (
			'result' => $result,
			'pager_info' => $pager_info,
			'daterange' => $daterange,
			'query_string' => '?'.http_build_query($params) 
	) );
} );

$app->get ( '/detail/:group_id', function ($group_id) use ($app, $twig) {
	$result = \lib\db\LogData::getList ( $group_id );
	$detail = \lib\db\LogGroup::getDetail ( $group_id );
	print $twig->render ( 'log_groups/detail.twig', array (
			'result' => $result, 
			'detail' => $detail,
			'status_list' => \lib\db\LogGroup::$DISPLAY_LOG_TYPES
	) );
} );

$app->post ( '/detail/:group_id', function ($group_id) use ($app, $twig) {
	$params = $app->request()->post();
	$msgs = array();
	$result = \lib\db\LogGroup::updateStatus( $group_id, $params, $msgs );
	$app->redirect( '/' );
} );

$app->post ( '/csv', function () use ($app, $twig) {
	$params = $app->request ()->post ();
	
	$res = $app->response();
	$res['Content-Description'] = 'File Transfer';
	$res['Content-Type'] = 'application/octet-stream';
	$res['Content-Disposition'] ='attachment; filename=serverlog.csv';
	$res['Content-Transfer-Encoding'] = 'binary';
	$res['Expires'] = '0';
	$res['Cache-Control'] = 'must-revalidate';
	$res['Pragma'] = 'public';
	if(isset($params['start']) && $params['end']) {
		$result = \lib\db\LogGroup::printCSV($params['start'], $params['end']);
	} else {
		$result = \lib\db\LogGroup::printCSV();
	}
} );