<?php
header("Access-Control-Allow-Origin: *");
// Defines
define('ROOT_DIR', dirname(__DIR__, 1));

class Merchant
{
	private $config;
	public function __construct()
	{
		// read config.json
		$config_filename = ROOT_DIR.'/config.json';

		if (!file_exists($config_filename)) {
		    throw new Exception("Can't find ".$config_filename);
		}
		$this->config = json_decode(file_get_contents($config_filename), true);
	}

	public function get_merchant_list($post)
	{
		$cache = $this->config['cache'];

		$file = ROOT_DIR.'/fpx/merchant.json';
		$current_time = time();
		$expire_time = $cache * 60;

		if(file_exists($file) && $current_time - $expire_time < filemtime($file)) {
			$merchant_list = json_decode(file_get_contents($file),true);
		} else {
			require ('conn.php');
			$stm = $pdo->query("SELECT * FROM merchant");
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			$list = NULL;
			
			foreach ($rows as $key => $value) {
				$merchant_list[$key] = $key.$value;
			}
			file_put_contents($file, json_encode($merchant_list));
		}
		
		$content = array();
		$content['merchant_list'] = $merchant_list;

		return $content;
	}
}
