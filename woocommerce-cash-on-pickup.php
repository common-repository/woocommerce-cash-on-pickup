<?php
/**
 * Plugin Name: woocommerce-cash-on-pickup
 * Plugin URI: http://www.tmcr.nl/woocommerce-cash-on-pickup
 * Description: This plugin adds a payment option cash on pickup, if for example the customer wants to pick up the product at the 
 * webshop owners store. This is different from cash on delivery because this often costs money and the product is delivered
 * to the customer.
 * Version: 1.1
 * Author: Chantal Rosmuller
 * Author URI: http://www.tmcr.nl
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('plugins_loaded', 'woocommerce_gateway_name_init', 0);
 
function woocommerce_gateway_name_init() {
 
    if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
 
    /**
     * Localisation
     */
    load_plugin_textdomain('wc-gateway-name', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
    
    /**
     * Gateway class
     */
    class WC_Gateway_CASH_ON_PICKUP extends WC_Payment_Gateway {
    
        // Go wild in here

        public function __construct() {
            $this->id               = 'cop';
            $this->icon             = apply_filters('woocommerce_cop_icon', '');
            $this->has_fields       = false;
            $this->method_title     = __( 'Cash on pickup', 'woocommerce' );
            
            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title            = $this->get_option( 'title' );
            $this->description      = $this->get_option( 'description' );

            // Actions
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_thankyou_bacs', array( $this, 'thankyou_page' ) );
        }


        /**
        * Initialise Gateway Settings Form Fields
        *
        * @access public
        * @return void
        */
        function init_form_fields() {

        // $shipping_methods = array();

        // if ( is_admin() ){
        //     foreach ( $woocommerce->shipping->load_shipping_methods() as $method ) {
        //         $shipping_methods[ $method->id ] = $method->get_title();
        //     }
        // }

        $this->form_fields = array(
            'enabled' => array(
                            'title' => __( 'Enable/Disable', 'woocommerce' ),
                            'type' => 'checkbox',
                            'label' => __( 'Enable Cash On Pickup', 'woocommerce' ),
                            'default' => 'yes'
                        ),
            'title' => array(
                            'title' => __( 'Title', 'woocommerce' ),
                            'type' => 'text',
                            'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                            'default' => __( 'Cash On Pickup', 'woocommerce' ),
                            'desc_tip'      => true,
                        ),
            'description' => array(
                            'title' => __( 'Customer Message', 'woocommerce' ),
                            'type' => 'textarea',
                            'default' => __( "Pay your order in cash as you pick it up at our store.", 'woocommerce' )
                        ),
            // 'enable_for_methods' => array(
            //     'title'         => __( 'Enable for shipping methods', 'woocommerce' ),
            //     'type'          => 'multiselect',
            //     'class'         => 'chosen_select',
            //     'css'           => 'width: 450px;',
            //     'default'       => '',
            //     'description'   => __( 'If COP is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'woocommerce' ),
            //     'options'       => $shipping_methods,
            //     'desc_tip'      => true,),
            );

        }
    
        /**
         * Admin Panel Options
         * - Options for bits like 'title' 
         *
         * @access public
         * @return void
         */
        public function admin_options() {
            ?>
            <h3><?php _e( 'COP Payment', 'woocommerce' ); ?></h3>
            <p><?php _e('Allows Cash on Pickup payments.', 'woocommerce' ); ?></p>
            <table class="form-table">
            <?php
                // Generate the HTML For the settings form.
                $this->generate_settings_html();
            ?>
            </table><!--/.form-table-->
            <?php
        }




    } // end class
    
    /**
    * Add the Gateway to WooCommerce
    **/
    function woocommerce_add_gateway_name_gateway($methods) {
        $methods[] = 'WC_Gateway_CASH_ON_PICKUP';
        return $methods;
    }
    
    add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_name_gateway' );
} 
