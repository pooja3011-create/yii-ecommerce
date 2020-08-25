<?php

use yii\helpers\Url; ?>

<table width="100%" cellpadding="3" cellspacing="3" style="border:2px solid grey;color:black;font-family: comic sans ms;padding-left: 30px;padding-top: 15px;" background="#EEF3F7" width="275" height="95">
    <tr>
        <td>
            <!--<a target="_blank" href="<?php // echo \Yii::$app->params['frontPath']; ?>"><img width="207" height="169" src="<?php // echo Yii::$app->params['imgPath']; ?>images/logo.jpg" border="0"/></a>-->
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>      
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            Hello <?php echo $params["first_name"] . ' ' . $params['last_name']; ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>        
    <tr>
        <td style="font-family: comic sans ms;font-size: 15px">
            <strong>You can use the following link within to reset your password:</strong>
            <br>
            <a href="<?php echo Url::to(['site/reset-password'], true) . '&token=' . $params['password_reset_token']; ?>"><?php echo Url::to(['site/reset-password'], true) . '&token=' . $params['password_reset_token']; ?></a>
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