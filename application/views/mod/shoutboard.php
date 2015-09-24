<p>
    This page is only accessible by mods. Mods may post messages here to communicate with the other mods and coordinate file adding.
</p>
<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<form action="/mod/shoutboard" method="post">
    <input type="text" name="title" placeholder="Post Title" />
    <textarea name="text" rows="5" placeholder="Type your message here"></textarea>
    <input class="button" type="submit" value="Post" />
</form>
<?php foreach ($mod_posts as $mod_post) : ?>
    <div class="announcement">
        <h4><?=$mod_post->title;?></h4>
        <div class="row">
            <div class="large-12 columns">
                <span class="label"><a href="/profile/view/<?=$announcement->username;?>">Posted by <?=$mod_post->display_name;?></a></span><span class="label secondary"><?=date("l, F j, Y, g:i:s a", strtotime($mod_post->time));?></span>
            </div>
        </div>

        <p>
            <?=nl2br($mod_post->text);?>
        </p>
    </div>
<?php endforeach; ?>
