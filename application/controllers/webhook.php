<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook extends CI_Controller {

	private $array_of_role = array('won','lost','survived','apprenticeseer','beholder','cultist','cursed','detective','guardianangel','gunner','harlot','seer','tanner','traitor','villager','wildchild','wolf','beautiful');
	
	private $isRunning;

	public function __construct() {
        parent::__construct();
        $this->load->library('telegram', array(BOT_KEY));
        $this->load->model(array('mincoming_request','mincoming_request_processed','mstat','mplayer','mgroup_player','mgame_player'));
        if(!isset($_SESSION['last_id_incoming_request'])){
        	$_SESSION['last_id_incoming_request'] = $this->mincoming_request->get_last_id_incoming_request_processed();
        }
    }

	public function setWebHook(){
		print_r($this->telegram->setWebhook(BOT_URL, '@'.BOT_CRT));
	}

	public function hamimiirmasaraswati(){
		$content = file_get_contents("php://input");
		$update = json_decode($content, true);
		if (!$update) {
			// receive wrong update, must not happen
		  	exit;
		}
		if (isset($update["message"])) {
			// 1. log incoming request
			$insert = array();
			$insert["update_id"] = $update["update_id"];
			$insert["message_id"] = $update["message"]["message_id"];
			$insert["date"] = $update["message"]["date"];
			$insert["text"] = $update["message"]["text"];
			$insert["from_first_name"] = $update["message"]["from"]["first_name"];
			$insert["from_last_name"] = $update["message"]["from"]["last_name"];
			$insert["from_username"] = $update["message"]["from"]["username"];
			$insert["chat_id"] = $update["message"]["chat"]["id"];
			$insert["chat_title"] = $update["message"]["chat"]["title"];
			$insert["chat_username"] = $update["message"]["chat"]["username"];
			$insert["chat_type"] = $update["message"]["chat"]["type"];
			$this->mincoming_request->insert($insert);
			$this->proceedTransaction($insert);
		}

	}

	private function proceedTransaction($incoming_request){
		// VALIDATE TRANSACTION
		if(isset($incoming_request)){
			if(isset($incoming_request['text']) && !empty($incoming_request['text'])){
				if($incoming_request['chat_type']=='group'){
					$this->dispatch($incoming_request);
				}else{
					$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /setgroup'));
				}
			}
		}
		$this->insertIncomingRequestProcessed($hundredTrx['id']);
	}

	private function dispatch($incoming_request){
		$message = array();
		$first_message = '';
		if(strpos(strtolower($incoming_request['text']), '@boboboibot')){
			$message = explode('@', strtolower($incoming_request['text']));
			if(count($message)>0){
				if($message[1]=='boboboibot'){
					$first_message = $message[0];
				}
			}
		}else{
			$message = explode(' ', $incoming_request['text']);
			if(count($message)>0){
				$first_message=$message[0];
			}
		}
		if($first_message==MOSTGROUP){
			$role = strtolower($message[1]);
			if(in_array($role, $this->array_of_role)){
				if($role=='beautiful'){
					$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'Im not sure all girls here are those whom you mean it. but I have one, that sit in front of me at office.'));
				}else{
					$this->commandMostGroup($incoming_request, $role);
				}

			}else{
				// send message role not found
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'role not found, please refer to http://werewolf.parawuff.com'));
			}
		}else if($first_message==LEASTGROUP){
			$role = strtolower($message[1]);
			if(in_array($role, $this->array_of_role)){
				$this->commandLeastGroup($incoming_request, $role);
			}else{
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'role not found, please refer to http://werewolf.parawuff.com'));
			}
		}else if($first_message==SETGROUP){
			$this->setGroupPlayer($incoming_request);
		}else if($first_message==MOSTGAME){
			$role = strtolower($message[1]);
			if(in_array($role, $this->array_of_role)){
				if($role=='beautiful'){
					$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'Im not sure all girls here are those whom you mean it. but I have one, that sit in front of me at office.'));
				}else{
					$this->commandMostGame($incoming_request, $role);
				}

			}else{
				// send message role not found
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'role not found, please refer to http://werewolf.parawuff.com'));
			}
		}else if($first_message==LEASTGAME){
			$role = strtolower($message[1]);
			if(in_array($role, $this->array_of_role)){
				$this->commandLeastGame($incoming_request, $role);
			}else{
				// send message role not found
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'role not found, please refer to http://werewolf.parawuff.com'));
			}
		}else if($first_message==SETGAME){
			$this->setGamePlayer($incoming_request);
		}else if($first_message==HELP){
			$this->help($incoming_request);
		}else if($first_message==ME){
			$this->me($incoming_request);
		}else if($first_message==STATME){
			$this->statme($incoming_request);
		}else if($first_message==RANDOMKILL){
			$this->randomkill($incoming_request);
		}else{
			//$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'command not found, please type /help to list all command.'));
		}
	}

	private function insertIncomingRequestProcessed($id_incoming_request){
		$request = array(
			'id_incoming_request' => $id_incoming_request
		);
		return $this->mincoming_request_processed->insert($request);
	}

	private function commandMostGroup($incoming_request, $role){
		$group_players = $this->mgroup_player->get_by_group($incoming_request['chat_title']);
		//print_r($group_players);
		if($group_players){
			$array_of_player_name = array();
			foreach($group_players as $gr){
				array_push($array_of_player_name, $gr->player_name);
			}
			$result = $this->mstat->get_common_role($array_of_player_name, $role, 'desc');
			$content = 'Most 10 common '.$role.' in group:'. PHP_EOL;
			$i = 0;
			$last_update = 0;
			if($result){
				foreach($result as $res){
					$i++; 
					$res->player_name;
					$content .= $i.'. '.$res->player_name.' '.$res->stats_role .'%'. PHP_EOL;
					$last_update = $res->datetime;
				}
				$last_update = $last_update + 7*60*60;
				$content.= 'last update : ';
				$content.= gmdate("d-m-Y H:i:s", $last_update);
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));
			}else{
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'no stats for '.$role.' in this group'));
			}
			
		}else{
			// send message that no registered group
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /setgroup'));
		}
	}
	private function commandMostGame($incoming_request, $role){
		$game_players = $this->mgame_player->get_by_game($incoming_request['chat_title']);
		if($game_players){
			$array_of_player_name = array();
			foreach($game_players as $gp){
				array_push($array_of_player_name, $gp->player_name);
			}
			$result = $this->mstat->get_common_role($array_of_player_name, $role, 'desc');
			$content = 'Most 10 common '.$role.' in game:'. PHP_EOL;
			$i = 0;
			$last_update = 0;
			if($result){
				foreach($result as $res){
					$i++; 
					$res->player_name;
					$content .= $i.'. '.$res->player_name.' '.$res->stats_role .'%'. PHP_EOL;
					$last_update = $res->datetime;
				}
				$last_update = $last_update + 7*60*60;
				$content.= 'last update : ';
				$content.= gmdate("d-m-Y H:i:s", $last_update);
$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));
			}else{
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'no stats for '.$role.' in this game'));
			}
			
		}else{
			// send message that no registered group
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /setgame'));
		}
	}


	private function commandLeastGroup($incoming_request, $role){
		$game_players = $this->mgame_player->get_by_game($incoming_request['chat_title']);
		if($game_players){
			$array_of_player_name = array();
			foreach($game_players as $gp){
				array_push($array_of_player_name, $gp->player_name);
			}
			$result = $this->mstat->get_common_role($array_of_player_name, $role, 'asc');
			$content = 'Most 10 common '.$role.' in game:'. PHP_EOL;
			$i = 0;
			$last_update = 0;
			if($result){
				foreach($result as $res){
					$i++; 
					$res->player_name;
					$content .= $i.'. '.$res->player_name.' '.$res->stats_role .'%'. PHP_EOL;
					$last_update = $res->datetime;
				}
				$last_update = $last_update + 7*60*60;
				$content.= 'last update : ';
				$content.= gmdate("d-m-Y H:i:s", $last_update);
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));
			}else{
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'no stats for '.$role.' in this group'));
			}
			
		}else{
			// send message that no registered group
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /setgroup'));
		}
	}

	private function commandLeastGame($incoming_request, $role){
		$game_players = $this->mgame_player->get_by_game($incoming_request['chat_title']);
		//print_r($group_players);
		if($game_players){
			$array_of_player_name = array();
			foreach($game_players as $gp){
				array_push($array_of_player_name, $gp->player_name);
			}
			$result = $this->mstat->get_common_role($array_of_player_name, $role, 'asc');
			$content = 'Least 10 common '.$role.' in game:'. PHP_EOL;
			$i = 0;
			$last_update = 0;
			if($result){
				foreach($result as $res){
					$i++; 
					$res->player_name;
					$content .= $i.'. '.$res->player_name.' '.$res->stats_role .'%'. PHP_EOL;
					$last_update = $res->datetime;
				}
				$last_update = $last_update + 7*60*60;
				$content.= 'last update : ';
				$content.= gmdate("d-m-Y H:i:s", $last_update);
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));
			}else{
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'no stats for '.$role.' in this game'));
			}
			
		}else{
			// send message that no registered group
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /setgame'));
		}
	}


	private function setGroupPlayer($incoming_request){
		$group_name = $incoming_request['chat_title'];
		$content = $incoming_request['text'];
		if(strpos(strtolower($content),'@boboboibot')){
			$content = explode('@',$content);
			$player_name = $incoming_request['from_first_name'].' '.$incoming_request['from_last_name'];			
			$this->mgroup_player->insert(array('group_name'=>$group_name, 'player_name'=>$player_name),$player_name);
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$player_name.' is added to this group'));
			
		}else{
			$players = explode("\n", $content);
			$insert = array();
			$arrPlayers = array();
			foreach($players as $player){
				$pl = explode(":", $player);
				if(count($pl)>0){
					$player_name = $pl[0];
					$player_name = preg_replace('/[.,^"`´]/', '', $player_name);
					$player_name = str_replace(": ", '', $player_name);
					$player_name = str_replace("'", '', $player_name);
					$player_name = str_replace('"', '', $player_name);
					array_push($insert, array('group_name'=>$group_name, 'player_name'=>$player_name));
					array_push($arrPlayers, $player_name);
				}
			}
			$this->mgroup_player->insert_batch($insert,$arrPlayers);
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'Set '.count($insert).' players into group success'));
		}
	}

	private function setGamePlayer($incoming_request){
		$game_name = $incoming_request['chat_title'];
		$players = explode("\n", $incoming_request['text']);
		$insert = array();
		foreach($players as $player){
			$pl = explode(":", $player);
			if(count($pl)>0){
				$player_name = $pl[0];
				$player_name = preg_replace('/[.,^"`´]/', '', $player_name);
				$player_name = str_replace(": ", '', $player_name);
				$player_name = str_replace("'", '', $player_name);
				$player_name = str_replace('"', '', $player_name);
				array_push($insert, array('game_name'=>$game_name, 'player_name'=>$player_name));
			}
		}
		$this->mgame_player->insert_batch($insert);
		$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'Set '.count($insert).' players into game success'));
	}
	
	private function statme($incoming_request){
		$player_stat = $this->mstat->get_player_stat($incoming_request['from_first_name'].' '.$incoming_request['from_last_name']);
		if($player_stat){
			$content = 'stats of '.$incoming_request['from_first_name'].' '.$incoming_request['from_last_name'].PHP_EOL;
			$content .= 'total game played: '.$player_stat->total_game.PHP_EOL;
			$total = 0;
			foreach($this->array_of_role as $role){
				$stat_role = 'stats_'.$role;
				if($player_stat->$stat_role){
					$content .= $role.': '.$player_stat->$stat_role.'%'.PHP_EOL;	
					if($role!='won' && $role!='lost' && $role!='survived'){
						$total = $total + $player_stat->$stat_role;
					}
				}
			}
			$content .= 'total percentage '. $total.PHP_EOL;
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));	
		}else{
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'user not found, please register your user into your group by typing /setgroup@boboboibot'));	
		}
	}

	private function randomkill($incoming_request){
		$rand = mt_rand(0,10000000000);
		$game_players = $this->mgame_player->get_by_game($incoming_request['chat_title']);
		if($game_players){
			$mod = count($game_players);
			$idx = $rand % $mod;
			$player_name = $game_players[$idx]->player_name;
			if(strpos($player_name,'setgame')){
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'Im not in the mood to hate anybody. Decide by yourself :*'));
			}else{
				$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'please kill '.$player_name.' whom I hate most!'));
			}
		}else{
			$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>'this command is allowed only if you are in Telegram group. So invite me to your group and ensure your group is registered by typing command /setgroup list_of_player'));
		}
	}


	private function help($incoming_request){
		$content = '/setgroup - set werewolf players in your group as our reference and their statistic will be automatically updated'.PHP_EOL;
		$content .= '/setgame - /setgame list_of_player while you are in a game, set up your player as our reference to get their statistic. You should run this command every game that you are playing'.PHP_EOL;
		$content .= '/mostgroup - get most common role in your group e.g /mostgroup wolf'.PHP_EOL;
		$content .= '/leastgroup - get least common role in your group e.g /leastgroup wolf'.PHP_EOL;
		$content .= '/mostgame - get most common role in your active game e.g /mostgame wolf'.PHP_EOL;
		$content .= '/leastgame - get least common role in your active game e.g /leastgame wolf'.PHP_EOL;
		$content .= '/statme - get your werewolf stats'.PHP_EOL;
		$content .= '/randomkill - first /setgame list_of_player and then let boboboibot choose to kill one whom it hates at most'.PHP_EOL;
		$content .= '/help - list all commands'.PHP_EOL;
		$content .= '/me - salam cheers from kirundadeh :*'.PHP_EOL;
		$content .= 'notes : only players with at least 10 times playing will show up in statistic';
		$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));
	}

	private function me($incoming_request){
		$content = 'salam cheers from kirundadeh :*';
		$this->telegram->sendMessage(array('chat_id'=>$incoming_request['chat_id'], 'text'=>$content));
	}

}