<?php
namespace lib\db;
require_once(dirname(__FILE__) . '/../Database.php');

class LogGroup
{
	const STATUS_UNCONFIRMED = 0;
	
	const STATUS_CONFIRMED = 1;
	
	const STATUS_INVESTIGATING = 2;
	
	public static $DISPLAY_LOG_TYPES = array(
			self::STATUS_UNCONFIRMED => '未確認',
			self::STATUS_CONFIRMED => '確認済',
			self::STATUS_INVESTIGATING => '確認中'
	);
	
    /**
     * @param $params
     * @param $msgs
     * @param $history Generated log_group history
     * @return \ORM
     */
    public static function save($log_data)
    {
        try {
        	$log_date = $log_data['date'];
        	$interval = $log_date - CONFIG_LOG_GROUP_INTERVAL_SECONDS;
        	$last_data = \ORM::for_table('log_group')
        		->where_gt('last_log_date', $interval)
        		->order_by_desc('log_group.last_log_date')
        		->find_one();
        	
            if (empty($last_data)) {
                $log_group = \ORM::for_table('log_group')->create();
                $log_group->set('first_log_date', $log_date);
                $log_group->set('last_log_date', $log_date);
                $log_group->set('last_status', self::STATUS_UNCONFIRMED);
            } else {
                $log_group = $last_data;
                if(!isset($log_group['last_log_date']) || $log_date > $log_group['last_log_date']) {
                	$log_group->set('last_log_date', $log_date);                	
                }
            }

            $log_types = $log_group['log_types'];
            if (strpos ($log_types, '['.$log_data['log_type'].']') === FALSE) {
            	$log_group->set('log_types', $log_types.'['.$log_data['log_type'].']');
            }
            $log_group->save();

            $log_data->set('group_id', $log_group->id());
            $log_data->save();
            return $log_group;
        } catch (Exception $e) {
            error_log($e->getTraceAsString());
        }
        return null;
    }

    public static function updateStatus($group_id, $params, &$msgs) {
    	$log_group = \ORM::for_table('log_group')
		            ->where('id', $group_id)
		            ->find_one();
    	if($log_group){
    		$log_group->set('last_status', $params['last_status']);
    		$log_group->set('last_comment', $params['last_comment']);
    		$log_group->set('last_confirm_date', time());
    		$log_group->save();
    	}
    }
    
    /**
     * @param Int $page
     * @param Array $hide_status
     */
    public static function getList($page = null, $params = null, &$pager_info = null)
    {
        $query = \ORM::for_table('log_group')
        		->select('log_group.id')
		        ->select('log_group.last_status')
		        ->select('log_group.log_types')
		        ->select('log_group.first_log_date')
		        ->select('log_group.last_log_date');

        if(isset($params['d'])) {
        	$daterange_array = explode("-", $params['d']);
        	$query = $query->where_gte('log_group.last_log_date', $daterange_array[0])
        					->where_lte('log_group.last_log_date', $daterange_array[1]);
        }

        $query->order_by_desc('log_group.last_log_date');

        if (!empty($page) && is_numeric($page)) {
            $count_query = clone $query;
            $total_count = $count_query->count();
            $query = $query->limit(\lib\Constants::ITEMS_PER_PAGE)->offset(($page - 1) * \lib\Constants::ITEMS_PER_PAGE);

            $result = $query->find_many();
            $count = count($result);

            $pager_info = \lib\Database::getPagerInfo($page, $count, $total_count);
        } else {
            $result = $query->find_many();
        }

        foreach($result as $record) {
        	self::addDisplayAttributes($record);
        }
        
        return $result;
    }

    
    public static function addDisplayAttributes(&$record){
		$display_log_types = '';
		if (isset ( $record ['log_types'] )) {
			$log_types = preg_replace ( "/^\[|\]$/", "", $record ['log_types'] );
			$log_types_array = explode ( '][', $log_types );
			$display_log_types_array = array ();
			foreach ( $log_types_array as $type ) {
				$display_type = @\lib\Constants::$DISPLAY_LOG_TYPES [$type];
				if (! empty ( $display_type )) {
					$display_log_types_array [] = $display_type;
				}
			}
			asort ( $display_log_types_array );
			$display_log_types = implode ( ", ", $display_log_types_array );
		}
		
		$record ['display_log_types'] = $display_log_types;
		$record ['display_last_status'] = @self::$DISPLAY_LOG_TYPES [$record ['last_status']];
    }


    /**
     * @param Int $log_group_id
     */
    public static function getDetail($log_group_id)
    {
        $detail = \ORM::for_table('log_group')
            ->where('id', $log_group_id)
            ->find_one();
        return $detail;
    }


    /**
     * @param Int $log_group_id
     */
    public static function delete($log_group_id)
    {
        $detail = \ORM::for_table('log_group')
            ->where('id', $log_group_id)
            ->find_one()
            ->delete();
        return true;
    }
    
    /**
     * @param Int $page
     * @param Array $hide_status
     */
    public static function printCSV($start = null, $end = null)
    {

    	$query = \ORM::for_table('log_group')
    	->select('log_group.id')
    	->select('log_group.last_status')
    	->select('log_group.last_comment')
    	->select('log_group.last_confirm_date')
    	->select('log_group.log_types')
    	->select('log_group.first_log_date')
    	->select('log_group.last_log_date');
    
    	if(is_numeric($start) && is_numeric($end) && ($start < $end)) {
    		$query = $query->where_gte('log_group.last_log_date', $start)
    		->where_lte('log_group.last_log_date', $end);
    	}
    	
    	$query->order_by_desc('log_group.last_log_date');
    	$result = $query->find_many();
    	
    	mb_convert_encoding('"内容","検知日時","確認状況","コメント","確認日時"', "SJIS", "UTF-8");
    	foreach($result as $record) {
    		self::addDisplayAttributes($record);
    		
    		$fields = array();    		
    		$fields[] =  mb_convert_encoding($record['display_log_types'], "SJIS", "UTF-8");
    		$fields[] = date('Y/m/d H:i', $record['last_log_date']);
    		$fields[] =  mb_convert_encoding($record['display_last_status'], "SJIS", "UTF-8");
    		$fields[] =  mb_convert_encoding($record['last_comment'], "SJIS", "UTF-8");
    		if(isset($record['last_confirm_date']) && $record['last_confirm_date'] != null) {
    			$fields[] = date('Y/m/d H:i', $record['last_confirm_date']);
    		} else {
    			$fields[] = "";
    		}
    		
    		fputcsv(fopen('php://output', 'w'), $fields);
    	}

    }

	
}