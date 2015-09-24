<p>
    Here you can change your password, email address, and display name.
</p>
<div id="error-shell">
    <?php if ($error) : ?>
        <div data-alert class="alert-box alert">
            Something went wrong with your submission, please make sure all fields are completed properly.<br />
            <?=validation_errors('<span>', '</span><br />');?>
        </div>
    <?php endif; ?>
</div>
<form id="edit-profile-form" action="/profile/edit" method="post">

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

    <div class="row">
        <div class="large-6 columns">
            <div class="form-input">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" value="<?=set_value('email', $user->email);?>" />
            </div>
        </div>

        <div class="large-6 columns">
            <div class="form-input">
                <label for="display_name">Display Name</label>
                <input type="text" name="display_name" id="display_name" value="<?=set_value('display_name', $user->display_name);?>" />
            </div>
        </div>
    </div>

    <input class="button" type="submit" value="Edit Profile" id="registerbutton" />

</form>
