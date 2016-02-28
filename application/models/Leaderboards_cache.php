<?php
/*
 * Model for `announcements` table
 */
class Leaderboards_cache extends ActiveRecord\Model {
	static $table_name = "leaderboards_cache";

    public static function get_all_lbs() {
        $lbs = array();
        $lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 1),
				'order' => 'id desc'
			)
		);
		$lbs['overall_leaderboards'] = (array) json_decode(base64_decode($lb->data));

        $lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 2),
				'order' => 'id desc'
			)
		);
		$lbs['speed_leaderboards'] = (array) json_decode(base64_decode($lb->data));

        $lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 3),
				'order' => 'id desc'
			)
		);
		$lbs['jumpstream_leaderboards'] = (array) json_decode(base64_decode($lb->data));

        $lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 4),
				'order' => 'id desc'
			)
		);
		$lbs['jack_leaderboards'] = (array) json_decode(base64_decode($lb->data));

        $lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 5),
				'order' => 'id desc'
			)
		);
		$lbs['technical_leaderboards'] = (array) json_decode(base64_decode($lb->data));

        $lb = Leaderboards_cache::find(array(
				'conditions' => array('type = ?', 6),
				'order' => 'id desc'
			)
		);
		$lbs['stamina_leaderboards'] = (array) json_decode(base64_decode($lb->data));

        return $lbs;
    }

}
