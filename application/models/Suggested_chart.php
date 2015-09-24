<?php
/**
 * Packs model
 */
class Suggested_chart extends ActiveRecord\Model {
	static $table_name = "suggested_charts";

    public static function get_list() {
        return Suggested_chart::all(array(
                "select"    => "suggested_charts.*, users.username as username, users.display_name as display_name",
                "joins"     => "LEFT JOIN `users` ON `suggested_charts`.user_id = `users`.id",
                "order"     => "id desc"
            )
        );
    }

    public static function added_chart($id) {

    }

    public static function rejected_chart($id) {

    }
}
