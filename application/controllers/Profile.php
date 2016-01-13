<?php
/**
 * Edit Profile Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
	}

	public function index() {
		redirect('home');
	}

    public function edit() {
		$this->require_login();
        $this->data['subtitle'] = "Edit Profile";
        $this->data['user'] = User::find($this->session->userdata('user_id'));
        if ($_POST) {

			if ($this->form_validation->run('edit_profile') == FALSE) {
	        	$this->content_view = 'profiles/edit';
				$this->data['error'] = true;
			} else {
				$pass = $this->input->post('pass');
                if (!empty($pass))
	                $this->data['user']->password = crypt($this->input->post('pass') . $this->data['user']->salt);

                $this->data['user']->email = $this->input->post('email');
                $this->data['user']->display_name = $this->input->post('display_name');
                $this->data['user']->save();

				$this->session->set_userdata('password', $this->data['user']->password);
                $this->session->set_userdata('email', $this->data['user']->email);
                $this->session->set_userdata('display_name', $this->data['user']->display_name);

	            $this->content_view = 'profiles/edit_success';
			}
        }
		else {
            $this->content_view = "profiles/edit";
        }
    }

    public function view($username) {
		$username = urldecode($username);
        if (empty($username) || User::count(array('conditions' => array('username = ?', $username))) != 1)
            redirect('home');
        $this_user = User::find_by_username($username);
        $this->data['user'] = $this_user;
        $this->data['subtitle'] = $this_user->display_name;
		$this->data['scores'] = User_score::get_scores_for_user($this_user->id);
		if (isset($_GET['recalc'])) {
			error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
			set_time_limit(0);

			foreach ($this->data['scores'] as $score) {
				$chart = Ranked_file::find($score->file_id);
				$calculated_difficulty = $this->_process_everything($chart->raw_file, $chart->rate);
				$score->difficulty_score = $calculated_difficulty;
			}
		}
		$approved_scores = User_score::get_scores_for_user_approved($this_user->id, "difficulty_score DESC");
		$overall_leaderboard = User_score::get_overall_leaderboard();
		$speed_leaderboard = User_score::get_speed_leaderboard();
		$jumpstream_leaderboard = User_score::get_jumpstream_leaderboard();
		$jack_leaderboard = User_score::get_jack_leaderboard();
		$technical_leaderboard = User_score::get_technical_leaderboard();
		$stamina_leaderboard = User_score::get_stamina_leaderboard();

		$i = 1;
		foreach ($overall_leaderboard as $row) {
			if ($row['username'] == $this_user->display_name) {
				$this->data['overall_rank'] = $i;
				$this->data['overall_score'] = $row['average_score'];
			}
			$i++;
		}

		$i = 1;
		foreach ($speed_leaderboard as $row) {
			if ($row['username'] == $this_user->display_name) {
				$this->data['speed_rank'] = $i;
				$this->data['speed_score'] = $row['average_score'];
			}
			$i++;
		}

		$i = 1;
		foreach ($jumpstream_leaderboard as $row) {
			if ($row['username'] == $this_user->display_name) {
				$this->data['jumpstream_rank'] = $i;
				$this->data['jumpstream_score'] = $row['average_score'];
			}
			$i++;
		}

		$i = 1;
		foreach ($jack_leaderboard as $row) {
			if ($row['username'] == $this_user->display_name) {
				$this->data['jack_rank'] = $i;
				$this->data['jack_score'] = $row['average_score'];
			}
			$i++;
		}

		$i = 1;
		foreach ($technical_leaderboard as $row) {
			if ($row['username'] == $this_user->display_name) {
				$this->data['technical_rank'] = $i;
				$this->data['technical_score'] = $row['average_score'];
			}
			$i++;
		}

		$i = 1;
		foreach ($stamina_leaderboard as $row) {
			if ($row['username'] == $this_user->display_name) {
				$this->data['stamina_rank'] = $i;
				$this->data['stamina_score'] = $row['average_score'];
			}
			$i++;
		}

		foreach ($approved_scores as $score) {
			$this->data['top_score'] = $score->difficulty_score;
			break;
		}

		$categories_required = 1;
		$individual_required = 1;
		if ($this->data['top_score'] < 15) {
			$categories_required = 1;
			$individual_required = 1;
		}
		else if ($this->data['top_score'] < 20) {
			$categories_required = 2;
			$individual_required = 1;
		}
		else if ($this->data['top_score'] < 24) {
			$categories_required = 2;
			$individual_required = 2;
		}
		else if ($this->data['top_score'] < 28) {
			$categories_required = 3;
			$individual_required = 2;
		}
		else if ($this->data['top_score'] < 31) {
			$categories_required = 3;
			$individual_required = 3;
		}
		else {
			$categories_required = 3;
			$individual_required = 5;
		}

		$this->data['categories_required'] = $categories_required;
		$this->data['individual_required'] = $individual_required;



        $this->content_view = "profiles/view";
    }

}
