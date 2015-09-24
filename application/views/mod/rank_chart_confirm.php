<p>Finally, confirm the details below and fix anything incorrect as needed. Most calculated values are not editable. File type designation is left up to the discretion of the submitter, but note that pattern adjustments are considered for the final difficulty value.</p>
<?php if ($num_charts_error) : ?>
    <div data-alert class="alert-box alert">
        The file you entered appears to have <?=$number_of_charts;?> charts. Only the first chart was processed.
    </div>
<?php endif; ?>
<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>

<?php
    $c = Ranked_file::count(array('conditions' => array('title = ? AND subtitle = ? AND artist = ? AND rate = ?', $meta['title'], $meta['subtitle'], $meta['artist'], $rate)));

    if ($c > 0) :
        $f = Ranked_file::find(array('conditions' => array('title = ? AND subtitle = ? AND artist = ? AND rate = ?', $meta['title'], $meta['subtitle'], $meta['artist'], $rate)));
        ?>
        <div data-alert class="alert-box alert">
            This appears to be a duplicate of <a href="/charts/view/<?=$f->id;?>" style="color: #ffffff; text-decoration: underline;">this chart.</a>
        </div>
    <?php endif;
?>

<form action="/mod/rank_chart" method="post" id="rank-chart-confirm">
    <input type="hidden" name="step" value="2" />
    <input type="hidden" name="auto_type" value="<?=$meta['autodetermined_file_type'];?>" />
    <div class="row">
        <div class="large-4 columns">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?=set_value('title', $meta['title']);?>" />
        </div>

        <div class="large-4 columns">
            <label for="subtitle">Subtitle</label>
            <input type="text" name="subtitle" id="subtitle" value="<?=set_value('subtitle', $meta['subtitle']);?>" />
        </div>

        <div class="large-4 columns">
            <label for="artist">Artist</label>
            <input type="text" name="artist" id="artist" value="<?=set_value('artist', $meta['artist']);?>" />
        </div>
    </div>

    <div class="row">
        <div class="large-4 columns">
            <label for="rate">Rate</label>
            <input type="number" name="rate" id="rate" value="<?=set_value('rate', $rate);?>" readonly />
        </div>

        <div class="large-4 columns">
            <label for="length">Length</label>
            <input type="number" name="length" id="length" value="<?=set_value('length', $meta['length_in_seconds']);?>" readonly />
        </div>

        <div class="large-4 columns">
            <label for="dance_points">Dance Points</label>
            <input type="number" name="dance_points" id="dance_points" value="<?=set_value('dance_points', $meta['dance_points']);?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="large-6 columns">
            <label for="notes">Notes</label>
            <input type="number" name="notes" id="notes" value="<?=set_value('notes', $meta['notes']);?>" readonly />
        </div>

        <div class="large-6 columns">
            <label for="taps">Taps</label>
            <input type="number" name="taps" id="taps" value="<?=set_value('taps', $meta['taps']);?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="large-3 columns">
            <label for="jumps">Jumps</label>
            <input type="number" name="jumps" id="jumps" value="<?=set_value('jumps', $meta['jumps']);?>" readonly />
        </div>

        <div class="large-2 columns">
            <label for="hands">Hands</label>
            <input type="number" name="hands" id="hands" value="<?=set_value('hands', $meta['hands']);?>" readonly />
        </div>

        <div class="large-2 columns">
            <label for="quads">Quads</label>
            <input type="number" name="quads" id="quads" value="<?=set_value('quads', $meta['quads']);?>" readonly />
        </div>

        <div class="large-2 columns">
            <label for="mines">Mines</label>
            <input type="number" name="mines" id="mines" value="<?=set_value('mines', $meta['mines']);?>" readonly />
        </div>
        <div class="large-3 columns">
            <label for="holds">Holds</label>
            <input type="number" name="holds" id="holds" value="<?=set_value('holds', $meta['holds']);?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="large-3 columns">
            <label for="peak_nps">Peak NPS</label>
            <input type="number" name="peak_nps" id="peak_nps" value="<?=set_value('peak_nps', $meta['peak_NPS']);?>" readonly />
        </div>

        <div class="large-3 columns">
            <label for="avg_nps">Average NPS</label>
            <input type="number" name="avg_nps" id="avg_nps" value="<?=set_value('avg_nps', round($meta['average_NPS'], 2));?>" readonly />
        </div>
        <?php
    		$average = ($percentage_relevant_distributions_floor + $percentage_relevant_distributions_ceil) / 2;
    		$d_factored = $average * $division_factor;
    	?>
        <div class="large-3 columns">
            <label for="avg_weighted_nps">Averaged Weighted NPS</label>
            <input type="number" name="avg_weighted_nps" id="avg_weighted_nps" value="<?=set_value('avg_weighted_nps', round($average, 2));?>" readonly />
        </div>

        <div class="large-3 columns">
            <label for="difficulty_score">Calculated Difficulty Score</label>
            <input type="number" name="difficulty_score" id="difficulty_score" value="<?=set_value('difficulty_score', round($calculated_difficulty, 2));?>" readonly />
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
            <label for="raw_file">Raw File Contents</label>
            <textarea name="raw_file" id="raw_file" readonly><?=set_value('raw_file', $file);?></textarea>
        </div>
    </div>

    <div class="row">
        <div class="large-3 columns">
            <label for="stamina_file">Is this a stamina intensive file?</label>
            <input type="radio" name="stamina_file" id="stamina_no" value="0" <?=set_radio('stamina_file', '0', TRUE);?> /><label for="stamina_no">No</label>
            <input type="radio" name="stamina_file" id="stamina_yes" value="1" <?=set_radio('stamina_file', '1');?> /><label for="stamina_yes">Yes</label>
        </div>

        <div class="large-3 columns">
            <label for="file_type">File Type</label>
            <select name="file_type" id="file_type">
                <option value=""></option>
                <option value="speed" <?=set_select('file_type', 'speed');?>>Speed</option>
                <option value="jack" <?=set_select('file_type', 'jack');?>>Jack</option>
                <option value="jumpstream" <?=set_select('file_type', 'jumpstream');?>>Jumpstream</option>
                <option value="technical" <?=set_select('file_type', 'technical');?>>Technical</option>
            </select>
        </div>

        <div class="large-3 columns">
            <label for="date_ranked">Time File Was Ranked</label>
            <input type="text" id="date_ranked" name="date_ranked" value="<?=set_value('date_ranked', date("Y-m-d H:i:s", time()));?>" readonly />
        </div>

        <div class="large-3 columns">
            <label for="pack_id">Pack This File Is In</label>
            <select name="pack_id" id="pack_id">
                <option value=""></option>
                <?php foreach($packs as $pack) : ?>
                    <option value="<?=$pack->id;?>" <?=set_select('pack_id', $pack->id);?>><?=$pack->name;?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <input type="submit" class="button" value="Submit File" />

</form>
