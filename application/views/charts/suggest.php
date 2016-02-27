<p>
    Use the form below to suggest a chart to be added to the ranked charts index.
</p>
<div id="error-shell">
    <?php if ($error) : ?>
        <div data-alert class="alert-box alert">
            Something went wrong with your submission, please make sure all fields are completed properly.<br />
            <?=validation_errors('<span>', '</span><br />');?>
        </div>
    <?php endif; ?>
</div>
<form action="/charts/suggest" method="post" id="suggest-form">
    <div class="row">
        <div class="large-12 columns">
            <label for="upload">Choose a file (Only supported in modern browsers)</label>
            <input name="upload" id="upload" type="file" />
            <input type="button" class="button" id="file-button" value="Select A File" />
            <label for="file">OR - Paste Full .SM File Contents Here:</label>
            <textarea name="file" id="file" rows="20"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="large-3 columns">
            <label for="title">Song Title</label>
            <input type="text" name="title" id="title" />
        </div>
        <div class="large-2 columns">
            <label for="artist">Song Artist</label>
            <input type="text" name="artist" id="artist" />
        </div>
        <div class="large-2 columns">
            <label for="chart">Chart</label>
            <select name="chart" id="chart">
                <option value="Beginner">Beginner</option>
                <option value="Light">Light</option>
                <option value="Standard">Standard</option>
                <option value="Heavy" selected>Heavy</option>
                <option value="Oni">Oni</option>
                <option value="Edit">Edit</option>
            </select>
        </div>
        <div class="large-1 columns">
            <label for="rate">Rate</label>
            <input type="number" step="0.1" min="0.5" max="2.0" name="rate" value="1.0" id="rate" />
        </div>
        <div class="large-2 columns">
            <label for="file_type">File Type</label>
            <select name="file_type" id="file_type">
                <option value=""></option>
                <option value="speed">Speed</option>
                <option value="jumpstream">Jumpstream</option>
                <option value="jack">Jack</option>
                <option value="technical">Technical</option>
                <option value="stamina, speed">Stamina Speed</option>
                <option value="stamina, jumpstream">Stamina Jumpstream</option>
                <option value="stamina, jack">Stamina Jack</option>
                <option value="stamina, technical">Stamina Technical</option>
            </select>
        </div>
        <div class="large-2 columns">
            <label for="pack">Pack</label>
            <input type="text" name="pack" id="pack" />
        </div>
    </div>
    <input type="submit" class="button" value="Submit" />
</form>
