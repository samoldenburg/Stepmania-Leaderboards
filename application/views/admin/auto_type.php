<p>
    Auto file types listed out below.
</p>
<table id="auto-file-type">
    <thead>
        <tr>
            <th>
                Title
            </th>
            <th>
                Rate
            </th>
            <th>
                Difficulty Score
            </th>
            <th>
                Manual File Type
            </th>
            <th>
                Auto File Type
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($charts as $chart) : ?>
            <tr>
                <td>
                    <a href="/charts/view/<?=$chart->id;?>"><?=$chart->title;?></a>
                </td>
                <td>
                    <?=number_format($chart->rate, 1);?>x
                </td>
                <td>
                    <?=number_format($chart->difficulty_score, 2);?>
                </td>
                <td>
                    <?=$chart->file_type;?>
                </td>
                <td>
                    <?=$chart->auto_type;?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
