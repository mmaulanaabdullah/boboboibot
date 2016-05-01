<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crawler extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->library('telegram', array(BOT_KEY));
        $this->load->model(array('mplayer','mstat','mincoming_request','mincoming_request_processed','mgroup_player'));
        if(!isset($_SESSION['last_id_incoming_request'])){
        	$_SESSION['last_id_incoming_request'] = $this->mincoming_request->get_last_id_incoming_request_processed();
        }
    }

    public function requestPlayers(){
    	$url="http://werewolf.parawuff.com/Home/PlayerNames";

		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);
		// Execute
		$results=curl_exec($ch);
		// Closing
		curl_close($ch);

		$results = json_decode($results, true);
		$total_result = count($results);
		$total_insert = 0;
		foreach($results as $res){
			if(!$this->mplayer->get_by_player($res)){
				//$res = preg_replace('/[^A-Za-z0-9\ -]/', '', $res); // Removes special chars.
				$res = preg_replace('/[.,^"`´]/', '', $res);
				$res = str_replace("'", '', $res);
				$res = str_replace('"', '', $res);
				// $res = str_replace("`", '', $res);
				// $res = str_replace("´", '', $res);
				// $res = str_replace("^", '', $res);
				if($this->mplayer->insert(array('player_name'=>$res))){
					$total_insert++;
				}
			}
		}
		$count = $this->mplayer->delete_whitespace();
		$total_insert = $total_insert - $count;
		$return = array('total_result'=>$total_result, 'total_insert'=>$total_insert);
		print_r($return);
		//return json_encode($return);
    }

    public function executeCrawling(){
    	$start = $this->input->get('start');
    	$max_thread= $this->input->get('end');
    	// Execute
		$mh = curl_multi_init();
    	for($i=$start;$i<$max_thread;$i++){
    		$url[$i] = "http://localhost/boboboibotcom/crawler/requestAllPlayerStats?offset=$i"; 
    		$ch[$i] = curl_init();
			$ch[$i] = curl_init();
			curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, false);
			// Will return the response, if false it print the response
			curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
			// Set the url
			curl_setopt($ch[$i], CURLOPT_URL,$url[$i]);
			// Set header
			curl_setopt($ch[$i], CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
			curl_multi_add_handle($mh,$ch[$i]);
    	}

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
		
	    for($i=$start;$i<$max_thread;$i++){
	    	$curlError = curl_error($ch[$i]);
			if($curlError == "") {
				$res[$i] = curl_multi_getcontent($ch[$i]);
			} else {
				print "Curl error on handle $i: $curlError\n";
			}
			// Remove and close the handle
			curl_multi_remove_handle($mh, $ch[$i]);
			curl_close($ch[$i]);
	    }
		curl_multi_close($mh);
		
		for($i=$start;$i<$max_thread;$i++){
	    	print_r($res[$i]);
	    }
		
	}

    public function requestAllPlayerStats(){
    	$offset = $this->input->get('offset');
    	$offset = $offset * 100;
    	//$players = $this->mplayer->get_limit(100, $offset);
    	$players = $this->mgroup_player->get_all_distinct(100, $offset);
    	$return = array();
    	$f=0;
    	$nf=0;
    	$br=0;
    	if($players){
	    	if(count($players)>0){
	    		foreach($players as $player){
	    			if(!empty($player->player_name)){
	    				$ret = array();
	    				$ret['player_name'] = $player->player_name;
	    				$ret['status'] = $this->requestPlayerStats($player->player_name);
	    				if($ret['status']=='xml broken'){
	    					$br++;
	    				}else if($ret['status']=='player not found'){
	    					$nf++;
	    				}else{
	    					$f++;
	    				}
	    				array_push($return, $ret);
	    			}
	    		}
	    	}
    	}
    	$return['f'] = $f;$return['nf'] = $nf;$return['br'] = $br;
    	print_r($return);
    	return $return;
    }

    private function requestPlayerStats($playerName=""){
    	$decodePlayerName=urlencode($playerName);
    	$param="playerName=".$decodePlayerName."&json=1";
    	$url="http://werewolf.parawuff.com/Home/PlayerStats?".$param;
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
		//$result=stripslashes($result);

		$return = array();
		if (!strpos($result, 'not found')) {
			$xml=simplexml_load_string($result) or print_r($result);
			if($xml){
				$arrEll = (array)$xml;
				$arrStats = array( 
					'total_game' => 0, 'won' => 0, 'lost' => 0, 'survived' => 0, 'apprenticeseer'=>0,
					'beholder' => 0, 'cultist' => 0, 'cursed' => 0, 'detective' => 0, 'guardianangel'=>0,
					'gunner' => 0, 'harlot' => 0, 'seer'=> 0, 'tanner'=> 0,
					'traitor'=>0, 'villager' => 0, 'wildchild' => 0, 'wolf' => 0
				);
				$arrStats['player_name']=$playerName;
				foreach($arrEll as $ell){
					$ell = (array) $ell;
					foreach($ell['tr'] as $eltd){
						$eltd = (array) $eltd;
						if(strtolower($eltd['td'][0])=='games played'){
							$arrStats['total_game'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='won'){
							$arrStats['won'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='lost'){
							$arrStats['lost'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='survived'){
							$arrStats['survived'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='apprenticeseer'){
							$arrStats['apprenticeseer'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='beholder'){
							$arrStats['beholder'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='cultist'){
							$arrStats['cultist'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='cursed'){
							$arrStats['cursed'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='detective'){
							$arrStats['detective'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='guardianangel'){
							$arrStats['guardianangel'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='gunner'){
							$arrStats['gunner'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='harlot'){
							$arrStats['harlot'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='seer'){
							$arrStats['seer'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='tanner'){
							$arrStats['tanner'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='traitor'){
							$arrStats['traitor'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='villager'){
							$arrStats['villager'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='wildchild'){
							$arrStats['wildchild'] = $eltd['td'][1];
						}else if(strtolower($eltd['td'][0])=='wolf'){
							$arrStats['wolf'] = $eltd['td'][1];
						}
					}
				}
				if($arrStats['total_game']!=0){
					$arrStats['stats_won'] = $arrStats['won'] * 100 / $arrStats['total_game'];
					$arrStats['stats_lost'] = $arrStats['lost'] * 100 / $arrStats['total_game'];
					$arrStats['stats_survived'] = $arrStats['survived'] * 100 / $arrStats['total_game'];
					$arrStats['stats_apprenticeseer'] = $arrStats['apprenticeseer'] * 100 / $arrStats['total_game'];
					$arrStats['stats_beholder'] = $arrStats['beholder'] * 100 / $arrStats['total_game'];
					$arrStats['stats_cultist'] = $arrStats['cultist'] * 100 / $arrStats['total_game'];
					$arrStats['stats_cursed'] = $arrStats['cursed'] * 100 / $arrStats['total_game'];
					$arrStats['stats_detective'] = $arrStats['detective'] * 100 / $arrStats['total_game'];
					$arrStats['stats_guardianangel'] = $arrStats['guardianangel'] / $arrStats['total_game'];
					$arrStats['stats_gunner'] = $arrStats['gunner'] * 100 / $arrStats['total_game'];
					$arrStats['stats_harlot'] = $arrStats['harlot'] * 100 / $arrStats['total_game'];
					$arrStats['stats_seer'] = $arrStats['seer'] * 100 / $arrStats['total_game'];
					$arrStats['stats_tanner'] = $arrStats['tanner'] * 100 / $arrStats['total_game'];
					$arrStats['stats_traitor'] = $arrStats['traitor'] * 100 / $arrStats['total_game'];
					$arrStats['stats_villager'] = $arrStats['villager'] * 100 / $arrStats['total_game'];
					$arrStats['stats_wildchild'] = $arrStats['wildchild'] * 100 / $arrStats['total_game'];
					$arrStats['stats_wolf'] = $arrStats['wolf'] * 100 / $arrStats['total_game'];
					$arrStats['datetime']= time();
					$return['status'] = $this->mstat->insert($arrStats);	
				}	
			}else{
				$return['status'] = 'xml broken';
			}
		}else{
			$return['status'] = 'player not found';
		}
		return $return; 
    }

}
