<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->library('telegram', array(BOT_KEY));
        $this->load->model(array('mincoming_request','mincoming_request_processed','mplayer','mstat','mgroup_player'));
    }

    public function test6(){
    	print_r($this->telegram->getMe());
    }


	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function test(){
		//  Initiate curl
		$url="http://werewolf.parawuff.com/Home/PlayerNames";

		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);

		print_r($result);
		// Will dump a beauty json :3
		//var_dump(json_decode($result, true));		
	}

	public function test2(){
		$url="http://werewolf.parawuff.com/Home/PlayerStats?playerName=?????&json=1";

		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);
		// Set header
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);
		$result=str_replace("\u003c","<",$result);
		$result=str_replace("\u003e",">",$result);
		$result=str_replace("class","",$result);
		$result=str_replace('"',"",$result);
		$result=str_replace('=\\table table-hover\\',"",$result);
		if (!strpos($result, 'not found')) {
			$xml=simplexml_load_string($result) or die("Error: Cannot create object");		    
		}else{
			echo "player not found";
		}	
	}

	public function test3(){
		$url="https://api.telegram.org/bot".BOT_KEY."/getMe";

		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);

		print_r($result);
	}

	public function test4(){
		print_r (json_encode(array("test"=>"maulana")));
	}

	public function test5(){
		$ch1 = curl_init();
		$ch2 = curl_init();
		$urls = array(
		  "http://localhost/boboboibotcom/api/test4",
		  "http://localhost/boboboibotcom/api/test4"
		);

		// Disable SSL verification
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch1, CURLOPT_URL,$urls[0]);
		// Set header
		curl_setopt($ch1, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

		// Disable SSL verification
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch2, CURLOPT_URL,$urls[1]);
		// Set header
		curl_setopt($ch2, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

		// Execute
		$mh = curl_multi_init();
		curl_multi_add_handle($mh,$ch1);
		curl_multi_add_handle($mh,$ch2);

		$active = null;
		$mrc = array();
		//execute the handles
		do {
		    $mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc == CURLM_OK) {
		    if (curl_multi_select($mh) != -1) {
		        do {
		            $mrc = curl_multi_exec($mh, $active);
		        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
		    }
		}
		// Check for any errors
	    if ($mrc != CURLM_OK) {
	      	trigger_error("Curl multi read error $execReturnValue\n", E_USER_WARNING);
	    }
		
		$curlError = curl_error($ch1);
		if($curlError == "") {
			$res1 = curl_multi_getcontent($ch1);
		} else {
			print "Curl error on handle 1: $curlError\n";
		}
		// Remove and close the handle
		curl_multi_remove_handle($mh, $ch1);
		curl_close($ch1);

		$curlError = curl_error($ch2);
		if($curlError == "") {
			$res2 = curl_multi_getcontent($ch2);
		} else {
			print "Curl error on handle 2: $curlError\n";
		}
		// Remove and close the handle
		curl_multi_remove_handle($mh, $ch2);
		curl_close($ch2);

		curl_multi_close($mh);
		print_r($res1);print_r($res2);
	}

	public function test7(){
		$this->mincoming_request_processed->insert(array('id_incoming_request'=>1));
	}

	public function test8(){
		print_r($this->mstat->get_common_role(
				array('Muhammad Maulana Abdullah', 'Sigit Wardoyo', 'Fadita Cahyaning Putri'), 'wolf', 'asc'
			));
	}

	public function test9(){
		echo BASEPATH;
		echo "<br/>";
		echo APPPATH;

	}

	public function test10(){
		while(true){
			echo 'aak;'
		}
		sleep(2);
	}
}
