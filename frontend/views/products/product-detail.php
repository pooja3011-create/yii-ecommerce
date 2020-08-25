<?php

use yii\helpers\Url;

echo '<pre>';
print_r($productArr);
echo '</pre>';
$user_id = 1;
$url = Url::to(['products/add-to-cart','product_id'=>$productArr['id'],'user_id'=>$user_id]);
$wishlistUrl = Url::to(['products/add-to-wishlist','product_id'=>$productArr['id'],'user_id'=>$user_id]);
?>
<a href="javascript:;" onclick="return addtocart('<?php echo $url; ?>');">add to cart</a>
<a href="javascript:;" onclick="return addtowishlist('<?php echo $wishlistUrl; ?>');">Wishlist</a>