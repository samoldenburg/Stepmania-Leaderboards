<?php
/*
 * Model for `announcements` table
 */
class Announcement extends ActiveRecord\Model {
	static $table_name = "announcements";

	public static function get_announcements() {
		return Announcement::all(
			array(
				'select' 	=> 'announcements.*, users.username as username, users.display_name as display_name',
				'order' 	=> 'time desc',
				'joins'		=> "LEFT JOIN users ON announcements.user_id = users.id",
				'limit'		=> 5
			)
		);
	}
}
