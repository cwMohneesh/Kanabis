<?php
/**
 * Rating's Template
 */

global $product;
$averange_rating= $product->get_rating_html();
$rating_count= $product->get_rating_count();

?>
<div class="rating-single-product">
<?php
// if we have some rating we'll show the div content.
if ($averange_rating!=''){
echo $averange_rating ." <span class='rating-text'>".$rating_count." ". _n("REVIEW","REVIEWS",$rating_count,"yit")." </span>";
}
?>
</div>
<div class="clearfix"></div>