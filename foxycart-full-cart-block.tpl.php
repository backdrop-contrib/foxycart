<?php

?>
<div id="fc_cart">
<h2>Your Cart</h2>
<div class="fc_clear"></div>
<table>
<thead>
<th>item</th>
<th>qty</th>
<th>price</th>
</thead>
<tbody id="cart_content">
</tbody>
</table>
<a href="https://<?php echo $fc_domain; ?>.foxycart.com/cart?cart=checkout" id="fc_checkout_link">Check Out</a>
<a href="https://<?php echo $fc_domain; ?>.foxycart.com/cart?cart=view" class="foxycart" style="float:right">Edit Cart</a>
<div class="fc_clear"></div>
<?php echo $logo; ?>
</div>
