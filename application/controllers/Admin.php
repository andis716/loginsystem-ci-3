<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		// method ini di gunakan apakah user memiliki session
		// untuk mencegah siapapun masuk ke halaman menggunakan url
		parent::__construct();
		is_logged_in();

	}
	

	public function index() {
		
		$data['title'] = 'Dashboard';
		$data['user'] = $this->db->get_where('user',
		['email' => $this->session->userdata('email')])->row_array();
		// untuk menampilkan nama user yang login
		// echo 'Selamat datang ' . $data['user']['name'];
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('admin/index', $data);
		$this->load->view('templates/footer');

	}

	public function role() {
		
		$data['title'] = 'Role';
		$data['user'] = $this->db->get_where('user',
		['email' => $this->session->userdata('email')])->row_array();
		// query role
		$data['role'] = $this->db->get('user_role')->result_array();
		// untuk menampilkan nama user yang login
		// echo 'Selamat datang ' . $data['user']['name'];
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('admin/role', $data);
		$this->load->view('templates/footer');

	}

	public function roleAccess($role_id) {
		
		$data['title'] = 'Role Access';
		$data['user'] = $this->db->get_where('user',
		['email' => $this->session->userdata('email')])->row_array();
		// query role dan kirimkan role_id
		$data['role'] = $this->db->get_where('user_role', ['id' => $role_id ])->row_array();
		// ambil data menu kecuali admin
		$this->db->where('id !=', 1);
		$data['menu'] = $this->db->get('user_menu')->result_array();
		// untuk menampilkan nama user yang login
		// echo 'Selamat datang ' . $data['user']['name'];
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('admin/role-access', $data);
		$this->load->view('templates/footer');

	}

	public function changeAccess()
	{
		$menu_id = $this->input->post('menuId');
		$role_id = $this->input->post('roleId');

		// siapkan data untuk dimasukan ke querynya
		$data = [
			'role_id' => $role_id,
			'menu_id' => $menu_id
		];
		// query berdasarkan data
		$result = $this->db->get_where('user_access_menu', $data);
		// cek apakah data ada atau tidak ada
		if ($result->num_rows() < 1 ) {
			$this->db->insert('user_access_menu', $data);
		} else {
			$this->db->delete('user_access_menu', $data);
		}
		$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">
		Access Changed !</div>');
	}



}
