<?php
/**
 * Login Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaderboards extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
	}

	public function index() {
        redirect("leaderboards/overall");
	}

    public function overall() {
        $this->data['subtitle'] = "Overall Rankings";
        $this->content_view = "leaderboards/overall";
        $this->data['overall_leaderboards'] = User_score::get_overall_leaderboard();
    }

    public function speed() {
        $this->data['subtitle'] = "Speed Player Rankings";
        $this->content_view = "leaderboards/speed";
		$this->data['overall_leaderboards'] = User_score::get_speed_leaderboard();
    }

    public function jumpstream() {
        $this->data['subtitle'] = "Jumpstream Player Rankings";
        $this->content_view = "leaderboards/jumpstream";
		$this->data['overall_leaderboards'] = User_score::get_jumpstream_leaderboard();
    }

    public function jack() {
        $this->data['subtitle'] = "Jack Player Rankings";
        $this->content_view = "leaderboards/jack";
		$this->data['overall_leaderboards'] = User_score::get_jack_leaderboard();
    }

    public function technical() {
        $this->data['subtitle'] = "Technical Player Rankings";
        $this->content_view = "leaderboards/technical";
		$this->data['overall_leaderboards'] = User_score::get_technical_leaderboard();
    }

    public function stamina() {
        $this->data['subtitle'] = "Stamina Player Rankings";
        $this->content_view = "leaderboards/stamina";
		$this->data['overall_leaderboards'] = User_score::get_stamina_leaderboard();
    }
}
