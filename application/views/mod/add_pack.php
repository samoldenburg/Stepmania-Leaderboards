<p>
    Before files can be ranked, they must be associated with a pack. If your pack is not already available in the database you can add a new one here.<br />
    For single releases or miscelanneous files there is an already existing pack named "Single Releases".
</p>
<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<form action="/mod/add_pack" method="post" id="add-pack-form">

    <div class="form-input">
        <label for="name">Pack Name</label>
        <input type="text" id="name" name="name" value="<?=set_value('abbreviation');?>" />
    </div>

    <div class="form-input">
        <label for="abbreviation">Abbreviation</label>
        <input type="text" id="abbreviation" name="abbreviation" value="<?=set_value('abbreviation');?>" />
    </div>

    <div class="form-input">
        <label for="download_link">Pack Download Link (Use SMO Link)</label>
        <input type="text" id="download_link" name="download_link" value="<?=set_value('abbreviation');?>" />
    </div>

    <input class="button" type="submit" value="Add Pack" />
</form>
