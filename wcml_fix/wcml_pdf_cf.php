<?php /**
 * Plugin Name: Woocommerce Multilingual PDF currency fix for WPML
 * Author Name: Pavel Riha
 * Plugin URI: http://www.papik-wordpress.eu
 * Description: WPML support for for WooCommerce PDF Invoices & Packing Slips using Woocommerce Multilingual from WPML with multi-currencies feature
 * Version: 1.0
 */

 if(!defined('ABSPATH')) { exit;}
 
 class WCML_PDF_CF{
     
     private $cs_all;
     private $currency;
     private $decimal_separator;
     private $thousand_separator;
     private $decimals;
     private $price_format;
     /**
      * 
      * @param int $id - shop_order $id
      */
     public function __construct($id){
        $this->cs_all = get_option('_wcml_settings');
        $this->currency = get_post_meta($id,'_order_currency',true);
        if(isset($this->cs_all["currency_options"][ $this->currency])) {
            $this->decimal_separator  =  $this->cs_all["currency_options"][ $this->currency]['decimal_sep'];
            $this->thousand_separator =  $this->cs_all["currency_options"][ $this->currency]['thousand_sep'];
            $this->decimals =  $this->cs_all["currency_options"][ $this->currency]['num_decimals'];
            $this->price_format  = $this->get_pdf_wcml_price_format( $this->cs_all['currency_options'][ $this->currency]['position']);

            add_filter ('wc_get_price_decimal_separator',array($this,'decimal_separator'),10,1);
            add_filter ('wc_get_price_thousand_separator',array($this,'thousand_separator'),10,1);
           // add_filter ('wc_get_price_decimals',array($this,'price_decimals'),10,1);
            add_filter('pre_option_woocommerce_price_num_decimals',array($this,'price_decimals'),10,1);
            add_filter ('woocommerce_currency',array($this,'currency'),10,1);
            //add_filter ('woocommerce_currency_symbol',array($this,'currency_symbol'),10,2);
            add_filter ('woocommerce_price_format',array($this,'price_format'),10,1);
            add_filter('plugin_locale',array($this,'get_locale'),10,1);
        }
         
     }
     public function get_pdf_wcml_price_format($currency_pos){
       
        switch ( $currency_pos ) {
		case 'left' :
			$format = '%1$s%2$s';
		break;
		case 'right' :
			$format = '%2$s%1$s';
		break;
		case 'left_space' :
			$format = '%1$s&nbsp;%2$s';
		break;
		case 'right_space' :
			$format = '%2$s&nbsp;%1$s';
		break;
		default: 
		$format = '%1$s%2$s';
	}
        return $format;
     }
     public function price_format($format){
         if(!empty($this->price_format)){ 
             return $this->price_format;
         }
         else {
             return $format;
         }
     }
     public function decimal_separator($separator){
          if(!empty($this->decimal_separator)){ 
             return $this->decimal_separator;
         }
         else {
             return $separator;
         }
        
     }
     public function thousand_separator($separator){
         if(!empty($this->thousand_separator)){ 
             return $this->thousand_separator;
         }
         else {
             return $separator;
         }
     }
    public function price_decimals($decimals){
         if(!empty($this->decimals)){ 
             return $this->decimals;
         }
         else {
             return $decimals;
         }
     }
     public function currency($currency){
          if(!empty($this->currency)){ 
             return $this->currency;
         }
         else {
             return $currency;
         }
      }
    public function currency_symbol($symbol, $currency){
         if($currency == 'CZK')    return htmlentities($symbol);
		 else return $symbol;
      }
     public function get_locale($locale){
         return $locale;
     }
 }
 
 add_action( 'wpo_wcpdf_process_template_order','load_WCML_PDF_CF',10,2);
 
 function load_WCML_PDF_CF($type,$order_id){
      //if($type=='invoice'){
        // echo "<pre>"; var_dump(get_class_methods($order));echo "</pre>";
        // $id = $order->get_id();
        //  $id = $order->order_id;
        new WCML_PDF_CF($order_id);
   // }
 }