Add the following code to the shipping_method.phtml file(app\design\frontend\default\YOUR_THEME\template\checkout\onepage\shipping_method.phtml), inside of the form tags.
<?php echo $this->getChildHtml('deliverydate') ?>



Now for the admin area:
Add code below to the order template file(app/design/adminhtml/default/default/template/sales/order/view/tab/info.phtml) to display the choosen date for the admin when viewing the order. You can add it anywhere you want, I generally add it to the Shipping and Handeling Information Box.
<?php echo "<strong>".$this->helper('deliverydate')->__('Desired Arrival Date').":</strong> ".$this->helper('deliverydate')->getFormatedDeliveryDate($_order->getShippingArrivalDate()); ?>