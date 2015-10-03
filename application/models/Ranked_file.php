<?php
/*
 * Model for `ranked_files` table
 */
class Ranked_file extends ActiveRecord\Model {
	static $table_name = "ranked_files";

	public static function get_all_charts() {
		return Ranked_file::all(array(
				"select" => "`ranked_files`.id, `ranked_files`.title, `ranked_files`.artist, `ranked_files`.rate, `ranked_files`.stamina_file, `ranked_files`.file_type, `ranked_files`.pack_id, `ranked_files`.length, `ranked_files`.difficulty_score, `ranked_files`.new_difficulty_score, `packs`.name AS pack_name, `packs`.abbreviation AS pack_abbr",
				"joins" =>	"LEFT JOIN `packs` ON `packs`.id = `ranked_files`.pack_id"
			)
		);
	}
}
