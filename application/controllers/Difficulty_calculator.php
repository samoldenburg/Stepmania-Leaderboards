<?php
/**
 * Difficulty calculator Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Difficulty_calculator extends MY_Controller {
    function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
        $this->data['subtitle'] = "SM File Parser";
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
	}

    public function index() {
        if ($_POST) {
            if ($this->form_validation->run('parser') == FALSE) {
                $this->content_view = "parser/difficulty_calculator";
				$this->data['error'] = true;
            } else {
                // Main parser logic here
        		// Run validation trim leading and trailing whitespace
        		$this->_process_everything();
            }
        } else {
            $this->data['error'] = false;
            $this->content_view = "parser/difficulty_calculator";
        }
    }
}
