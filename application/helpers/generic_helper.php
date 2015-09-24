<?php
function nl2p($string, $line_breaks = true, $xml = true) {
    $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

    // It is conceivable that people might still want single line-breaks
    // without breaking into a new paragraph.
    if ($line_breaks == true)
    return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'), trim($string)).'</p>';
    else
    return '<p>'.preg_replace(
    array("/([\n]{2,})/i", "/([\r\n]{3,})/i","/([^>])\n([^<])/i"),
    array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),

    trim($string)).'</p>';
}

function get_chat_color($user_level) {
    $color = "";
    switch ($user_level) {
        // Admin
        case 3:
            $color = "#DF5A49";
            break;
        // Mod
        case 2:
            $color = "#E27A3F";
            break;
        // Other
        default:
            $color = get_random_color();
    }
    return $color;
}

function get_random_color() {
    $colors = array(
        "#45B29D",
        "#43AC6A",
        "#a0d3e8",
        "#BB3E5E",
        "#408EBF",
        "#893EBB",
        "#4FBB3E",
        "#8DC247",
        "#4E47C2",
        "#C1447E"
    );
    shuffle($colors);
    return $colors[0];
}


function ahrens_moving_average($input_data,	// data to be smoothed
                               $period,		// smoothing window
                               $x_end) {	// end of input data

    //--------------------------------------------------------------
    // create the output array

    $avg = array();

    //--------------------------------------------------------------
    // the AMA may be used to smooth raw data or the output of
    // other calculations. since other calculations may introduce
    // significant phase lag so there is no guarantee that the
    // input data will actually start at the beginning of the
    // array passed to this function. this loop searches for the
    // first actual datapoint in the input series.

    $x_start = 1;
    for ($a = $x_start; $a <= $x_end; $a++) {
        if (isset($input_data[$a]) && is_numeric($input_data[$a])) {
            $x_start = $a;
            break;
        }
    }

    //--------------------------------------------------------------
    // the first raw data point in a series may be (significantly)
    // divergent from the data that follows. this loop creates a
    // reasonably representative set of initial values to seed the
    // average.  it uses an "expanding period" simple moving average
    // over the first N samples (where N equals the period of the
    // average)

    $count = 0;
    $total = 0;
    for ($a = $x_start; $a < $x_start + $period && $a <= $x_end; $a++) {
        $count++;
        $total += $input_data[$a];
        $avg[$a] = $total / $count;
    }

    //--------------------------------------------------------------
    // once the seed values have been calculated, shift gears and
    // calculate the AMA for $x_start + $period through $x_end

    for ($a = $x_start + $period; $a <= $x_end; $a++) {
        $numerator = $input_data[$a] - ($avg[$a-1] + $avg[$a-$period]) / 2;
        $avg[$a]   = $avg[$a-1] + $numerator / $period;
    }

    return $avg;
}

function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

function replace_emotes($string) {
    // Also doing this here cuz yolo
    $string = preg_replace( '@(?<![.*">])\b(?:(?:https?|ftp|file)://|[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i', '<a href="\0" target="_blank">\0</a>', $string );

    $string = str_replace("&gt;C", "<img class='emote' src='/assets/emotes/1431415213293s.jpg' />", $string);
    $string = str_replace("[*Waffles*]", "<img class='emote' src='/assets/emotes/1929599010102s.jpg' />", $string);
    $string = str_replace("SuperShibe", "<img class='emote' src='/assets/emotes/4594815335468s.png' />", $string);
    $string = str_replace("HE", "<img class='emote' src='/assets/emotes/48984948456541s.jpg' />", $string);
    $string = str_replace("Spongecarlton", "<img class='emote' src='/assets/emotes/31813597199175s.jpg' />", $string);
    $string = str_replace("Bearpocalypse", "<img class='emote' src='/assets/emotes/1356136544654s.gif' />", $string);
    $string = str_replace("RTT", "<img class='emote' src='/assets/emotes/183258135987193571s.jpg' />", $string);
    $string = str_replace("Shep", "<img class='emote' src='/assets/emotes/182475183475s.jpg' />", $string);
    return $string;
}

function get_mod_alert_count() {
    $sf_count = Suggested_chart::count(array('conditions' => 'status = "pending"'));
    $ps_count = User_score::count(array('conditions' => 'status = "pending"'));

    $total_count = $sf_count + $ps_count;
    return $total_count;
}

function get_mod_alert_count_pending_scores() {
    $ps_count = User_score::count(array('conditions' => 'status = "pending"'));
    return $ps_count;
}

function get_mod_alert_count_suggested_charts() {
    $sf_count = Suggested_chart::count(array('conditions' => 'status = "pending"'));
    return $sf_count;
}

function write_to_mod_log($message) {
    $logfile = fopen("logs/mod_log.txt", "a");
    $timestamp = date("Y-m-d H:i:s");
    $line_to_write = "[" . $timestamp . "] " . $message . "\n";
    fwrite($logfile, $line_to_write);
    fclose($logfile);
}

function calculate_dp_percent($score) {
    $max_dp_max = $score->total_dance_points;
    $max_dp_achieved = ($score->marvelous_count * 2) + ($score->perfect_count * 2) + ($score->great_count * 1) + ($score->boo_count * -4) + ($score->miss_count * -8) + ($score->ok_count * 6) + ($score->mines_hit * -8);
    return ($max_dp_achieved / $max_dp_max) * 100;
}

function calculate_ex_oni_percent($score) {
        $ex_oni_dp_max = 3 * ($score->file_taps + $score->file_holds);
        $ex_oni_dp_achieved = (3 * ($score->marvelous_count + $score->ok_count)) + (2 * ($score->perfect_count)) + $score->great_count + (-2 * ($score->mines_hit));
        return ($ex_oni_dp_achieved / $ex_oni_dp_max) * 100;
}

function abbreviate_file_type($file_type, $is_stamina) {
   $abbr_file_type = "";
   if ($is_stamina) {
       $abbr_file_type .= "STAM ";
   }
   switch ($file_type) {
       case "speed":
           $abbr_file_type .= "SPD";
           break;
       case "jumpstream":
           $abbr_file_type .= "JS";
           break;
       case "jack":
           $abbr_file_type .= "JACK";
           break;
       case "technical":
           $abbr_file_type .= "TECH";
           break;
   }
   return $abbr_file_type;
}
