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
		$lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 1),
				'order' => 'id desc'
			)
		);
		$this->data['overall_leaderboards'] = (array) json_decode(base64_decode($lb->data));
    }

    public function speed() {
        $this->data['subtitle'] = "Speed Player Rankings";
        $this->content_view = "leaderboards/speed";
		$lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 2),
				'order' => 'id desc'
			)
		);
		$this->data['overall_leaderboards'] = (array) json_decode(base64_decode($lb->data));
    }

    public function jumpstream() {
        $this->data['subtitle'] = "Jumpstream Player Rankings";
        $this->content_view = "leaderboards/jumpstream";
		$lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 3),
				'order' => 'id desc'
			)
		);
		$this->data['overall_leaderboards'] = (array) json_decode(base64_decode($lb->data));
    }

    public function jack() {
        $this->data['subtitle'] = "Jack Player Rankings";
        $this->content_view = "leaderboards/jack";
		$lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 4),
				'order' => 'id desc'
			)
		);
		$this->data['overall_leaderboards'] = (array) json_decode(base64_decode($lb->data));
    }

    public function technical() {
        $this->data['subtitle'] = "Technical Player Rankings";
        $this->content_view = "leaderboards/technical";
		$lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 5),
				'order' => 'id desc'
			)
		);
		$this->data['overall_leaderboards'] = (array) json_decode(base64_decode($lb->data));
    }

    public function stamina() {
        $this->data['subtitle'] = "Stamina Player Rankings";
        $this->content_view = "leaderboards/stamina";
		$lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 6),
				'order' => 'id desc'
			)
		);
		$this->data['overall_leaderboards'] = (array) json_decode(base64_decode($lb->data));
    }

	public function generate_cache() {
		set_time_limit(0);
		$overall_leaderboards = User_score::get_overall_leaderboard();
		$this->data['overall_leaderboards'] = $overall_leaderboards;
		$lc = Leaderboards_cache::create(array('type' => 1, 'data' => base64_encode(json_encode($overall_leaderboards))));
		$speed_leaderboards = User_score::get_speed_leaderboard();
		$lc = Leaderboards_cache::create(array('type' => 2, 'data' => base64_encode(json_encode($speed_leaderboards))));
		$jumpstream_leaderboards = User_score::get_jumpstream_leaderboard();
		$lc = Leaderboards_cache::create(array('type' => 3, 'data' => base64_encode(json_encode($jumpstream_leaderboards))));
		$jack_leaderboards = User_score::get_jack_leaderboard();
		$lc = Leaderboards_cache::create(array('type' => 4, 'data' => base64_encode(json_encode($jack_leaderboards))));
		$technical_leaderboards = User_score::get_technical_leaderboard();
		$lc = Leaderboards_cache::create(array('type' => 5, 'data' => base64_encode(json_encode($technical_leaderboards))));
		$stamina_leaderboards = User_score::get_stamina_leaderboard();
		$lc = Leaderboards_cache::create(array('type' => 6, 'data' => base64_encode(json_encode($stamina_leaderboards))));

        $old_caches = Leaderboards_cache::all(
            'order' => 'id ASC',
            'limit' => 6
        );

        foreach ($old_caches as $oc) {
            $oc->delete();
        }

		$this->content_view = "lc";
	}
}
