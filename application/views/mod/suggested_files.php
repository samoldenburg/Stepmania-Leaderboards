<p>
    Suggested files from users are listed below. Pending files will add to the mod counter in the navigation.
</p>
<table id="suggest-table">
    <thead>
        <tr>
            <th>
                Song Title
            </th>
            <th>
                Artist
            </th>
            <th>
                Chart
            </th>
            <th>
                Pack
            </th>
            <th>
                Rate
            </th>
            <th>
                File Type
            </th>
            <th>
                Status
            </th>
            <th>
                Suggested By
            </th>
            <th colspan="2">
                Actions
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($suggested_files as $suggested_file) : ?>
            <tr class="<?=$suggested_file->status;?>">
                <td>
                    <?=$suggested_file->title;?>
                </td>
                <td>
                    <?=$suggested_file->artist;?>
                </td>
                <td>
                    <?=$suggested_file->chart;?>
                </td>
                <td>
                    <?=$suggested_file->pack;?>
                </td>
                <td>
                    <?=number_format($suggested_file->rate, 1);?>x
                </td>
                <td>
                    <?=ucfirst($suggested_file->file_type)?>
                </td>
                <td>
                    <?=ucfirst($suggested_file->status);?>
                </td>
                <td>
                    <a href="/profile/view/<?=$suggested_file->username;?>"><?=$suggested_file->display_name;?></a>
                </td>
                <td>
                    <span class="label primary"><a href="/mod/suggested_files/<?=$suggested_file->id;?>/added">Added</a></span><span class="label warning"><a href="/mod/suggested_files/<?=$suggested_file->id;?>/pending">Pending</a></span><span class="label alert"><a href="/mod/suggested_files/<?=$suggested_file->id;?>/rejected">Rejected</a></span>
                </td>
                <td>
                    <?php if (!empty($suggested_file->raw_file)) : ?>
                        <span class="label primary"><a href="/mod/rank_chart/?suggested_id=<?=$suggested_file->id;?>&suggested_rate=<?=number_format($suggested_file->rate, 1);?>">Rank This File Now</a></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
