<?php
/**
 * Simple Ajax Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "ajax";
	}

	public function index() {
		$this->layout_view = "layout";
        $this->content_view = 'home';
	}

	public function username_exists($username = null) {
		if (!empty($username)) {
	        $this->content_view = 'ajax/username_exists';
			$this->data['exists'] = User::count(array('conditions' => array('username = ?', $username)));
		}
		else
			$this->data['exists'] = 2;
	}

	public function displayname_exists($username = null) {
		if (!empty($username)) {
	        $this->content_view = 'ajax/username_exists';
			$this->data['exists'] = User::count(array('conditions' => array('display_name = ?', $username)));
		}
		else
			$this->data['exists'] = 2;
	}

	public function add_chat_message() {
		if (!$this->input->post()) {
			$this->content_view = 'ajax/username_exists';
			$this->data['exists'] = "Error";
		} else {
			$last_message_sent = Chat_log::find(
				array(
					'conditions' => array('user_id = ?', $this->session->userdata('user_id')),
					'limit' => 1,
					'order' => 'time desc'
				)
			);
			$this->data['spam_error'] = false;
			$mtime = strtotime($last_message_sent->time);
			$this->data['mtime'] = strtotime($last_message_sent->time);
			if ($mtime > (time() - 3)) {
				$this->data['spam_error'] = true;
			}
			else if (!empty(trim(htmlentities($this->input->post('chat-type'))))) {
				$attributes = array(
					"user_id" 	=> $this->session->userdata('user_id'),
					"color" 	=> $this->session->userdata('chat_color'),
					"message" 	=> htmlentities($this->input->post('chat-type'))
				);
				$new_chatlog = new Chat_log($attributes);
				$new_chatlog->save();
			}
			$this->content_view = 'ajax/chat_log';
			$this->data['chat'] = Chat_log::get_chat_log();
		}
	}

	public function upload_screenshot() {
		$this->content_view = 'ajax/upload_screenshot';
		if(!file_exists('uploads/screenshots/' . $this->session->userdata('username'))) {
			mkdir('uploads/screenshots/'. $this->session->userdata('username'));
		}

		$config['upload_path']		= 'uploads/screenshots/'. $this->session->userdata('username');
        $config['allowed_types']    = 'jpg|png';
        $config['max_size']         = '5000';
		$config['max_width'] 		= '1920';
		$config['max_height'] 		= '1080';

        $this->load->library('upload', $config);

		if (!$this->upload->do_upload()) {
			$this->data['error'] = $this->upload->display_errors();
			$this->content_view = 'ajax/upload_fail';
        }
        else {
            $this->data['path'] = '/uploads/screenshots/'. $this->session->userdata('username') . '/' . $this->upload->data('file_name');
        }
	}

	public function get_chat() {
		$this->content_view = 'ajax/chat_log';
		$this->data['chat'] = Chat_log::get_chat_log();
	}
}
