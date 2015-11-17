<?php
/*
 * Model for `users` table
 */
class User_score extends ActiveRecord\Model {
	static $table_name = "user_scores";

	public static function get_recent_scores() {
		return User_score::all(array(
                'select' => 'user_scores.*, users.username as username, users.display_name as display_name, ranked_files.id as file_id, ranked_files.pack_id as pack_id, ranked_files.stamina_file as stamina_file, ranked_files.file_type as file_type, ranked_files.title as title, ranked_files.subtitle as subtitle, ranked_files.artist as artist, ranked_files.rate as file_rate, ranked_files.length as length, ranked_files.dance_points as total_dance_points, ranked_files.taps as file_taps, ranked_files.holds as file_holds, ranked_files.mines as file_mines, ranked_files.difficulty_score as difficulty_score, packs.name as pack_name, packs.abbreviation as pack_abbreviation',
                'conditions' => '(status = "approved" OR status = "below_aa") AND user_scores.date_achieved <= CURDATE() AND user_id IS NOT NULL',
                'joins' => array(
                    'LEFT JOIN users ON users.id = user_scores.user_id',
                    'LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id',
                    'LEFT JOIN packs ON ranked_files.pack_id = packs.id'
                ),
                'order' => 'user_scores.id DESC',
				'limit' => '10'
            )
        );
	}

	public static function get_scores_for_recalculate() {
		return User_score::all(array(
				'select' => 'user_scores.id, ranked_files.title, ranked_files.rate, users.username',
				'joins' => array(
					'LEFT JOIN ranked_files ON user_scores.file_id = ranked_files.id',
					'LEFT JOIN users ON user_scores.user_id = users.id'
				)
			)
		);
	}

	public static function get_score_for_recalculate($id) {
		return User_score::all(array(
				'select' => 'user_scores.*, users.username as username, users.display_name as display_name, ranked_files.pack_id as pack_id, ranked_files.stamina_file as stamina_file, ranked_files.file_type as file_type, ranked_files.title as title, ranked_files.subtitle as subtitle, ranked_files.artist as artist, ranked_files.rate as file_rate, ranked_files.length as length, ranked_files.dance_points as total_dance_points, ranked_files.taps as file_taps, ranked_files.holds as file_holds, ranked_files.mines as file_mines, ranked_files.difficulty_score as difficulty_score, packs.name as pack_name, packs.abbreviation as pack_abbreviation',
				'joins' => array(
					'LEFT JOIN users ON users.id = user_scores.user_id',
					'LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id',
					'LEFT JOIN packs ON ranked_files.pack_id = packs.id'
				),
				'conditions' => array("user_scores.id = ?", $id)
			)
		);
	}

    public static function get_scores_for_user($user_id, $order = "user_scores.date_achieved DESC", $extra_conditions = "") {
        return User_score::all(array(
                'select' => 'user_scores.*, users.username as username, users.display_name as display_name, ranked_files.pack_id as pack_id, ranked_files.stamina_file as stamina_file, ranked_files.file_type as file_type, ranked_files.title as title, ranked_files.subtitle as subtitle, ranked_files.artist as artist, ranked_files.rate as file_rate, ranked_files.length as length, ranked_files.dance_points as total_dance_points, ranked_files.taps as file_taps, ranked_files.holds as file_holds, ranked_files.mines as file_mines, ranked_files.difficulty_score as difficulty_score, ranked_files.new_difficulty_score as new_difficulty_score, packs.name as pack_name, packs.abbreviation as pack_abbreviation',
                'conditions' => array('(status = "approved" OR status = "below_aa") ' . $extra_conditions . ' AND user_id = ? AND ranked_files.id IS NOT NULL', $user_id),
                'joins' => array(
                    'LEFT JOIN users ON users.id = user_scores.user_id',
                    'LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id',
                    'LEFT JOIN packs ON ranked_files.pack_id = packs.id'
                ),
                'order' => $order
            )
        );
    }

	public static function get_scores_for_user_approved($user_id, $order = "user_scores.date_achieved DESC", $extra_conditions = "") {
        return User_score::all(array(
                'select' => 'user_scores.*, users.username as username, users.display_name as display_name, ranked_files.pack_id as pack_id, ranked_files.stamina_file as stamina_file, ranked_files.file_type as file_type, ranked_files.title as title, ranked_files.subtitle as subtitle, ranked_files.artist as artist, ranked_files.rate as file_rate, ranked_files.length as length, ranked_files.dance_points as total_dance_points, ranked_files.taps as file_taps, ranked_files.holds as file_holds, ranked_files.mines as file_mines, ranked_files.difficulty_score as difficulty_score, packs.name as pack_name, packs.abbreviation as pack_abbreviation',
                'conditions' => array('status = "approved" ' . $extra_conditions . ' AND user_id = ? AND ranked_files.id IS NOT NULL', $user_id),
                'joins' => array(
                    'LEFT JOIN users ON users.id = user_scores.user_id',
                    'LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id',
                    'LEFT JOIN packs ON ranked_files.pack_id = packs.id'
                ),
                'order' => $order
            )
        );
    }

    public static function get_scores_for_chart($chart_id, $order = "user_scores.date_achieved DESC", $extra_conditions = "") {
        return User_score::all(array(
                'select' => 'user_scores.*, users.username as username, users.display_name as display_name, ranked_files.pack_id as pack_id, ranked_files.stamina_file as stamina_file, ranked_files.file_type as file_type, ranked_files.title as title, ranked_files.subtitle as subtitle, ranked_files.artist as artist, ranked_files.rate as file_rate, ranked_files.length as length, ranked_files.dance_points as total_dance_points, ranked_files.taps as file_taps, ranked_files.holds as file_holds, ranked_files.mines as file_mines, ranked_files.difficulty_score as difficulty_score, packs.name as pack_name, packs.abbreviation as pack_abbreviation',
                'conditions' => array('(status = "approved" OR status = "below_aa") ' . $extra_conditions . ' AND user_id IS NOT NULL AND ranked_files.id = ?', $chart_id),
                'joins' => array(
                    'LEFT JOIN users ON users.id = user_scores.user_id',
                    'LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id',
                    'LEFT JOIN packs ON ranked_files.pack_id = packs.id'
                ),
                'order' => $order
            )
        );
    }

	public static function get_top_10_percentile() {
		$total_count = User_score::count();
		$floor_count = floor($total_count * 0.1);
		$all_scores = User_score::all(array(
				'select' => "user_scores.*, ranked_files.difficulty_score as difficulty_score",
				'joins' => "LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id",
				'order' => "difficulty_score desc",
				'limit' => $floor_count
			)
		);

		$last_score = 0;
		foreach ($all_scores as $score) {
			$last_score = $score->difficulty_score;
		}
		return $last_score;
	}

	public static function get_pending_scores() {
		return User_score::all(array(
				'select' => 'user_scores.*, users.username as username, users.display_name as display_name, ranked_files.pack_id as pack_id, ranked_files.stamina_file as stamina_file, ranked_files.file_type as file_type, ranked_files.title as title, ranked_files.subtitle as subtitle, ranked_files.artist as artist, ranked_files.rate as file_rate, ranked_files.length as length, ranked_files.dance_points as total_dance_points, ranked_files.taps as file_taps, ranked_files.holds as file_holds, ranked_files.mines as file_mines, ranked_files.difficulty_score as difficulty_score, packs.name as pack_name, packs.abbreviation as pack_abbreviation',
				'conditions' => 'was_pending = 1 AND ranked_files.id IS NOT NULL',
				'joins' => array(
					'LEFT JOIN users ON users.id = user_scores.user_id',
					'LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id',
					'LEFT JOIN packs ON ranked_files.pack_id = packs.id'
				),
				'order' => "id desc"
			)
		);
	}

	public static function get_all_scores($order = "user_scores.date_achieved DESC", $extra_conditions = "") {
        return User_score::all(array(
                'select' => 'user_scores.*, users.username as username, users.display_name as display_name, ranked_files.pack_id as pack_id, ranked_files.stamina_file as stamina_file, ranked_files.file_type as file_type, ranked_files.title as title, ranked_files.subtitle as subtitle, ranked_files.artist as artist, ranked_files.rate as file_rate, ranked_files.length as length, ranked_files.dance_points as total_dance_points, ranked_files.taps as file_taps, ranked_files.holds as file_holds, ranked_files.mines as file_mines, ranked_files.difficulty_score as difficulty_score, packs.name as pack_name, packs.abbreviation as pack_abbreviation',
                'conditions' => '(status = "approved" OR status = "below_aa") ' . $extra_conditions . ' AND ranked_files.id IS NOT NULL',
                'joins' => array(
                    'LEFT JOIN users ON users.id = user_scores.user_id',
                    'LEFT JOIN ranked_files ON ranked_files.id = user_scores.file_id',
                    'LEFT JOIN packs ON ranked_files.pack_id = packs.id'
                ),
                'order' => $order
            )
        );
    }

	public static function get_overall_leaderboard() {
		// For ease of editing later I'm not going to try to wiggle this into one query
		$users = User::all();
		$return_array = array();
		$speed_leaderboard = User_score::get_speed_leaderboard();
		$jumptream_leaderboard = User_score::get_jumpstream_leaderboard();
		$jack_leaderboard = User_score::get_jack_leaderboard();
		$technical_leaderboard = User_score::get_technical_leaderboard();
		$stamina_leaderboard = User_score::get_stamina_leaderboard();
		foreach ($users as $user) {
			$scores = User_score::get_scores_for_user_approved($user->id, "difficulty_score DESC");
			$top_sum = 0;
			$top_score = 0;
			foreach ($scores as $score) {
				$top_score = $score->difficulty_score;
				break;
			}
			$i = 0;
			$scores_to_use = 1;
			if ($top_score < 15)
				$scores_to_use = 1;
			else if ($top_score < 20)
				$scores_to_use = 2;
			else if ($top_score < 24)
				$scores_to_use = 2;
			else if ($top_score < 28)
				$scores_to_use = 3;
			else if ($top_score < 31)
				$scores_to_use = 3;
			else
				$scores_to_use = 3;

			$uscores = array();
			$uscores['speed_score'] = 0;
			$uscores['jumpstream_score'] = 0;
			$uscores['jack_score'] = 0;
			$uscores['technical_score'] = 0;
			$uscores['stamina_score'] = 0;

			foreach ($speed_leaderboard as $row) {
				if ($row['username'] == $user->display_name) {
					$uscores['speed_score'] =  $row['average_score'];
				}
			}
			foreach ($jumptream_leaderboard as $row) {
				if ($row['username'] == $user->display_name) {
					$uscores['jumpstream_score'] =  $row['average_score'];
				}
			}
			foreach ($jack_leaderboard as $row) {
				if ($row['username'] == $user->display_name) {
					$uscores['jack_score'] =  $row['average_score'];
				}
			}
			foreach ($technical_leaderboard as $row) {
				if ($row['username'] == $user->display_name) {
					$uscores['technical_score'] =  $row['average_score'];
				}
			}
			foreach ($stamina_leaderboard as $row) {
				if ($row['username'] == $user->display_name) {
					$uscores['stamina_score'] = $row['average_score'];
				}
			}

			$avg = 0;
			$total = 0;
			rsort($uscores);
			$c = 0;
			$add_to_leaderboard = true;
			foreach ($uscores as $uscore) {
				$c++;
				$total += $uscore;
				if ($uscore == 0)
					$add_to_leaderboard = false;
				if ($c == $scores_to_use)
					break;
			}

			#echo $user->display_name . ": " . $scores_to_use . "<br />";
			#echo User_score::count(array('conditions' => array('status = "approved" AND user_id = ?', $user->id))) . "<br />";
			#echo "<pre>";
			#print_r($uscores);
			#echo "</pre>";

			$avg = $total / $c;

			$user_array = array(
				"username" => $user->display_name,
				"profile_link" => "/profile/view/" . $user->username,
				"average_score" => $avg,
				"scores" => $scores
			);
			if ($add_to_leaderboard)
				array_push($return_array, $user_array);
		}
		// sort the array using an anonymous function
		array_sort_by_column($return_array, 'average_score');
		return $return_array;
	}

	public static function get_speed_leaderboard() {
		// For ease of editing later I'm not going to try to wiggle this into one query
		$users = User::all();
		$return_array = array();
		foreach ($users as $user) {
			$scores = User_score::get_scores_for_user_approved($user->id, "difficulty_score DESC", 'AND ranked_files.file_type = "speed"');
			$top_sum = 0;
			$top_score = 0;
			foreach ($scores as $score) {
				$top_score = $score->difficulty_score;
				break;
			}
			$i = 0;
			$scores_to_use = 1;
			if ($top_score < 15)
				$scores_to_use = 1;
			else if ($top_score < 20)
				$scores_to_use = 1;
			else if ($top_score < 24)
				$scores_to_use = 2;
			else if ($top_score < 28)
				$scores_to_use = 2;
			else if ($top_score < 31)
				$scores_to_use = 3;
			else
				$scores_to_use = 5;


			foreach ($scores as $score) {
				$top_sum += $score->difficulty_score;

				$i++;
				if ($i == $scores_to_use)
					break;
			}
			$avg = $top_sum / $scores_to_use;

			$user_array = array(
				"username" => $user->display_name,
				"profile_link" => "/profile/view/" . $user->username,
				"average_score" => $avg
			);
			if ($i == $scores_to_use && $avg != 0)
				array_push($return_array, $user_array);
		}
		// sort the array using an anonymous function
		array_sort_by_column($return_array, 'average_score');
		return $return_array;
	}

	public static function get_jumpstream_leaderboard() {
		// For ease of editing later I'm not going to try to wiggle this into one query
		$users = User::all();
		$return_array = array();
		foreach ($users as $user) {
			$scores = User_score::get_scores_for_user_approved($user->id, "difficulty_score DESC", 'AND ranked_files.file_type = "jumpstream"');
			$top_sum = 0;
			$top_score = 0;
			foreach ($scores as $score) {
				$top_score = $score->difficulty_score;
				break;
			}
			$i = 0;
			$scores_to_use = 1;
			if ($top_score < 15)
				$scores_to_use = 1;
			else if ($top_score < 20)
				$scores_to_use = 1;
			else if ($top_score < 24)
				$scores_to_use = 2;
			else if ($top_score < 28)
				$scores_to_use = 2;
			else if ($top_score < 31)
				$scores_to_use = 3;
			else
				$scores_to_use = 5;


			foreach ($scores as $score) {
				$top_sum += $score->difficulty_score;

				$i++;
				if ($i == $scores_to_use)
					break;
			}
			$avg = $top_sum / $scores_to_use;

			$user_array = array(
				"username" => $user->display_name,
				"profile_link" => "/profile/view/" . $user->username,
				"average_score" => $avg
			);
			if ($i == $scores_to_use && $avg != 0)
				array_push($return_array, $user_array);
		}
		// sort the array using an anonymous function
		array_sort_by_column($return_array, 'average_score');
		return $return_array;
	}

	public static function get_jack_leaderboard() {
		// For ease of editing later I'm not going to try to wiggle this into one query
		$users = User::all();
		$return_array = array();
		foreach ($users as $user) {
			$scores = User_score::get_scores_for_user_approved($user->id, "difficulty_score DESC", 'AND ranked_files.file_type = "jack"');
			$top_sum = 0;
			$top_score = 0;
			foreach ($scores as $score) {
				$top_score = $score->difficulty_score;
				break;
			}
			$i = 0;
			$scores_to_use = 1;
			if ($top_score < 15)
				$scores_to_use = 1;
			else if ($top_score < 20)
				$scores_to_use = 1;
			else if ($top_score < 24)
				$scores_to_use = 2;
			else if ($top_score < 28)
				$scores_to_use = 2;
			else if ($top_score < 31)
				$scores_to_use = 3;
			else
				$scores_to_use = 5;


			foreach ($scores as $score) {
				$top_sum += $score->difficulty_score;

				$i++;
				if ($i == $scores_to_use)
					break;
			}
			$avg = $top_sum / $scores_to_use;

			$user_array = array(
				"username" => $user->display_name,
				"profile_link" => "/profile/view/" . $user->username,
				"average_score" => $avg
			);
			if ($i == $scores_to_use && $avg != 0)
				array_push($return_array, $user_array);
		}
		// sort the array using an anonymous function
		array_sort_by_column($return_array, 'average_score');
		return $return_array;
	}

	public static function get_technical_leaderboard() {
		// For ease of editing later I'm not going to try to wiggle this into one query
		$users = User::all();
		$return_array = array();
		foreach ($users as $user) {
			$scores = User_score::get_scores_for_user_approved($user->id, "difficulty_score DESC", 'AND ranked_files.file_type = "technical"');
			$top_sum = 0;
			$top_score = 0;
			foreach ($scores as $score) {
				$top_score = $score->difficulty_score;
				break;
			}

			$i = 0;
			$scores_to_use = 1;
			if ($top_score < 15)
				$scores_to_use = 1;
			else if ($top_score < 20)
				$scores_to_use = 1;
			else if ($top_score < 24)
				$scores_to_use = 2;
			else if ($top_score < 28)
				$scores_to_use = 2;
			else if ($top_score < 31)
				$scores_to_use = 3;
			else
				$scores_to_use = 5;


			foreach ($scores as $score) {
				$top_sum += $score->difficulty_score;

				$i++;
				if ($i == $scores_to_use)
					break;
			}
			$avg = $top_sum / $scores_to_use;

			$user_array = array(
				"username" => $user->display_name,
				"profile_link" => "/profile/view/" . $user->username,
				"average_score" => $avg
			);
			if ($i == $scores_to_use && $avg != 0)
				array_push($return_array, $user_array);
		}
		// sort the array using an anonymous function
		array_sort_by_column($return_array, 'average_score');
		return $return_array;
	}

	public static function get_stamina_leaderboard() {
		// For ease of editing later I'm not going to try to wiggle this into one query
		$users = User::all();
		$return_array = array();
		foreach ($users as $user) {
			$scores = User_score::get_scores_for_user_approved($user->id, "difficulty_score DESC", 'AND ranked_files.stamina_file = 1');
			$top_sum = 0;
			$top_score = 0;
			foreach ($scores as $score) {
				$top_score = $score->difficulty_score;
				break;
			}
			$i = 0;
			$scores_to_use = 1;
			if ($top_score < 15)
				$scores_to_use = 1;
			else if ($top_score < 20)
				$scores_to_use = 1;
			else if ($top_score < 24)
				$scores_to_use = 2;
			else if ($top_score < 28)
				$scores_to_use = 2;
			else if ($top_score < 31)
				$scores_to_use = 3;
			else
				$scores_to_use = 5;


			foreach ($scores as $score) {
				$top_sum += $score->difficulty_score;

				$i++;
				if ($i == $scores_to_use)
					break;
			}
			$avg = $top_sum / $scores_to_use;

			$user_array = array(
				"username" => $user->display_name,
				"profile_link" => "/profile/view/" . $user->username,
				"average_score" => $avg
			);
			if ($i == $scores_to_use && $avg != 0)
				array_push($return_array, $user_array);
		}
		// sort the array using an anonymous function
		array_sort_by_column($return_array, 'average_score');
		return $return_array;
	}
}
