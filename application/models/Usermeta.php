<?php
/*
 * Model for `users` table
 */
class Usermeta extends ActiveRecord\Model {
	static $table_name = "usermeta";

    public static function get_user_level($userid) {
        $meta = Usermeta::find(
            array(
                'select'        => 'meta_value',
                'conditions'    => array('user_id = ? AND meta_name="user_level"', $userid)
            )
        );
        return intval($meta->meta_value);
    }

}
