<p>Select a .sm file, or paste the full .sm contents into the textarea below. Full file analysis and a difficulty algorithm will be run against the file submitted and returned for your reference. This is not a submission form to have something ranked on the site. <a href="/charts/suggest">Click here to suggest a file to be ranked.</a></p>
<ul class="disc" style="color:#999999;">
	<li>SM Files Only. DWI, DS, SSC are not supported at this time.</li>
	<li>Only the first difficulty in the file will be analyzed.</li>
	<li>Mines will be ignored. Holds will be counted for DP adjustments but will not display in the preview window.</li>
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
