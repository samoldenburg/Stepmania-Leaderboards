<p>
    Files submitted that are in the top 10% of all scores will appear here for moderator approval.
</p>
<table id="pending-scores-table">
    <thead>
        <tr>
            <th>
                Chart Name
            </th>
            <th>
                User
            </th>
            <th>
                Rate
            </th>
            <th>
                Difficulty
            </th>
            <th>
                Date Achieved
            </th>
            <th>
                Grade
            </th>
            <th>
                DP %
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
                Screenshot
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($scores as $score) : ?>
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
            <tr class="<?=$score->status;?>">
                <td>
                    <a href="/charts/view/<?=$score->file_id;?>"><?=$score->title;?></a>
                    <?php if ($user_level >= 2 || $score->username == $this->session->userdata('username')) : ?>
                        <br /><span class="label warning"><a href="/scores/edit/<?=$score->id;?>">Edit</a></span>
                        <span class="label alert"><a href="/scores/remove/<?=$score->id;?>">Remove</a></span>
                    <?php endif; ?>
                    <br />
                    <span class="label primary"><a href="/mod/pending_scores/<?=$score->id;?>/approved">Approved</a></span>
                    <span class="label warning"><a href="/mod/pending_scores/<?=$score->id;?>/pending">Pending</a></span>
                    <span class="label warning"><a href="/mod/pending_scores/<?=$score->id;?>/below_aa">Below AA</a></span>
                    <span class="label alert"><a href="/mod/pending_scores/<?=$score->id;?>/rejected">Rejected</a></span>
                </td>
                <td>
                    <a href="/profile/view/<?=$score->username;?>"><?=$score->display_name;?></a>
                </td>
                <td>
                    <?=number_format($score->file_rate, 1);?>x
                </td>
                <td>
                    <?=number_format($score->difficulty_score, 2);?>
                </td>
                <td>
                    <?=date("m/d/Y", strtotime($score->date_achieved));?>
                </td>
                <td>
                    <?=$grade_earned;?>
                </td>
                <td>
                    <?=number_format($max_dp_percent, 2);?>%
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
                    <a href="<?=$score->screenshot_url;?>" target="_blank">View Screenshot</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
