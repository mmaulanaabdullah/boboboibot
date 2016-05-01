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
class Mplayer extends CI_Model {
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    //INSERT or CREATE FUNCTION
    function insert($program){
        $this->db->insert('players', $program);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    function insert_batch($program){
        $this->db->insert_batch('players', $program);
    }

    
    //GET FUNCTION
    function get_by_id($players_id){
        $this->db->select('players.*');
        $this->db->where('id',$players_id);
        $query = $this->db->get('players');
        if($query->num_rows()>0){
            $result = $query->result();
            return $result[0];
        }else{
            return false;
        }
    }

    function get_by_player($player_name){
        $this->db->where('player_name',$player_name);
        $this->db->order_by('player_name','asc');
        $query = $this->db->get('players');
        if($query->num_rows()>0){
            return true;
        }else{
            return false;
        }
    }

    function get_all(){
        $this->db->limit(10);
        $query = $this->db->get('players');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_limit($limit,$offset){
        $query = $this->db->get('players',$limit,$offset);
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    
    //UPDATE FUNCTION
    function update($players_id, $program){
        $this->db->where('id',$players_id);
        return $this->db->update('players', $program);
    }
    
    
    //DELETE FUNCTION
    function delete($id){
    	$this->db->where('id',$id);
    	$this->db->delete('players');
    	if($this->db->affected_rows()>0){
    		return true;
    	}
    	else{
    		return false;
    	}
    }

    function delete_whitespace(){
        $this->db->where('player_name','');
        $this->db->delete('players');
        return $this->db->affected_rows();
    }
}
