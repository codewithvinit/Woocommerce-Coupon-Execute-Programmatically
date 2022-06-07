add_filter( 'woocommerce_cart_subtotal', 'royal_woocommerce_filter_checkout_for_coupons', 10, 3 );
function royal_woocommerce_filter_checkout_for_coupons( $subtotal, $compound, $cart ) {     
    //global $woocommerce;
    
    if(isset($_GET['coupon_code'])) {
        $couponid = $_GET['coupon_code']; 
        
        $_coupondata = new WC_Coupon($couponid);
        $store_credit = $_coupondata->amount;
        
        $minimum_amount = $_coupondata->minimum_amount;
        $cartsubtotal = WC()->cart->subtotal;
        $coupon_name = $couponid;
        $excluded_product_categories = $_coupondata->get_excluded_product_categories();
        $_status = 0;   
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            
            $product_id = $cart_item['product_id'];
            $terms = get_the_terms ( $product_id, 'product_cat' );
            foreach ( $terms as $term ) {
                $cat_id = $term->term_id;
                if(!in_array($cat_id, $excluded_product_categories)) {
                    $_status = 1;
                } else {
                    $_status = 0;
                    break;
                }
            }
        }
        if($_status == 1) {
            if($cartsubtotal >= $minimum_amount) {
                $coupon = array($coupon_name => $store_credit);
                // Apply the store credit coupon to the cart & update totals
                $cart->applied_coupons = array($coupon_name);
                $cart->set_discount_total($store_credit);
                $cart->set_total( $cart->get_subtotal() - $store_credit);
                $cart->coupon_discount_totals = $coupon;
            }
            
        
        }

    }
    return $subtotal; 
}
