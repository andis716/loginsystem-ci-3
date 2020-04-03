<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	// method default yang akan selalu di jalankan
	public function __construct()
	{		// method validation
		parent::__construct();
		$this->load->library('form_validation');
	}
	// method login
	public function index(){

		// Buat set rules nya 
		$this->form_validation->set_rules('email','Email','required|trim|valid_email');
		$this->form_validation->set_rules('password','Password','required|trim');
		// validasi
		if ($this->form_validation->run() == false ) {
			$data['title'] = 'Login page';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/login');
			$this->load->view('templates/auth_footer');
		} else { // jika validasi berhasil
			$this->_login(); // buat method login private
		} // method ini di alihkan ke method private _login
	}
	// membuat method login dengan private function
	private function _login() {
		// yang hanya bisa di eksekusi oleh class method ini sendiri
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		// query databasse
		$user = $this->db->get_where('user',['email' => $email])->row_array();
		// cek apakah usernya ?
		if($user) { // jika usernya ada
			// jika usernya activ
				if($user['is_active'] == 1) {
						// cek password
						if(password_verify($password,$user['password'])) {
								$data = ['email' => $user['email'],'role_id' => $user['role_id']];
								$this->session->set_userdata($data);
								// arahkan berdasarkan role_id
								if($user['role_id'] == 1) {
									redirect('admin');
								} else {
									redirect('user');
								}
							
						} else {
								$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
								Wrong password  !</div>');
								redirect('auth');
						}

				} else {
						$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
						This email has not been activated !</div>');
						redirect('auth');
				}

		} else {
			// tampilkan pesan jika user tidak ada di dalam database
			$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
			Email is not registered !</div>');
			redirect('auth');
		}
	}

	public function registration() {
		// method registration
		// Buat set rules nya
		$this->form_validation->set_rules('name','Name','required|trim|is_unique[user.name]',
																			['is_unique' =>'This name has already registered']);
		$this->form_validation->set_rules('email','Email','required|trim|valid_email|is_unique[user.email]',
																			['is_unique' =>'This email has already registered']);
		$this->form_validation->set_rules('password1','Password','required|trim|min_length[4]|matches[password2]',
																			['matches' =>'password dont match!','min_length' => 'password too short!']);
		$this->form_validation->set_rules('password2','Password','required|trim|matches[password1]');
		// jika registrasi gagal maka tampilkan
		if($this->form_validation->run() == false ) {
			$data['title'] = 'PDO Registration';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/registration');
			$this->load->view('templates/auth_footer');
			// jika registrasi berhasil maka alihkan ke halaman login
		} else {
			$email = $this->input->post('email', true);
			$data = [
				'name' => htmlspecialchars($this->input->post('name', true)),
				'email' => htmlspecialchars($email),
				'image' => 'default.jpg',
				'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
				'role_id' => 2,
				'is_active' => 0,
				'date_created' => time()
			]; 	// masukan ke dalam database user
			// siapkan token untuk pengiriman email
			$token = base64_encode (random_bytes(32));
			// siapkan user token
			$user_token = [
				'email' => $email,
				'token' => $token,
				'date_created' => time()
			];

			$this->db->insert('user', $data);
			$this->db->insert('user_token', $user_token);
			// send email
			$this->_sendEmail($token, 'verify'); // di buat dengan function private di bawah method ini
			// tampilkan pesan berhasil di halaman login
			$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">
			Your acount has been created. Please Activate your account !</div>');
			redirect('auth');
		}
	}

	private function _sendEmail( $token, $type ) {
		$config = [
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_user' => 'facs.suport@gmail.com',
			'smtp_pass' => 'andis.costom',
			'smtp_port' => 465,
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'newline' => "\r\n"
		];
		// $this->load->library('email', $config);
		// $this->email->set_newline("\r\n");
		// panggil library dari codeigniternya
		$this->email->initialize($config);
		// siapkan email
		if($type == 'verify') {
			
			$this->email->from('facs.suport@gmail.com', 'PDO_Design');
			$this->email->to($this->input->post('email'));
			$this->email->subject('Account verification');
			$this->email->message('Click this link to verify your account :
				<a href="'. base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token='
				. urlencode($token) . '">Active</a>'); // script ini untuk email verify		
		} else if ($type == 'forgot') {
			
			$this->email->from('facs.suport@gmail.com', 'PDO_Design');
			$this->email->to($this->input->post('email'));
			$this->email->subject('Reset Password');
			$this->email->message('Click this link to reset your password :
				<a href="'. base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token='
				. urlencode($token) . '">Reset</a>'); // script ini untuk email verify
		}

		if($this->email->send()) {
			return true;
		} else {
			echo $this->email->print_debugger();
			die;
		}
		// setelah user berhasil register ddan di kirimi emai veritification
		// maka user langsung di masukan kedalam database ( skrip pada no 97 )
	}

	
	public function verify() {
		$email = $this->input->get('email');
		$token = $this->input->get('token');

		$user = $this->db->get_where('user', ['email' => $email])->row_array();
		// cek apakah ada usernya
		if($user) {
			$user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
			
			if($user_token) { // tentukan waktu
				if(time() - $user_token['date_created'] < (60 * 60 * 24)) {
					// jika email kurang dari 1 hari maka user masih bisa activation
					$this->db->set('is_active', 1);
					$this->db->where('email', $email);
					$this->db->update('user');
					
					$this->db->delete('user_token', ['email' => $email]);
					
						$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">
						'. $email . ' has been Activated! Please Login.</div>');
						redirect('auth');
				} else {
					
					$this->db->delete('user', ['email' => $email]);
					$this->db->delete('user_token', ['email' => $email]);
					
					$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
					Account activation failed ! expired.</div>');
					redirect('auth');
				}
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
				Account activation failed ! Token Invalid.</div>');
				redirect('auth');
			}
		} else {
			$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
			Account activation failed ! Wrong email.</div>');
			redirect('auth');
		}
	} 
	
	public function logout() { // mrthod logout
		// membersikan session sekaligus mengembalikan ke halaman login
		$this->session->unset_userdata('email');
		$this->session->unset_userdata('role_id');
		// tampilkan pesan logout berhasil di halaman login
		$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">
		You have been logout !</div>');
		redirect('auth');
	}

	public function blocked() {
		$this->load->view('auth/blocked');
	}

	public function forgotpassword() {

		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

		if($this->form_validation->run() == false ) {
			
			$data['title'] = 'Forgot Password';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/forgot-password');
			$this->load->view('templates/auth_footer');
		} else {
			$email = $this->input->post('email');
			$user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])
			->row_array();

			if($user) {
				$token = base64_encode(random_bytes(32));
				$user_token = [
					'email' => $email,
					'token' => $token,
					'date_created' =>time()
				];
				$this->db->insert('user_token', $user_token);
				$this->_sendEmail($token, 'forgot');

					$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">
					Please check your email to reset your password !</div>');
					redirect('auth/forgotpassword');
			} else {
				
				$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
				Email is not registered or activated !</div>');
				redirect('auth/forgotpassword');
			}
		}
	}

	public function resetPassword() {
		$email= $this->input->get('email') ;
		$token = $this->input->get('token') ;
		
		$user = $this->db->get_where('user', ['email' => $email])->row_array();
		
		if($user) {
			
			$user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
			if($user_token) {
				
				$this->session->set_userdata('reset_email', $email);
				$this->changePassword();
			} else {
				
				$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
				Reset password failed ! wrong token.</div>');
				redirect('auth');
			}
		} else {
			
			$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">
			Reset password failed ! wrong email.</div>');
			redirect('auth');
		}
	}

	public function changePassword()
	{
		if(!$this->session->userdata('reset_email')) {
			redirect('auth');
		}
		$this->form_validation->set_rules('password1','Password',
		'required|trim|min_length[4]|matches[password2]');
		$this->form_validation->set_rules('password2','Confirm password',
		'required|trim|matches[password1]');

		if($this->form_validation->run() == false) {

		$data['title'] = 'Change Password';
		$this->load->view('templates/auth_header', $data);
		$this->load->view('auth/change-password');
		$this->load->view('templates/auth_footer');
		} else {
				$password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
				$email = $this->session->userdata('reset_email');

				$this->db->set('password', $password);
				$this->db->where('email', $email);
				$this->db->update('user');
				// hapus session
				$this->session->unset_userdata('reset_email');

			$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">
			Password has been changed! Please login.</div>');
			redirect('auth');
		}
		
	}
	
	
}