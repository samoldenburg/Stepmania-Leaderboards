<?php
/**
 * About Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
        $this->data['subtitle'] = "About";
	}

	public function index() {
        $this->content_view = "about/about";
	}

    public function faq() {
        $this->data['subtitle'] = "Frequently Asked Questions";
        $this->content_view = "about/faq";
    }
}
