<p>Submit a .sm file, cool stuff might happen.</p>
<p>
	Coded by Wafles<br />
	Pattern Analysis theory and formula help from MinaciousGrace
</p>
<ul class="disc" style="color:#999999;">
	<li>SM Files Only. DWI, DS, SSC are not supported at this time.</li>
	<li>Only the first difficulty in the file will be analyzed.</li>
	<li>Mines and holds will be ignored. Holds will be counted for DP adjustments.</li>
</ul>
<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<form action="/difficulty_calculator" method="post" id="parser-form">
	<label for="upload">Choose a file (Only supported in modern browsers)</label>
	<input name="upload" id="upload" type="file" />
    <input type="button" class="button" id="file-button" value="Select A File" />
	<label for="file">OR - Paste Full .SM File Contents Here:</label>
	<textarea name="file" id="file" rows="20"></textarea>
	<label for="rate">Rate to judge at. e.g. 1.0, 1.1, 1.2, etc..</label>
	<input name="rate" id="rate" type="number" step="0.1" value="1.0" style="width: 100px;" />
	<input class="button expand" type="submit" value="Submit" />
</form>
