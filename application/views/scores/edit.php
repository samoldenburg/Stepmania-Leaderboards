<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<?php if ($error_nc) : ?>
    <div data-alert class="alert-box alert">
        The note or hold count you specified appears to be higher than what is possible for the file. Please check your numbers and try again.<br />
    </div>
<?php endif; ?>
<p>
    Enter the details about your score below. A screenshot is required, other fields are optional. If your score is in the top 10% (subject to change) of all ranked files, a moderator will be required to approve your score before it is made public.
</p>
<p>
    <strong>If your score is less than a AA it will be removed. Only Judge 4 scores are allowed.</strong>
</p>
<div data-alert class="alert-box alert" id="err-filetype" style="display: none;">
    The filetype you are attempting to upload is not allowed. Only JPG/PNG is allowed.
</div>
<div data-alert class="alert-box alert" id="err-filesize" style="display: none;">
    The uploaded file exceeds the maximum allowed size allowed. Maximum file size is 1MB.
</div>
<div data-alert class="alert-box alert" id="err-dimensions" style="display: none;">
    The image you are attempting to upload doesn't fit into the allowed dimensions. Max dimensions are 1920x1080.
</div>
<p>
    <form id="ss-upload-form" action="/ajax/upload_screenshot" data-action="/ajax/upload_screenshot" enctype="multipart/form-data" method="post">
        <input type="file" name="userfile" id="userfile" style="display: none;" />
        <input type="button" class="button" id="upload-ss-btn" value="Upload Screenshot" />
        <input type="submit" style="display: none;" value="Upload" />
    </form>
    <div id="image-preview">
        <img src="<?=set_value('screenshot_url', $user_score->screenshot_url);?>" />
    </div>
</p>
<form action="/scores/edit/<?=$user_score->id;?>" method="post">
    <input type="hidden" name="screenshot_url" id="screenshot_url" value="<?=set_value('screenshot_url', $user_score->screenshot_url);?>" />
    <div class="row">
        <div class="large-3 columns">
            <label for="song_name">Chart Name</label>
            <input type="text" name="song_name" id="song_name" value="<?=$song->title;?>" readonly />
        </div>
        <div class="large-3 columns">
            <label for="song_artist">Artist</label>
            <input type="text" name="song_artist" id="song_artist" value="<?=$song->artist;?>" readonly />
        </div>
        <div class="large-3 columns">
            <label for="song_rate">Rate</label>
            <input type="text" name="song_rate" id="song_rate" value="<?=number_format($song->rate, 1);?>x" readonly />
        </div>
        <div class="large-3 columns">
            <label for="score_achieved">Date Score Was Achieved</label>
            <input type="text" class="fdatepicker" id="score_achieved" name="score_achieved" value="<?=set_value('score_achieved', date('m/d/Y', strtotime($user_score->date_achieved)));?>"/>
        </div>
    </div>
    <p><strong>Optional:</strong> Input your judgment counts. These may be evaluated to show the grade you got, as well as display on individual file leaderboards.</p>
    <div class="row">
        <div class="large-2 columns">
            <label for="marvelous_count">Marvelous Count</label>
            <input type="number" min="0" name="marvelous_count" id="marvelous_count" value="<?=set_value('marvelous_count', $user_score->marvelous_count);?>" />
        </div>
        <div class="large-2 columns">
            <label for="perfect_count">Perfect Count</label>
            <input type="number" min="0" name="perfect_count" id="perfect_count" value="<?=set_value('perfect_count', $user_score->perfect_count);?>" />
        </div>
        <div class="large-2 columns">
            <label for="great_count">Great Count</label>
            <input type="number" min="0" name="great_count" id="great_count" value="<?=set_value('great_count', $user_score->great_count);?>" />
        </div>
        <div class="large-2 columns">
            <label for="good_count">Good Count</label>
            <input type="number" min="0" name="good_count" id="good_count" value="<?=set_value('good_count', $user_score->good_count);?>" />
        </div>
        <div class="large-2 columns">
            <label for="boo_count">Boo Count</label>
            <input type="number" min="0" name="boo_count" id="boo_count" value="<?=set_value('boo_count', $user_score->boo_count);?>" />
        </div>
        <div class="large-2 columns">
            <label for="miss_count">Miss Count</label>
            <input type="number" min="0" name="miss_count" id="miss_count" value="<?=set_value('miss_count', $user_score->miss_count);?>" />
        </div>
    </div>
    <div class="row">
        <div class="large-6 columns">
            <label for="ok_count">OK Count (Successful Holds and Rolls)</label>
            <input type="number" min="0" name="ok_count" id="ok_count" value="<?=set_value('ok_count', $user_score->ok_count);?>" />
        </div>
        <div class="large-6 columns">
            <label for="mines_hit">Mines Hit</label>
            <input type="number" min="0" name="mines_hit" id="mines_hit" value="<?=set_value('mines_hit', $user_score->mines_hit);?>" />
        </div>
    </div>
    <input type="submit" class="button" value="Edit Score" />
</form>
