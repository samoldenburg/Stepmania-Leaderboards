<?php
/*
 * Model for `users` table
 */
class User extends ActiveRecord\Model {
	static $table_name = "users";

	public static function make_salt() {
		$length = 32;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	/**
	 * Determine login data
	 */
	public static function validate_login($username, $password) {
		// Get User record
		$this_user = User::find_by_username($username);
		if (empty($this_user))
			return false;

		// Set up variables
		$hashed_password = $this_user->password;
		$salt = $this_user->salt;
		$user_input = $password . $salt;

		// Do validation
		if (hash_equals($hashed_password, crypt($user_input, $hashed_password))) {
		   	return true;
		} else {
			return false;
		}
	}

	/**
	 * Register new user
	 */
	public static function register($username, $password, $email, $display_name) {
		$salt = User::make_salt();
		$hashed_pass = crypt($password . $salt);

		$attributes = array(
    		'username' 		=> $username,
    		'password' 		=> $hashed_pass,
    		'salt'			=> $salt,
			'email'			=> $email,
			'display_name'  => $display_name
        );
		$new_user = new User($attributes);
		$new_user->save();

		$attributes = array(
			'user_id' 		=> $new_user->id,
			'meta_name' 	=> 'user_level',
			'meta_value' 	=> 1
		);
		$new_meta = new Usermeta($attributes);
		$new_meta->save();
	}

    public static function get_online_users() {
        return User::all(array(
                'select' => "username, display_name",
                'conditions' => array('last_login > ?', date('Y-m-d H:i:s', time()-300))
            )
        );
    }
}
