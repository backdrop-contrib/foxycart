<?php
/**
 * @file
 * Displays a Foxy.io minicart block.
 *
 * Available variables:
 * - $fc_domain: The domain name of the shopping cart, as configured at Foxy.io.
 *
 * @ingroup themeable
 */
?>

<p data-fc-id="minicart" style="display: none;">
  <a href="https://<?php print $fc_domain; ?>/cart?cart=view">
    <span data-fc-id="minicart-quantity">0</span>
    <span data-fc-id="minicart-singular"> <?php print t('item'); ?> </span>
    <span data-fc-id="minicart-plural"> <?php print t('items'); ?> </span>
    <?php print t('in cart. Total cost: $'); ?><span data-fc-id="minicart-order-total">0</span>
  </a>
</p>

<p data-fc-id="minicart-empty" style="display: none;"><?php print t('Your cart is empty'); ?></p>
