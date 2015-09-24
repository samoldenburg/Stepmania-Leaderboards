<table id="pack-table">
    <thead>
        <tr>
            <th>Pack Name</th>
            <th>Common Abbreviation</th>
            <th>Number of Ranked Files</th>
            <th>Average Difficulty</th>
            <th>Download Link</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($packs as $pack) : ?>
            <tr>
                <td>
                    <?php if ($user_level >= 2) : ?>
                        <span class="label warning"><a href="/mod/edit_pack/<?=$pack->id;?>">Edit</a></span>
                    <?php endif; ?>
                    <a href="/packs/view/<?=$pack->id;?>"><?=$pack->name;?></a>
                </td>
                <td>
                    <?=$pack->abbreviation;?>
                </td>
                <td>
                    <?=$pack->file_count;?>
                </td>
                <td>
                    <?php
                        if (!empty($pack->average))
                            echo number_format($pack->average, 2);
                    ?>
                </td>
                <td>
                    <?php
                        $dl_link = $pack->download_link;
                        if (strlen($dl_link) > 40)
                            $dl_link = substr($dl_link, 0, 40) . "...";
                    ?>
                    <i class="fi-download"></i> <a href="<?=$pack->download_link;?>" target="_blank" download><?=$dl_link;?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
