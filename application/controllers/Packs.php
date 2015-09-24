<?php
/**
 * Packs view Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Packs extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
	}

	public function index() {
        $this->data['subtitle'] = "Pack List";
		$this->data['packs'] = Pack::get_all_pack_info();
        $this->content_view = 'packs/list';
	}

	public function view ($id = null) {
		if (empty($id))
			redirect('packs');
		else if (Pack::count(array('conditions' => array('id = ?', $id))) == 0)
			redirect('packs');
		else {
			$this->data['pack'] = Pack::get_single_pack_info($id);
			$this->data['subtitle'] = $this->data['pack']->name;
			$this->data['songs'] = Ranked_file::all(
				array(
					'conditions' => array("pack_id = ?", $id),
					'order' => "rate ASC"
				)
			);
			$this->content_view = 'packs/view';
		}
	}
}
