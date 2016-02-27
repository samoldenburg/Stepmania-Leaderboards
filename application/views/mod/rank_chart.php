<p>Select .SM and Rate to Rank</p>
<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<form action="/mod/rank_chart" method="post" id="parser-form">
    <input type="hidden" name="step" value="1" />
	<label for="upload">Choose a file (Only supported in modern browsers)</label>
	<input name="upload" id="upload" type="file" />
    <input type="button" class="button" id="file-button" value="Select A File" />
	<label for="file">OR - Paste Full .SM File Contents Here:</label>
	<textarea name="file" id="file" rows="20"><?php if (isset($chart)) { echo $chart->raw_file; } ?></textarea>
	<label for="rate">Rate to judge at. e.g. 1.0, 1.1, 1.2, etc..</label>
	<input name="rate" id="rate" type="number" step="0.1" value="<?=(isset($rate) ? $rate : '1.0')?>" style="width: 100px;" />
	<input class="button expand" type="submit" value="Submit" />
</form>
