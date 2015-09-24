<?php
/**
 * Logout Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
        $this->data['subtitle'] = "Login";
	}

	public function index(){
        $this->session->sess_destroy();
        redirect('home');
	}
}
