<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admins
 *
 * @author Maulnick
 */
class Mstat extends CI_Model {
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    //INSERT or CREATE FUNCTION
    function insert($program){
        $this->delete_by_player_name($program['player_name']);
        $this->db->insert('stats', $program);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
    //GET FUNCTION
    function get_by_id($stats_id){
        $this->db->select('stats.*');
        $this->db->where('id',$stats_id);
        $query = $this->db->get('stats');
        if($query->num_rows()>0){
            $result  = $query->result();
            return $result[0];
        }else{
            return false;
        }
    }

    function get_all(){
        $query = $this->db->get('stats');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_limit($limit,$offset){
        $query = $this->db->get('stats',$limit,$offset);
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_common_role($array_of_player_name, $role, $order_by){
        $this->db->select('stats.id, stats.player_name, stats.stats_'.$role.' as stats_role, stats.datetime');
        $this->db->where_in('player_name', $array_of_player_name);
        $this->db->where('total_game >',10);
        $this->db->order_by('stats_'.$role, $order_by);
        $this->db->limit(10);
        $query = $this->db->get('stats');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_player_stat($player_name){
        $this->db->select('stats.*');
        $this->db->where_in('player_name', $player_name);
        $query = $this->db->get('stats');
        if($query->num_rows()==1){
            $result = $query->result();
            return $result[0];
        }else{
            return false;
        }
    }

    
    //UPDATE FUNCTION
    function update($stats_id, $program){
        $this->db->where('id',$stats_id);
        return $this->db->update('stats', $program);
    }
    
    
    //DELETE FUNCTION
    function delete($id){
    	$this->db->where('id',$id);
    	$this->db->delete('stats');
    	if($this->db->affected_rows()>0){
    		return true;
    	}
    	else{
    		return false;
    	}
    }

    function delete_by_player_name($player_name){
        $this->db->where("player_name ='".$player_name."' COLLATE utf8_unicode_ci");
        $this->db->delete('stats');
        if($this->db->affected_rows()>0){
            return true;
        }
        else{
            return false;
        }
    }

}
