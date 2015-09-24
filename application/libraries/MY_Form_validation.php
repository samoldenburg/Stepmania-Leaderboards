<?php

class MY_Form_validation extends CI_Form_validation {

	function __construct($rules = array()){
    	parent::__construct($rules);
		$this->_config_rules = $rules;
	}


    public function username_unique($username) {
        $this->set_message('username_unique', 'The username you provided is already in use.');
        return User::count(array('conditions' => array('username = ?', $username))) == 0;
    }

    public function displayname_unique($username) {
        $this->set_message('displayname_unique', 'The display name you provided is already in use.');
        return User::count(array('conditions' => array('display_name = ?', $username))) == 0;
    }

	public function packname_unique($pack_name) {
		$this->set_message('packname_unique', "The pack you're trying to enter already exists.");
		return Pack::count(array('conditions' => array('name = ?', $pack_name))) == 0;
	}

}
