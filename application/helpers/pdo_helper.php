<?php
function is_logged_in()
{
	$ci = get_instance();
	// function ini di gunakan untuk meng instance siasi agar 
	// is_logged_in di kenali di semua controller
	if(!$ci->session->userdata('email')) {
		redirect('auth');
	} else {
		$role_id = $ci->session->userdata('role_id');
		$menu = $ci->uri->segment(1);

		// query menu
		$queryMenu = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
		$menu_id = $queryMenu['id'];

		// query user access
		$userAccess = $ci->db->get_where('user_access_menu', 
				['role_id' => $role_id,
				'menu_id' => $menu_id]
			);

			if($userAccess->num_rows() < 1 ) {
				redirect('auth/blocked');
			}
	}
}
// function check_access di gunakan halaman role_access
// function ini di gunakan untuk mengececk role access
function check_access($role_id, $menu_id) {
// instance siasi terlebih dahulu agar function di kenali di halaman view
	$ci = get_instance();
	// lakukan query untuk mencari data dari tabel user-access_menu
	$ci->db->where('role_id', $role_id);
	$ci->db->where('menu_id', $menu_id);
	$result = $ci->db->get('user_access_menu');

	if($result->num_rows() > 0 ) {
		return "checked='checked' ";
	}




}
?>
