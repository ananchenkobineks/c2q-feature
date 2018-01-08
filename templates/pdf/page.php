<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script src="<?php echo C2QF_PLUGIN_DIR_URL; ?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?php echo C2QF_PLUGIN_DIR_URL; ?>assets/js/jszip.min.js"></script>
    <script src="<?php echo C2QF_PLUGIN_DIR_URL; ?>assets/js/kendo.all.min.js"></script>
</head>
<body>
    
    <?php include( 'styles.php' ); ?>

    <div class="action-box">
        <button class="button button-print"  onclick="window.print()">Save/Print</a>
        <button class="button button-export" onclick="getPDF('.pdf-page')">Send to Email</button>

        <div class="alert alert-success">
            Your Quote Invoice has just been emailed!
        </div>
    </div>

    <?php
        global $woocommerce;

        $current_cart = WC()->session->cart;
        $woocommerce->cart->empty_cart();

        $post_id = $_GET['quote_id'];
        $c2q_quoted_products = get_post_meta( $post_id, 'c2q_quoted_products', true );
    ?>
    
    <div class="pdf-page size-a4">
        <div class="pdf-body">

            <header>
                <div class="document-header">
                    <div class="company-information left">
                        <div class="company-logo left">
                            <a href="<?php echo get_home_url(); ?>" title="<?php _e('SECURITY DEVICES INTERNATIONAL INC.'); ?>">
                                <img src="<?php echo get_option("wc_pip_company_logo"); ?>">
                            </a>
                        </div>
                        <div class="company-contacts left">
                            <h1><?php _e('SECURITY DEVICES INTERNATIONAL INC.'); ?></h1>
                            <address>
                                <p>107 Audubon Road</p>
                                <p>Building 2, Suite 201</p>
                                <p>Wakefield, MA 01880</p>
                            </address>
                            <p>Email: <a href="mailto:dthrasher@securitydii.com">dthrasher@securitydii.com</a></p>
                            <p>Phone: <a href="tel:(905) 582-6402 x104">(905) 582-6402 x104</a></p>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="quote right">
                        <h2>Quote</h2>
                        <table>
                            <tr>
                                <td><?php _e( 'Date'); ?></td>
                                <td><?php echo date('m/d/Y'); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e( 'Quote'); ?> #</td>
                                <td><?php echo get_the_title($post_id); ?></td>
                            </tr>
                        </table>

                    </div>
                    <div class="clear"></div>
                </div>
                <div class="document-user-info">
                    <table class="quote-for left">
                        <tr>
                            <td><?php _e( 'Quote for'); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <?php $user_meta = get_user_meta( get_current_user_id() ); ?>
                                
                                <p><?php echo $user_meta['billing_company'][0]; ?></p>
                                <p><?php echo $user_meta['billing_first_name'][0].' '.$user_meta['billing_last_name'][0]; ?></p>
                                <p><?php echo $user_meta['billing_address_1'][0] .' '.$user_meta['billing_address_2'][0]; ?></p>
                                <p><?php echo $user_meta['billing_city'][0].', '.$user_meta['billing_state'][0].', '.$user_meta['billing_postcode'][0] ?></p>
                            </td>
                        </tr>
                    </table>
                    <table class="quote-valid right">
                        <tr>
                            <td><?php _e( 'Quote valid for 45 days from issuance'); ?></td>
                        </tr>
                    </table>
                    <div class="clear"></div>
                </div>
            </header>

            <main class="document-body invoice-body">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th class="quantity"><?php _e( 'Quantity'); ?></th>
                            <th class="item-number"><?php _e( 'Item Number'); ?></th>
                            <th class="description"><?php _e( 'Description'); ?></th>
                            <th class="unit-price"><?php _e( 'Unit Price'); ?></th>
                            <th class="total"><?php _e( 'Total'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 20px;">
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>

                        <?php $full_total = 0; ?>
                        <?php foreach($c2q_quoted_products as $product): ?>

                            <?php
                                $qty = $product['qty'];
                                $item_number = get_post_meta($product['id'], '_sku', true);

                                if (isset($product['org_price'])) {
                                    $org_price = $product['org_price'];
                                } else {
                                    $org_price = get_post_meta($product['id'], '_price', true);
                                }

                                $price = $product['price'];
                                $total = $product['org_price'] * $qty;
                                $full_total = $full_total + ($price * $qty);
                            ?>

                            <tr>
                                <td class="quantity"><?php echo $qty; ?></td>
                                <td class="item-number"><?php echo $item_number; ?></td>
                                <td class="description"><?php echo $product['title']; ?></td>
                                <td class="unit-price"><?php echo wc_price( $org_price ); ?></td>
                                <td class="total"><?php echo wc_price( $total ); ?></td>
                            </tr>

                            <?php $pc = ($org_price > 0 ? number_format(floatval(100 - ($price * 100 / $org_price)), 2) : 0); ?>

                            <?php if( $pc >= 1 ): ?>

                                <tr>
                                    <td></td>
                                    <td><?php echo str_replace('.00', '', $pc); ?>% <?php _e('Discount'); ?></td>
                                    <td></td>
                                    <td class="percent">-<?php echo $pc; ?>%</td>
                                    <td class="discount-total">-
                                    <?php 
                                        echo wc_price($org_price * $qty - $price * $qty);
                                    ?>
                                    </td>
                                </tr>

                            <?php endif; ?>

                            <tr style="height: 20px;">
                                <td></td><td></td><td></td><td></td><td></td>
                            </tr>

                            <?php
                                //$woocommerce->cart->add_to_cart(7390);
                                //$woocommerce->cart->add_to_cart(7405);
                                WC()->cart->add_to_cart(  $product['id'], $qty );
                            ?>

                        <?php endforeach; ?>


                        <?php
                            $shipping_tax_rates = WC_Tax::get_shipping_tax_rates();
                            $tax_rate = 0;
                            $tax_price = 0;

                            if( !empty($shipping_tax_rates) ) {
                                foreach($shipping_tax_rates as $key => $data) {
                                    $tax_rate = $data['rate'];
                                }

                                $temp_prod = new WC_Product();
                                $tax_total = c2q_get_price_including_tax($temp_prod, array('qty' => 1, 'price' => $full_total));

                                $tax_price = $tax_total - $full_total;
                                $full_total = $tax_total;
                            }
                        ?>

                        <tr>
                            <td></td>
                            <td><?php _e('Taxes'); ?></td>
                            <td><?php _e('State Taxes'); ?>-<?php echo $tax_rate; ?>% <?php _e('(if applicable)'); ?></td>
                            <td class="unit-price"><?php echo wc_price($tax_price); ?></td>
                            <td class="total"><?php echo wc_price($tax_price); ?></td>
                        </tr>
                        <tr style="height: 20px;">
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>

                        <?php
                            //WC()->session->cart = $current_cart;
                            //$contents = WC()->cart->cart_contents;
                            //WC()->cart->empty_cart();
                            //$woocommerce->cart->cart_contents = 11;
                            //WC()->cart = "1";
                            //$cart = WC()->session->cart;
                            //$woocommerce->cart->empty_cart();
                            //$woocommerce->cart->add_to_cart(7405);
                        
                            $customer = WC()->customer;

                            $packages = array( array(
                                'contents'          => WC()->cart->cart_contents,
                                'applied_coupons'   => WC()->cart->cart_contents,
                                'destination' => array(
                                    'country'   => $customer->get_shipping_country(),
                                    'state'     => $customer->get_shipping_state(),
                                    'postcode'  => $customer->get_shipping_postcode(),
                                    'city'      => $customer->get_shipping_city()
                                )
                            ) );

                            $packages = apply_filters('woocommerce_cart_shipping_packages', $packages);

                            WC()->shipping->calculate_shipping( $packages );
                            $shipping_methods = WC()->shipping->packages;
                        ?>

                        <?php if( isset($shipping_methods[0]['rates']) ): ?>
                        <?php foreach( $shipping_methods[0]['rates'] as $rate ): ?>

                                <?php $full_total = $full_total + $rate->cost; ?>

                                <tr>
                                    <td></td>
                                    <td><?php _e('Shipping'); ?></td>
                                    <td><?php echo $rate->label ?></td>
                                    <td class="unit-price"><?php echo wc_price( $rate->cost ); ?></td>
                                    <td class="total"><?php echo wc_price( $rate->cost ); ?></td>
                                </tr>

                        <?php endforeach; ?>
                        <?php endif; ?>

                        <?php
                            $woocommerce->cart->empty_cart();
                            WC()->session->cart = $current_cart;
                        ?>

                        <tr style="height: 50px;">
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="total" colspan="3">
                                <?php _e('Total'); ?>
                            </td>
                            <td class="value" colspan="2">
                                <?php echo wc_price($full_total); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </main>


        </div>
    </div>
    
    <div class="responsive-message"></div>

    <script>
        function getPDF(selector) {

            $('.button-export').attr("disabled", true);

            kendo.drawing.drawDOM($(selector)).then(function(group){
                kendo.drawing.pdf.toBlob(group, function(blob) {

                    var form = new FormData();
                    form.append("pdfFile", blob);

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "", true);
                    xhr.send(form);

                    xhr.onload = function () {
                        $(".alert-success").css('display', 'inline-block').delay(3000).fadeOut();
                        $('.button-export').attr("disabled", false);
                    };

                });
            });
        }
    </script>
</body>
</html>