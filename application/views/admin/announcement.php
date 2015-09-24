<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<form action="/admin/post_announcement" method="post">
    <label for="title">Announcement Title</label>
    <input type="text" name="title" id="title" />

    <label for="text">Announcement text, HTML is allowed</label>
    <textarea name="text" id="text" rows="20"></textarea>

    <input type="submit" class="button" value="Post" />
</form>
