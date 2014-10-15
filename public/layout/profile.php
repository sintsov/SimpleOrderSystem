<?php
/**
 * Template user profile
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */
?>

<!-- Profile -->
<div class="form-group" id="profile-logout-button">
    <form action="" id="form-user-signout" method="post">
        <input type="hidden" name="action" value="/user/signout/" />
        <input class="btn btn-danger" value="SignOut" type="submit">
    </form>
</div>

<div id="profile">
    <div class="panel-heading">
        <h3 class="panel-title"></h3>
    </div>
    <div class="panel-body">

        <div id="left-menu-profile"><div>
                <div class="profile-header">
                    <div class="profile-name">Hello, <?=getUserInfo('name')?>!</div>
                    <div class="profile-name">Balance: <span id="amount" class="badge">0</span>$</div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs navigation">
            <li><a href="#createOrder" data-toggle="tab">Create order</a></li>
            <li><a href="#payment">Make a payment</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" id="createOrder">
                <form action="" id="form-createOrder" method="post">
                    <div class="alert alert-danger" id="form-createOrder-alert" style="display: none;"></div>
                    <input type="hidden" name="action" value="/user/createOrder/" />
                    <div class="form-group">
                        <input placeholder="Title" class="form-control" name="title" id="title" value="" type="text">
                    </div>
                    <div class="form-group">
                        <input placeholder="Cost" class="form-control" name="cost" id="cost" value="" type="text">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="text" id="text" rows="5" placeholder="Describe your offer" ></textarea>
                    </div>
                    <?
                    $send = '';
                    if (getUserInfo('balance') == 0){
                        $send = 'disabled="disabled"';
                    ?>
                        <div class="alert alert-disclemer">To create an order you have to make a payment</div>
                    <? }?>
                    <input class="btn btn-success btn-lg" <?=$send?> value="Create" type="submit">
                </form>
            </div>
            <div class="tab-pane fade" id="payment">
                <form action="" id="form-payment" method="post">
                    <div class="alert alert-danger" id="form-payment-alert" style="display: none;"></div>
                    <input type="hidden" name="action" value="/user/payment/" />
                    <div class="form-group">
                        <select class="form-control" name="payment_id" id="payment_id">
                            <option value="1">Payment Fake System</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input placeholder="Total" class="form-control" name="total" id="total" value="" type="text">
                    </div>
                    <input class="btn btn-success btn-lg" value="Payment" type="submit">
                </form>
            </div>
        </div>
    </div>
</div>