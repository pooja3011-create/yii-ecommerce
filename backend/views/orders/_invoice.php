<?php

use yii\helpers\Url; ?>
<section id="content">
    <section class="vbox bg-white">       
        <section class="scrollable wrapper">
            <div class="row">
                <div class="text-center">
                    <img src="<?php echo Url::to('@web/images/login-logo.png', true); ?>" height="100" width="100"/>
                </div>                
            </div>
            <div class="col-sm-12">
                <h3>Order Receipt</h3>
            </div>
            <div>
                <div class="col-sm-6">
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
                <div class="col-sm-6 text-right">
                    <?php if ($orderArr['invoice_id'] != '') {
                        ?>
                        <strong>Order Receipt: </strong>#<?php
                        echo $orderArr['invoice_id'];
                    }
                    if ($orderArr['invoice_date'] != '') {
                        ?><br><strong>Date: </strong><?php
                        echo date('d/m/Y', strtotime($orderArr['invoice_date']));
                    }
                    ?>         
                </div>
            </div>           
            <div class="line"></div>
            <div class="col-sm-12">
                <table class="table">
                    <thead>
                        <tr></tr>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
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
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td><?php echo $product['order_qty']; ?></td>
                                        <td>S$<?php echo $product['product_price']; ?></td>
                                        <td>S$<?php
                                            $price = $product['product_price'] * $product['order_qty'];
                                            $totalAmount = $totalAmount + $price;
                                            echo $price;
                                            ?></td>

                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Subtotal</strong></td>
                            <td>S$<?php echo number_format($totalAmount, 2); ?></td>
                        </tr>

                        <tr>
                            <td colspan="3" class="text-right no-border"><strong>Shipping & Handling</strong></td>
                            <td>S$<?php echo number_format($orderArr['shipping_rate'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right no-border"><strong>Tax(<?php echo $orderArr['tax_rate']; ?>%)</strong></td>
                            <td><?php
                                $tax = ($totalAmount * $orderArr['tax_rate']) / 100;
                                echo 'S$' . number_format($tax, 2);
                                ?></td>
                        </tr>

                        <tr>
                            <td colspan="3" class="text-right"><strong>Subtotal</strong></td>
                            <td>S$<?php
                                $subtotal = $totalAmount + ($orderArr['shipping_rate'] + $tax);
                                echo number_format($subtotal, 2);
                                ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Less: Coupon Code Discount(<?php echo ($orderArr['promocode'] != '') ? $orderArr['promocode'] : '-'; ?>)</strong></td>
                            <td>S$<?php
                                $discount = 0;
                                echo number_format($discount, 2);
                                ?></td>
                        </tr>

                        <tr>
                            <td colspan="3" class="text-right no-border"><strong>Grand Total</strong></td>
                            <td><strong>S$<?php echo number_format($subtotal - $discount, 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right no-border"><strong>Payment Method</strong></td>
                            <td><strong><?php echo  $orderArr['payment_method']; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center">
                &copy; <?php echo date('Y'); ?> Boucle, All Rights Reserved.
            </div>
        </section>
    </section>
    <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
</section>
