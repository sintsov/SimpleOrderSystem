<?php
/**
 * Template order list
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */

if (isset($orders)){
    foreach ($orders as $order) {
        ?>
        <div class="col-sm-10 order" data-id="<?=$order['id']?>">
            <h3><?=$order['title']?></h3>
            <h4><span class="label label-default">$ <?=getPrice($order['cost'])?></span></h4>
            <p><?=$order['text']?></p>
            <? if (isAuth() && !isCustomer()){?>
                <p>
                    <form action="" class="form-order-makeit" method="post">
                        <input type="hidden" name="action" value="/order/makeit/" />
                        <input type="hidden" name="order-id" value="<?=$order['id']?>" />
                        <button type="submit" class="btn btn-primary">Make It!</button>
                    </form>
                </p>
            <?}?>
            <small class="text-muted"><?=timeElapsedString(date("c", $order['created_at']))?></small>
            </h4>
        </div>
        <div class="col-sm-2 user-photo">
            <a href="#" class="pull-right"><img src="/img/anonymous.png" class="img-circle user-photo"></a> <!-- http://api.randomuser.me/portraits/thumb/men/1.jpg -->
        </div>


        <div class="row divider">
            <div class="col-sm-12"><hr></div>
        </div>
    <?}?>
<?}?>