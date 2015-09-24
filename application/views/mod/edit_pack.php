<?php if ($error) : ?>
    <div data-alert class="alert-box alert">
        Something went wrong with your submission, please make sure all fields are completed properly.<br />
        <?=validation_errors('<span>', '</span><br />');?>
    </div>
<?php endif; ?>
<form action="/mod/edit_pack/<?=$pack->id;?>" method="post" id="add-pack-form">

    <div class="form-input">
        <label for="name">Pack Name</label>
        <input type="text" id="name" name="name" value="<?=set_value('name', $pack->name);?>" />
    </div>

    <div class="form-input">
        <label for="abbreviation">Abbreviation</label>
        <input type="text" id="abbreviation" name="abbreviation" value="<?=set_value('abbreviation', $pack->abbreviation);?>" />
    </div>

    <div class="form-input">
        <label for="download_link">Pack Download Link (Use SMO Link)</label>
        <input type="text" id="download_link" name="download_link" value="<?=set_value('download_link', $pack->download_link);?>" />
    </div>

    <input class="button" type="submit" value="Edit Pack" />
</form>

<form action="/mod/delete_pack/<?=$pack->id;?>">
    <input type="hidden" value="<?=$pack->id;?>" name="pack_id" />
    <input type="submit" class="button alert" value="Delete Pack" />
</form>
