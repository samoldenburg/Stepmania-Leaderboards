<div id="chat-contents">
    <?php foreach($chat as $chat_row) : ?>
        <div class="chat-row">
            <span class="label" style="background: <?=$chat_row->color;?>;"><a href="/profile/view/<?=$chat_row->username;?>"><?=$chat_row->display_name;?></a></span><span class="label secondary"><?=date("M j, g:i:s a", strtotime($chat_row->time));?></span>
            <div class="message">
                <?=replace_emotes($chat_row->message);?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if ($spam_error) : ?>
        <div class="chat-row">
            <span class="label alert">Error</span>
            <div class="message">
                You're sending too many messages. Please wait a few seconds.
            </div>
        </div>
    <?php endif; ?>
</div>
