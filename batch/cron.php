<?php
include_once(dirname(__FILE__) . '/../lib/Core.php');

// AIDE LOG
$output = null;
try{
	exec(CONFIG_AIDE_EXEC.' --check | egrep "^(added:|removed:|changed:)"', $output);
	
	if(!empty($output)){
		$params = array(
				'new_content' => implode("\n", $output),
				'all_content' => '',
				'file_path' => '',
				'log_type' => \lib\Constants::LOG_TYPE_AIDE
		);
		$new_data = \lib\db\LogData::save($params, $msgs);
		if(!empty($new_data)){
			\lib\db\LogGroup::save($new_data);
		}
	}	

	exec(CONFIG_AIDE_EXEC.' --update');
	shell_exec("ls -lath /var/lib/aide");
	if(file_exists(CONFIG_AIDE_NEWDB)){
		exec('mv '.CONFIG_AIDE_NEWDB.' '.CONFIG_AIDE_DB);
	}	
} catch (Exception $e) {
	var_dump($e);
}


// LAST LOG
$output = null;
try{
	exec(CONFIG_LAST_COMMAND, $output);

	if(!empty($output)){
		$latest_data = \lib\db\LogData::getLatestData(\lib\Constants::LOG_TYPE_LAST);
		$output_diff = $output;
		
		if(!empty($latest_data)){
			$latest_output = explode("\n", $latest_data['all_content']);
			$output_diff = array_diff($output, $latest_output);
		}
		
		if (!empty($output_diff)) {
			$params = array(
					'new_content' => implode("\n", $output_diff),
					'all_content' => implode("\n", $output),
					'file_path' => '',
					'log_type' => \lib\Constants::LOG_TYPE_LAST
			);
			
			$new_data = \lib\db\LogData::save($params, $msgs);
			if(!empty($new_data)){
				\lib\db\LogGroup::save($new_data);
			}
		}
	}
} catch (Exception $e) {

}

\lib\db\LogData::refreshLastCheck();