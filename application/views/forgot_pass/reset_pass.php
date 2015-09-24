<p>
    To complete your password reset, enter a new password below.
</p>
<div id="error-shell">
    <?php if ($error) : ?>
        <div data-alert class="alert-box alert">
            Something went wrong with your submission, please make sure all fields are completed properly.<br />
            <?=validation_errors('<span>', '</span><br />');?>
        </div>
    <?php endif; ?>
</div>
<form id="edit-profile-form" action="/forgot_pass/reset_pass/<?=$token;?>" method="post">

    <div class="row">
        <div class="large-6 columns">
            <div class="form-input">
                <label for="pass">New Password</label>
                <input type="password" name="pass" id="pass" />
            </div>
        </div>
        <div class="large-6 columns">
            <div class="form-input">
                <label for="confirm_pass">Confirm New Password</label>
                <input type="password" name="confirm_pass" id="confirm_pass" />
            </div>
        </div>
    </div>

    <input class="button" type="submit" value="Edit Profile" id="registerbutton" />

</form>
