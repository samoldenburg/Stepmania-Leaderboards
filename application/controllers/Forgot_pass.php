<?php
/**
 * Login Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot_pass extends MY_Controller {

	function __construct() {
        parent::__construct();
		$this->layout_view = "layout";
		$this->data['title'] = "Stepmania Leaderboards";
        $this->data['subtitle'] = "Password Recovery";
	}

	public function index(){
        if ($this->input->post()) {
            if (empty($this->input->post('username'))) {
                $this->content_view = "forgot_pass/index";
            } else {
                $user_to_reset = User::find_by_username($this->input->post('username'));
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randstring = '';
                for ($i = 0; $i < 16; $i++) {
                    $randstring .= $characters[rand(0, strlen($characters))];
                }
                $user_to_reset->pass_key = $randstring;
                $user_to_reset->save();
                $link = "http://" . $_SERVER['HTTP_HOST'] . "/forgot_pass/reset_pass/" . $randstring;
                $email_content = "A password reset has been requested for your account. If you did not request this reset please disregard this message. Otherwise, open the link below to continue.<br /><a href=" . $link . ">" . $link . "</a>";

                $config = Array(
                  'protocol' => 'sendmail',
                  'mailtype' => 'html',
                  'charset' => 'utf-8',
                  'wordwrap' => TRUE
                );
                $this->load->library('email', $config);
                $this->email->from('noreply@smleaderboards.net', 'Stepmania Leaderboards');
                $this->email->to($user_to_reset->email);
                $this->email->subject('Stepmania Leaderboards - Password Recovery');
                $this->email->message($email_content);
                $this->email->send();

                $this->content_view = "forgot_pass/recover_confirm";


            }
        } else {
            $this->content_view = "forgot_pass/index";
        }
	}

    public function reset_pass($token = null) {
        if (empty($token))
            redirect('forgot_pass');
        else if (User::count(array('conditions' => array('pass_key = ?', $token))) == 0)
			redirect('forgot_pass');

        $this->data['token'] = $token;

        $this->data['user'] = User::find_by_pass_key($token);
        if ($_POST) {

			if ($this->form_validation->run('pass_reset') == FALSE) {
	        	$this->content_view = 'forgot_pass/reset_pass';
				$this->data['error'] = true;
			} else {
	            $this->data['user']->password = crypt($this->input->post('pass') . $this->data['user']->salt);
                $this->data['user']->pass_key = "";
                $this->data['user']->save();

	            $this->content_view = 'forgot_pass/reset_pass_confirm';
			}
        }
		else {
            $this->content_view = "forgot_pass/reset_pass";
        }

    }
}
