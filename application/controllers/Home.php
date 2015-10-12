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

		$this->load->helper('twitch');
		load_api_interface();

		$result = json_decode(file_get_contents("https://api.twitch.tv/kraken/streams?game=StepMania&limit=4"));

		$this->data['streams'] = $result->streams;
	}
}
