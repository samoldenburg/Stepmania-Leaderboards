<?php
    function float2rat($n, $tolerance = 1.e-6) {
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
?>
<div class="row">
    <div class="large-6 columns">
        <h2>File Details</h2>
        <?php if ($num_charts_error) : ?>
            <div data-alert class="alert-box alert">
                The file you entered appears to have <?=$number_of_charts;?> charts. Only the first chart was processed.
            </div>
        <?php endif; ?>
        <?php if (isset($pack)) : ?>
            <p>
                Available in <a href="/packs/view/<?=$pack->id;?>"><?=$pack->name;?></a>.
            </p>
        <?php endif; ?>
        <p>
        	<?php

        		// Hide extra values
        		if (!$show_tests) {
        			unset($meta['nontrivial_DP']);
        			unset($meta['nontrivial_DP_needed_for_AA']);
        			unset($meta['alotted_misses_ignoring_free_DP']);
        			unset($meta['alotted_misses_NOT_ignoring_free_DP']);
        			unset($meta['miss_factor_between_these']);
        			unset($meta['weighted_AA_metric']);
        			unset($meta['programmatically_derived_interval']);
        			unset($meta['stamina_ratio_diff']);
        			unset($meta['stamina_ratio_trivial']);
        			unset($meta['NPS_adjustment_from_free_misses']);
                    unset($meta['dance_points_for_grade_A']);
                    unset($meta['dance_points_for_grade_B']);
                    unset($meta['dance_points_from_holds']);
                    unset($meta['stamina_ratio']);
                    unset($meta['points_lost_stream']);
                    unset($meta['points_lost_jumpstream']);
                    unset($meta['points_lost_jack']);
                    unset($meta['points_lost_undetermined']);
                    unset($meta['points_lost_technical']);
                    unset($meta['points_proportions_lost_stream']);
                    unset($meta['points_proportions_lost_jumpstream']);
                    unset($meta['points_proportions_lost_jack']);
                    unset($meta['points_proportions_lost_undetermined']);
                    unset($meta['points_proportions_lost_technical']);
                    unset($meta['autodetermined_file_type']);

        		}

        		foreach ($meta as $key => $val) : ?>
        		<?php
        			$formatted_key = str_replace("_", " ", $key);
        			if ($key != "bpms" && $key != "stops") : ?>
        				<strong><?=ucwords($formatted_key);?>:</strong> <?=$val;?><br />
        			<?php elseif ($key == "bpms") : ?>
        				<strong>BPMS:</strong>
        				<pre style="font-size: 11px; line-height: 1.2em; max-height: 200px; overflow: auto;"><?php print_r($val);?></pre>
                    <?php elseif ($key == "stops") : ?>
                        <strong>STOPS:</strong>
                        <pre style="font-size: 11px; line-height: 1.2em; max-height: 200px; overflow: auto;"><?php print_r($val);?></pre>
                    <?php endif; ?>
        		<?php endforeach;
        	?>
        </p>
    </div>
    <div class="large-6 columns">
        <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false) : ?>
        <!-- File preview box starts here -->
        <h2>File Preview</h2>
        <p>Use Z, X, N, M, to pretend like you're actually playing the file.</p>
        <div id="file-preview-box">
        	<div id="arrow-mask"></div>
        	<div id="receptors">
        		<i class="receptor-sprite left"></i>
        		<i class="receptor-sprite down"></i>
        		<i class="receptor-sprite up"></i>
        		<i class="receptor-sprite right"></i>
        		<i class="receptor-lights left"></i>
        		<i class="receptor-lights down"></i>
        		<i class="receptor-lights up"></i>
        		<i class="receptor-lights right"></i>
        	</div>

        	<?php
        		$arrow_height = (end($filled_distances)['time_from_beginning_of_file'] * $cspeed_factor) + $arrow_pixel_offset;
        	?>
        	<div id="arrows">
        		<div id="arrow-scroll-box" style="height: <?=$arrow_height;?>px; color: #ffffff;">
        			<?php
        				foreach ($filled_distances as $beat_sub_count => $arrow_line) : ?>
        					<?php


        						$denomination = $beat_sub_count - floor($beat_sub_count);
        						$fraction_parts = float2rat($denomination);

        						$color = "grey";
        						if ($fraction_parts[1] == 1) {
        							$color = "red";
        						}
        						else if ($fraction_parts[1] == 2) {
        							$color = "blue";
        						}
        						else if ($fraction_parts[1] == 3) {
        							$color = "purple";
        						}
        						else if ($fraction_parts[1] == 4) {
        							$color = "yellow";
        						}
        						else if ($fraction_parts[1] == 6) {
        							$color = "pink";
        						}
        						else if ($fraction_parts[1] == 8) {
        							$color = "orange";
        						}
        						else if ($fraction_parts[1] == 12) {
        							$color = "teal";
        						}

        						$top_offset = ($arrow_line['time_from_beginning_of_file'] * $cspeed_factor) + $arrow_pixel_offset;
        						if (intval($arrow_line['left']) == 1) : ?>
        							<i class="arrow left <?=$color;?>" style="top: <?=$top_offset;?>px;"></i>
        						<?php endif;

        						if (intval($arrow_line['down']) == 1) : ?>
        							<i class="arrow down <?=$color;?>" style="top: <?=$top_offset;?>px;"></i>
        						<?php endif;

        						if (intval($arrow_line['up']) == 1) : ?>
        							<i class="arrow up <?=$color;?>" style="top: <?=$top_offset;?>px;"></i>
        						<?php endif;

        						if (intval($arrow_line['right']) == 1) : ?>
        							<i class="arrow right <?=$color;?>" style="top: <?=$top_offset;?>px;"></i>
        						<?php endif;
        					?>

        				<?php endforeach;
        			?>
        		</div>
        	</div>
        </div>
        <script>
        	jQuery(document).ready(function($) {
        		var total_time = <?=end($filled_distances)['time_from_beginning_of_file'];?> * 1000;
        		var total_scroll = <?=$arrow_height;?>;

        		$("#play").click(function() {
        			$("#arrows").animate({
        				scrollTop: total_scroll + "px"
        			}, total_time, 'linear', function() {
        				setTimeout(function() {
        					$("#rewind").click();
        					setTimeout(function() {
        						$("#play").click();
        					}, 1000);
        				}, 5000);
        			});
        		});

        		$("#pause").click(function() {
        			$("#arrows").stop();
        		});

        		setTimeout(function() {
        			$("#play").click();
        		}, 2000);

        		$("#rewind").click(function() {
        			$("#arrows").stop(true);
        			$("#arrows").animate({
        				scrollTop: "0px"
        			}, 1, 'linear');
        		});

        		$(document).keydown(function( event ) {
        			console.log(event.which);
        				if (event.which == 90) {
        					// Z
        					$(".receptor-lights.left").addClass("bright");
        					setTimeout(function() {
        						$(".receptor-lights.left").removeClass("bright");
        					}, 50);
        				}
        				if (event.which == 88) {
        					// X
        					$(".receptor-lights.down").addClass("bright");
        					setTimeout(function() {
        						$(".receptor-lights.down").removeClass("bright");
        					}, 50);
        				}
        				if (event.which == 78) {
        					// N
        					$(".receptor-lights.up").addClass("bright");
        					setTimeout(function() {
        						$(".receptor-lights.up").removeClass("bright");
        					}, 50);
        				}
        				if (event.which == 77) {
        					// M
        					$(".receptor-lights.right").addClass("bright");
        					setTimeout(function() {
        						$(".receptor-lights.right").removeClass("bright");
        					}, 50);
        				}
        			});
        	});
        </script>
        <div id="control-buttons">
            <button id="rewind" class="button"><i class="fi-rewind"></i></button>
            <button id="play" class="button"><i class="fi-play"></i></button>
            <button id="pause" class="button"><i class="fi-pause"></i></button>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($user_scores)) : ?>
    <h3>
        User Scores
    </h3>
    <table id="chart-scores-table">
        <thead>
            <tr>
                <th>
                    User Name
                </th>
                <th>
                    Grade
                </th>
                <th>
                    DP %
                </th>
                <th>
                    EX ONI %
                </th>
                <th>
                    MA
                </th>
                <th>
                    PA
                </th>
                <th>
                    GA
                </th>
                <th>
                    GoA
                </th>
                <th>
                    BA
                </th>
                <th>
                    MC
                </th>
                <th>
                    OK
                </th>
                <th>
                    Mines
                </th>
                <th>
                    Date Achieved
                </th>
                <th>
                    Screenshot
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($user_scores as $score) : ?>
                <?php
                    $max_dp_percent = calculate_dp_percent($score);

                    $grade_earned = "<img src='/assets/img/c.png' />";
                    if ($max_dp_percent > 65) {
                        $grade_earned = "<img src='/assets/img/b.png' />";
                    }
                    if ($max_dp_percent > 80) {
                        $grade_earned = "<img src='/assets/img/a.png' />";
                    }
                    if ($max_dp_percent > 93) {
                        $grade_earned = "<img src='/assets/img/aa.png' />";
                    }
                    if ($max_dp_percent == 100) {
                        $grade_earned = "<img src='/assets/img/aaa.png' />";
                    }
                    if ($max_dp_percent == 100 && $score->perfect_count == 0) {
                        $grade_earned = "<img src='/assets/img/aaaa.png' />";
                    }

                    $ex_oni_percent = calculate_ex_oni_percent($score);
                ?>
                <tr>
                    <td>
                        <a href="/profile/view/<?=$score->username;?>"><?=$score->display_name;?></a>
                    </td>
                    <td>
                        <?=$grade_earned;?>
                    </td>
                    <td>
                        <?=number_format($max_dp_percent, 2);?>%
                    </td>
                    <td>
                        <?=number_format($ex_oni_percent, 2);?>%
                    </td>
                    <td>
                        <?=$score->marvelous_count;?>
                    </td>
                    <td>
                        <?=$score->perfect_count;?>
                    </td>
                    <td>
                        <?=$score->great_count;?>
                    </td>
                    <td>
                        <?=$score->good_count;?>
                    </td>
                    <td>
                        <?=$score->boo_count;?>
                    </td>
                    <td>
                        <?=$score->miss_count;?>
                    </td>
                    <td>
                        <?=$score->ok_count;?>
                    </td>
                    <td>
                        <?=$score->mines_hit;?>
                    </td>
                    <td>
                        <?=date("m/d/Y", strtotime($score->date_achieved));?>
                    </td>
                    <td>
                        <a href="<?=$score->screenshot_url;?>" target="_blank">View Screenshot</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
	if ($show_tests) : ?>
    <h2>Raw File</h2>
    <pre style="font-size: 11px; line-height: 1.2em; max-height: 200px; overflow: auto;"><?php print_r($file);?></pre>

		<h2>Notes</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 200px; overflow: auto;"><?php print_r($notes);?></pre>
		<h2>Processed Notes Iteration #1 (Measures)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($processed['measures']);?></pre>
		<h2>Processed Notes Iteration #2 (Beats)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($processed['beats']);?></pre>
		<h2>Processed Notes Iteration #3 (Enumerated Beats)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($processed['enumerated']);?></pre>


		<h2>Processed Notes Iteration #4 (Enumerated with Timings)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($filled_distances);?></pre>
<?php /*
		<h1>Difficulty Relevant Array Outputs</h1>
		<h2><?=$programmatically_derived_interval;?>s Column Distributions</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($column_distributions_auto);?></pre>

		<h2>Half Inteval Simple</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($removed_irrelevant_half_interval_simple);?></pre>
        <h2>Inteval Simple</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($interval_simple);?></pre>
        <h2>Double Inteval Simple</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($double_interval_simple);?></pre>

		<h2>0.25s Column Distributions Formatted</h2>
		<p>Format: [line] [pattern_deviation] [hand_deviation] [weighted_jack_density]</p>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php foreach ($column_distributions_250ms as $num => $row) { echo "{$num}\t\t{$row['coefficient_of_variation']}\t\t\t{$row['hand_factor']}\r\n"; } ?></pre>

		<h2>0.5s Column Distributions</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($column_distributions_500ms);?></pre>
*/ ?>
		<h2>1s Column Distributions</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($column_distributions_auto);?></pre>

        <h2>Sekrit Array</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($interval_simple_sekrit);?></pre>

        <h2>Points Per Interval</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($simple_points_array);?></pre>

        <h2>Difficulty Per Interval</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($interval_simple);?></pre>
<?php /*
		<h2>2s Column Distributions</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($column_distributions_2s);?></pre>


		<h2>Notes Per Second</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($nps_graph_array);?></pre>
		<h2>Notes Per Second Distributions</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($nps_distributions);?></pre>
		<h2>Upper NPS Bound</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?=$nps_upper_bound;?></pre>
		<h2>Relevant Distributions (Floor Round)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($nps_floor_distributions);?></pre>
		<h2>Relevant Distributions (Ceil Round)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($nps_ceil_distributions);?></pre>
		<h2>Below Relevant Distributions (Floor Round)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($below_nps_floor);?></pre>
		<h2>Below Relevant Distributions (Ceil Round)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($below_nps_ceil);?></pre>
		<h2>Above Relevant Distributions</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($above_nps_dist);?></pre>
		<h2>Relative Distributions of Relevant Sections (Floor)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($percentage_relevant_distributions_floor);?></pre>
		<h2>Relative Distributions of Relevant Sections (Ceil)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($percentage_relevant_distributions_ceil);?></pre>

		<h2>Lengths of Relevant Sections (Floor)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($difficult_section_lengths_floor);?></pre>

		<h2>Lengths of Relevant Sections (Ceil)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($difficult_section_lengths_ceil);?></pre>

		<h2>Lengths of Trivial Sections (Ceil)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($trivial_section_lengths_ceil);?></pre>

		<h2>Lengths of Trivial Sections (Floor)</h2>
		<pre style="font-size: 11px; line-height: 1.2em; max-height: 400px; overflow: auto;"><?php print_r($trivial_section_lengths_floor);?></pre>
*/ ?>
	<?php endif;
?>
<?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false) : ?>
<h2>NPS Graph</h2>
<p>Hover for NPS at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		$nps_graph_array = ahrens_moving_average($nps_graph_array, 3, count($nps_graph_array));
		$column_widths = 100 / count($nps_graph_array);
		$left = 0;
		foreach ($nps_graph_array as $second => $graph_row) : ?>
			<?php
				$height = $graph_row / $meta['peak_NPS'] * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=$second;?>s: <?=$graph_row;?> NPS" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $meta['peak_NPS'];
				for ($i = 0; $i <= $peak_nps; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak_nps * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> NPS</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($nps_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($nps_graph_array);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",$i);?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<?php
		$average_nps_perc = $meta['average_NPS'] / $meta['peak_NPS'] * 100;
	?>
	<div id="average-legend" style="bottom: <?=$average_nps_perc;?>%;">
		<?=$meta['average_NPS'];?> Average NPS
	</div>
</div>
<?php /*
<h2>Distribution Pattern Analysis Graph (0.25s sections)</h2>
<p>Hover for applied difficulty at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		$column_widths = 100 / count($column_distributions_250ms);
		$left = 0;
		$peak = 0;
		foreach ($column_distributions_250ms as $key => $val) {
			if ($val['nps_factored_with_pattern_analysis'] > $peak)
				$peak = $val['nps_factored_with_pattern_analysis'];
		}
		foreach ($column_distributions_250ms as $second => $graph_row) : ?>
			<?php
				$height = $graph_row['nps_factored_with_pattern_analysis'] / $peak * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=($second / 4);?>s: <?=$graph_row['nps_factored_with_pattern_analysis'];?> diff" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $peak;
				for ($i = 0; $i <= $peak; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> (diff)</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($nps_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($column_distributions_250ms);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",($i / 4));?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
</div>

<h2>Distribution Pattern Analysis Graph (0.5s sections)</h2>
<p>Hover for applied difficulty at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		$column_widths = 100 / count($column_distributions_500ms);
		$left = 0;
		$peak = 0;
		foreach ($column_distributions_500ms as $key => $val) {
			if ($val['nps_factored_with_pattern_analysis'] > $peak)
				$peak = $val['nps_factored_with_pattern_analysis'];
		}
		foreach ($column_distributions_500ms as $second => $graph_row) : ?>
			<?php
				$height = $graph_row['nps_factored_with_pattern_analysis'] / $peak * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=($second / 2);?>s: <?=$graph_row['nps_factored_with_pattern_analysis'];?> diff" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $peak;
				for ($i = 0; $i <= $peak; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> (diff)</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($nps_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($column_distributions_500ms);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",($i / 2));?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
</div>

<h2>Distribution Pattern Analysis Graph (1s sections)</h2>
<p>Hover for applied difficulty at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		$column_widths = 100 / count($column_distributions_1s);
		$left = 0;
		$peak = 0;
		foreach ($column_distributions_1s as $key => $val) {
			if ($val['nps_factored_with_pattern_analysis'] > $peak)
				$peak = $val['nps_factored_with_pattern_analysis'];
		}
		foreach ($column_distributions_1s as $second => $graph_row) : ?>
			<?php
				$height = $graph_row['nps_factored_with_pattern_analysis'] / $peak * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=$second;?>s: <?=$graph_row['nps_factored_with_pattern_analysis'];?> diff" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $peak;
				for ($i = 0; $i <= $peak; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> (diff)</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($nps_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($column_distributions_1s);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",($i));?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
</div>

<h2>Distribution Pattern Analysis Graph (2s sections)</h2>
<p>Hover for applied difficulty at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		$column_widths = 100 / count($column_distributions_2s);
		$left = 0;
		$peak = 0;
		foreach ($column_distributions_2s as $key => $val) {
			if ($val['nps_factored_with_pattern_analysis'] > $peak)
				$peak = $val['nps_factored_with_pattern_analysis'];
		}
		foreach ($column_distributions_2s as $second => $graph_row) : ?>
			<?php
				$height = $graph_row['nps_factored_with_pattern_analysis'] / $peak * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=($second * 2);?>s: <?=$graph_row['nps_factored_with_pattern_analysis'];?> diff" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $peak;
				for ($i = 0; $i <= $peak; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> (diff)</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($nps_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($column_distributions_2s);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",($i * 2));?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
</div>

*/ ?>
<h2>Distribution Pattern Analysis Graph (Programmatic Interval <?=$programmatically_derived_interval;?>s sections)</h2>
<p>Hover for applied difficulty at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		//$column_distributions_auto['nps_factored_with_pattern_analysis'] = ahrens_moving_average($column_distributions_auto['nps_factored_with_pattern_analysis'], 20, count($column_distributions_auto['nps_factored_with_pattern_analysis']));
        $simple_pattern_graph_array = array();
        foreach ($column_distributions_auto as $val) {
            array_push($simple_pattern_graph_array, $val['nps_factored_with_pattern_analysis']);
        }
		$simple_pattern_graph_array = ahrens_moving_average($simple_pattern_graph_array, 5, count($simple_pattern_graph_array));
		$column_widths = 100 / count($simple_pattern_graph_array);
		$left = 0;
		$peak = 0;
		foreach ($simple_pattern_graph_array as $key => $val) {
			if ($val > $peak)
				$peak = $val;
		}
		foreach ($simple_pattern_graph_array as $second => $graph_row) : ?>
			<?php
				$height = $graph_row / $peak * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=($second  / (pow($programmatically_derived_interval, -1)));?>s: <?=$graph_row;?> diff" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $peak;
				for ($i = 0; $i <= $peak; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> (diff)</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($simple_pattern_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($column_distributions_auto);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",($i / (pow($programmatically_derived_interval, -1))));?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php /* <h2>Distribution Pattern Analysis Graph (Half Interval)</h2>
<p>Hover for applied difficulty at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		$column_widths = 100 / count($column_distribution_graphs["0.25"]);
		$left = 0;
		$peak = 0;
		foreach ($column_distribution_graphs["0.25"] as $key => $val) {
			if ($val['nps_factored_with_pattern_analysis'] > $peak)
				$peak = $val['nps_factored_with_pattern_analysis'];
		}
		foreach ($column_distribution_graphs["0.25"] as $second => $graph_row) : ?>
			<?php
				$height = $graph_row['nps_factored_with_pattern_analysis'] / $peak * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=($second  / (pow($programmatically_derived_interval/2, -1)));?>s: <?=$graph_row['nps_factored_with_pattern_analysis'];?> diff" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $peak;
				for ($i = 0; $i <= $peak; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> (diff)</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($nps_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($column_distribution_graphs["0.25"]);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",($i / (pow($programmatically_derived_interval/2, -1))));?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
</div>
<h2>Distribution Pattern Analysis Graph (Double Interval)</h2>
<p>Hover for applied difficulty at current time</p>
<div id="graph">
	<?php
		// ---------- NPS GRAPH GENERATION STARTS HERE ---------- //
		$column_widths = 100 / count($column_distribution_graphs["0.5"]);
		$left = 0;
		$peak = 0;
		foreach ($column_distribution_graphs["0.5"] as $key => $val) {
			if ($val['nps_factored_with_pattern_analysis'] > $peak)
				$peak = $val['nps_factored_with_pattern_analysis'];
		}
		foreach ($column_distribution_graphs["0.5"] as $second => $graph_row) : ?>
			<?php
				$height = $graph_row['nps_factored_with_pattern_analysis'] / $peak * 100;
				$left += $column_widths;
			?>
			<div class="bar has-tip" data-tooltip aria-haspopup="true" title="<?=($second  / (pow($programmatically_derived_interval*2, -1)));?>s: <?=$graph_row['nps_factored_with_pattern_analysis'];?> diff" style="width: <?=$column_widths;?>%; height: <?=$height;?>%; left: <?=$left;?>%;">
			</div>
		<?php endforeach;
	?>
	<div id="left-legend">
		<div id="ll-shell">
			<?php
				$peak_nps = $peak;
				for ($i = 0; $i <= $peak; $i++) {
					if ($i % 5 == 0) :
						$perc_top = $i / $peak * 100;
						?>
						<div class="left-legend" style="bottom: <?=$perc_top;?>%;"><?=$i;?> (diff)</div>
					<?php endif;
				}
			?>
		</div>
	</div>
	<div id="bottom-legend" style="left: <?=(1 / count($nps_graph_array) * 100);?>%;">
		<div id="bl-shell">
			<?php
				$peak_time = count($column_distribution_graphs["0.5"]);
				for ($i = 0; $i <= $peak_time; $i++) {
					if ($i % (ceil($peak_time / 15)) == 0) :
						$perc_left = $i / $peak_time * 100;
						?>
						<div class="bottom-legend" style="left: <?=$perc_left;?>%;"><?=gmdate("i:s",($i / (pow($programmatically_derived_interval*2, -1))));?></div>
					<?php endif;
				}
			?>
		</div>
	</div>
</div> */ ?>

<h2 style="margin-top: 150px;">Difficulty Information</h2>
<p>Difficulties below are a work in progress.</p>
<h3>Raw Average NPS Score</h3>
<div class="alert-box">
<h4><strong>Score:</strong> <?=round($meta['average_NPS']);?></h4>
</div>
<h3>NPS Adjusted</h3>
<div class="alert-box">
<?php
	$fudge = round((($percentage_relevant_distributions_floor + $percentage_relevant_distributions_ceil) / 2), 0);
?>
<h4><strong>Score:</strong> <?=$fudge;?></h4>
</div>
<h3>Difficulty Score</h3>
<div class="alert-box">
<?php
	$division_score = round((((($percentage_relevant_distributions_floor + $percentage_relevant_distributions_ceil) / 2) * $division_factor) * $NPS_adjustment_from_free_misses), 2);
	$weighted_pattern_score = $division_score * $average_difficulty_weight;
	$with_stamina_factor = $weighted_pattern_score * ($stamina_multiplier * $stamina_factor);
?>
<h4><strong>Score:</strong> <?=$calculated_difficulty;?></h4>
</div>
<p>
    <small><strong>Note: </strong>For now this calculation is only the NPS Adjusted value times my stamina multiplier calculation.<br />
        A more robust calculator will be coded soon. Unfortunately the original calculations are no longer being used on this site.<br /> -Wafles</small>
</p>
