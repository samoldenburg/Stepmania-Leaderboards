<h4>Search</h4>
<form action="#" id="chart-search">
    <div class="row">
        <div class="large-3 columns">
            <label for="chart-name">Chart Name</label>
            <input type="text" id="chart-name" name="chart-name" autofocus />
        </div>
        <div class="large-3 columns">
            <label for="artist-name">Artist</label>
            <input type="text" id="artist-name" name="artist-name" />
        </div>
        <div class="large-3 columns">
            <label for="pack-name">Pack Name</label>
            <input type="text" id="pack-name" name="pack-name" />
        </div>
        <div class="large-3 columns">
            <label for="rate-val">Rate</label>
            <input type="number" step="0.1" min="0.5" max="2.0" id="rate-val" name="rate-val" value="1.0" />
            <label for="disable-rate"><input type="checkbox" name="disable-rate" id="disable-rate" checked="checked" /> Disable Rate Filter</label>
        </div>
    </div>
    <div class="row">
        <div class="large-4 columns">
            <label for="file-type">File Type</label>
            <select name="file-type" id="file-type">
                <option value="">Show All</option>
                <option value="stamina">Stamina</option>
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
        <div class="large-4 columns">
            <p class="diff-display-shell">
                <strong>Min Difficulty: </strong> <span id="min-diff-display-val">0</span>
            </p>
            <div id="min-diff-slider" class="range-slider" data-slider data-options="start: 0; end: 40; initial: 0; step: 1; display_selector: #min-diff-display-val;">
                <span class="range-slider-handle" role="slider" tabindex="0"></span>
                <span class="range-slider-active-segment"></span>
                <input id="min-diff-slider-val" type="hidden">
            </div>
        </div>
        <div class="large-4 columns">
            <p class="diff-display-shell">
                <strong>Max Difficulty: </strong> <span id="max-diff-display-val">0</span>
            </p>
            <div id="max-diff-slider" class="range-slider" data-slider data-options="start: 0; end: 40; initial: 40; step: 1; display_selector: #max-diff-display-val;">
                <span class="range-slider-handle" role="slider" tabindex="0"></span>
                <span class="range-slider-active-segment"></span>
                <input id="max-diff-slider-val" type="hidden">
            </div>
        </div>
    </div>

</form>
<table id="song-table">
    <thead>
        <tr>
            <th>
                Song Title
            </th>
            <th>
                Song Artist
            </th>
            <th>
                Rate
            </th>
            <th>
                Difficulty
            </th>
            <th>
                Pack
            </th>
            <th>
                Length
            </th>
            <th>
                File Type
            </th>
            <th>

            </th>
        </tr>
    </thead>
    <tbody>
        <?php /*
        <tr>
            <td>
                <?php if ($user_level >= 2) : ?>
                    <span class="label warning"><a href="/mod/edit_chart/<?=$song->id;?>">Edit</a></span>
                <?php endif; ?>
                <a href="/charts/view/<?=$song->id;?>"><?=$song->title;?></a>
            </td>
            <td>
                <?=$song->artist;?>
            </td>
            <td>
                <?=number_format($song->rate, 1);?>x
            </td>
            <td>
                <?=number_format($song->difficulty_score, 2);?>
            </td>
            <td>
                <a href="/packs/view/<?=$song->pack_id;?>"><?=$song->pack_name;?></a>  <?=(!empty($song->pack_abbr) ? "(" . $song->pack_abbr . ")" : "");?>
            </td>
            <td>
                <?=gmdate("i:s", $song->length);?>
            </td>
            <td>
                <?php
                    $typestring = "";
                    if ($song->stamina_file)
                        $typestring .= "Stamina, ";
                    $typestring .= ucwords($song->file_type);
                ?>
                <?=$typestring;?>
            </td>
            <td>
            <?php if ($logged_in) : ?>
                    <span class="label primary"><a href="/scores/submit/<?=$song->id;?>">Submit Score</a></span>
            <?php endif; ?>
            </td>
        </tr>

        */ ?>
        <?php foreach ($songs as $song) : ?><tr><td><?php if ($user_level >= 2) : ?><span class="label warning"><a href="/mod/edit_chart/<?=$song->id;?>">Edit</a></span><?php endif; ?><a href="/charts/view/<?=$song->id;?>"><?=$song->title;?></a></td><td><?=$song->artist;?></td><td><?=number_format($song->rate, 1);?>x</td><td><?=number_format($song->difficulty_score, 2);?></td><td><a href="/packs/view/<?=$song->pack_id;?>"><?=$song->pack_name;?></a>  <?=(!empty($song->pack_abbr) ? "(" . $song->pack_abbr . ")" : "");?></td><td><?=gmdate("i:s", $song->length);?></td><td><?php $typestring = "";if ($song->stamina_file) {$typestring .= "Stamina, ";}$typestring .= ucwords($song->file_type);?><?=$typestring;?></td><td><?php if ($logged_in) : ?><span class="label primary"><a href="/scores/submit/<?=$song->id;?>">Submit Score</a></span><?php endif; ?></td></tr>
        <?php endforeach; ?>
    </tbody>
</table>
