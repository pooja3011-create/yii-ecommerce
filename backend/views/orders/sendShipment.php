<?php

use yii\helpers\Url; ?>

<table width="100%" cellpadding="3" cellspacing="3" style="border:2px solid grey;color:black;font-family: comic sans ms;padding-left: 30px;padding-top: 15px;" background="#EEF3F7" width="275" height="95">
    <tr>
        <td>
            <img src="<?php echo Url::to('@web/images/login-logo.png', true); ?>" height="50" width="50"/>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>      
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            Hello <?php echo $params["name"]; ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>        
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            Shipment information for order no.<?php echo $params['order_id']; ?> as below:
        </td>
    </tr>   
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            <strong>Carrier:</strong><?php echo $params['carrier']; ?><br>
            <strong>Tracking Number:</strong><?php echo $params['traking_number']; ?><br>
            <strong>Date Shipped:</strong><?php echo date('d/m/Y', strtotime($params['shipped_date'])); ?><br>
            <strong>Shipped By:</strong><?php echo $params['shipment_from']; ?><br>
            <strong>Notes:</strong><?php echo $params['shipment_note']; ?><br>
            <strong>Products:</strong>
            <?php
            foreach ($params['products'] as $product) {
               echo $product['name'].'<br>';
            }
            ?>

        </td>        
    </tr>
    <tr>
        <td>
            &nbsp;
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