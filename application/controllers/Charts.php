<?php
/**
 * Charts view Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Charts extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
	}

	public function index() {
        $this->data['subtitle'] = "Ranked Charts List";
        $this->data['songs'] = Ranked_file::get_all_charts();
        $this->content_view = "charts/list";
	}

    public function view($id = null) {
        if (empty($id))
			redirect('charts');
		else if (Ranked_file::count(array('conditions' => array('id = ?', $id))) == 0)
			redirect('charts');
        else {
            error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
            $file = Ranked_file::find($id);
			$pack = Pack::find($file->pack_id);
            $this->data['user_scores'] = User_score::get_scores_for_chart($id);
			$this->data['pack'] = $pack;
            $this->data['subtitle'] = $file->title;
            $this->_process_everything($file->raw_file, $file->rate);
        }
    }

	public function suggest() {
		$this->data['subtitle'] = "Suggest New Chart";
		if ($this->input->post()) {
            if ($this->form_validation->run('suggest_file') == FALSE) {
	            $this->content_view = "charts/suggest";
				$this->data['error'] = true;
            } else {
                $this->content_view = "charts/suggest_success";
                $attributes = array(
                    "title"     => $this->input->post('title'),
                    "artist"    => $this->input->post('artist'),
					"pack"      => $this->input->post('pack'),
					"rate"      => $this->input->post('rate'),
					"chart"		=> $this->input->post('chart'),
                    "raw_file"  => $this->input->post('file'),
                    "file_type" => $this->input->post('file_type'),
					"user_id"   => $this->session->userdata('user_id')
                );
                $new_announcement = new Suggested_chart($attributes);
                $new_announcement->save();
            }
        } else {
            $this->content_view = "charts/suggest";
        }
	}
}
