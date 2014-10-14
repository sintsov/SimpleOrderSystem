<?php
/**
 * Template user profile
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */
?>
<!-- Profile -->
<div class="panel-heading">
    <h3 class="panel-title">Profile</h3>
</div>
<div class="panel-body" id="profileLeftPanel">
    <div id="left-menu-profile"><div>
            <div class="profile-header">
                <div class="profile-name"><?=getUserInfo('name')?></div>
            </div>
        </div>
    </div>

    <div class="trpLeftMenuProfileButton">
        <form action="" id="form-user-sigout" method="post">
            <input type="hidden" name="action" value="/user/signout/" />
            <input class="btn btn-danger" value="SignOut" type="submit">
        </form>
    </div>
</div>

