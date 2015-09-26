<?php
/**
 * Index & Homepage Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
	}

	public function index(){
        $this->content_view = 'home';
		$this->data['announcements'] = Announcement::get_announcements();
		$this->data['overall_leaderboards'] = User_score::get_overall_leaderboard();

		#$this->load->helper('twitch');
		#load_api_interface();

		#$interface = new twitch;
		#$streams = $interface->getStreamsObjects('StepMania');
		#$this->data['streams'] = $streams;
	}
}
