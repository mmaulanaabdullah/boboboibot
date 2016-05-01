<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worker extends CI_Controller {

	private $array_of_role = array('apprenticeseer','beholder','cultist','cursed','detective','guardianangel','gunner','harlot','seer','tanner','villager','wildchild','wolf');
	private static $is_running = true;
	private static $is_global_running = true;

	public function __construct() {
        parent::__construct();
        $this->load->library('telegram', array(BOT_KEY));
        $this->load->model(array('mincoming_request','mincoming_request_processed', 'mgroup_player', 'mplayer', 'mstat','mgame_player'));
        if(!isset($_SESSION['last_id_incoming_request'])){
        	$_SESSION['last_id_incoming_request'] = $this->mincoming_request->get_last_id_incoming_request_processed();
        }
        if(!isset($_SESSION['is_running'])){
        	$_SESSION['is_running'] = true;
        }
        if(!isset($_SESSION['is_global_running'])){
        	$_SESSION['is_global_running'] = true;
        }
    }

    public function run(){
    	while(file_exists(GLOBALRUNNERFILE)){
    		if(file_exists(LOCALRUNNERFILE)){
    			echo 'running';
    		}
    		sleep(2);
    		echo 'is_global_running';
    	}
    }

    public function disableWorker(){
    	$_SESSION['is_running'] = false;
    	if($_SESSION['is_running']){echo 1;}else{echo 0;}
    }

    public function disableGlobalWorker(){
    	$_SESSION['is_global_running'] = false;	
    	if($_SESSION['is_global_running']){echo 1;}else{echo 0;}
    }

    /**
	* insert Function
	*/
	private function proceedTransaction(){
		$this->is_running = false;
		$lastHundredTrx = $this->mincoming_request->get_last_hundred_transaction($_SESSION['last_id_incoming_request']);
		$numRow = count($lastHundredTrx);
		if($numRow>0){
			$_SESSION['last_id_incoming_request'] = $_SESSION['last_id_incoming_request'] + $numRow + 1;
			// PROCEED TRX BASED ON MESSAGE
			foreach($lastHundredTrx as $hundredTrx){
				// VALIDATE TRANSACTION
				if(isset($hundredTrx)){
					if(isset($hundredTrx['text']) && !empty($hundredTrx['text'])){
						if($hundredTrx['chat_type']=='group'){
							$this->dispatch($hundredTrx);
						}else{
							$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /SETGROUPPLAYER'));
						}
					}
				}
				$this->insertIncomingRequestProcessed($hundredTrx['id']);
			}
		}
	}

	private function dispatch($incoming_request){
		$message = explode(' ', $incoming_request['text']);
		if(count($message)>1){
			if($message[0]==MOSTGROUP){
				$role = strtolower($message[1]);
				if(in_array($role, $this->array_of_role)){
					$this->commandMost($incoming_request, $role);
				}else{
					// send message role not found
					$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'role not found, please refer to http://werewolf.parawuff.com'));
				}
			}else if($message[0]==LEASTGROUP){
				$role = strtolower($message[1]);
				if(in_array($role, $this->array_of_role)){
					$this->commandLeast($incoming_request, $role);
				}else{
					// send message role not found
				}
			}else if($message[0]==SETGROUP){

			}else if($message[0]==MOSTGAME){

			}else if($message[0]==LEASTGAME){

			}else if($message[0]==SETGAME){

			}else{
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'command not found, please type /HELP to list all command.'));
			}
		}else if(count($message)==1){
			// $message[0]==OVERALLSTATS
			if($message[0]==OVERALLSTATS){

			}else if($message[0]==HELP){

			}else{

			}
		}else{
			// send message no command recognized
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'command not found, please type /HELP to list all command.'));
		}
	}

	private function insertIncomingRequestProcessed($id_incoming_request){
		$request = array(
			'id_incoming_request' => $id_incoming_request
		);
		return $this->mincoming_request_processed->insert($request);
	}

	private function commandMostGroup($incoming_request, $role){
		// /most ROLE e.g most wolf
		$group_players = $this->mgroup_player->get_by_group($incoming_request["chat_title"]);
		if($group_players){
			$array_of_player_name = array();
			foreach($group_players as $group_player){
				array_push($array_of_player_name, $group_player['player_name']);
			}
			$result = $this->mstat->get_common_role($array_of_player_name, $role, 'desc');
			$content = 'Most 10 common '.$role.' in group:\n';
			foreach($result as $res){
				$content .= $res['player_name'] .' '.$res['stats_'.$role].'% \n';
			}
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));
		}else{
			// send message that no registered group
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /SETGROUPPLAYER'));
		}
	}

	private function commandMostGame($incoming_request, $role){
		// /most ROLE e.g most wolf
		if(isset($incoming_request)){
			if($incoming_request['chat_type']=='group'){
				$group_players = $this->mgroup_player->get_by_group($incoming_request["chat_title"]);
				if($group_players){
					$array_of_player_name = array();
					foreach($group_players as $group_player){
						array_push($array_of_player_name, $group_player['player_name']);
					}
					$result = $this->mstat->get_common_role($array_of_player_name, $role, 'desc');
					print_r($result);
				}else{
					// send message that no registered group
					$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group and ensure your group is registered by commad /SETGROUPPLAYER'));
				}
			}else{
				// send message that it should be sent from group
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group and ensure your group is registered by commad /SETGROUPPLAYER'));
			}
		}
	}


	private function commandLeastGroup(){

	}

	private function commandLeastGame(){

	}


	private function setGroupPlayer($incoming_request){
		explode(' ', $incoming_request['text']);
		
	}

	private function setGamePlayer($incoming_request){

	}

	private function help($incoming_request){

	}
}
