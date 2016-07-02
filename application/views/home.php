<div class="row">
    <div class="large-8 columns">
        <?php if (!empty($streams)) : ?>
            <h3>Streams</h3>
            <div id="stream-shell">
                <?php $c = 0; ?>
                <?php foreach($streams as $stream) : ?>
                    <div class="stream large-3 columns">
                        <a href="<?=$stream->channel->url;?>" target="_blank"><img src="<?=$stream->preview->medium;?>" /></a>
                        <h5><a href="<?=$stream->channel->url;?>" target="_blank"><?=$stream->channel->display_name;?></a></h5>
                        <p class="s-p">
                            Playing <?=$stream->channel->game;?> for <?=$stream->viewers;?> <?=($stream->viewers == 1 ? "viewer" : "viewers"); ?>.
                        </p>
                    </div>
                <?php $c++; if ($c == 4) break; endforeach; ?>
                <?php for ($i = 0; $i < (4 - $c); $i++) : ?>
                    <div class="stream large-2 columns"><br /></div>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
        <h3 style="clear: both;">Recent Stuff</h3>
        <div style="margin-bottom: 1.5em;">
            <ul class="tabs" data-tab>
                <li class="tab-title active"><a href="#panel11">Scores</a></li>
                <li class="tab-title"><a href="#panel21">Ranked Files</a></li>
                <li class="tab-title"><a href="#panel31">Packs</a></li>
            </ul>
        </div>
        <div class="tabs-content" style="margin-bottom: 0;">
            <div class="content active" id="panel11">
                <div class="row">
                    <div class="large-12 columns">
                        <table id="recent-scores-table">
                            <thead>
                                <tr>
                                    <th>
                                        User Name
                                    </th>
                                    <th>
                                        Chart
                                    </th>
                                    <th>
                                        Grade
                                    </th>
                                    <th>
                                        DP %
                                    </th>
                                    <th>
                                        Difficulty
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
                                <?php foreach ($recent_scores as $score) : ?>
                                    <?php
                                        $max_dp_percent = calculate_dp_percent($score);

                                        $grade_earned = "<img src='/assets/img/c.png' />";
                                        $gradesort = 0;
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
                                            <a href="/profile/view/<?=$score->username;?>"><?=$score->display_name;?></a>
                                        </td>
                                        <td>
                                            <a href="/charts/view/<?=$score->file_id;?>"><?=$score->title;?></a>
                                        </td>
                                        <td data-order="<?=$gradesort?>">
                                            <?=$grade_earned;?>
                                        </td>
                                        <td>
                                            <?=number_format($max_dp_percent, 2);?>%
                                        </td>
                                        <td>
                                            <?=number_format($score->difficulty_score, 2);?>
                                        </td>
                                        <td>
                                            <?=date("m/d/Y", strtotime($score->date_achieved));?>
                                        </td>
                                        <td>
                                            <a href="<?=$score->screenshot_url;?>" target="_blank">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="content" id="panel21">
                <div class="row">
                    <div class="large-12 columns">
                        <table id="recent-files-table">
                            <thead>
                                <tr>
                                    <th>
                                        Song Title
                                    </th>
                                    <th>
                                        Artist
                                    </th>
                                    <th>
                                        Rate
                                    </th>
                                    <th>
                                        Difficulty
                                    </th>
                                    <th>
                                        Pack
                                    </th>
                                    <th>
                                        File Type
                                    </th>
                                    <th>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($new_songs as $song) : ?>
                                    <tr>
                                        <td>
                                            <a href="/charts/view/<?=$song->id;?>"><?=$song->title;?></a>
                                        </td>
                                        <td>
                                            <?=$song->artist;?>
                                        </td>
                                        <td>
                                            <?=number_format($song->rate, 1);?>x
                                        </td>
                                        <td>
                                            <?=number_format($song->difficulty_score, 2);?>
                                        </td>
                                        <td>
                                            <a href="/packs/view/<?=$song->pack_id;?>"><?=$song->pack_name;?></a>  <?=(!empty($song->pack_abbr) ? "(" . $song->pack_abbr . ")" : "");?>
                                        </td>
                                        <td>
                                            <?php
                                                $typestring = "";
                                                if ($song->stamina_file)
                                                    $typestring .= "Stamina, ";
                                                $typestring .= ucwords($song->file_type);
                                            ?>
                                            <?=$typestring;?>
                                        </td>
                                        <td>
                                            <?php if ($logged_in) : ?>
                                                <span class="label primary"><a href="/scores/submit/<?=$song->id;?>">Submit Score</a></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="content" id="panel31">
                <div class="row">
                    <div class="large-12 columns">
                        <table id="recent-pack-table">
                            <thead>
                                <tr>
                                    <th>Pack Name</th>
                                    <th>Number of Ranked Files</th>
                                    <th>Average Difficulty</th>
                                    <th>Download Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($new_packs as $pack) : ?>
                                    <tr>
                                        <td>
                                            <?php if ($user_level >= 2) : ?>
                                                <span class="label warning"><a href="/mod/edit_pack/<?=$pack->id;?>">Edit</a></span>
                                            <?php endif; ?>
                                            <a href="/packs/view/<?=$pack->id;?>"><?=$pack->name;?></a>
                                        </td>
                                        <td>
                                            <?=$pack->file_count;?>
                                        </td>
                                        <td>
                                            <?php
                                                if (!empty($pack->average))
                                                    echo number_format($pack->average, 2);
                                            ?>
                                        </td>
                                        <td>
                                            <i class="fi-download"></i> <a href="<?=$pack->download_link;?>" target="_blank" download>Download</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <h3 style="clear: both;">Announcements</h3>
        <?php foreach ($announcements as $announcement) : ?>
            <div class="announcement">
                <h4><?=$announcement->title;?></h4>
                <div class="row">
                    <div class="large-12 columns">
                        <span class="label"><a href="/profile/view/<?=$announcement->username;?>">Posted by <?=$announcement->display_name;?></a></span><span class="label secondary"><?=date("l, F j, Y, g:i:s a", strtotime($announcement->time));?></span>

                        <?php if ($user_level >= 3) : ?>
                            <span class="label alert"><a href="/admin/edit_announcement/<?=$announcement->id;?>">Edit</a></span>
                        <?php endif; ?>
                    </div>
                </div>

                <p>
                    <?=nl2br($announcement->text);?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="large-4 columns" id="home-sidebar">
        <h3>Top 10</h3>
        <div>
            <ul class="tabs" data-tab>
                <li class="tab-title active"><a href="#panel12">Speed</a></li>
                <li class="tab-title"><a href="#panel22">Jumpstream</a></li>
                <li class="tab-title"><a href="#panel32">Jack</a></li>
                <li class="tab-title"><a href="#panel42">Technical</a></li>
                <li class="tab-title"><a href="#panel52">Stamina</a></li>
                <li class="tab-title"><a href="#panel62">Overall</a></li>
            </ul>
        </div>
        <div class="tabs-content" style="margin-bottom: 0;">
            <div class="content active" id="panel12">
                <table id="home-top-10-speed">
                    <thead>
                        <tr>
                            <th>
                                Rank
                            </th>
                            <th>
                                User
                            </th>
                            <th>
                                Skill Score
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lb-content">
                        <?php $i = 1; ?>
                        <?php foreach($leaderboards['speed_leaderboards'] as $lb_row) : ?>
                            <tr>
                                <td>
                                    <?=$i;?>
                                </td>
                                <td>
                                    <a href="<?=$lb_row->profile_link;?>"><?=$lb_row->username;?></a>
                                </td>
                                <td>
                                    <?=number_format($lb_row->average_score, 2);?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                            if ($i == 11)
                                break;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="content" id="panel22">
                <table id="home-top-10-jumpstream">
                    <thead>
                        <tr>
                            <th>
                                Rank
                            </th>
                            <th>
                                User
                            </th>
                            <th>
                                Skill Score
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lb-content">
                        <?php $i = 1; ?>
                        <?php foreach($leaderboards['jumpstream_leaderboards'] as $lb_row) : ?>
                            <tr>
                                <td>
                                    <?=$i;?>
                                </td>
                                <td>
                                    <a href="<?=$lb_row->profile_link;?>"><?=$lb_row->username;?></a>
                                </td>
                                <td>
                                    <?=number_format($lb_row->average_score, 2);?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                            if ($i == 11)
                                break;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="content" id="panel32">
                <table id="home-top-10-jack">
                    <thead>
                        <tr>
                            <th>
                                Rank
                            </th>
                            <th>
                                User
                            </th>
                            <th>
                                Skill Score
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lb-content">
                        <?php $i = 1; ?>
                        <?php foreach($leaderboards['jack_leaderboards'] as $lb_row) : ?>
                            <tr>
                                <td>
                                    <?=$i;?>
                                </td>
                                <td>
                                    <a href="<?=$lb_row->profile_link;?>"><?=$lb_row->username;?></a>
                                </td>
                                <td>
                                    <?=number_format($lb_row->average_score, 2);?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                            if ($i == 11)
                                break;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="content" id="panel42">
                <table id="home-top-10-technical">
                    <thead>
                        <tr>
                            <th>
                                Rank
                            </th>
                            <th>
                                User
                            </th>
                            <th>
                                Skill Score
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lb-content">
                        <?php $i = 1; ?>
                        <?php foreach($leaderboards['technical_leaderboards'] as $lb_row) : ?>
                            <tr>
                                <td>
                                    <?=$i;?>
                                </td>
                                <td>
                                    <a href="<?=$lb_row->profile_link;?>"><?=$lb_row->username;?></a>
                                </td>
                                <td>
                                    <?=number_format($lb_row->average_score, 2);?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                            if ($i == 11)
                                break;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="content" id="panel52">
                <table id="home-top-10-stamina">
                    <thead>
                        <tr>
                            <th>
                                Rank
                            </th>
                            <th>
                                User
                            </th>
                            <th>
                                Skill Score
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lb-content">
                        <?php $i = 1; ?>
                        <?php foreach($leaderboards['stamina_leaderboards'] as $lb_row) : ?>
                            <tr>
                                <td>
                                    <?=$i;?>
                                </td>
                                <td>
                                    <a href="<?=$lb_row->profile_link;?>"><?=$lb_row->username;?></a>
                                </td>
                                <td>
                                    <?=number_format($lb_row->average_score, 2);?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                            if ($i == 11)
                                break;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="content" id="panel62">
                <table id="home-top-10-overall">
                    <thead>
                        <tr>
                            <th>
                                Rank
                            </th>
                            <th>
                                User
                            </th>
                            <th>
                                Skill Score
                            </th>
                        </tr>
                    </thead>
                    <tbody id="lb-content">
                        <?php $i = 1; ?>
                        <?php foreach($leaderboards['overall_leaderboards'] as $lb_row) : ?>
                            <tr>
                                <td>
                                    <?=$i;?>
                                </td>
                                <td>
                                    <a href="<?=$lb_row->profile_link;?>"><?=$lb_row->username;?></a>
                                </td>
                                <td>
                                    <?=number_format($lb_row->average_score, 2);?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                            if ($i == 11)
                                break;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p style="margin-top: -22px;font-size: 1.25rem;text-align: right;margin-bottom: 0;">
            <small><a href="/leaderboards/overall">View Full List</a></small>
        </p>
        <h3>Chat</h3>
        <div id="chat-box" class="always-visible">
            <div id="chat-contents">
            </div>
        </div>
        <?php if ($this->session->userdata('user_id') && $this->session->userdata('chat_color')) : ?>
            <form id="chat-form" action="#" method="post">
                <textarea id="chat-type" name="chat-type" rows="1" placeholder="Type a message and hit enter" maxlength="1000"></textarea>
                <input type="submit" style="display: none;" value="Send" />
            </form>
            <div id="online-users">
                Online Users (<?=count($online_users)?>): <br>
                <?php foreach ($online_users as $online_user) : ?>
                    <a href="/profile/view/<?=$online_user->username?>"><?=$online_user->display_name?></a>&nbsp;
                <?php endforeach; ?>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $("#chat-type").keydown(function(e) {
                        if (e.which == 13) {
                            var cdata = $("#chat-form").serialize();
                            $("#chat-type").val("");
                            $.ajax({
                                type: "POST",
                                url: "/ajax/add_chat_message",
                                data: cdata
                            }).done(function(data) {
                                $("#chat-box").html(data);
                                $("#chat-box").scrollTop($("#chat-contents").height() + 9999);
                            }).fail(function() {
                                console.log("????");
                            });
                            return false;
                        }
                    });
                });
            </script>
        <?php endif; ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var scrolled_up = false;
                $('#chat-box').scroll(function() {
                    var s = $(this).scrollTop();

                    if (s < $("#chat-contents").height())
                        scrolled_up = true;
                    else
                        scrolled_up = false;
                });
                function get_chat_log() {
                    $.ajax({
                        type: "GET",
                        url: "/ajax/get_chat"
                    }).done(function(data) {
                        $("#chat-box").html(data);
                        if (!scrolled_up)
                            $("#chat-box").scrollTop($("#chat-contents").height() + 9999);
                        setTimeout(function() {
                            get_chat_log();
                        }, 5000);
                    }).fail(function() {
                        console.log("????");
                    });
                }
                get_chat_log();
            });
        </script>

    </div>
</div>
