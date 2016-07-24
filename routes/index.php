<?php
/** トップ画面*/
$app->get ( '/', function () use ($app, $twig) {
	$result = \lib\db\LogGroup::getList ( 1, null, $pager_info );
	print $twig->render ( 'log_groups/list.twig', array (
			'result' => $result,
			'pager_info' => $pager_info 
	) );
} );

$app->get ( '/page/:page', function ($page) use ($app, $twig) {
	$result = \lib\db\LogGroup::getList ( $page, null, $pager_info );
	print $twig->render ( 'log_groups/list.twig', array (
			'result' => $result,
			'pager_info' => $pager_info 
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
	$params = $app->request ()->post ();
	$msgs = array();
	$result = \lib\db\LogGroup::updateStatus( $group_id, $params, $msgs );
	$app->redirect( '/' );
} );