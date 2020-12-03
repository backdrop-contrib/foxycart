<?php
/**
 * @file
 * Displays a Foxy.io full-cart block.
 *
 * Available variables:
 * - $fc_domain: The domain name of the shopping cart, as configured at Foxy.io.
 * - $logo: Markup to display the Foxy.io logo.
 *
 * @ingroup themeable
 */
?>

<div id="fc_cart">
  <table id="fc_cart_contents">
    <thead>
      <th><?php print t('Item'); ?></th>
      <th><?php print t('Quantity'); ?></th>
      <th><?php print t('Price'); ?></th>
    </thead>
    <tbody id="fc_cart_items"></tbody>
  </table>

  <p id="fc_cart_message"><?php print t('Your cart is empty'); ?></p>
  <a class="fc_cart_link view" href="https://<?php print $fc_domain; ?>/cart?cart=view" style="float: left;"><?php print t('Edit cart'); ?></a>
  <a class="fc_cart_link checkout" href="https://<?php print $fc_domain; ?>/cart?cart=checkout" style="float: right;"><?php print t('Checkout'); ?></a>
  <div class="clearfix">&nbsp;</div>

  <?php if ($logo): ?>
    <p><?php echo $logo; ?></p>
  <?php endif; ?>
</div>
