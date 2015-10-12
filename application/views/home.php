<div class="row">
    <div class="large-8 columns">
        <?php /* if (!empty($streams)) : ?>
            <h3>Streams</h3>
            <div id="stream-shell">
                <?php $c = 0; ?>
                <?php foreach($streams as $stream) : ?>
                    <div class="stream large-3 columns">
                        <a href="<?=$stream['channel']['url'];?>" target="_blank"><img src="<?=$stream['preview']['medium'];?>" /></a>
                        <h5><a href="<?=$stream['channel']['url'];?>" target="_blank"><?=$stream['channel']['display_name'];?></a></h5>
                        <p class="s-p">
                            Playing <?=$stream['channel']['game'];?> for <?=$stream['viewers'];?> <?=($stream['viewers'] == 1 ? "viewer" : "viewers"); ?>.
                        </p>
                    </div>
                <?php $c++; if ($c == 4) break; endforeach; ?>
                <?php for ($i = 0; $i < (4 - $c); $i++) : ?>
                    <div class="stream large-2 columns"><br /></div>
                <?php endfor; ?>
            </div>
        <?php endif; */ ?>
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
    <div class="large-4 columns">
        <h3>Top 10</h3>
        <table id="home-top-10">
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
                <tr>
                    <td colspan="3">
                        Loading...
                    </td>
                </tr>
            </tbody>
        </table>
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
                $.get("ajax/get_home_leaderboard", function(data) {
                    $("#lb-content").html(data);
                });
                
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
