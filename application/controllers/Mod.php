<?php
/**
 * Mod base controller.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Mod extends MY_Controller {
    function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
        $this->data['title'] = "Moderator Control Panel";
        $this->require_mod();
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
	}

    function index() {
        // For simplicity sake we'll make people use the nav up top
        redirect('home');
    }

    function rank_chart() {
        $this->data['subtitle'] = "Rank New Chart";
        if (intval($this->input->post('step')) == 1) {
            if ($this->form_validation->run('parser') == FALSE) {
                $this->content_view = "mod/rank_chart";
				$this->data['error'] = true;
            } else {
                // Validation good, second form!
                $this->_process_everything();
                $this->data['packs'] = Pack::all(array('order' => 'name asc'));
                $this->content_view = "mod/rank_chart_confirm";
            }
        } elseif (intval($this->input->post('step')) == 2) {
            if ($this->form_validation->run('rank_chart') == FALSE) {
                $this->data['packs'] = Pack::all(array('order' => 'name asc'));
				$this->data['error'] = true;
                $this->content_view = "mod/rank_chart_confirm";
            } else {
                // Validation good, save the file!
                $attributes = array(
                    'pack_id'           => $this->input->post('pack_id'),
                    'stamina_file'      => $this->input->post('stamina_file'),
                    'file_type'         => $this->input->post('file_type'),
                    'auto_type'         => $this->input->post('auto_type'),
                    'date_ranked'       => $this->input->post('date_ranked'),
                    'rate'              => $this->input->post('rate'),
                    'title'             => $this->input->post('title'),
                    'subtitle'          => $this->input->post('subtitle'),
                    'artist'            => $this->input->post('artist'),
                    'length'            => $this->input->post('length'),
                    'dance_points'      => $this->input->post('dance_points'),
                    'notes'             => $this->input->post('notes'),
                    'taps'              => $this->input->post('taps'),
                    'jumps'             => $this->input->post('jumps'),
                    'hands'             => $this->input->post('hands'),
                    'quads'             => $this->input->post('quads'),
                    'holds'             => $this->input->post('holds'),
                    'mines'             => $this->input->post('mines'),
                    'peak_nps'          => $this->input->post('peak_nps'),
                    'avg_nps'           => $this->input->post('avg_nps'),
                    'avg_weighted_nps'  => $this->input->post('avg_weighted_nps'),
                    'difficulty_score'  => 0, // TODO: Re-enable
                    'raw_file'          => $this->input->post('raw_file'),
                );
                $new_file = new Ranked_file($attributes);
                $new_file->save();

                $rate = $this->input->post('rate');

                $log_string = $this->session->userdata('username') .
                    " (" . $this->session->userdata('display_name') . ") ranked new file: " .
                    number_format($rate, 1) . "x " .
                    $this->input->post('title') . " - " . $this->input->post('difficulty_score');
                write_to_mod_log($log_string);

                $this->data['rank_success'] = true;
                Mod::edit_chart($new_file->id);
            }
        } else {
            $this->content_view = "mod/rank_chart";

        }
    }

    function edit_chart($id) {
        $this->data['subtitle'] = "Edit Ranked Chart";
        $this->data['chart'] = Ranked_file::find($id);
        if (intval($this->input->post('step')) == 2) {
            if ($this->form_validation->run('rank_chart') == FALSE) {
                $this->data['packs'] = Pack::all(array('order' => 'name asc'));
				$this->data['error'] = true;
                $this->content_view = "mod/edit_chart";
            } else {
                // Validation good, save the file!

                $this->data['chart']->pack_id             = $this->input->post('pack_id');
                $this->data['chart']->stamina_file        = $this->input->post('stamina_file');
                $this->data['chart']->file_type           = $this->input->post('file_type');
                $this->data['chart']->date_ranked         = $this->input->post('date_ranked');
                $this->data['chart']->rate                = $this->input->post('rate');
                $this->data['chart']->title               = $this->input->post('title');
                $this->data['chart']->subtitle            = $this->input->post('subtitle');
                $this->data['chart']->artist              = $this->input->post('artist');
                $this->data['chart']->length              = $this->input->post('length');
                $this->data['chart']->dance_points        = $this->input->post('dance_points');
                $this->data['chart']->notes               = $this->input->post('notes');
                $this->data['chart']->taps                = $this->input->post('taps');
                $this->data['chart']->jumps               = $this->input->post('jumps');
                $this->data['chart']->hands               = $this->input->post('hands');
                $this->data['chart']->quads               = $this->input->post('quads');
                $this->data['chart']->holds               = $this->input->post('holds');
                $this->data['chart']->mines               = $this->input->post('mines');
                $this->data['chart']->peak_nps            = $this->input->post('peak_nps');
                $this->data['chart']->avg_nps             = $this->input->post('avg_nps');
                $this->data['chart']->avg_weighted_nps    = $this->input->post('avg_weighted_nps');
                $this->data['chart']->difficulty_score    = $this->input->post('difficulty_score');
                $this->data['chart']->raw_file            = $this->input->post('raw_file');

                $this->data['chart']->save();

                $rate = $this->input->post('rate');

                $log_string = $this->session->userdata('username') .
                    " (" . $this->session->userdata('display_name') . ") edited ranked chart: " .
                    $this->input->post('title') . " " .
                    number_format($rate, 1) . "x " .
                    " - " . $this->input->post('difficulty_score');
                write_to_mod_log($log_string);

                $this->data['rank_success'] = true;
                $this->content_view = "mod/edit_chart";
            }
        } else {
            $this->data['packs'] = Pack::all(array('order' => 'name asc'));
            $this->content_view = "mod/edit_chart";
        }
    }

    function add_additional_rate($chart_id) {
        $this->data['subtitle'] = "Rank New Chart";
        $this->data['chart'] = Ranked_file::find($chart_id);
        $this->content_view = "mod/rank_chart";
    }

    function delete_chart($chart_id) {
        if (Ranked_file::count(array('conditions' => array('id = ?', $chart_id))) != 1)
            redirect('home');
        $this->content_view = "mod/delete_chart_success";
        $chart = Ranked_file::find($chart_id);
        $title = $chart->title;
        $diff = $chart->difficulty_score;
        $rate = $chart->rate;
        $chart->delete();

        $scores = User_score::all(array('conditions' => array('file_id = ?', $chart_id)));
        foreach ($scores as $score) {
            $score->status = "removed";
            $score->save();
        }

        $log_string = $this->session->userdata('username') .
            " (" . $this->session->userdata('display_name') . ") deleted chart: " .
            $title . " " .
            number_format($rate, 1) . "x " . " - " . $diff;
        write_to_mod_log($log_string);
    }

    function add_pack() {
        $this->data['subtitle'] = "Add New Pack";
        if ($_POST) {
            if ($this->form_validation->run('add_pack') == FALSE) {
                $this->content_view = "mod/add_pack";
				$this->data['error'] = true;
            } else {
                Pack::create_pack($this->input->post('name'), $this->input->post('abbreviation'), $this->input->post('download_link'));
                $log_string = $this->session->userdata('username') .
                    " (" . $this->session->userdata('display_name') . ") added new pack: " .
                    $this->input->post('name') . " " .
                    $this->input->post('download_link');
                write_to_mod_log($log_string);
                $this->content_view = "mod/add_pack_success";
            }
        }
        else {
            $this->data['error'] = false;
            $this->content_view = "mod/add_pack";
        }
    }

    function edit_pack($id) {
        $this->data['subtitle'] = "Edit Pack";
        $this->data['pack'] = Pack::find($id);
        if ($this->input->post()) {
            if ($this->form_validation->run('edit_pack') == FALSE) {
                $this->content_view = "mod/edit_pack";
				$this->data['error'] = true;
            } else {
                $this->data['pack']->name = $this->input->post('name');
                $this->data['pack']->abbreviation = $this->input->post('abbreviation');
                $this->data['pack']->download_link = $this->input->post('download_link');

                $this->data['pack']->save();
                $log_string = $this->session->userdata('username') .
                    " (" . $this->session->userdata('display_name') . ") edited pack: " .
                    $this->input->post('name') . " " .
                    $this->input->post('download_link');
                write_to_mod_log($log_string);
                $this->content_view = "mod/add_pack_success";
            }
        } else {
            $this->content_view = "mod/edit_pack";
        }
    }

    function delete_pack($pack_id) {
        if (Pack::count(array('conditions' => array('id = ?', $pack_id))) != 1)
            redirect('home');
        $this->content_view = "mod/delete_pack_success";
        $pack = Pack::find($pack_id);
        $pack_name = $pack->name;
        $pack->delete();
        $log_string = $this->session->userdata('username') .
            " (" . $this->session->userdata('display_name') . ") deleted pack: " .
            $pack_name;
        write_to_mod_log($log_string);
    }

    function shoutboard() {
        $this->data['subtitle'] = "Mod Shoutboard";
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
                $new_announcement = new Mod_forum($attributes);
                $new_announcement->save();
            }
        }
        $this->data['mod_posts'] = Mod_forum::get_announcements();
        $this->content_view = "mod/shoutboard";
    }

    function suggested_files($id = null, $status = null) {
        if (!empty($id) && !empty($status) && ($status == "added" || $status == "rejected" || $status == "pending")) {
            $id = intval($id);
            $sf = Suggested_chart::find($id);
            $sf->status = $status;
            $sf->save();
        }
        $this->data['subtitle'] = "User Suggested Charts";
        $this->content_view = "mod/suggested_files";
        $this->data['suggested_files'] = Suggested_chart::get_list();
    }

    function pending_scores($id = null, $status = null) {
        if (!empty($id) && !empty($status) && ($status == "pending" || $status == "below_aa" || $status == "approved" || $status == "rejected")) {
            $id = intval($id);
            $sf = User_score::find($id);
            $sf->status = $status;
            $sf->save();
        }
        $this->data['subtitle'] = "Approve Pending Scores";
        $this->data['scores'] = User_score::get_pending_scores();
        $this->content_view = "mod/pending_scores";
    }

    function mod_log() {
        $this->data['subtitle'] = "Moderator Log";
        $file = file_get_contents('logs/mod_log.txt');
        $file_lines = preg_split('/\n|\r\n?/', $file);
        $file_lines = array_reverse($file_lines);
        $file = implode("\n",$file_lines);
        $this->data['file'] = $file;
        $this->content_view = "mod/mod_log";
    }
}
