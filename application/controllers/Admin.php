<?php
/**
 * Admin base controller.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {
    function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
        $this->data['title'] = "Admin Control Panel";
        $this->require_admin();
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
	}

    function index() {
        // For simplicity sake we'll make people use the nav up top
        redirect('home');
    }

    function batch_recalculate() {
        $this->data['subtitle'] = "Batch Recalculate Files";
        $this->content_view = "admin/batch_recalculate";
        $this->data['files'] = Ranked_file::all(array('select' => 'id, title, rate, artist, difficulty_score'));
    }


    function batch_recalculate_scores() {
        $this->data['subtitle'] = "Batch Recalculate User Scores";
        $this->content_view = "admin/batch_recalculate_scores";
        $this->data['files'] = Ranked_file::all(array('select' => 'id, title, rate, artist, difficulty_score'));
        $this->data['scores'] = User_score::get_scores_for_recalculate();
    }

    function recalculate($id) {
        $file = Ranked_file::find($id);
        $calculated_difficulty = $this->_process_everything($file->raw_file, $file->rate);
        $file->difficulty_score = $calculated_difficulty;
        $meta = $this->data['meta'];
        $file->taps = $meta['taps'];
        $file->notes = $meta['notes'];
        $file->holds = $meta['holds'];
        $file->auto_type = $meta['autodetermined_file_type'];
        $file->save();

        $this->layout_view = "ajax";
        $this->content_view = "ajax/recalculate";
        $this->data['new_difficulty'] = $calculated_difficulty;
    }

    function recalculate_score($id) {
        $score_to_calculate = User_score::get_score_for_recalculate($id);
        $score_only = User_score::find($id);
        $file_only = Ranked_file::find($score_to_calculate[0]->file_id);
        $this->data['score'] = $score_to_calculate;

        $user_percent = (calculate_dp_percent($score_to_calculate[0])) / 100;
        if ($user_percent < 0.91) {
            $this->data['new_difficulty'] = "Score below 91%, not calculated.";
        } else {
            if ($user_percent > 0.97)
                $user_percent = 0.97;
            $this->_process_everything($file_only->raw_file, $file_only->rate, $user_percent);
            $applied_difficulty = $this->data['user_score_goal_result'];
            $score_only->applied_score = $applied_difficulty;
            $score_only->save();
            $this->data['new_difficulty'] = $applied_difficulty;
        }

        $this->layout_view = "ajax";
        $this->content_view = "ajax/recalculate_score";
    }

    function regen_user_scores() {
        $this->data['subtitle'] = "Regen User Scores";
        $scores = User_score::get_all_scores();
        foreach($scores as $score) {
            $max_dp_max = $score->total_dance_points;
            $max_dp_achieved = ($score->marvelous_count * 2) + ($score->perfect_count * 2) + ($score->great_count * 1) + ($score->boo_count * -4) + ($score->miss_count * -8) + ($score->ok_count * 6) + ($score->mines_hit * -8);
            $max_dp_percent = ($max_dp_achieved / $max_dp_max) * 100;

            if ($max_dp_percent < 93) {
                $score->status = "below_aa";
                $score->save();
            }
        }
        $this->content_view = "admin/regen_user_scores";
    }

    function post_announcement() {
        $this->data['subtitle'] = "Post New Announcement";
        if ($this->input->post()) {
            if ($this->form_validation->run('announcement') == FALSE) {
                $this->content_view = "admin/announcement";
				$this->data['error'] = true;
            } else {
                $this->content_view = "admin/announcement_success";
                $attributes = array(
                    "title"     => $this->input->post('title'),
                    "text"      => $this->input->post('text'),
                    "user_id"   => $this->session->userdata('user_id')
                );
                $new_announcement = new Announcement($attributes);
                $new_announcement->save();
            }
        } else {
            $this->content_view = "admin/announcement";
        }
    }

    function edit_announcement($id) {
        $this->data['subtitle'] = "Edit Announcement";
        $this->data['announcement'] = Announcement::find($id);
        if ($this->input->post()) {
            if ($this->form_validation->run('announcement') == FALSE) {
                $this->content_view = "admin/edit_announcement";
				$this->data['error'] = true;
            } else {
                $this->data['announcement']->title = $this->input->post('title');
                $this->data['announcement']->text = $this->input->post('text');

                $this->data['announcement']->save();
                $this->content_view = "admin/announcement_success";
            }
        } else {
            $this->content_view = "admin/edit_announcement";
        }
    }

    function test() {
        // just a generic test function that i'll use to test new features occasionally
        $this->data['subtitle'] = "Test Page";
        $this->content_view = "admin/test";
    }

    function auto_type_test() {
        $this->data['subtitle'] = "Auto File Type Comparison Page";
        $this->data['charts'] = Ranked_file::all();
        $this->content_view = "admin/auto_type";
    }
}
