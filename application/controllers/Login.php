<?php
/**
 * Login Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
        $this->data['subtitle'] = "Login";
	}

	public function index(){
        if ($_POST) {
            $username = $this->input->post('login_username');
            $password = $this->input->post('login_pass');

            $valid_login = User::validate_login($username, $password);

            if ($valid_login) {
                $this_user = User::find_by_username($username);
				$user_level = Usermeta::get_user_level($this_user->id);
				$session_data = array(
					'user_id' 		=> $this_user->id,
					'username' 		=> $this_user->username,
					'password' 		=> $this_user->password,
					'email'			=> $this_user->email,
                    'display_name'  => $this_user->display_name,
					'user_level'	=> $user_level,
					'chat_color'	=> get_chat_color(intval($user_level)),
					'redirect'		=> $this->session->userdata('redirect')
				);
				$this->session->set_userdata($session_data);
				redirect($this->session->userdata('redirect'));
            } else {
	        	$this->content_view = 'login_error';
            }
        }
		else {
        	redirect('home');
		}
	}
}
