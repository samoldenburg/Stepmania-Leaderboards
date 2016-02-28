<?php
/**
 * Packs model
 */
class Pack extends ActiveRecord\Model {
	static $table_name = "packs";

    public static function create_pack($pack_name, $abbreviation, $download_link) {
        $attributes = array(
            'name'              => $pack_name,
			'abbreviation'		=> $abbreviation,
            'download_link'     => $download_link
        );
        $new_pack = new Pack($attributes);
        $new_pack->save();
    }

	// SELECT `packs`.*, (SELECT COUNT(*) FROM `ranked_files` WHERE `ranked_files`.`pack_id` = `packs`.`id`) AS file_count FROM `packs`
	public static function get_all_pack_info() {
		return Pack::all(array(
				"select" => "`packs`.*, (SELECT COUNT(*) FROM `ranked_files` WHERE `ranked_files`.`pack_id` = `packs`.`id`) AS file_count, (SELECT AVG(difficulty_score) FROM `ranked_files` WHERE `ranked_files`.`pack_id` = `packs`.`id` AND `ranked_files`.`rate` = 1) as average"
			)
		);
	}

    public static function get_recent_packs() {
		return Pack::all(array(
				"select" => "`packs`.*, (SELECT COUNT(*) FROM `ranked_files` WHERE `ranked_files`.`pack_id` = `packs`.`id`) AS file_count, (SELECT AVG(difficulty_score) FROM `ranked_files` WHERE `ranked_files`.`pack_id` = `packs`.`id` AND `ranked_files`.`rate` = 1) as average",
                'order' => '`packs`.id DESC',
                'limit' => 10
			)
		);
	}

	public static function get_single_pack_info($id) {
		return Pack::find(array(
				"select" => "`packs`.*, (SELECT COUNT(*) FROM `ranked_files` WHERE `ranked_files`.`pack_id` = `packs`.`id`) AS file_count",
				"conditions" => array("id = ?", $id)
			)
		);
	}
}
