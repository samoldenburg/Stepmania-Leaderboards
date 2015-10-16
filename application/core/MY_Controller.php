<?php
/**
 * Base controller modifications. Extend all controllers with MY_Controller rather than CI_Controller
 */
class MY_Controller extends CI_Controller {
	protected $layout_view = NULL;
    protected $content_view = NULL;
	protected $data;
	function __construct() {
		parent::__construct();
		$this->load->driver('session');
		$this->session->set_userdata('redirect', current_url());
		if ($this->session->userdata('username')) {
			$this->data['logged_in'] = true;
			$this->data['user_level'] = $this->session->userdata('user_level');
		}
		else {
			$this->data['logged_in'] = false;
			$this->data['user_level'] = 0;
		}
		$this->data['title'] = "Stepmania Leaderboards";
	}
	/**
	* Require login to use controller
	*/
	protected function require_login() {
    	if($this->session->userdata('username')) {
    	} else {
    		$this->session->set_userdata('redirect', current_url());
    		redirect('login');
    	}
	}
	/**
	*/
	protected function require_admin() {
		if ($this->session->userdata('user_level') < 3)
			redirect('home');
	}
	protected function require_mod() {
		if ($this->session->userdata('user_level') < 2)
			redirect('home');
	}
	// Source: https://gist.github.com/jehoshua02/1961900
	// Custom output function to give layouts and views their own separate entities, allows for easier templating
	public function _output($output){
        if($this->content_view !== FALSE && empty($this->content_view)) $this->content_view = $this->router->class . '/' . $this->router->method;
        $yield = file_exists(APPPATH . 'views/' . $this->content_view . EXT) ? $this->load->view($this->content_view, $this->data, TRUE) : FALSE;
        if($this->layout_view)
            echo $this->load->view('layout/' . $this->layout_view, array('yield' => $yield), TRUE);
        else
            echo $yield;
    }
	// SM Parse functions start here
	// get the meta information about this .sm file
    protected function _retreive_file_meta($file) {
		$file_lines = preg_split('/\n|\r\n?/', $file);
		$return = array();
		// Just setting up the order of the meta array
		// We use this to directly output in order later, so its nice to put it in the expected order now.
		$return['title'] = "";
		$return['subtitle'] = "";
		$return['artist'] = "";
		$return['rate'] = "";
		$return['length'] = "";
		$return['length_in_seconds'] = "";
		$return['dance_points'] = "";
		$return['dance_points_from_holds'] = "";
		$return['nontrivial_DP'] = "";
		$return['dance_points_for_grade_AA'] = "";
		$return['dance_points_for_grade_A'] = "";
		$return['dance_points_for_grade_B'] = "";
		$return['nontrivial_DP_needed_for_AA'] = "";
		$return['alotted_misses_ignoring_free_DP'] = "";
		$return['alotted_misses_NOT_ignoring_free_DP'] = "";
		$return['miss_factor_between_these'] = "";
		$return['weighted_AA_metric'] = "";
		$return['NPS_adjustment_from_free_misses'] = "";
		$return['notes'] = "";
		$return['taps'] = "";
		$return['jumps'] = "";
		$return['hands'] = "";
		$return['quads'] = "";
		$return['holds'] = "";
		$return['mines'] = "";
		$return['left_hand_percent'] = "";
		$return['right_hand_percent'] = "";
		$return['peak_NPS'] = "";
		$return['average_NPS'] = "";
		$return['bpms'] = array();
		$return['stops'] = array();
		$total_holds = 0;
		$total_mines = 0;
		$total_notes = 0;
		$total_taps = 0;
		$total_jumps = 0;
		$total_hands = 0;
		$total_quads = 0;
		$total_left_hand = 0;
		$total_right_hand = 0;
		$bpms_started = false;
		$bpms_finished = false;
		$bpms_parsed = false;
		$bpms_string = "";
		$stops_started = false;
		$stops_finished = false;
		$stops_parsed = false;
		$stops_string = "";
		$eof_found = false;
		$notes_started = false;
		foreach ($file_lines as $line) {
			// check for title match
			$title = $this->_matches_title($line);
			if ($title)
				$return['title'] = $title;
			// check for subtitle match
			$subtitle = $this->_matches_subtitle($line);
			if ($subtitle)
				$return['subtitle'] = $subtitle;
			// check for artist match
			$artist = $this->_matches_artist($line);
			if ($artist)
				$return['artist'] = $artist;
			// Process BPMS
			$bpms = $this->_matches_bpms($line);
			if ($bpms || ($bpms_started && !$bpms_finished)) {
				$bpms_started = true;
				if (strpos($line, ';') !== false)
					$bpms_finished = true;
				$bpms_string .= $line;
			}
			// BPMs string is complete, run through parse BPMs and append to meta
			if ($bpms_finished && (!$bpms_parsed)) {
				$bpms_string = str_replace("#BPMS:", "", $bpms_string);
				$bpms_string = str_replace(";", "", $bpms_string);
				$bpms = $this->_parse_bpms($bpms_string);
				$return['bpms'] = $bpms;
				$bpms_parsed = true;
			}
			// Process Stops
			$stops = $this->_matches_stops($line);
			if ($stops || ($stops_started && !$stops_finished)) {
				$stops_started = true;
				if (strpos($line, ';') !== false)
					$stops_finished = true;
				$stops_string .= $line;
			}
			// Stops string is complete, run through parse BPMs and append to meta
			if ($stops_finished && (!$stops_parsed)) {
				$stops_string = str_replace("#STOPS:", "", $stops_string);
				$stops_string = str_replace(";", "", $stops_string);
				$stops = $this->_parse_stops($stops_string);
				$return['stops'] = $stops;
				$stops_parsed = true;
			}
			// Get notes_started section
			$txt = $line;
			$re1='(\\d)';$re2='(\\d)';$re3='(\\d)';$re4='(\\d)';
			if ($c=preg_match("/".$re1.$re2.$re3.$re4."/is", $txt, $matches)) {
				if (strlen($line) == 4)
			  		$notes_started = true;
			}
			// mark eof found
			if (trim($line) == ";" && $notes_started) {
				$eof_found = true;
				break;
			}
			if (strlen($line) == 4 && !$eof_found) {
				// noteline
				// grab some meta, replace holds mines and rolls
				// for the purpose of this calculator rolls = holds
				$total_holds += substr_count($line, "2");
				$total_holds += substr_count($line, "4");
				$total_mines += substr_count($line, "M");
				$line = str_replace("M", "0", $line);
				$line = str_replace("2", "1", $line);
				$line = str_replace("4", "1", $line);
				$line = str_replace("3", "0", $line);
				// fill in some additional meta
				$notes_this_line = substr_count($line, "1");
				$total_notes += $notes_this_line;
				if ($notes_this_line > 0)
					$total_taps++;
				if ($notes_this_line == 2)
					$total_jumps++;
				if ($notes_this_line == 3)
					$total_hands++;
				if ($notes_this_line == 4)
					$total_quads++;
				// Split the noteline to get some additional meta
				$note_array = str_split($line);
				if ($note_array[0] == "1")
					$total_left_hand++;
				if ($note_array[1] == "1")
					$total_left_hand++;
				if ($note_array[2] == "1")
					$total_right_hand++;
				if ($note_array[3] == "1")
					$total_right_hand++;
			}
		}
		// Flush out all meta values into the return array
		$return['dance_points'] = ($total_taps * 2) + ($total_holds * 6);
		$return['dance_points_from_holds'] = $total_holds * 6;
		$return['dance_points_for_grade_AA'] = ceil($return['dance_points'] * 0.93);
		$return['dance_points_for_grade_A'] = ceil($return['dance_points'] * 0.8);
		$return['dance_points_for_grade_B'] = ceil($return['dance_points'] * 0.65);
		$return['notes'] = $total_notes;
		$return['taps'] = $total_taps;
		$return['jumps'] = $total_jumps;
		$return['hands'] = $total_hands;
		$return['quads'] = $total_quads;
		$return['left_hand_percent'] = round((($total_left_hand / $total_notes) * 100),3) . "%";
		$return['right_hand_percent'] = round((($total_right_hand / $total_notes) * 100),3) . "%";
		$return['holds'] = $total_holds;
		$return['mines'] = $total_mines;
		return $return;
	}
	// Returns an array containing only the notes sections of the file
	protected function _file_notes_only($file) {
		$file_lines = preg_split('/\n|\r\n?/', $file);
		$first_notes = 0;
		$eof = 0;
		$eof_found = false;
		// Loop through lines, find when the notes start
		foreach ($file_lines as $line) {
			$first_notes++;
			$txt = $line;
			$re1='(\\N)';$re2='(\\N)';$re3='(\\N)';$re4='(\\N)';
			if ($c=preg_match("/".$re1.$re2.$re3.$re4."/is", $txt, $matches)) {
				if (strlen($line) == 4)
			  		break;
			}
		}
		$first_notes--;
		// ; denotes end of file/difficulty, cut after we find the first
		foreach ($file_lines as $line) {
			$eof++;
			if ($line == ";") {
				$eof_found = true;
				break;
			}
		}
		// Slice the array and replace holds, mines, and rolls
		$file_starting_at_notes = array_slice($file_lines, $first_notes, $eof - $first_notes - 1);
		foreach ($file_starting_at_notes as &$line) {
			$line = str_replace("M", "0", $line);
			$line = str_replace("4", "2", $line);
			$line = str_replace("3", "0", $line);
		}
		return $file_starting_at_notes;
	}
	protected function _process_notes($notes) {
		// Input expected is the output from file_notes_only()
		$file = array();
		$measure = array();
		foreach ($notes as $line) {
			// ", // measure #"
			$re1='(,)';	# Any Single Character 1
			$re2='(\\s+)';	# White Space 1
			$re3='(\\/)';	# Any Single Character 2
			$re4='(\\/)';	# Any Single Character 3
			$re5='(\\s+)';	# White Space 2
			$re6='((?:[a-z][a-z]+))';	# Word 1
			$re7='(\\s+)';	# White Space 3
			$re8='(\\d+)';	# Integer Number 1
			if ($line == "," || $c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5.$re6.$re7.$re8."/is", $line, $matches)) {
				array_push($file, $measure);
				$measure = array();
				continue;
			}
			$note_array = str_split($line);
			foreach ($note_array as &$val) {
				$val = intval($val);
			}
			$note_array['distance_from_previous_notes'] = 0.00;
			$note_array['time_from_beginning_of_file'] = 0.00;
			array_push($measure, $note_array);
		}
		$beats = array();
		foreach ($file as $measure) {
			$beat = array_chunk($measure, (count($measure) / 4));
			$beats = array_merge($beats, $beat);
		}
		$enumerated = array();
		$beat_count = 0;
		foreach ($beats as $beat) {
			$sub_beat_count = count($beat);
			$i = 0;
			foreach ($beat as $noteline) {
				$beatval = strval($beat_count + ($i / $sub_beat_count));
				$enumerated[$beatval] = $noteline;
				$i++;
			}
			$beat_count++;
		}
		$return['measures'] = $file;
		$return['beats'] = $beats;
		$return['enumerated'] = $enumerated;
		return $return;
	}
	protected function _fill_distances($enumerated_beats, $bpm_changes, $stops, $rate) {
		$current_bpm = $bpm_changes[0]['bpm'];
		$current_time = 0;
		$current_beat = 0;
		$last_left_s = null;
		$last_down_s = null;
		$last_up_s = null;
		$last_right_s = null;
		$last_left_hand = null;
		$last_right_hand = null;
		$bpm_pointer = 1;
		$stop_pointer = 0;
		$filtered = array();
		foreach ($enumerated_beats as $beat => $line) {
			$filtered[$beat]['measure'] = floor(floor($beat) / 4);
			$filtered[$beat]['beat'] = floor($beat);
			$filtered[$beat]['bpm'] = "";
			$filtered[$beat]['note_denomination'] = $this->_float2rat(($beat - floor($beat)))[1];
			$filtered[$beat]['cumulative_time'] = 0;
			$filtered[$beat]['ms_interval_row'] = 0;
			$filtered[$beat]['left'] = $line[0];
			$filtered[$beat]['down'] = $line[1];
			$filtered[$beat]['up'] = $line[2];
			$filtered[$beat]['right'] = $line[3];
			$filtered[$beat]['left_time_from_beg'] = null;
			$filtered[$beat]['down_time_from_beg'] = null;
			$filtered[$beat]['up_time_from_beg'] = null;
			$filtered[$beat]['right_time_from_beg'] = null;
			$filtered[$beat]['left_time_from_last_ms'] = null;
			$filtered[$beat]['down_time_from_last_ms'] = null;
			$filtered[$beat]['up_time_from_last_ms'] = null;
			$filtered[$beat]['right_time_from_last_ms'] = null;
			$filtered[$beat]['left_hand_time_from_last_ms'] = null;
			$filtered[$beat]['right_hand_time_from_last_ms'] = null;
			$filtered[$beat]['tapsleft'] = $line[0] > 0 ? 1 : 0;
			$filtered[$beat]['tapsdown'] = $line[1] > 0 ? 1 : 0;
			$filtered[$beat]['tapsup'] = $line[2] > 0 ? 1 : 0;
			$filtered[$beat]['tapsright'] = $line[3] > 0 ? 1 : 0;
			$filtered[$beat]['tap_type'] = $filtered[$beat]['tapsleft'] + $filtered[$beat]['tapsdown'] + $filtered[$beat]['tapsup'] + $filtered[$beat]['tapsright'];
			$filtered[$beat]['singles'] = $filtered[$beat]['tap_type'] == 1 ? 1 : 0;
			$filtered[$beat]['jumps'] = $filtered[$beat]['tap_type'] == 2 ? 1 : 0;
			$filtered[$beat]['hands'] = $filtered[$beat]['tap_type'] == 3 ? 1 : 0;
			$filtered[$beat]['quads'] = $filtered[$beat]['tap_type'] == 4 ? 1 : 0;
			$filtered[$beat]['lefthandjump'] = ($filtered[$beat]['tapsleft'] == 1 && $filtered[$beat]['tapsdown'] == 1) ? 1 : 0;
			$filtered[$beat]['righthandjump'] = ($filtered[$beat]['tapsup'] == 1 && $filtered[$beat]['tapsright'] == 1) ? 1 : 0;
			$filtered[$beat]['left_freeze'] = $line[0] == 2 ? 1 : 0;
			$filtered[$beat]['down_freeze'] = $line[1] == 2 ? 1 : 0;
			$filtered[$beat]['up_freeze'] = $line[2] == 2 ? 1 : 0;
			$filtered[$beat]['right_freeze'] = $line[3] == 2 ? 1 : 0;
			$filtered[$beat]['left_hand_freezes'] = $filtered[$beat]['left_freeze'] + $filtered[$beat]['down_freeze'];
			$filtered[$beat]['right_hand_freezes'] = $filtered[$beat]['up_freeze'] + $filtered[$beat]['right_freeze'];
			$filtered[$beat]['total_freezes'] = $filtered[$beat]['left_freeze'] + $filtered[$beat]['down_freeze'] + $filtered[$beat]['up_freeze'] + $filtered[$beat]['right_freeze'];
			$filtered[$beat]['points_left_hand'] = ($filtered[$beat]['tapsleft'] == 1 || $filtered[$beat]['tapsdown'] == 1) ? (2 + (6 * $filtered[$beat]['left_hand_freezes'])) : 0;
			$filtered[$beat]['points_right_hand'] = ($filtered[$beat]['tapsup'] == 1 || $filtered[$beat]['tapsright'] == 1) ? (2 + (6 * $filtered[$beat]['right_hand_freezes'])) : 0;
			$filtered[$beat]['points_row'] = ($filtered[$beat]['tap_type'] > 0) ? (2 + (6 * $filtered[$beat]['total_freezes'])) : 0;
			$filtered[$beat]['points_per_note'] = $filtered[$beat]['points_row'] / $filtered[$beat]['tap_type'];
			if (doubleval($beat) == 0) {
				$filtered[$beat]['distance_from_previous_notes'] = null;
				$filtered[$beat]['ms_interval_row'] = null;
				$filtered[$beat]['time_from_beginning_of_file'] = 0;
				$filtered[$beat]['cumulative_time'] = 0;
				$filtered[$beat]['bpm'] = $current_bpm;
				// Times from beginning per column
				$filtered[$beat]['left_time_from_beg'] = $line[0] > 0 ? 0 : null;
				$filtered[$beat]['down_time_from_beg'] = $line[1] > 0 ? 0 : null;
				$filtered[$beat]['up_time_from_beg'] = $line[2] > 0 ? 0 : null;
				$filtered[$beat]['right_time_from_beg'] = $line[3] > 0 ? 0 : null;
				// Times from last column, per column
				$last_left_s = $line[0] > 0 ? 0 : null;
				$last_down_s = $line[1] > 0 ? 0 : null;
				$last_up_s = $line[2] > 0 ? 0 : null;
				$last_right_s = $line[3] > 0 ? 0 : null;
				$filtered[$beat]['left_time_from_last_ms'] = $line[0] > 0 ? 0 : null;
				$filtered[$beat]['down_time_from_last_ms'] = $line[1] > 0 ? 0 : null;
				$filtered[$beat]['up_time_from_last_ms'] = $line[2] > 0 ? 0 : null;
				$filtered[$beat]['right_time_from_last_ms'] = $line[3] > 0 ? 0 : null;
				// Times from last hand (arrows on left/right, not 3note hands), per hand
				$last_left_hand = ($line[0] > 0 || $line[1] > 0) ? 0 : null;
				$last_right_hand = ($line[2] > 0 || $line[3] > 0) ? 0 : null;
				$filtered[$beat]['left_hand_time_from_last_ms'] = ($line[0] > 0 || $line[1] > 0) ? 0 : null;
				$filtered[$beat]['right_hand_time_from_last_ms'] = ($line[2] > 0 || $line[3] > 0) ? 0 : null;
			} else {
				$beat_subset = doubleval($beat) - doubleval($current_beat);
				if (doubleval($beat) >= doubleval($bpm_changes[$bpm_pointer]['beat'])) {
					if (array_key_exists($bpm_pointer, $bpm_changes)) {
						$current_bpm = $bpm_changes[$bpm_pointer]['bpm'];
						$bpm_pointer++;
					}
				}
				$stop_time = 0;
				if (doubleval($beat) >= doubleval($stops[$stop_pointer]['beat'])) {
					if (array_key_exists($stop_pointer, $stops)) {
						$stop_time = $stops[$stop_pointer]['stop_duration'] / $rate;
						#echo "applied stop: " . $stops[$stop_pointer]['beat'] . " - " . $stop_time . "<br />";
						$stop_pointer++;
					}
				}
				$beats_per_second = $current_bpm / 60;
				$seconds_per_beat_subset = ($beat_subset / $beats_per_second) / $rate;
				$filtered[$beat]['distance_from_previous_notes'] = $seconds_per_beat_subset;
				$filtered[$beat]['ms_interval_row'] = $seconds_per_beat_subset;
				$filtered[$beat]['bpm'] = $current_bpm;
				$current_time += $seconds_per_beat_subset + $stop_time;
				$filtered[$beat]['time_from_beginning_of_file'] = $current_time;
				$filtered[$beat]['cumulative_time'] = $current_time;
				// Times from beginning per column
				$filtered[$beat]['left_time_from_beg'] = $line[0] > 0 ? $current_time : null;
				$filtered[$beat]['down_time_from_beg'] = $line[1] > 0 ? $current_time : null;
				$filtered[$beat]['up_time_from_beg'] = $line[2] > 0 ? $current_time : null;
				$filtered[$beat]['right_time_from_beg'] = $line[3] > 0 ? $current_time : null;
				// Times from last column, per column
				$filtered[$beat]['left_time_from_last_ms'] = $line[0] > 0 ? (($current_time - $last_left_s) * 1000) : null;
				$filtered[$beat]['down_time_from_last_ms'] = $line[1] > 0 ? (($current_time - $last_down_s) * 1000) : null;
				$filtered[$beat]['up_time_from_last_ms'] = $line[2] > 0 ? (($current_time  - $last_up_s) * 1000) : null;
				$filtered[$beat]['right_time_from_last_ms'] = $line[3] > 0 ? (($current_time - $last_right_s) * 1000) : null;
				$last_left_s = $line[0] > 0 ? $current_time : $last_left_s;
				$last_down_s = $line[1] > 0 ? $current_time : $last_down_s;
				$last_up_s = $line[2] > 0 ? $current_time : $last_up_s;
				$last_right_s = $line[3] > 0 ? $current_time : $last_right_s;
				// Times from last hand (arrows on left/right, not 3note hands), per hand
				$filtered[$beat]['left_hand_time_from_last_ms'] = ($line[0] > 0 || $line[1] > 0) ? (($current_time - $last_left_hand) * 1000) : null;
				$filtered[$beat]['right_hand_time_from_last_ms'] = ($line[2] > 0 || $line[3] > 0) ? (($current_time - $last_right_hand) * 1000) : null;
				$last_left_hand = ($line[0] > 0 || $line[1] > 0) ? $current_time : $last_left_hand;
				$last_right_hand = ($line[2] > 0 || $line[3] > 0) ? $current_time : $last_right_hand;
			}
			$current_beat = $beat;
		}
		return $filtered;
	}
	protected function _standard_deviation($sample){
		if(is_array($sample)){
			$mean = array_sum($sample) / count($sample);
			foreach($sample as $key => $num) $devs[$key] = pow($num - $mean, 2);
			return sqrt(array_sum($devs) / (count($devs) - 1));
		}
	}
	// this function is retarded
	protected function _get_column_distributions($enumerated_beats_with_timing, $interval, $avg_relevant_nps, $hand_factor_weight, $anchor_index_weight, $one_hand_index_weight) {
		// some calculation thing
		$distance_factor = 16;
		$calculated_outliers = pow($avg_relevant_nps, -1) * $distance_factor * 1000;
		$current_second = 0;
		$removed_outliers = 0;
		$next_break = $interval;
		$return = array();
		$current_interval['taps'] = 0;
		$current_interval['notes'] = 0;
		$current_interval['left'] = 0;
		$current_interval['down'] = 0;
		$current_interval['up'] = 0;
		$current_interval['right'] = 0;
		$current_interval['singles'] = 0;
		$current_interval['jumps'] = 0;
		$current_interval['hands'] = 0;
		$current_interval['quads'] = 0;
		$current_interval['points'] = 0;
		$current_interval['left_hand_jumps'] = 0;
		$current_interval['right_hand_jumps'] = 0;
		$current_interval['left_hand_taps'] = 0;
		$current_interval['right_hand_taps'] = 0;
		$current_interval['split_hand_density'] = 0;
		$timings['left'] = array();
		$timings['down'] = array();
		$timings['up'] = array();
		$timings['right'] = array();
		$timings_hands['left'] = array();
		$timings_hands['right'] = array();
		$last['left'] = 0;
		$last['down'] = 0;
		$last['up'] = 0;
		$last['right'] = 0;
		$iteration_notes = 0;
		$left_hand_notes = 0;
		$right_hand_notes = 0;
		$previous_note['left'] = false;
		$previous_note['down'] = false;
		$previous_note['up'] = false;
		$previous_note['right'] = false;
		$jack_density = 0;
		$current_interval['interval_type'] = "undetermined";
		// This is just me being lazy
		$simple_difficulty_array = array();

		foreach ($enumerated_beats_with_timing as $beat => $noteline) {
			if ($current_second >= $next_break) {
				$next_break += $interval;
				// this is standard deviation for the whole section's note counts
				$standard_deviation = $this->_standard_deviation($current_interval);
				$mean = ($current_interval['left'] + $current_interval['down'] + $current_interval['up'] + $current_interval['right']) / 4;
				$current_interval['nps'] = $iteration_notes / $interval;
				$current_interval['standard_deviation'] = $standard_deviation;
				$current_interval['coefficient_of_variation'] = ($standard_deviation / $mean);
				$current_interval['calculated_outliers'] = $calculated_outliers;
				/*
				foreach ($timings['left'] as $key => $val) {
					if ($val >= $calculated_outliers) {
						unset($timings['left'][$key]);
						$removed_outliers++;
					}
				}
				foreach ($timings['down'] as $key => $val) {
					if ($val >= $calculated_outliers) {
						unset($timings['down'][$key]);
						$removed_outliers++;
					}
				}
				foreach ($timings['up'] as $key => $val) {
					if ($val >= $calculated_outliers) {
						unset($timings['up'][$key]);
						$removed_outliers++;
					}
				}
				foreach ($timings['right'] as $key => $val) {
					if ($val >= $calculated_outliers) {
						unset($timings['right'][$key]);
						$removed_outliers++;
					}
				}
				*/
				/*
				foreach ($timings_hands['left'] as $key => $val) {
					if ($val >= $calculated_outliers) {
						unset($timings_hands['left'][$key]);
					}
				}
				foreach ($timings_hands['right'] as $key => $val) {
					if ($val >= $calculated_outliers) {
						unset($timings_hands['right'][$key]);
					}
				}
				*/
				$current_interval['removed_outliers'] = $removed_outliers;
				$total_ms_timings = array_merge($timings['left'], $timings['down'], $timings['up'], $timings['right']);
				$total_ms_deviation = $this->_standard_deviation($total_ms_timings);
				$total_ms_cv = $total_ms_deviation / (array_sum($total_ms_timings) / count($total_ms_timings));
				$current_interval['column_stats']['standard_deviation'] = $total_ms_deviation;
				$current_interval['column_stats']['coefficient_of_variation'] = $total_ms_cv;
				$current_interval['column_stats']['left']['standard_deviation'] = $this->_standard_deviation($timings['left']);
				$current_interval['column_stats']['down']['standard_deviation'] = $this->_standard_deviation($timings['down']);
				$current_interval['column_stats']['up']['standard_deviation'] = $this->_standard_deviation($timings['up']);
				$current_interval['column_stats']['right']['standard_deviation'] = $this->_standard_deviation($timings['right']);
				$mean = array_sum($timings['left']) / count($timings['left']);
				$current_interval['column_stats']['left']['coefficient_of_variation'] = $current_interval['column_stats']['left']['standard_deviation'] / $mean;
				$mean = array_sum($timings['down']) / count($timings['down']);
				$current_interval['column_stats']['down']['coefficient_of_variation'] = $current_interval['column_stats']['down']['standard_deviation'] / $mean;
				$mean = array_sum($timings['up']) / count($timings['up']);
				$current_interval['column_stats']['up']['coefficient_of_variation'] = $current_interval['column_stats']['up']['standard_deviation'] / $mean;
				$mean = array_sum($timings['right']) / count($timings['right']);
				$current_interval['column_stats']['right']['coefficient_of_variation'] = $current_interval['column_stats']['right']['standard_deviation'] / $mean;
				$current_interval['left_hand_density'] = round($left_hand_notes / $iteration_notes, 3);
				$current_interval['right_hand_density'] = round($right_hand_notes / $iteration_notes, 3);
				$current_interval['hand_deviation'] = $this->_standard_deviation(array($current_interval['left_hand_density'], $current_interval['right_hand_density']));
				$hand_deviation = $current_interval['hand_deviation'] == 0 ? 0.1 : $current_interval['hand_deviation'];
				$current_interval['weighted_jack_density'] = $jack_density / $interval;
				$current_interval['timings']['left'] = $timings['left'];
				$current_interval['timings']['down'] = $timings['down'];
				$current_interval['timings']['up'] = $timings['up'];
				$current_interval['timings']['right'] = $timings['right'];
				$current_interval['timings']['left_hand'] = $timings_hands['left'];
				$current_interval['timings']['right_hand'] = $timings_hands['right'];
				$current_interval['single_density_notes'] = $current_interval['singles'] / $current_interval['notes'];
				$current_interval['single_density_taps'] = $current_interval['singles'] / $current_interval['taps'];
				$current_interval['jump_density_notes'] = 2 * $current_interval['jumps'] / $current_interval['notes'];
				$current_interval['jump_density_taps'] = 2 * $current_interval['jumps'] / $current_interval['taps'];
				$current_interval['hand_density_notes'] = 3 * $current_interval['hands'] / $current_interval['notes'];
				$current_interval['hand_density_taps'] = 3 * $current_interval['hands'] / $current_interval['taps'];
				$current_interval['quad_density_notes'] = 4 * $current_interval['quads'] / $current_interval['notes'];
				$current_interval['quad_density_taps'] = 4 * $current_interval['quads'] / $current_interval['taps'];
				$sum_of_cv = $current_interval['coefficient_of_variation'] +
				$current_interval['column_stats']['coefficient_of_variation'] +
				$current_interval['column_stats']['left']['coefficient_of_variation'] +
				$current_interval['column_stats']['down']['coefficient_of_variation'] +
				$current_interval['column_stats']['up']['coefficient_of_variation'] +
				$current_interval['column_stats']['right']['coefficient_of_variation'];
				$current_interval['cv_sum'] = $sum_of_cv;
				$standard_deviation_left_hand = $this->_standard_deviation(array_merge($timings['left'], $timings['down']));
				$standard_deviation_right_hand = $this->_standard_deviation(array_merge($timings['up'], $timings['right']));
				$mean_left_hand = (array_sum($timings['left']) + array_sum($timings['down'])) / (count($timings['left']) + count($timings['down']));
				$mean_right_hand = (array_sum($timings['up']) + array_sum($timings['right'])) / (count($timings['up']) + count($timings['right']));
				$cv_left_hand = $standard_deviation_left_hand / $mean_left_hand;
				$cv_right_hand = $standard_deviation_right_hand / $mean_right_hand;
				$current_interval['cv_left_hand'] = $cv_left_hand;
				$current_interval['cv_right_hand'] = $cv_right_hand;
				$nps['left'] = $current_interval['left'] / $interval;
				$nps['down'] = $current_interval['down'] / $interval;
				$nps['up'] = $current_interval['up'] / $interval;
				$nps['right'] = $current_interval['right'] / $interval;
				$current_interval['finger_nps'] = $nps;
				$left_distr_avg = array_sum($timings['left']) / count($timings['left']) / 2;
				$down_distr_avg = array_sum($timings['down']) / count($timings['down']) / 2;
				$up_distr_avg = array_sum($timings['up']) / count($timings['up']) / 2;
				$right_distr_avg = array_sum($timings['right']) / count($timings['right']) / 2;
				$current_interval['left_hand_jack'] = max($current_interval['left'], $current_interval['down']) / $current_interval['left_hand_notes'];
				$current_interval['right_hand_jack'] = max($current_interval['up'], $current_interval['right']) / $current_interval['right_hand_notes'];
				$cvleftarrow  = $current_interval['column_stats']['left']['coefficient_of_variation'];
				$cvdownarrow  = $current_interval['column_stats']['down']['coefficient_of_variation'];
				$cvuparrow    = $current_interval['column_stats']['up']['coefficient_of_variation'];
				$cvrightarrow = $current_interval['column_stats']['right']['coefficient_of_variation'];




				$max_finger = max($nps);
				// Difficulty info nonsense
				// Multiplier range = 1-4
				// roll is our base multiplier, all other patterns should be higher than that
				// first we'll factor hand bias
				$hand_coefficient = $current_interval['coefficient_of_variation'];
				$hand_factor = (pow((0.88 * $hand_coefficient), 2) - (0.26 * $hand_coefficient) + 0.0838) + 1;
				$current_interval['hand_factor'] = $hand_factor;
				$anchor_index = $max_finger / (array_sum($nps) / count($nps));
				$current_interval['anchor_index'] = $anchor_index;
				$one_hand_index_left = $cv_left_hand * (count($timings['left']) + count($timings['down'])) / count($total_ms_timings);
				$one_hand_index_right = $cv_right_hand * (count($timings['up']) + count($timings['right'])) / count($total_ms_timings);
				$one_hand_index_left += 1;
				$one_hand_index_right += 1;
				$current_interval['one_hand_index_left'] = $one_hand_index_left;
				$current_interval['one_hand_index_right'] = $one_hand_index_right;
				$avg_left_hand_timings = array_sum($timings_hands['left']) / count($timings_hands['left']);
				$avg_right_hand_timings = array_sum($timings_hands['right']) / count($timings_hands['right']);
				$current_interval['avg_left_hand_timings'] = $avg_left_hand_timings;
				$current_interval['avg_right_hand_timings'] = $avg_right_hand_timings;
				$left_merged = array_merge($timings['left'], $timings['down']);
				$right_merged = array_merge($timings['up'], $timings['right']);
				$left_merged_avg = array_sum($left_merged) / count($left_merged);
				$right_merged_avg = array_sum($right_merged) / count($right_merged);
				$left_hand_jump_index = $left_merged_avg / $avg_left_hand_timings;
				$right_hand_jump_index = $right_merged_avg / $avg_right_hand_timings;
				$current_interval['left_hand_jump_index'] = $left_hand_jump_index;
				$current_interval['right_hand_jump_index'] = $right_hand_jump_index;
				#$current_interval['nps_factored_with_pattern_analysis'] = ($hand_factor * $hand_factor_weight) * ($anchor_index * $anchor_index_weight) * ($one_hand_index_left * $one_hand_index_weight) * ($one_hand_index_right * $one_hand_index_weight);

				// hand mod strikes, adding in additional weight for jumps
				$current_interval['left_mod_strikes'] = ($current_interval['left_hand_taps'] + ($current_interval['left_hand_jumps'] * 0.5)) * (1/(pow($current_interval['cv_left_hand'], -0.04)));
				$current_interval['right_mod_strikes'] = ($current_interval['right_hand_taps'] + ($current_interval['right_hand_jumps'] * 0.5)) * (1/(pow($current_interval['cv_right_hand'], -0.04)));


				// "if this file was evenly difficult on both hands for the whole file, taking all the hardest hands, how many strikes per second would it be"
				$current_interval['max_mod_strikes'] = max($current_interval['left_mod_strikes'], $current_interval['right_mod_strikes']);
				$current_interval['nps_factored_with_pattern_analysis'] = $current_interval['max_mod_strikes'];
				if (is_infinite($current_interval['nps_factored_with_pattern_analysis']))
					$current_interval['nps_factored_with_pattern_analysis'] = 0;
				$current_interval['expected_difficulty'] = $current_interval['max_mod_strikes'] + ($current_interval['split_hand_density'] * 0.1);



				array_push($return, $current_interval);
				// Reset the interval specific variables
				$current_interval = array();
				$current_interval['taps'] = 0;
				$current_interval['notes'] = 0;
				$current_interval['left'] = 0;
				$current_interval['down'] = 0;
				$current_interval['up'] = 0;
				$current_interval['right'] = 0;
				$current_interval['singles'] = 0;
				$current_interval['jumps'] = 0;
				$current_interval['hands'] = 0;
				$current_interval['quads'] = 0;
				$current_interval['points'] = 0;
				$current_interval['left_hand_jumps'] = 0;
				$current_interval['right_hand_jumps'] = 0;
				$current_interval['left_hand_taps'] = 0;
				$current_interval['right_hand_taps'] = 0;
				$iteration_notes = 0;
				$left_hand_notes = 0;
				$right_hand_notes = 0;
				$jack_density = 0;
				$current_interval['split_hand_density'] = 0;
				$previous_note['left'] = false;
				$previous_note['down'] = false;
				$previous_note['up'] = false;
				$previous_note['right'] = false;
				$timings['left'] = array();
				$timings['down'] = array();
				$timings['up'] = array();
				$timings['right'] = array();
				$timings_hands['left'] = array();
				$timings_hands['right'] = array();
				$current_interval['interval_type'] = "undetermined";
			}
			$current_distance = $noteline['distance_from_previous_notes'];
			$last_left_copy = $last['left'];
			$last_down_copy = $last['down'];
			$last_up_copy = $last['up'];
			$last_right_copy = $last['right'];
			if ($noteline['left'] >= 1) {
				$current_interval['left']++;
				$current_interval['notes']++;
				$left_hand_notes++;
				$iteration_notes++;
				if ($previous_note['left'])
					$jack_density++;
				$previous_note['left'] = true;
				$current_timing = $current_second - $last['left'];
				array_push($timings['left'], ($current_timing * 1000));
				$last['left'] = $current_second;
			} else {
				$previous_note['left'] = false;
			}
			if ($noteline['down'] >= 1) {
				$current_interval['down']++;
				$current_interval['notes']++;
				$left_hand_notes++;
				$iteration_notes++;
				if ($previous_note['down'])
					$jack_density++;
				$previous_note['down'] = true;
				$current_timing = $current_second - $last['down'];
				array_push($timings['down'], ($current_timing * 1000));
				$last['down'] = $current_second;
			} else {
				$previous_note['down'] = false;
			}
			if ($noteline['up'] >= 1) {
				$current_interval['up']++;
				$current_interval['notes']++;
				$right_hand_notes++;
				$iteration_notes++;
				if ($previous_note['up'])
					$jack_density++;
				$previous_note['up'] = true;
				$current_timing = $current_second - $last['up'];
				array_push($timings['up'], ($current_timing * 1000));
				$last['up'] = $current_second;
			} else {
				$previous_note['up'] = false;
			}
			if ($noteline['right'] >= 1) {
				$current_interval['right']++;
				$current_interval['notes']++;
				$right_hand_notes++;
				$iteration_notes++;
				if ($previous_note['right'])
					$jack_density++;
				$previous_note['right'] = true;
				$current_timing = $current_second - $last['right'];
				array_push($timings['right'], ($current_timing * 1000));
				$last['right'] = $current_second;
			} else {
				$previous_note['right'] = false;
			}
			if ($noteline['tap_type'] > 0) {
				$current_interval['taps']++;
			}
			if ($noteline['left'] >= 1 || $noteline['down'] >= 1) {
				$current_interval['left_hand_taps']++;
			}
			if ($noteline['right'] >= 1 || $noteline['up'] >= 1) {
				$current_interval['right_hand_taps']++;
			}
			if ($noteline['tap_type'] == 1) {
				$current_interval['singles']++;
			} else if ($noteline['tap_type'] == 2) {
				$current_interval['jumps']++;
			} else if ($noteline['tap_type'] == 3) {
				$current_interval['hands']++;
			} else if ($noteline['tap_type'] == 4) {
				$current_interval['quads']++;
			}
			if ($noteline['lefthandjump'] == 1) {
				$current_interval['left_hand_jumps']++;
			}
			if ($noteline['righthandjump'] == 1) {
				$current_interval['right_hand_jumps']++;
			}

			// split_hand_density
			if (($noteline['left'] >= 1 && $noteline['up'] >=1) ||
				($noteline['left'] >= 1 && $noteline['right'] >=1) ||
				($noteline['down'] >= 1 && $noteline['up'] >=1) ||
				($noteline['down'] >= 1 && $noteline['right'] >=1)) {
				$current_interval['split_hand_density']++;
			}

			if ($last['left'] == $current_second || $last['down'] == $current_second) {
				$last_left_hand = $current_second - max(array($last_left_copy, $last_down_copy));
				array_push($timings_hands['left'], ($last_left_hand * 1000));
			}
			if ($last['up'] == $current_second || $last['right'] == $current_second) {
				$last_right_hand = $current_second - max(array($last_up_copy, $last_right_copy));
				array_push($timings_hands['right'], ($last_right_hand * 1000));
			}
			$current_interval['points'] += $noteline['points_row'];
			$current_second = $noteline['time_from_beginning_of_file'];
		}
		return $return;
	}
	protected function _get_average_difficulty_adjustment($interval_array) {
		$total = 0;
		$count = 0;
		foreach ($interval_array as $key => $val) {
			$total += $val['nps_factored_with_pattern_analysis'];
			$count++;
		}
		$calculated = $total / $count;
		return ($calculated);
	}
	protected function _generate_nps_distribution_array($enumerated_beats_with_timing) {
		$current_second = 0;
		$nps_array = array();
		foreach ($enumerated_beats_with_timing as $line) {
			if ($line['time_from_beginning_of_file'] > $current_second)
				$current_second++;
			$notes_this_line = intval($line['left']) + intval($line['down']) + intval($line['up']) + intval($line['right']);
			$nps_array[$current_second] += $notes_this_line;
		}
		while (end($nps_array) == 0) {
			array_pop($nps_array);
		}
		return $nps_array;
	}
	protected function _get_stamina_multiplier($nps_graph_array, $percentage_relevant_distributions_floor, $relevant_sections_stamina_factor, $trivial_sections_stamina_factor) {
		$running_total_factor = 1;
		foreach ($nps_graph_array as $val) {
			if ($val >= $percentage_relevant_distributions_floor) {
				$running_total_factor *= $relevant_sections_stamina_factor;
			} else {
				$running_total_factor /= $trivial_sections_stamina_factor;
			}
			#echo $running_total_factor . "<br />";
			if ($running_total_factor < 1)
				$running_total_factor = 1;
		}
		return $running_total_factor;
	}
	protected function _get_nps_distributions($nps_distributions, $max_nps) {
		$distributions = array();
		for ($i = 0; $i <= $max_nps; $i++) {
			$distributions[$i] = 0;
		}
		foreach ($nps_distributions as $nps) {
			$distributions[$nps]++;
		}
		return $distributions;
	}
	protected function _get_nps_baseline($distributions, $length) {
		$max = 0;
		foreach ($distributions as $nps => $dist) {
			if (($dist / $length) > 0.05 && $nps > $max)
				$max = $nps;
		}
		return $max;
	}
	protected function _get_nps_floor_relevant_distributions($nps_distributions, $nps_upper_bound, $factor) {
		$floor = floor($nps_upper_bound * $factor);
		$new_distributions = array();
		foreach ($nps_distributions as $nps => $dist) {
			if ($nps >= $floor && $nps <= $nps_upper_bound)
				$new_distributions[$nps] = $dist;
		}
		return $new_distributions;
	}
	protected function _get_nps_ceil_relevant_distributions($nps_distributions, $nps_upper_bound, $factor) {
		$floor = ceil($nps_upper_bound * $factor);
		$new_distributions = array();
		for ($i = $floor; $i <= $nps_upper_bound; $i++) {
			$new_distributions[$i] = $nps_distributions[$i];
		}
		return $new_distributions;
	}
	protected function _calculate_nontrivial_dp($dance_points, $hold_points, $below_distribution) {
		$free_points = 0;
		foreach ($below_distribution as $nps => $seconds) {
			$free_points += 2 * ($nps * $seconds);
		}
		$dp = $dance_points - $hold_points - $free_points;
		return $dp;
	}
	protected function _get_percentage_relevant_distributions($distributions) {
		$total_notes_in_distribution = 0;
		$total_seconds_in_distribution = 0;
		foreach ($distributions as $nps => $seconds) {
			$total_notes_in_distribution += $nps * $seconds;
			$total_seconds_in_distribution += $seconds;
		}
		return $total_notes_in_distribution / $total_seconds_in_distribution;
	}
	protected function _get_difficult_section_lengths($nps_array, $floor) {
		$sections = array();
		$total_seconds_in_current_section = 0;
		$last_nps = 0;
		foreach ($nps_array as $nps) {
			if ($nps < $floor && $last_nps != 0 && $total_seconds_in_current_section != 0) {
				array_push($sections, $total_seconds_in_current_section);
				$total_seconds_in_current_section = 0;
			}
			if ($nps >= $floor) {
				$total_seconds_in_current_section++;
			}
			$last_nps = $nps;
		}
		return $sections;
	}
	protected function _get_trivial_section_lengths($nps_array, $floor) {
		$sections = array();
		$total_seconds_in_current_section = 0;
		$last_nps = 0;
		foreach ($nps_array as $nps) {
			if ($nps > $floor && $last_nps != 0 && $total_seconds_in_current_section != 0) {
				array_push($sections, $total_seconds_in_current_section);
				$total_seconds_in_current_section = 0;
			}
			if ($nps <= $floor) {
				$total_seconds_in_current_section++;
			}
			$last_nps = $nps;
		}
		return $sections;
	}
	protected function _get_peak_nps($nps_array) {
		$max = 0;
		foreach ($nps_array as $val) {
			if ($val > $max)
				$max = $val;
		}
		return $max;
	}
	protected function _get_average_nps($nps_array) {
		$total_seconds = sizeof($nps_array);
		$total_notes = 0;
		foreach ($nps_array as $val) {
			$total_notes += $val;
		}
		return ($total_notes / $total_seconds);
	}
	protected function _matches_title($text) {
		$re1='(#)';	# Any Single Character 1
		$re2='(TITLE)';	# Word 1
		$re3='(:)';	# Any Single Character 2
		$re4='.*?';	# Non-greedy match on filler
		$re5='(;)';	# Any Single Character 3
		$return = $text;
		if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5."/is", $text, $matches)) {
			$c1=$matches[1][0];
			$word1=$matches[2][0];
			$c2=$matches[3][0];
			$c3=$matches[4][0];
			$return = str_replace($c1, "", $return);
			$return = str_replace($word1, "", $return);
			$return = str_replace($c2, "", $return);
			$return = str_replace($c3, "", $return);
			return $return;
		} else {
			return false;
		}
	}
	protected function _matches_subtitle($text) {
		$re1='(#)';	# Any Single Character 1
		$re2='(SUBTITLE)';	# Word 1
		$re3='(:)';	# Any Single Character 2
		$re4='.*?';	# Non-greedy match on filler
		$re5='(;)';	# Any Single Character 3
		$return = $text;
		if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5."/is", $text, $matches)) {
			$c1=$matches[1][0];
			$word1=$matches[2][0];
			$c2=$matches[3][0];
			$c3=$matches[4][0];
			$return = str_replace($c1, "", $return);
			$return = str_replace($word1, "", $return);
			$return = str_replace($c2, "", $return);
			$return = str_replace($c3, "", $return);
			return $return;
		} else {
			return false;
		}
	}
	protected function _matches_artist($text) {
		$re1='(#)';	# Any Single Character 1
		$re2='(ARTIST)';	# Word 1
		$re3='(:)';	# Any Single Character 2
		$re4='.*?';	# Non-greedy match on filler
		$re5='(;)';	# Any Single Character 3
		$return = $text;
		if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5."/is", $text, $matches)) {
			$c1=$matches[1][0];
			$word1=$matches[2][0];
			$c2=$matches[3][0];
			$c3=$matches[4][0];
			$return = str_replace($c1, "", $return);
			$return = str_replace($word1, "", $return);
			$return = str_replace($c2, "", $return);
			$return = str_replace($c3, "", $return);
			return $return;
		} else {
			return false;
		}
	}
	protected function _matches_bpms($text) {
		$re1='(#)';	# Any Single Character 1
		$re2='(BPMS)';	# Word 1
		$re3='(:)';	# Any Single Character 2
		$re4='.*?';	# Non-greedy match on filler
		$return = $text;
		if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4."/is", $text, $matches)) {
			$c1=$matches[1][0];
			$word1=$matches[2][0];
			$c2=$matches[3][0];
			$c3=$matches[4][0];
			$return = str_replace($c1, "", $return);
			$return = str_replace($word1, "", $return);
			$return = str_replace($c2, "", $return);
			$return = str_replace($c3, "", $return);
			return $return;
		} else {
			return false;
		}
	}
	protected function _parse_bpms($text) {
		$bpms = explode(",", $text);
		foreach ($bpms as &$bpm) {
			if (!empty($bpm)) {
				$bpm = explode("=", $bpm);
				$bpm['beat'] = $bpm[0];
				$bpm['bpm'] = $bpm[1];
				unset($bpm[0]);
				unset($bpm[1]);
			}
		}
		return $bpms;
	}
	protected function _matches_stops($text) {
		$re1='(#)';	# Any Single Character 1
		$re2='(STOPS)';	# Word 1
		$re3='(:)';	# Any Single Character 2
		$re4='.*?';	# Non-greedy match on filler
		$return = $text;
		if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4."/is", $text, $matches)) {
			$c1=$matches[1][0];
			$word1=$matches[2][0];
			$c2=$matches[3][0];
			$c3=$matches[4][0];
			$return = str_replace($c1, "", $return);
			$return = str_replace($word1, "", $return);
			$return = str_replace($c2, "", $return);
			$return = str_replace($c3, "", $return);
			return $return;
		} else {
			return false;
		}
	}
	protected function _parse_stops($text) {
		$stops = explode(",", $text);
		foreach ($stops as &$stop) {
			if (!empty($stop)) {
				$stop = explode("=", $stop);
				$stop['beat'] = $stop[0];
				$stop['stop_duration'] = $stop[1];
				unset($stop[0]);
				unset($stop[1]);
			}
		}
		return $stops;
	}
	protected function _float2rat($n, $tolerance = 1.e-6) {
	    $h1=1; $h2=0;
	    $k1=0; $k2=1;
	    $b = 1/$n;
	    do {
	        $b = 1/$b;
	        $a = floor($b);
	        $aux = $h1; $h1 = $a*$h1+$h2; $h2 = $aux;
	        $aux = $k1; $k1 = $a*$k1+$k2; $k2 = $aux;
	        $b = $b-$a;
	    } while (abs($n-$h1/$k1) > $n*$tolerance);
	    return array($h1, $k1);
	}
	protected function _remove_lower_bound($array, $minimum) {
		$return = array();
		foreach ($array as $val) {
			if ($val > $minimum)
				array_push($return, $val);
		}
		return $return;
	}
	protected function _get_adjusted_max_simple($simple_array) {
		$simple_array = array_filter($simple_array, "round");
		$total = count($simple_array);
		$distributions = array();
		foreach ($simple_array as $rounded_value) {
			$distributions[$rounded_value] = 0;
		}
		foreach ($simple_array as $rounded_value) {
			$distributions[$rounded_value]++;
		}
		ksort($distributions);
		$running_total = 0;
		$baseline = 0;
		while ($running_total / $total < 0.05) {
			end($distributions);
			$baseline = key($distributions);
			$running_total += end($distributions);
			#echo $running_total . "/" . $total . "=" . ($running_total/$total) . "<br />";
			array_pop($distributions);
		}
		return $baseline;
	}
	// returns an average with heavily weighted outliers
	protected function _factor_outliers_simple($simple_array_removed_irrelevant, $baseline) {
		$simple_array = array_filter($simple_array_removed_irrelevant, "round");
		$total = count($simple_array);
		$distributions = array();
		foreach ($simple_array as $rounded_value) {
			$distributions[$rounded_value] = 0;
		}
		foreach ($simple_array as $rounded_value) {
			$distributions[$rounded_value]++;
		}
		$weighted_array = array();
		$total_freq = 0;
		$total_diffs = 0;
		foreach ($distributions as $diff => $frequency) {
			if ($diff > $baseline) {
				$weighted_array[$diff] = pow($diff, 1.5) * $frequency;
				$total_freq +=  pow($diff, 1.5) * $frequency;
				$total_diffs += (pow($diff, 1.5) * $frequency) * $diff;
			}
			else {
				$weighted_array[$diff] = $frequency;
				$total_freq += $frequency;
				$total_diffs += $frequency * $diff;
			}
		}
		/* debug
		echo $baseline . "<br />";
		ksort($weighted_array);
		echo "<pre>";
		print_r($weighted_array);
		echo "</pre>";
		*/
		return $total_diffs / $total_freq;
	}

	// NEW CALCULATIONS START HERE
	protected function _get_simple_expected_difficulty_array($column_distributions) {
		$simple_array = array();
		foreach($column_distributions as $val) {
			$interval_array = array();
			$interval_array['expected_difficulty'] = $val['expected_difficulty'];
			$interval_array['dance_points'] = $val['points'];


			array_push($simple_array, $interval_array);
		}
		return $simple_array;
	}

	protected function _get_expected_user_skill_result($simple_expected_difficulty_array, $target_grade, $total_dance_points) {
		// This is a poor man's binary search, I think it has to be this way since we don't have any clue where to actually start.
		// Theoretical max here would be like 29.99, though that would be still only 30 runs to do, versus 2,999.
		// Step uno, +10
		$initial_x = 0;
		$step_x = 10;
		$result_grade = 0;
		$result_x = $this->_test_user_run($simple_expected_difficulty_array, $target_grade, $initial_x, $step_x, $total_dance_points);
		#echo "R1: " . $result_x . "<br />";

		// Step dos, +1
		$initial_x = (($result_x - $step_x) > 0)  ? ($result_x - $step_x) : 0;
		$step_x = 1;
		$result_grade = 0;
		$result_x = $this->_test_user_run($simple_expected_difficulty_array, $target_grade, $initial_x, $step_x, $total_dance_points);
		#echo "R2: " . $result_x . "<br />";

		// Step tres, +.1
		$initial_x = (($result_x - $step_x) > 0)  ? ($result_x - $step_x) : 0;
		$step_x = 0.1;
		$result_grade = 0;
		$result_x = $this->_test_user_run($simple_expected_difficulty_array, $target_grade, $initial_x, $step_x, $total_dance_points);
		#echo "R3: " . $result_x . "<br />";

		// Step final, +.01
		$initial_x = (($result_x - $step_x) > 0)  ? ($result_x - $step_x) : 0;
		$step_x = 0.01;
		$result_grade = 0;
		$result_x = $this->_test_user_run($simple_expected_difficulty_array, $target_grade, $initial_x, $step_x, $total_dance_points);
		#echo "R4: " . $result_x . "<br />";

		return $result_x;
	}

	// return value is total_run_points
	protected function _test_user_run($simple_expected_difficulty_array, $target_grade, $initial_x, $step_x, $total_dance_points) {
		$x = $initial_x;
		$force_quit = 0;
		$result_grade = 0;
		while ($result_grade < $target_grade) {
			if ($force_quit > 10000)
				break;

			$total_run_points = 0;
			foreach ($simple_expected_difficulty_array as $interval) {
				$adj_value = 2 * $interval['expected_difficulty'];
				$run_percent = (pow($x / $adj_value, 4));

				if ($run_percent > 1)
					$run_percent = 1;

				if ($adj_value == 0) // shit goes wack if this is true, div/0 and whatnot
					$total_run_points += $interval['dance_points'];
				else
					$total_run_points += $run_percent * $interval['dance_points'];

				#echo "RP: " . $total_run_points . "<br />";
			}

			$result_grade = $total_run_points / $total_dance_points;
			#echo "RG: " . $total_run_points . " - " . $total_dance_points . "<br />";
			$force_quit++;

			if ($result_grade < $target_grade)
				$x += $step_x;
		}
		return $x;
	}

	protected function _get_new_stamina_multiplier($simple_expected_difficulty_array, $calculated_difficulty_x) {
		// if difficulty is zero the below algorithm will cause INF, so avoid that..
		if ($calculated_difficulty_x <= 0) {
			$relevant_sections_stamina_factor = 1.005;
			$trivial_sections_stamina_factor = 1.004;
		} else {
			$relevant_sections_stamina_factor = 1.0052 + (1 - (pow($calculated_difficulty_x, -0.0001)));
			$trivial_sections_stamina_factor = 1.005 + (1 - (pow($calculated_difficulty_x, -0.0001))); // - (1 - (pow($calculated_difficulty_x, -0.01)))
		}
		#echo "RSSF: " . $relevant_sections_stamina_factor . "<br />";
		#echo "TSSF: " . $trivial_sections_stamina_factor . "<br />";


		// Need a non-zero average. This should inflate the average to only the actual difficult stuff stamina cares about.
		$expected_difficulties_non_zero = array();
		foreach ($simple_expected_difficulty_array as $interval) {
			if ($interval['expected_difficulty'] > 0)
				array_push($expected_difficulties_non_zero, $interval['expected_difficulty']);
		}
		$average_difficulties_g_zero = array_sum($expected_difficulties_non_zero) / count($expected_difficulties_non_zero);
		#echo $average_difficulties_g_zero . "<br />";


		$new_stamina_difficulties = array();

		$running_factor = 1;
		foreach ($simple_expected_difficulty_array as $interval) {
			if ($interval['expected_difficulty'] > $average_difficulties_g_zero)
				$running_factor  *= $relevant_sections_stamina_factor;
			else
				$running_factor /= $trivial_sections_stamina_factor;

			if ($running_factor < 1)
				$running_factor = 1;
			else if ($running_factor > 1.25)
				$running_factor = 1.25;

			$this_section['expected_difficulty'] = $interval['expected_difficulty'] * $running_factor;
			$this_section['dance_points'] = $interval['dance_points'];

			array_push($new_stamina_difficulties, $this_section);
			#echo $running_factor . "<br />";
		}
		// We need to normalize back down, or this value can get out of hand.
		$running_factor = pow($running_factor, 0.175);
		#echo $running_factor . "<br />";

		#echo "<pre>";
		#print_r($new_stamina_difficulties);
		#echo "</pre>";

		// Max bonus from stamina..
		if ($running_factor > 1.20)
			$running_factor = 1.20;
		return $new_stamina_difficulties;
	}

	protected function _process_everything($f = null, $r = null, $user_score_goal = null) {
		if (!empty($f) && !empty($r)) {
			$file = trim($f);
			$rate = doubleval($r);
		} else {
			$file = trim($this->input->post('file'));
			$rate = doubleval($this->input->post('rate'));
		}
		// Count charts, set up error
		$number_of_charts = substr_count($file, "#NOTES:");
		$this->data['number_of_charts'] = $number_of_charts;
		$this->data['num_charts_error'] = false;
		if ($number_of_charts > 1)
			$this->data['num_charts_error'] = true;
		$this->data['file'] = $file;
		$this->data['rate'] = $rate;
		// Some values we can tweak around as needed
		$factor = 0.7;
		$this->data['factor'] = $factor;
		$interval_factor = 0.7;
		$this->data['interval_factor'] = $interval_factor;
		$division_factor = 0.28;
		$this->data['division_factor'] = $division_factor;
		$grade_factor = 0.93;
		$this->data['grade_factor'] = $grade_factor;
		$hand_factor_weight = 1;
		$this->data['hand_factor_weight'] = $hand_factor_weight;
		$anchor_index_weight = 1;
		$this->data['anchor_index_weight'] = $anchor_index_weight;
		$one_hand_index_weight = 1;
		$this->data['one_hand_index_weight'] = $one_hand_index_weight;
		$pattern_factor = 1;
		$this->data['pattern_factor'] = $pattern_factor;
		$stamina_factor = 0.6;
		$this->data['stamina_factor'] = $stamina_factor;
		$cspeed_factor = 1400;
		$this->data['cspeed_factor'] = $cspeed_factor;
		$arrow_pixel_offset = 400;
		$this->data['arrow_pixel_offset'] = $arrow_pixel_offset;
		$show_tests = false;
		if ($_GET['show_tests'] == "true")
			$show_tests = true ;
		$this->data['show_tests'] = $show_tests;
		// ---------- PARSE LOGIC STARTS HERE ---------- //
		// Build the file meta data array
		$meta = $this->_retreive_file_meta($file);
		// Iteration #0 - raw notes
		$notes = $this->_file_notes_only($file);
		$this->data['notes'] = $notes;
		// Iteration #1-3 are handled here. I split these out in case we need anything besides enumerated
		// Trailing empty beats/measures are REMOVED
		$processed = $this->_process_notes($notes);
		$this->data['processed'] = $processed;
		// Iteration #4 - filled in distances and timings
		$filled_distances = $this->_fill_distances($processed['enumerated'], $meta['bpms'], $meta['stops'], doubleval($rate));
		$this->data['filled_distances'] = $filled_distances;
		// With that we can determine the length of the file.
		$meta['length_in_seconds'] = intval(end($filled_distances)['time_from_beginning_of_file']);
		$meta['length'] = gmdate("i:s", intval(end($filled_distances)['time_from_beginning_of_file']));
		// Now we can do an NPS Graph Array
		$nps_graph_array = $this->_generate_nps_distribution_array($filled_distances);
		$this->data['nps_graph_array'] = $nps_graph_array;
		// ...and determine some more meta!
		$meta['peak_NPS'] = $this->_get_peak_nps($nps_graph_array);
		$meta['average_NPS'] = $this->_get_average_nps($nps_graph_array);
		$meta['rate'] = $rate . "x";
		// ---------- CALCULATIONS FOR DIFFICULTY BEGIN HERE  ---------- //
		// Build some distributions for more calculations
		$nps_distributions = $this->_get_nps_distributions($nps_graph_array, $meta['peak_NPS']);
		$this->data['nps_distributions'] = $nps_distributions;
		$nps_upper_bound = $this->_get_nps_baseline($nps_distributions, intval(end($filled_distances)['time_from_beginning_of_file']));
		$this->data['nps_upper_bound'] = $nps_upper_bound;
		$nps_floor_distributions = $this->_get_nps_floor_relevant_distributions($nps_distributions, $nps_upper_bound, $factor);
		$this->data['nps_floor_distributions'] = $nps_floor_distributions;
		$nps_ceil_distributions = $this->_get_nps_ceil_relevant_distributions($nps_distributions, $nps_upper_bound, $factor);
		$this->data['nps_ceil_distributions'] = $nps_ceil_distributions;
		// Cut up the distributions
		$below_nps_floor = array_slice($nps_distributions, 0, floor($nps_upper_bound * $factor));
		$this->data['below_nps_floor'] = $below_nps_floor;
		$below_nps_ceil = array_slice($nps_distributions, 0, ceil($nps_upper_bound * $factor));
		$this->data['below_nps_ceil'] = $below_nps_ceil;
		$above_nps_dist = array_slice($nps_distributions, $nps_upper_bound + 1, null, true);
		$this->data['above_nps_dist'] = $above_nps_dist;
		// More meta!
		$meta['nontrivial_DP'] = $this->_calculate_nontrivial_dp($meta['dance_points'], $meta['dance_points_from_holds'], $below_nps_ceil);
		$trivial_dp = $meta['dance_points'] - $meta['nontrivial_DP'];
		$this->data['trivial_dp'] = $trivial_dp;
		$meta['nontrivial_DP_needed_for_AA'] = $meta['dance_points_for_grade_AA'] - $trivial_dp;
		$meta['alotted_misses_ignoring_free_DP'] = floor(($meta['nontrivial_DP'] - floor($meta['nontrivial_DP'] * $grade_factor)) / 10);
		$meta['alotted_misses_NOT_ignoring_free_DP'] = floor(($meta['dance_points'] - floor($meta['dance_points'] * $grade_factor)) / 10);
		$meta['miss_factor_between_these'] = $meta['alotted_misses_NOT_ignoring_free_DP'] / $meta['alotted_misses_ignoring_free_DP'];
		// Weighted percentages
		// TODO: Use this somewhere?
		$weighted_perc = round((100 - (7 * $meta['miss_factor_between_these'])), 2);
		$this->data['weighted_perc'] = $weighted_perc;
		$meta['weighted_AA_metric'] = $weighted_perc . "% (DP: " . ceil($weighted_perc / 100 * $meta['dance_points']) . ")";
		// This is the major Free DP adjustment calculation factor
		$meta['NPS_adjustment_from_free_misses'] = ($meta['dance_points'] - (($meta['alotted_misses_NOT_ignoring_free_DP'] - $meta['alotted_misses_ignoring_free_DP']) * 10)) / $meta['dance_points'];
		// Not used for anything yet.
		$difficult_section_lengths_floor = $this->_get_difficult_section_lengths($nps_graph_array, floor($nps_upper_bound * $stamina_factor));
		$this->data['difficult_section_lengths_floor'] = $difficult_section_lengths_floor;
		$difficult_section_lengths_ceil = $this->_get_difficult_section_lengths($nps_graph_array, ceil($nps_upper_bound * $stamina_factor));
		$this->data['difficult_section_lengths_ceil'] = $difficult_section_lengths_ceil;
		$trivial_section_lengths_floor = $this->_get_trivial_section_lengths($nps_graph_array, floor($nps_upper_bound * $stamina_factor));
		$this->data['trivial_section_lengths_floor'] = $trivial_section_lengths_floor;
		$trivial_section_lengths_ceil = $this->_get_trivial_section_lengths($nps_graph_array, ceil($nps_upper_bound * $stamina_factor));
		$this->data['trivial_section_lengths_ceil'] = $trivial_section_lengths_ceil;
		// Used in the fudged NPS calculation and division adjusted calculation
		$percentage_relevant_distributions_floor = $this->_get_percentage_relevant_distributions($nps_floor_distributions);
		$this->data['percentage_relevant_distributions_floor'] = $percentage_relevant_distributions_floor;
		$percentage_relevant_distributions_ceil = $this->_get_percentage_relevant_distributions($nps_ceil_distributions);
		$this->data['percentage_relevant_distributions_ceil'] = $percentage_relevant_distributions_ceil;
		// Column distributions for pattern analysis
		$avg_nps_factor = ($percentage_relevant_distributions_floor + $percentage_relevant_distributions_ceil) / 2;
		$this->data['avg_nps_factor'] = $avg_nps_factor;
		//$programmatically_derived_interval = pow(($percentage_relevant_distributions_floor + $percentage_relevant_distributions_ceil) / 2, -1) * 24;
		$programmatically_derived_interval = 0.5;
		$this->data['programmatically_derived_interval'] = $programmatically_derived_interval;
		$meta['programmatically_derived_interval'] = $programmatically_derived_interval;
		#$column_distributions_1s = get_column_distributions($filled_distances, 1, $avg_nps_factor);
		#$column_distributions_2s = get_column_distributions($filled_distances, 2, $avg_nps_factor);
		#$column_distributions_500ms = get_column_distributions($filled_distances, 0.50, $avg_nps_factor);
		#$column_distributions_250ms = get_column_distributions($filled_distances, 0.25, $avg_nps_factor);
		$column_distributions_auto = $this->_get_column_distributions($filled_distances, $programmatically_derived_interval, $avg_nps_factor, $hand_factor_weight, $anchor_index_weight, $one_hand_index_weight);
		$this->data['column_distributions_auto'] = $column_distributions_auto;
		$average_difficulty_weight = $this->_get_average_difficulty_adjustment($column_distributions_auto) * $pattern_factor;
		$this->data['average_difficulty_weight'] = $average_difficulty_weight;
		// testing intervals
		#$column_distribution_graphs["0.25"] = $this->_get_column_distributions($filled_distances, $programmatically_derived_interval / 2, $avg_nps_factor, $hand_factor_weight, $anchor_index_weight, $one_hand_index_weight);
		#$column_distribution_graphs["0.5"] = $this->_get_column_distributions($filled_distances, $programmatically_derived_interval * 2, $avg_nps_factor, $hand_factor_weight, $anchor_index_weight, $one_hand_index_weight);
		#$average_difficulty_weight_sections['half_interval'] = $this->_get_average_difficulty_adjustment($column_distribution_graphs["0.25"]) * $pattern_factor;
		$average_difficulty_weight_sections['interval'] = $this->_get_average_difficulty_adjustment($column_distributions_auto) * $pattern_factor;
		#$average_difficulty_weight_sections['double_interval'] = $this->_get_average_difficulty_adjustment($column_distribution_graphs["0.5"]) * $pattern_factor;
		#$average_difficulty_weight_sections['all_3'] = ($average_difficulty_weight_sections['half_interval'] + $average_difficulty_weight_sections['interval'] + $average_difficulty_weight_sections['double_interval']) / 3;
		#$this->data['average_difficulty_weight_sections'] = $average_difficulty_weight_sections;
		$this->data['column_distribution_graphs'] = $column_distribution_graphs;
		#$column_distributions_2s_relevant_only = get_relevant_slices($column_distributions_1s, $nps_graph_array, floor($nps_upper_bound * $factor), 2);
		// Redoing this below


		#$stamina_multiplier = $this->_get_stamina_multiplier($nps_graph_array, $percentage_relevant_distributions_floor, $relevant_sections_stamina_factor, $trivial_sections_stamina_factor);
		#$this->data['stamina_multiplier'] = $stamina_multiplier;
		#$stamina_ratio = array_sum($difficult_section_lengths_floor) / array_sum($trivial_section_lengths_floor);
		#$this->data['stamina_ratio'] = $stamina_ratio;
		#if ($stamina_multiplier < 1)
		#	$stamina_multiplier = 1;
		#$stamina_multiplier *= $stamina_ratio;
		#$stamina_normalization_factor = 0.05;
		#$stamina_multiplier = pow($stamina_multiplier, $stamina_normalization_factor);
		#$stamina_multiplier = $stamina_multiplier < 1 ? 1 : $stamina_multiplier;
		#$this->data['stamina_multiplier'] = $stamina_multiplier;
		#$meta['stamina_multiplier'] = $stamina_multiplier;
		#$meta['stamina_ratio_diff'] = array_sum($difficult_section_lengths_floor);
		#$meta['stamina_ratio_trivial'] = array_sum($trivial_section_lengths_floor);
		#$meta['stamina_ratio'] = $stamina_ratio;
		// turn false to hide test values
		$this->data['NPS_adjustment_from_free_misses'] = $meta['NPS_adjustment_from_free_misses'];
		$this->data['stamina_ratio'] = $meta['stamina_ratio'];
		$this->data['dance_points_from_holds'] = $meta['dance_points_from_holds'];
		// THIS IS THE MAIN THING BEING USED RIGHT NOW

		$fudge = (($percentage_relevant_distributions_floor + $percentage_relevant_distributions_ceil) / 2);

		// fudge is bad, very very bad. lets do something more clever
		// Simple array to start, may help memory load
		$simple_expected_difficulty_array = $this->_get_simple_expected_difficulty_array($column_distributions_auto);

		$calculated_difficulty_x = $this->_get_expected_user_skill_result($simple_expected_difficulty_array, 0.93, $meta['dance_points']);

		$new_stamina_difficulties = $this->_get_new_stamina_multiplier($simple_expected_difficulty_array, $calculated_difficulty_x);


		$this->data['meta'] = $meta;
		$this->data['calculated_difficulty_no_stamina'] = $calculated_difficulty_x * (1 / $programmatically_derived_interval);
		$calculated_difficulty_no_stamina = $this->data['calculated_difficulty_no_stamina'];
		#echo $calculated_difficulty_no_stamina . "<br />";


		$calculated_difficulty_with_stamina = $this->_get_expected_user_skill_result($new_stamina_difficulties, 0.93, $meta['dance_points']);
		$this->data['calculated_difficulty'] = $calculated_difficulty_with_stamina * (1 / $programmatically_derived_interval);
		$calculated_difficulty = $this->data['calculated_difficulty'];
		#echo $calculated_difficulty . "<br />";

		$this->data['new_stamina_difficulties'] = $new_stamina_difficulties;

		$this->content_view = "parser/results";
		return $calculated_difficulty;
	}
}
