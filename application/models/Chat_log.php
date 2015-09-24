<?php
/**
 * Chat log model
 */
class Chat_log extends ActiveRecord\Model {
	static $table_name = "chat_log";

    public static function get_chat_log() {
        /*return Chat_log::all(array(
                "select"    => "chat_log.*, users.username as username, users.display_name as display_name",
                "limit"     => 50,
                "joins"     => "left join users on chat_log.user_id = users.id",
				"order"		=> "id desc"
            )
        );*/

		$query =
		"SELECT * FROM (
			SELECT chat_log.*, users.username AS username, users.display_name AS display_name FROM chat_log LEFT JOIN users ON users.id = chat_log.user_id ORDER BY id DESC LIMIT 50
		) sub
		ORDER BY id ASC";
		return Chat_log::find_by_sql($query);
    }
}
