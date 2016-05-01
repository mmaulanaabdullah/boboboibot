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
class Mincoming_request_processed extends CI_Model {
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    //INSERT or CREATE FUNCTION
    function insert($program){
        $this->db->insert('incoming_request_processed', $program);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
    //GET FUNCTION
    function get_by_id($incoming_request_processed_id){
        $this->db->select('incoming_request_processed.*');
        $this->db->where('id',$incoming_request_processed_id);
        $query = $this->db->get('incoming_request_processed');
        if($query->num_rows()>0){
            $result = $query->result();
            return $result[0];
        }else{
            return false;
        }
    }

    function get_all(){
        $query = $this->db->get('incoming_request_processed');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }
    
    //UPDATE FUNCTION
    function update($incoming_request_processed_id, $program){
        $this->db->where('id',$incoming_request_processed_id);
        return $this->db->update('incoming_request_processed', $program);
    }
    
    
    //DELETE FUNCTION
    function delete($id){
    	$this->db->where('id',$id);
    	$this->db->delete('incoming_request_processed');
    	if($this->db->affected_rows()>0){
    		return true;
    	}
    	else{
    		return false;
    	}
    }

}

