<?php
/*
 * Model for `announcements` table
 */
class Mod_forum extends ActiveRecord\Model {
	static $table_name = "mod_forum";

	public static function get_announcements() {
		return Mod_forum::all(
			array(
				'select' 	=> 'mod_forum.*, users.username as username, users.display_name as display_name',
				'order' 	=> 'time desc',
				'joins'		=> "LEFT JOIN users ON mod_forum.user_id = users.id",
				'limit'		=> 20
			)
		);
	}
}
