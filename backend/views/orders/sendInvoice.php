<?php

use yii\helpers\Url;

$orderArr = $params;
?>

<table width="100%" cellpadding="3" cellspacing="3" style="border:2px solid grey;color:black;font-family: comic sans ms;padding-left: 30px;padding-right: 30px;padding-top: 15px;" background="#EEF3F7" width="275" height="95">
    <tr>
        <td>
            <div style="text-align: center;">
                <img src="<?php echo Url::to('@web/images/login-logo.png', true); ?>" height="100" width="100"/>
            </div>
        </td>        
    </tr>
    <tr>
        <td><h3>Order Receipt</h3></td>
    </tr>    
    <tr>
        <td>
            <div style="width: 45%;float: left;">
                <strong>Sold To: </strong><?php echo $orderArr['first_name']; ?>
                <br>
                    <?php
                    $address = $orderArr['shipping_address1'];

                    $address .= ($orderArr['shipping_address2'] != '') ? ', ' . $orderArr['shipping_address2'] : $orderArr['shipping_address2'];
                    $address .= ($orderArr['shipping_city'] != '') ? ', ' . $orderArr['shipping_city'] : $orderArr['shipping_city'];
                    $address .= ($orderArr['shipping_country_name'] != '') ? ', ' . $orderArr['shipping_country_name'] : $orderArr['shipping_country_name'];
                    $address .= ($orderArr['shipping_zip'] != '') ? '<br>' . $orderArr['shipping_zip'] : $orderArr['shipping_zip'];
                    $address .= ($orderArr['shipping_phone'] != '') ? '<br>Tel.: ' . $orderArr['shipping_phone'] : $orderArr['shipping_phone'];
                    ?>
                    <strong>Address: </strong><?php echo $address; ?>   
            </div>
            <div style="width: 45%;float: left;text-align: right;">
                <?php if ($orderArr['invoice_id'] != '') {
                    ?><strong>Order Receipt: </strong>#<?php echo $orderArr['invoice_id']; ?></p><?php
                }
                if ($orderArr['invoice_date'] != '') {
                    ?><br><strong>Date: </strong><?php echo date('d/m/Y', strtotime($orderArr['invoice_date'])); ?> <?php
                }
                ?>
            </div>
        </td>
    </tr>
         
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>        
     
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            <table style="background-color: transparent;  border-collapse: collapse;border-spacing: 0;margin-bottom: 20px;max-width: 100%;width: 100%;border: 1px solid;">
                <thead>
                    <tr> </tr>
                    <tr>
                        <th align="left" style="border: 1px solid;vertical-align: top;padding: 5px;">Product</th>                        
                        <th align="left" style="border: 1px solid;vertical-align: top;padding: 5px;">Quantity</th>
                        <th align="left" style="border: 1px solid;vertical-align: top;padding: 5px;">Price</th>
                        <th align="left" style="border: 1px solid;vertical-align: top;padding: 5px;">Subtotal</th>
                    </tr>

                </thead>
                <tbody>
                    <?php
                    $totalAmount = 0;
                    if (isset($orderArr['products']) && !empty($orderArr['products'])) {
                        foreach ($orderArr['products'] as $product) {
                            if ($product['shipment_status'] != '3' && $product['shipment_status'] != '6' && $product['shipment_status'] != '7' && $product['shipment_status'] != '8') {
                                ?>
                                <tr>
                                    <td align="left" style="border: 1px solid;vertical-align: top;padding: 5px;"><?php echo $product['product_name']; ?></td>                                    
                                    <td align="left" style="border: 1px solid;vertical-align: top;padding: 5px;"><?php echo $product['order_qty']; ?></td>
                                    <td align="left" style="border: 1px solid;vertical-align: top;padding: 5px;">S$<?php echo $product['product_price']; ?></td>
                                    <td align="left" style="border: 1px solid;vertical-align: top;padding: 5px;">S$<?php
                                        $price = $product['product_price'] * $product['order_qty'];
                                        $totalAmount = $totalAmount + $price;
                                        echo $price;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right" style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>Subtotal</strong></td>
                        <td style="border: 1px solid;vertical-align: top;padding: 5px;">S$<?php echo number_format($totalAmount, 2); ?></td>
                    </tr>

                    <tr>
                        <td colspan="3" align="right" style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>Shipping & Handling</strong></td>
                        <td style="border: 1px solid;vertical-align: top;padding: 5px;">S$<?php echo number_format($orderArr['shipping_rate'], 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right" style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>Tax(<?php echo $orderArr['tax_rate']; ?>%)</strong></td>
                        <td style="border: 1px solid;vertical-align: top;padding: 5px;"><?php
                            $tax = ($totalAmount * $orderArr['tax_rate']) / 100;
                            echo 'S$' . number_format($tax, 2);
                            ?></td>
                    </tr>

                    <tr>
                        <td colspan="3"  align="right" style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>Subtotal</strong></td>
                        <td style="border: 1px solid;vertical-align: top;padding: 5px;">S$<?php
                            $subtotal = $totalAmount + ($orderArr['shipping_rate'] + $tax);
                            echo number_format($subtotal, 2);
                            ?></td>
                    </tr>
                    <tr>
                        <td colspan="3"  align="right" style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>Less: Coupon Code Discount(<?php echo ($orderArr['promocode'] != '') ? $orderArr['promocode'] : '-'; ?>)</strong></td>
                        <td style="border: 1px solid;vertical-align: top;padding: 5px;">S$<?php
                            $discount = 0;
                            echo number_format($discount, 2);
                            ?></td>
                    </tr>

                    <tr>
                        <td colspan="3"  align="right" style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>Grand Total</strong></td>
                        <td style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>S$<?php echo number_format($subtotal - $discount, 2); ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"  align="right" style="border: 1px solid;vertical-align: top;padding: 5px;"><strong>Payment Method</strong></td>
                        <td style="border: 1px solid;vertical-align: top;padding: 5px;"><strong><?php echo $orderArr['payment_method']; ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>  
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            Regards,
        </td>
    </tr>  
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            Boucle Team.
        </td>
    </tr>    
    <tr>
        <td align="center" style="font-family: comic sans ms;font-size: 15px">
            <span style="font-size:11px">&copy; <?php echo date('Y'); ?> Boucle, All Rights Reserved. </span>
        </td>
    </tr>                                                   
</table>