<h3>
    Leaderboard Rankings
</h3>
<table>
    <?php if (isset($overall_rank)) : ?>
        <tr>
            <td>
                <a href="/leaderboards/overall"><strong>Overall Ranking:</strong></a>
            </td>
            <td>
                #<?=$overall_rank;?>
            </td>
            <td>
                <?=number_format($overall_score,2);?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if (isset($speed_rank)) : ?>
        <tr>
            <td>
                <a href="/leaderboards/speed"><strong>Speed Ranking:</strong></a>
            </td>
            <td>
                #<?=$speed_rank;?>
            </td>
            <td>
                <?=number_format($speed_score,2);?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if (isset($jumpstream_rank)) : ?>
        <tr>
            <td>
                <a href="/leaderboards/jumpstream"><strong>Jumpstream Ranking:</strong></a>
            </td>
            <td>
                #<?=$jumpstream_rank;?>
            </td>
            <td>
                <?=number_format($jumpstream_score,2);?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if (isset($jack_rank)) : ?>
        <tr>
            <td>
                <a href="/leaderboards/jack"><strong>Jack Ranking:</strong></a>
            </td>
            <td>
                #<?=$jack_rank;?>
            </td>
            <td>
                <?=number_format($jack_score,2);?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if (isset($technical_rank)) : ?>
        <tr>
            <td>
                <a href="/leaderboards/technical"><strong>Technical Ranking:</strong></a>
            </td>
            <td>
                #<?=$technical_rank;?>
            </td>
            <td>
                <?=number_format($technical_score,2);?>
            </td>
        </tr>
    <?php endif; ?>
    <?php if (isset($stamina_rank)) : ?>
        <tr>
            <td>
                <a href="/leaderboards/stamina"><strong>Stamina Ranking:</strong></a>
            </td>
            <td>
                #<?=$stamina_rank;?>
            </td>
            <td>
                <?=number_format($stamina_score,2);?>
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php if ($this->session->userdata('username') == $user->username || $this->session->userdata('user_level') == 3) : ?>
    <h3>
        Leaderboards Requirements
    </h3>
    <p>
        Your highest score is <?=number_format($top_score, 2);?>, and as such you must meet the following requirements to get a spot on the leaderboards:<br />
        <strong>Overall Leaderboards: </strong> You must quality for scores in <?=$categories_required;?> separate file type categories to gain a spot on the overall leaderboard.<br />
        <strong>Individual Skill Leaderboards: </strong> You must have <?=$individual_required;?> scores in any category in order to gain a spot on the leaderboard for that category.
    </p>
<?php endif; ?>

<?php if (count($scores) > 5) : ?>
    <h3>
        Screenshot Gallery
    </h3>
    <div id="profile-screenshot-gallery">
        <div class="slider slider-for">
            <?php foreach ($scores as $score) : ?>
                <div>
                    <img src="<?=$score->screenshot_url;?>" />
                </div>
            <?php endforeach;?>
    	</div>
    	<div class="slider slider-nav">
            <?php foreach ($scores as $score) : ?>
                <div>
                    <img src="<?=$score->screenshot_url;?>" />
                </div>
            <?php endforeach;?>
    	</div>
    </div>
<?php endif; ?>

<h3>
    Scores Achieved
</h3>
<table id="user-scores-table">
    <thead>
        <tr>
            <th>
                Chart Name
            </th>
            <th>
                Rate
            </th>
            <th>
                Difficulty
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
                File Type
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
        <?php foreach ($scores as $score) : ?>
            <?php
                $max_dp_percent = calculate_dp_percent($score);


                $gradesort = 0;
                $grade_earned = "<img src='/assets/img/c.png' />";
                if ($max_dp_percent > 65) {
                    $grade_earned = "<img src='/assets/img/b.png' />";
                    $gradesort = 1;
                }
                if ($max_dp_percent > 80) {
                    $grade_earned = "<img src='/assets/img/a.png' />";
                    $gradesort = 2;
                }
                if ($max_dp_percent > 93) {
                    $grade_earned = "<img src='/assets/img/aa.png' />";
                    $gradesort = 3;
                }
                if ($max_dp_percent == 100) {
                    $grade_earned = "<img src='/assets/img/aaa.png' />";
                    $gradesort = 4;
                }
                if ($max_dp_percent == 100 && $score->perfect_count == 0) {
                    $grade_earned = "<img src='/assets/img/aaaa.png' />";
                    $gradesort = 5;
                }

                $ex_oni_percent = calculate_ex_oni_percent($score);


            ?>
            <tr>
                <td>
                    <a href="/charts/view/<?=$score->file_id;?>"><?=$score->title;?></a>
                    <?php if ($user_level >= 2 || $score->username == $this->session->userdata('username')) : ?>
                        <br /><span class="label warning"><a href="/scores/edit/<?=$score->id;?>">Edit</a></span>
                        <span class="label alert"><a href="/scores/remove/<?=$score->id;?>">Remove</a></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?=number_format($score->file_rate, 1);?>x
                </td>
                <td>
                    <?=number_format($score->difficulty_score, 2);?>
                </td>
                <td data-order="<?=$gradesort?>">
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
                    <?php if ($score->stamina_file == 1) {
                        echo "Stamina, ";
                    } ?>
                    <?=ucwords($score->file_type);?>
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
