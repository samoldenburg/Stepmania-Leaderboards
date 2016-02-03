<?php $i = 1; ?>
<?php foreach($overall_leaderboards as $lb_row) : ?>
    <tr>
        <td>
            <?=$i;?>
        </td>
        <td>
            <a href="<?=$lb_row->profile_link;?>"><?=$lb_row->username;?></a>
        </td>
        <td>
            <?=number_format($lb_row->average_score, 2);?>
        </td>
    </tr>
<?php
    $i++;
    if ($i == 11)
        break;
endforeach; ?>
