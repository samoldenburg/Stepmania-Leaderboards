<?php
/**
 * Register Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
        $this->data['subtitle'] = "Register";
	}

	public function index(){
		$this->data['error'] = false;
        if ($_POST) {

			if ($this->form_validation->run('register') == FALSE) {
	        	$this->content_view = 'register';
				$this->data['error'] = true;
			} else {
	            $username = $this->input->post('username');
	            $password = $this->input->post('pass');
	            $password_confirm = $this->input->post('confirm_pass');
	            $email = $this->input->post('email');
	            $display_name = $this->input->post('display_name');

	            User::register($username, $password, $email, $display_name);
	            $this->content_view = 'register_success';
			}
        }
		else {
        	$this->content_view = 'register';
		}
	}
}
