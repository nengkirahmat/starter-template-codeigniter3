<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Satuan extends CI_Controller
{
	var $table = 'satuan';
	var $column_order = array('kode', 'name', null); //set column field database for datatable orderable
	var $column_search = array('kode', 'name'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_global', 'global');
	}

	public function index()
	{
		is_admin();
		$this->load->view('template/header');
		$this->load->view('satuan/index');
		$this->load->view('template/footer');
	}

	public function ajax_table()
	{
		is_admin();
		$list = $this->global->get_datatables($this->table, $this->column_order, $this->column_search, $this->order);
		$response = array();
		$no = $_POST['start'];
		foreach ($list as $r) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $r->id;
			$row[] = $r->kode;
			$row[] = $r->name;
			$row[] = '<a class="btn btn-sm btn-warning edit" href="javascript:void(0)" title="Edit" data-id="' . $r->id . '"><i class="bi bi-pencil-square"></i> </a> <a class="btn btn-sm btn-danger delete" href="javascript:void(0)" title="Hapus" data-id="' . $r->id . '"><i class="bi bi-trash"></i> </a>';
			$response[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->global->count_all($this->table),
			"recordsFiltered" => $this->global->count_filtered($this->table, $this->column_order, $this->column_search, $this->order),
			"data" => $response,
		);
		//output to json format
		echo json_encode($output);
	}

	public function store()
	{
		is_admin();

		$this->form_validation->set_rules('nama', 'Nama', 'required');

		if ($this->form_validation->run() == FALSE) {
			$response = array(
				'success' => false,
				'message' => validation_errors()
			);
			echo json_encode($response);
			exit;
		}

		$cek = $this->global->get_where(array('name' => $this->input->post('nama')), $this->table);
		if ($cek->num_rows() > 0) {
			echo json_encode(array('success' => false, 'message' => 'Nama satuan sudah ada'));
			exit;
		}

		$data = array(
			'name' => $this->input->post('nama'),
			'kode' => $this->input->post('kode'),
		);
		$res = $this->global->save($data, $this->table);
		if ($res) {
			echo json_encode(array('success' => true, 'message' => 'Simpan Berhasil'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Simpan Gagal'));
		}
	}

	public function update()
	{
		is_admin();
		$this->form_validation->set_rules('nama', 'Nama', 'required');

		if ($this->form_validation->run() == FALSE) {
			$response = array(
				'success' => false,
				'message' => validation_errors()
			);
			echo json_encode($response);
			exit;
		}
		$id = $this->input->post('id');
		$where = array('name' => $this->input->post('nama'));
		if ($id != null) {
			$where['id !='] = $id;
		}
		$digunakan = $this->global->get_where($where, $this->table)->num_rows();
		if ($digunakan > 0) {
			$response = array(
				'success' => false,
				'message' => 'Username sudah digunakan'
			);
			echo json_encode($response);
			exit;
		}

		$data = array(
			'name' => $this->input->post('nama'),
			'kode' => $this->input->post('kode'),
		);
		$res = $this->global->update(array('id' => $id), $data, $this->table);
		if ($res) {
			echo json_encode(array('success' => true, 'message' => 'Update Berhasil'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Update Gagal'));
		}
	}

	public function get_by_id($id)
	{
		$data = $this->global->get_by_id($id, $this->table);
		echo json_encode($data);
	}

	public function delete($id)
	{
		is_admin();
		$hapus = $this->global->delete_by_id($id, $this->table);
		if ($hapus) {
			echo json_encode(array("success" => true, "message" => "Hapus data Berhasil"));
		} else {
			echo json_encode(array("success" => false, "message" => "Hapus data Gagal"));
		}
	}

}
