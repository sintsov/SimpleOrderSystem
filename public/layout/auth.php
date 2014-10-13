<?php
/**
 * Template block authorization/registration
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */
?>
<!-- Sign In|Join -->
<div id="auth-panel">
    <ul class="nav nav-tabs navigation">
        <li><a href="#login" data-toggle="tab">Log in</a></li>
        <li><a href="#join">Join</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade" id="login">
            <form id="form-sigin" method="post">
            <div class="form-group">
                <input placeholder="Email" class="form-control" name="email" id="email" value="" type="text">
            </div>
            <div class="form-group">
                <input placeholder="Password" class="form-control" name="password" id="password" value="" type="password">
            </div>
            <input class="btn btn-success btn-lg" value="Sign In" type="submit">
            </form>
        </div>
        <div class="tab-pane fade" id="join">
            <form id="form-join" method="post">
                <label>Choose what you are doing:</label>
                <div class="form-group">
                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                        <label class="btn btn-primary"><input type="radio" name="role" value="1"> Customer</label>
                        <label class="btn btn-primary"><input type="radio" name="role" value="2"> Employee</label>
                    </div>
                </div>

                <div class="form-group">
                    <input placeholder="Your name" class="form-control" name="name" id="name" value="" type="text">
                </div>
                <div class="form-group">
                    <input placeholder="Email" class="form-control" name="email" id="email" value="" type="text">
                </div>
                <div class="form-group">
                    <input placeholder="Password" class="form-control" name="password" id="password" value="" type="password">
                </div>
                <div class="form-group">
                    <input placeholder="Confirm password" class="form-control" name="confirmPassword" id="confirmPassword" value="" type="password">
                </div>

                <input class="btn btn-success btn-lg" value="Join" type="submit">
            </form>
        </div>
    </div>
</div>
