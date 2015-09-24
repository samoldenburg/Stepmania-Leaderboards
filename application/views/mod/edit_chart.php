<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<?php if ($rank_success) : ?>
    <p>Your file has successfully been added to the database and is now available as a ranked file.</p>
    <p><a href="/mod/rank_chart">Rank another?</a></p>
<?php endif; ?>

<form action="/mod/edit_chart/<?=$chart->id;?>" method="post" id="rank-chart-confirm">
    <input type="hidden" name="step" value="2" />
    <div class="row">
        <div class="large-4 columns">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?=set_value('title', $chart->title);?>" />
        </div>

        <div class="large-4 columns">
            <label for="subtitle">Subtitle</label>
            <input type="text" name="subtitle" id="subtitle" value="<?=set_value('subtitle', $chart->subtitle);?>" />
        </div>

        <div class="large-4 columns">
            <label for="artist">Artist</label>
            <input type="text" name="artist" id="artist" value="<?=set_value('artist', $chart->artist);?>" />
        </div>
    </div>

    <div class="row">
        <div class="large-4 columns">
            <label for="rate">Rate</label>
            <input type="number" name="rate" id="rate" value="<?=set_value('rate', $chart->rate);?>" readonly />
        </div>

        <div class="large-4 columns">
            <label for="length">Length</label>
            <input type="number" name="length" id="length" value="<?=set_value('length', $chart->length);?>" readonly />
        </div>

        <div class="large-4 columns">
            <label for="dance_points">Dance Points</label>
            <input type="number" name="dance_points" id="dance_points" value="<?=set_value('dance_points', $chart->dance_points);?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="large-6 columns">
            <label for="notes">Notes</label>
            <input type="number" name="notes" id="notes" value="<?=set_value('notes', $chart->notes);?>" readonly />
        </div>

        <div class="large-6 columns">
            <label for="taps">Taps</label>
            <input type="number" name="taps" id="taps" value="<?=set_value('taps', $chart->taps);?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="large-3 columns">
            <label for="jumps">Jumps</label>
            <input type="number" name="jumps" id="jumps" value="<?=set_value('jumps', $chart->jumps);?>" readonly />
        </div>

        <div class="large-2 columns">
            <label for="hands">Hands</label>
            <input type="number" name="hands" id="hands" value="<?=set_value('hands', $chart->hands);?>" readonly />
        </div>

        <div class="large-2 columns">
            <label for="quads">Quads</label>
            <input type="number" name="quads" id="quads" value="<?=set_value('quads', $chart->quads);?>" readonly />
        </div>

        <div class="large-2 columns">
            <label for="mines">Mines</label>
            <input type="number" name="mines" id="mines" value="<?=set_value('mines', $chart->mines);?>" readonly />
        </div>
        <div class="large-3 columns">
            <label for="holds">Holds</label>
            <input type="number" name="holds" id="holds" value="<?=set_value('holds', $chart->holds);?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="large-3 columns">
            <label for="peak_nps">Peak NPS</label>
            <input type="number" name="peak_nps" id="peak_nps" value="<?=set_value('peak_nps', $chart->peak_nps);?>" readonly />
        </div>

        <div class="large-3 columns">
            <label for="avg_nps">Average NPS</label>
            <input type="number" name="avg_nps" id="avg_nps" value="<?=set_value('avg_nps', $chart->avg_nps);?>" readonly />
        </div>
        <div class="large-3 columns">
            <label for="avg_weighted_nps">Averaged Weighted NPS</label>
            <input type="number" name="avg_weighted_nps" id="avg_weighted_nps" value="<?=set_value('avg_weighted_nps', $chart->avg_weighted_nps);?>" readonly />
        </div>

        <div class="large-3 columns">
            <label for="difficulty_score">Calculated Difficulty Score</label>
            <input type="number" name="difficulty_score" id="difficulty_score" value="<?=set_value('difficulty_score', $chart->difficulty_score);?>" readonly />
        </div>
    </div>
    <div class="row">
        <div class="large-6 columns">
            <label for="raw_file">Raw File Contents</label>
            <textarea name="raw_file" id="raw_file" readonly><?=set_value('raw_file', $chart->raw_file);?></textarea>
        </div>
        <div class="large-6 columns">
            <label for="enumerated_files">Enumerated File Array</label>
            <textarea name="enumerated_file" id="enumerated_file" readonly><?=set_value('enumerated_file', $chart->enumerated_file);?></textarea>
        </div>
    </div>

    <div class="row">
        <div class="large-3 columns">
            <label for="stamina_file">Is this a stamina intensive file?</label>
            <input type="radio" name="stamina_file" id="stamina_no" value="0" <?=set_radio('stamina_file', '0', $chart->stamina_file == "0" ? true : false);?> /><label for="stamina_no">No</label>
            <input type="radio" name="stamina_file" id="stamina_yes" value="1" <?=set_radio('stamina_file', '1', $chart->stamina_file == "1" ? true : false);?> /><label for="stamina_yes">Yes</label>
        </div>

        <div class="large-3 columns">
            <label for="file_type">File Type</label>
            <select name="file_type" id="file_type">
                <option value=""></option>
                <option value="speed" <?=set_select('file_type', 'speed', $chart->file_type == "speed" ? true : false);?>>Speed</option>
                <option value="jack" <?=set_select('file_type', 'jack', $chart->file_type == "jack" ? true : false);?>>Jack</option>
                <option value="jumpstream" <?=set_select('file_type', 'jumpstream', $chart->file_type == "jumpstream" ? true : false);?>>Jumpstream</option>
                <option value="technical" <?=set_select('file_type', 'technical', $chart->file_type == "technical" ? true : false);?>>Technical</option>
            </select>
        </div>

        <div class="large-3 columns">
            <label for="date_ranked">Time File Was Ranked</label>
            <input type="text" id="date_ranked" name="date_ranked" value="<?=set_value('date_ranked', $chart->date_ranked);?>" readonly />
        </div>

        <div class="large-3 columns">
            <label for="pack_id">Pack This File Is In</label>
            <select name="pack_id" id="pack_id">
                <option value=""></option>
                <?php foreach($packs as $pack) : ?>
                    <option value="<?=$pack->id;?>" <?=set_select('pack_id', $pack->id, $chart->pack_id == $pack->id ? true : false);?>><?=$pack->name;?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <input type="submit" class="button" value="Edit Chart" />

</form>
<form action="/mod/add_additional_rate/<?=$chart->id;?>" method="post">
    <input type="hidden" value="<?=$chart->id;?>" name="chart_id" />
    <input type="submit" class="button warning" value="Add additional Rate" />
</form>
<form action="/mod/delete_chart/<?=$chart->id;?>" method="post">
    <input type="hidden" value="<?=$chart->id;?>" name="chart_id" />
    <input type="submit" data-confirm="Are you sure?" class="button alert" value="Delete Chart" />
</form>
