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
class Mgroup_player extends CI_Model {
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    //INSERT or CREATE FUNCTION
    function insert($program, $player_name){
        $this->delete($player_name);
        $this->db->insert('group_players', $program);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    function insert_batch($program,$player_name){
        $group_name='';
        if(count($program)>0){
            $group_name=$program[0]['group_name'];
        }
        $this->delete_by_player_name($player_name);
        $this->db->insert_batch('group_players', $program);
    }

    
    //GET FUNCTION
    function get_by_id($group_players_id){
        $this->db->select('group_players.*');
        $this->db->where('id',$group_players_id);
        $query = $this->db->get('group_players');
        if($query->num_rows()>0){
            $result = $query->result();
            return $result[0];
        }else{
            return false;
        }
    }

    function get_by_group($group_name){
        $this->db->select('group_players.*');
        $this->db->where('group_name',$group_name);
        $this->db->order_by('player_name','asc');
        $query = $this->db->get('group_players');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_all(){
        $this->db->limit(10);
        $query = $this->db->get('group_players');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }
    
    function get_all_distinct($limit,$offset){
        $this->db->distinct('player_name');
        $query = $this->db->get('group_players',$limit,$offset);
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_limit($limit,$offset){
        $query = $this->db->get('group_players',$limit,$offset);
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    
    //UPDATE FUNCTION
    function update($group_players_id, $program){
        $this->db->where('id',$group_players_id);
        return $this->db->update('group_players', $program);
    }
    
    
    //DELETE FUNCTION
    function delete($player_name){
        $this->db->where('player_name',$player_name);
        $this->db->delete('group_players');
        if($this->db->affected_rows()>0){
            return true;
        }
        else{
            return false;
        }
    }

    function delete_by_group_name($group_name){
         }
        else{
            return false;
        }
    }
    
    function delete_by_player_name($player_name){
        $this->db->where_in('player_name', $player_name);
        $this->db->delete('group_players');
        if($this->db->affected_rows()>0){
            return true;
        }
        else{
            return false;
        }
    }

    function delete_whitespace(){
        $this->db->where('player_name','');
        $this->db->delete('group_players');
        return $this->db->affected_rows();
    }
}