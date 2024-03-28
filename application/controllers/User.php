<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
	var $table = 'user';
	var $column_order = array('name', 'hp', 'email', 'username', 'level', 'created_at', null); //set column field database for datatable orderable
	var $column_search = array('name', 'hp', 'email', 'username', 'level', 'created_at'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('created_at' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_global', 'global');
	}

	public function index()
	{
		is_admin();
		$this->load->view('template/header');
		$this->load->view('user/index');
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
			$row[] = $r->name;
			$row[] = $r->hp;
			$row[] = $r->email;
			$row[] = $r->username;
			$level = '';
			if ($r->level == 1) {
				$level = 'Super Admin';
			} elseif ($r->level == 2) {
				$level = 'Admin';
			} elseif ($r->level == 3) {
				$level = 'Gudang';
			}
			$row[] = $level;
			$row[] = $r->created_at;
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
		$this->form_validation->set_rules('hp', 'No. Hp', 'required|numeric');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('level', 'Level', 'required|numeric');
		// $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');

		if ($this->form_validation->run() == FALSE) {
			$response = array(
				'success' => false,
				'message' => validation_errors()
			);
			echo json_encode($response);
			exit;
		}

		$cek = $this->global->get_where(array('username' => $this->input->post('username')), $this->table);
		if ($cek->num_rows() > 0) {
			echo json_encode(array('success' => false, 'message' => 'Username sudah terdaftar'));
			exit;
		}

		$data = array(
			'name' => $this->input->post('nama'),
			'hp' => $this->input->post('hp'),
			'email' => $this->input->post('email'),
			'username' => $this->input->post('username'),
			'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
			'level' => $this->input->post('level'),
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
		$this->form_validation->set_rules('hp', 'No. Hp', 'required|numeric');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('level', 'Level', 'required|numeric');
		// $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

		if ($this->form_validation->run() == FALSE) {
			$response = array(
				'success' => false,
				'message' => validation_errors()
			);
			echo json_encode($response);
			exit;
		}
		$id = $this->input->post('id');
		$where = array('username' => $this->input->post('username'));
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

		$cek = $this->global->get_by_id($id, $this->table);
		if ($this->input->post('password')) {
			$password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
		} else {
			$password = $cek->password;
		}
		$data = array(
			'name' => $this->input->post('nama'),
			'hp' => $this->input->post('hp'),
			'email' => $this->input->post('email'),
			'username' => $this->input->post('username'),
			'password' => $password,
			'level' => $this->input->post('level'),
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

	public function login()
	{
		if ($this->session->userdata('id')) {
			$this->session->set_flashdata('success', 'Kamu sudah login');
			redirect(base_url());
			exit;
		} else {
			$this->load->view('user/login');
		}
	}

	public function proses_login()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect(base_url('user/login'));
			exit;
		}
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$user = $this->global->get_where(array('username' => $username), $this->table)->row();
		if ($user) {
			if (password_verify($password, $user->password)) {
				$this->session->set_userdata('id', $user->id);
				$this->session->set_userdata('name', $user->name);
				$this->session->set_userdata('username', $user->username);
				$this->session->set_userdata('level', $user->level);
				$this->session->set_flashdata('success', 'Login Berhasil');
				redirect(base_url());
				exit;
			} else {
				$this->session->set_flashdata('error', 'Password salah');
				redirect(base_url('user/login'));
				exit;
			}
		}
		$this->session->set_flashdata('error', 'Pengguna tidak ditemukan');
		redirect(base_url('user/login'));
		exit;
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->session->set_flashdata('success', 'Logout Berhasil');
		redirect(base_url('user/login'));
		exit;
	}
}
