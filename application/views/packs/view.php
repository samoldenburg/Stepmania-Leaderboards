<p>
    <strong>File Count: </strong> <?=$pack->file_count;?><br />
    <strong>Download: </strong> <a href="<?=$pack->download_link;?>" target="_blank" download><?=$pack->download_link;?></a>
</p>
<h4>Files:</h4>
<table id="pack-single-table">
    <thead>
        <tr>
            <th>
                Song Title
            </th>
            <th>
                Song Artist
            </th>
            <th>
                Length
            </th>
            <th>
                File Type
            </th>
            <th>
                Rate
            </th>
            <th>
                Difficulty
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($songs as $song) : ?>
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
                    <?=number_format($song->rate, 1);?>x
                </td>
                <td>
                    <?=number_format($song->difficulty_score, 2);?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
