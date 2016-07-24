<?php
namespace lib\db;
use lib\Constants;

require_once(dirname(__FILE__) . '/../Database.php');

class LogData
{
    /**
     * @param $params
     * @param $msgs
     * @return \ORM
     */
    public static function save($params, &$msgs)
    {
        try {
            if (empty($params['id'])) {
                $log_data = \ORM::for_table('log_data')->create();
            } else {
                $log_data = \ORM::for_table('log_data')->where('id', $params['id'])->find_one();

            }

            if (empty($log_data)) {
                $msgs[] = "";
                return null;
            }
            $log_data->set('new_content', $params['new_content']);
            $log_data->set('all_content', $params['all_content']);
            $log_data->set('file_path', $params['file_path']);
            $log_data->set('log_type', $params['log_type']);

            if (empty($params['id'])) {
                $log_data->set('date', time());
            }
            $log_data->save();

            return $log_data;
        } catch (Exception $e) {
            error_log($e->getTraceAsString());
        }
        return null;
    }

    /**
     * @param Int $page
     * @param Array $hide_status
     */
    public static function getList($group_id)
    {

        $query = \ORM::for_table('log_data')
                ->select('log_data.id')
                ->select('log_data.group_id')
                ->select('log_data.new_content')
                ->select('log_data.file_path')
                ->select('log_data.log_type')
                ->select('log_data.date')
        		->where('log_data.group_id', $group_id);
        $query->order_by_desc('log_data.date');
        $result = $query->find_many();
        

        foreach($result as $record) {
        	$record['display_log_type'] = @\lib\Constants::$DISPLAY_LOG_TYPES[$record['log_type']];
        }
        return $result;
    }



    /**
     * @param Int $log_data_id
     */
    public static function getDetail($log_data_id)
    {
        $detail = \ORM::for_table('log_data')
            ->where('id', $log_data_id)
            ->find_one();
        return $detail;
    }
    
    public static function getLatestData($log_type)
    {
    	$detail = \ORM::for_table('log_data')
    	->where('log_type', $log_type)
    	->order_by_desc('date')
    	->limit(1)
    	->find_one();
    	return $detail;
    }
    
    public static function refreshLastCheck()
    {
    	$last_check_data = \ORM::for_table('log_data')
	    	->where('log_type', \lib\Constants::LOG_TYPE_LAST_CHECK)
	    	->order_by_desc('date')
	    	->limit(1)
	    	->find_one();
    	
    	if (empty($last_check_data)) {
    		$last_check_data = \ORM::for_table('log_data')->create();
    		$last_check_data->set('log_type', \lib\Constants::LOG_TYPE_LAST_CHECK);
    	}
    	$last_check_data->set('date', time());
    	$last_check_data->save();
    }


    /**
     * @param Int $log_data_id
     */
    public static function delete($log_data_id)
    {
        $detail = \ORM::for_table('log_data')
            ->where('id', $log_data_id)
            ->find_one()
            ->delete();
        return true;
    }

	
}