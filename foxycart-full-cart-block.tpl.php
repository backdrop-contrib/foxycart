<?php

// $Id$
?>
<div id="fc_cart">
<h2>Your Cart</h2>
<div class="fc_clear"></div>
<table id="fc_cart_contents">
<thead>
<th>item</th>
<th>qty</th>
<th>price</th>
</thead>
<tbody id="fc_cart_items">
</tbody>
</table>
<span id="fc_cart_message"></span>
<a class="fc_cart_link" href="https://<?php echo $fc_domain; ?>/cart?cart=checkout" id="fc_checkout_link" style="float:left">Check Out</a>
<a class="fc_cart_link" href="https://<?php echo $fc_domain; ?>/cart?cart=view" class="foxycart" style="float:right">Edit Cart</a>
<div class="fc_clear"></div>
<?php echo $logo; ?>
</div>
