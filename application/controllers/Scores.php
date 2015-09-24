<?php
/**
 * Submit scores Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Scores extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
        $this->data['title'] = "Submit Score";
	}

	public function index() {
        redirect('scores/submit');
	}

    public function submit($song_id = null) {
		$this->require_login();
        if (!($song_id)) {
            $this->content_view = "scores/submit";
            $this->data['songs'] = Ranked_file::get_all_charts();
            $this->data['packs'] = Pack::all(array('order' => 'name asc'));
        } else {
			if (Ranked_file::count(array('conditions' => array('id = ?', $song_id))) != 1)
				redirect('scores/submit');
			else {
				if ($_POST) {
					$song = Ranked_file::find($song_id);
					$this->data['song'] = $song;
					$total_notes = $this->input->post('marvelous_count')
						+ $this->input->post('perfect_count')
						+ $this->input->post('great_count')
						+ $this->input->post('good_count')
						+ $this->input->post('boo_count')
						+ $this->input->post('miss_count');
					$total_holds = $this->input->post('ok_count');
					if ($total_notes > $song->taps || $total_holds > $song->holds) {
						$this->content_view = "scores/confirm";
						$this->data['error_nc'] = true;
					} elseif ($this->form_validation->run('submit_score') == FALSE) {
			            $this->content_view = "scores/confirm";
						$this->data['error'] = true;
					} else {
			            $attributes = array(
							'user_id' => $this->session->userdata('user_id'),
							'file_id' => $song_id,
							'date_achieved' => $this->input->post('score_achieved'),
							'marvelous_count' => $this->input->post('marvelous_count'),
							'perfect_count' => $this->input->post('perfect_count'),
							'great_count' => $this->input->post('great_count'),
							'good_count' => $this->input->post('good_count'),
							'boo_count' => $this->input->post('boo_count'),
							'miss_count' => $this->input->post('miss_count'),
							'ok_count' => $this->input->post('ok_count'),
							'mines_hit' => $this->input->post('mines_hit'),
							'screenshot_url' => $this->input->post('screenshot_url')
						);
						$new_score = new User_score($attributes);
						$new_score->save();



						// Check if score is below AA
						$max_dp_max = $song->dance_points;
		                $max_dp_achieved = ($new_score->marvelous_count * 2) + ($new_score->perfect_count * 2) + ($new_score->great_count * 1) + ($new_score->boo_count * -4) + ($new_score->miss_count * -8) + ($new_score->ok_count * 6) + ($new_score->mines_hit * -8);
		                $max_dp_percent = ($max_dp_achieved / $max_dp_max) * 100;

		                if ($max_dp_percent < 93) {
							$new_score->status = "below_aa";
							$new_score->save();
						}

						// Check if score is in top 10% of all scores
						$this->data['score_pending'] = false;
						$percentile = User_score::get_top_10_percentile();
						if ($song->difficulty_score > $percentile) {
							$new_score->status = "pending";
							$new_score->was_pending = 1;
							$new_score->save();
							$this->data['score_pending'] = true;
						}

			            $this->content_view = "scores/success";
					}
		        } else {
		            $this->data['song'] = Ranked_file::find($song_id);
		            $this->content_view = "scores/confirm";
					$this->data['error'] = false;
				}
			}
        }
    }

	public function edit($score_id = null) {
		$this->require_login();
        if (!($score_id)) {
			redirect('home');
        } else {
			if (User_score::count(array('conditions' => array('id = ?', $score_id))) != 1)
				redirect('home');
			else {
				$user_score = User_score::find($score_id);
				$write_mod_log = false;

				if ($user_score->user_id != $this->session->userdata('user_id')) {
					if ($this->session->userdata('user_level') < 2) {
						redirect('home');
					} else {
						$write_mod_log = true;
					}
				}

				$this->data['user_score'] = $user_score;
				$song = Ranked_file::find($user_score->file_id);
				$this->data['song'] = $song;
				if ($_POST) {
					$total_notes = $this->input->post('marvelous_count')
						+ $this->input->post('perfect_count')
						+ $this->input->post('great_count')
						+ $this->input->post('good_count')
						+ $this->input->post('boo_count')
						+ $this->input->post('miss_count');
					$total_holds = $this->input->post('ok_count');
					if ($total_notes > $song->taps || $total_holds > $song->holds) {
						$this->content_view = "scores/edit";
						$this->data['error_nc'] = true;
					} elseif ($this->form_validation->run('submit_score') == FALSE) {
			            $this->content_view = "scores/edit";
						$this->data['error'] = true;
					} else {
						$user_score->file_id = $song->id;
						$user_score->date_achieved = $this->input->post('score_achieved');
						$user_score->marvelous_count = $this->input->post('marvelous_count');
						$user_score->perfect_count = $this->input->post('perfect_count');
						$user_score->great_count = $this->input->post('great_count');
						$user_score->good_count = $this->input->post('good_count');
						$user_score->boo_count = $this->input->post('boo_count');
						$user_score->miss_count = $this->input->post('miss_count');
						$user_score->ok_count = $this->input->post('ok_count');
						$user_score->mines_hit = $this->input->post('mines_hit');
						$user_score->screenshot_url = $this->input->post('screenshot_url');

						// Check if score is below AA
						$max_dp_max = $song->dance_points;
		                $max_dp_achieved = ($user_score->marvelous_count * 2) + ($user_score->perfect_count * 2) + ($user_score->great_count * 1) + ($user_score->boo_count * -4) + ($user_score->miss_count * -8) + ($user_score->ok_count * 6) + ($user_score->mines_hit * -8);
		                $max_dp_percent = ($max_dp_achieved / $max_dp_max) * 100;

		                if ($max_dp_percent < 93) {
							$user_score->status = "below_aa";
						}

						$user_score->save();

						if ($write_mod_log) {
							$log_string = $this->session->userdata('username') .
								" (" . $this->session->userdata('display_name') . ") removed user score: " .
								$scores_user->username . " (" . $scores_user->display_name . ") " .
								$song->title . " " . number_format($song->rate, 2) . "x";
							write_to_mod_log($log_string);
						}

			            $this->content_view = "scores/edit_success";
					}
		        } else {
		            $this->content_view = "scores/edit";
					$this->data['error'] = false;
				}
			}
        }
    }

	public function remove($score_id = null) {
		$this->require_login();
        if (!($score_id)) {
			redirect('home');
        } else {
			if (User_score::count(array('conditions' => array('id = ?', $score_id))) != 1)
				redirect('home');
			else {
				$user_score = User_score::find($score_id);
				$write_mod_log = false;

				if ($user_score->user_id != $this->session->userdata('user_id')) {
					if ($this->session->userdata('user_level') < 2) {
						redirect('home');
					} else {
						$write_mod_log = true;
					}
				}

				$this->data['user_score'] = $user_score;
				$song = Ranked_file::find($user_score->file_id);
				$this->data['song'] = $song;
				$user_score->status = "removed";
				$user_score->save();

				$scores_user = User::find($user_score->user_id);

				if ($write_mod_log) {
					$log_string = $this->session->userdata('username') .
						" (" . $this->session->userdata('display_name') . ") removed user score: " .
						$scores_user->username . " (" . $scores_user->display_name . ") " .
						$song->title . " " . number_format($song->rate, 2) . "x";
					write_to_mod_log($log_string);
				}

				$this->content_view = "scores/removed";
			}
        }
    }
}
