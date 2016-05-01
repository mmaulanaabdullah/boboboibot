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
class Mincoming_request extends CI_Model {
    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    //INSERT or CREATE FUNCTION
    function insert($program){
        $this->db->insert('incoming_request', $program);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
    //GET FUNCTION
    function get_limit($id_incoming_request_least, $limit = 100){
        $this->db->select('incoming_request.*');
        $this->db0>where('id >',$id_incoming_request_least);
        $this->db->limit($limit);
        $query = $this->db->get('incoming_request');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_by_id($incoming_request_id){
        $this->db->select('incoming_request.*');
        $this->db->where('id',$incoming_request_id);
        $query = $this->db->get('incoming_request');
        if($query->num_rows()==1){
            $result = $query->result();
            return $result[0];
        }else{
            return false;
        }
    }

    function get_all(){
        $query = $this->db->get('incoming_request');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function get_last_id_incoming_request(){
        $this->db->select('incoming_request.*');
        $this->db->limit(1);
        $this->db->order_by('id','desc');
        $query = $this->db->get('incoming_request');
        if($query->num_rows()==1){
            $result = $query->result();
            return $result[0]->id;
        }else{
            return 1;
        }
    }

    function get_last_id_incoming_request_processed(){
        $this->db->select('incoming_request_processed.*');
        $this->db->limit(1);
        $this->db->order_by('id_incoming_request','desc');
        $query = $this->db->get('incoming_request_processed');
        if($query->num_rows()==1){
            $result = $query->result();
            return $result[0]->id_incoming_request;
        }else{
            return $this->get_last_id_incoming_request();
        }
    }

    function get_last_hundred_transaction($id_incoming_request){
        $this->db->select('incoming_request.*');
        $this->db->where('id >',$id_incoming_request);
        $query = $this->db->get('incoming_request');
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    //UPDATE FUNCTION
    function update($incoming_request_id, $program){
        $this->db->where('id',$incoming_request_id);
        return $this->db->update('incoming_request', $program);
    }
    
    
    //DELETE FUNCTION
    function delete($id){
    	$this->db->where('id',$id);
    	$this->db->delete('incoming_request');
    	if($this->db->affected_rows()>0){
    		return true;
    	}
    	else{
    		return false;
    	}
    }

}

