<?php

namespace lib;
require_once(dirname(__FILE__) . '/../config/config.inc.php');

define("BASE_DIR", __DIR__ .'/..');
define("SCRIPTS_DIR", BASE_DIR.'/scripts');
define("WORK_DIR", BASE_DIR.'/out');

class Constants
{
    const PASSWORD_HASH_SUFFIX = '_SECRET';

    const SESSION_NAME = '00f3e8f1-fdd0-2f75-a81a-643a1b2b8a0d';

    const ITEMS_PER_PAGE = 20;

    const CALL_TIMEOUT = 20;
    
    const LOG_TYPE_AIDE = 'aide';
    
    const LOG_TYPE_LAST = 'last';
    
    const LOG_TYPE_LAST_CHECK = 'last_check';
    
    static $DISPLAY_LOG_TYPES = array(
    		self::LOG_TYPE_AIDE => 'ファイル更新',
    		self::LOG_TYPE_LAST => 'システムログイン',
    		self::LOG_TYPE_LAST_CHECK => '最終チェック日時'
    	);
}
