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
<form action="/charts/suggest" method="post">
    <div class="row">
        <div class="large-3 columns">
            <label for="title">Song Title</label>
            <input type="text" name="title" id="title" />
        </div>
        <div class="large-3 columns">
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
        <div class="large-3 columns">
            <label for="pack">Pack</label>
            <input type="text" name="pack" id="pack" />
        </div>
    </div>
    <input type="submit" class="button" value="Submit" />
</form>
