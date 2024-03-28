<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_global extends CI_Model {


	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function get_all($table){
		$res=$this->db->get($table)->result();
		return $res;
	}

	private function _get_datatables_query($table,$column_order,$column_search,$order)
	{
		$this->db->from($table);

		$i = 0;

		foreach ($column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
				}
				$i++;
			}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($order))
		{
			$order = $order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($table,$column_order,$column_search,$order)
	{
		$this->_get_datatables_query($table,$column_order,$column_search,$order);
		if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($table,$column_order,$column_search,$order)
	{
		$this->_get_datatables_query($table,$column_order,$column_search,$order);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($table)
	{
		$this->db->from($table);
		return $this->db->count_all_results();
	}

	public function get_where($where,$table){
		$this->db->where($where);
		$this->db->from($table);
		$query = $this->db->get();
		return $query;
	}

	public function get_by_id($id,$table)
	{
		$this->db->from($table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data,$table)
	{
		$res=$this->db->insert($table, $data);
		return $res; 
	}

	public function update($where, $data,$table)
	{
		$this->db->update($table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id,$table)
	{
		$this->db->where('id', $id);
		$this->db->delete($table);
		return true;
	}


}