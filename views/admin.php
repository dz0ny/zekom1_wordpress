<?php



/**
* Nastavitve
*/
class ZEKOM_Nastavitve
{
  
  function __construct()
  {
    add_action( 'admin_init', array( &$this, 'nastavitve_zekom_admin' ) );
    add_filter( 'pre_update_option_zekom_gaid', array( &$this, 'delete_cache'), 10, 2 );
    add_filter( 'pre_update_option_zekom_opis', array( &$this, 'delete_cache'), 10, 2 );
    add_filter( 'pre_update_option_zekom_url', array( &$this, 'delete_cache'), 10, 2 );
    add_filter( 'pre_update_option_zekom_bg', array( &$this, 'delete_cache'), 10, 2 );
    add_filter( 'pre_update_option_zekom_fg', array( &$this, 'delete_cache'), 10, 2 );
    add_filter( 'pre_update_option_zekom_gb', array( &$this, 'delete_cache'), 10, 2 );
    add_filter( 'pre_update_option_zekom_gbh', array( &$this, 'delete_cache'), 10, 2 );
  }

  public function delete_cache($newvalue, $oldvalue )
  {
    if ($newvalue != $oldvalue ) {
      delete_transient( "cookie_law_cache" );
    }
    return $newvalue;
  }
  public function nastavitve_zekom_admin() {

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    add_settings_section( 'zekom_nastavitve',
      'Nastavitve piškotkov in sledenja za potrebe ZEKOM-1',
      array( &$this, 'zekom_nastavitve_zaslon' ),
      'reading' );

    add_settings_field( 'zekom_gaid',
      'Google Analyitcs tracking koda',
      array( &$this, 'nastavi_text' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_gaid") );
    register_setting( 'reading', 'zekom_gaid' );

    add_settings_field( 'zekom_dnt',
      'Upoštevaj DNT',
      array( &$this, 'nastavi_chx' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_dnt") );
    register_setting( 'reading', 'zekom_dnt' );

    add_settings_field( 'zekom_opis',
      'Uvodni tekst, ki se prikaže uporabniku ob vpisu podatkov',
      array( &$this, 'nastavi_textarea' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_opis") );
    register_setting( 'reading', 'zekom_opis' );

    add_settings_field( 'zekom_url',
      'Url do strani z nastavitvami. Polje za nastavljanje piškotkov omogočite z <code>[nastavi_piskotke]</code>',
      array( &$this, 'nastavi_stran' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_url") );
    register_setting( 'reading', 'zekom_url' );

    add_settings_field( 'zekom_bg',
      'Barva ozadja pozivnega okna',
      array( &$this, 'nastavi_barva' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_bg", "privzeto"=>"#ffffff") );
    register_setting( 'reading', 'zekom_bg' );
    add_settings_field( 'zekom_fg',
      'Barva pisave pozivnega okna',
      array( &$this, 'nastavi_barva' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_fg", "privzeto"=>"#757575") );
    register_setting( 'reading', 'zekom_fg' );
    add_settings_field( 'zekom_gb',
      'Barva gumbov pozivnega okna',
      array( &$this, 'nastavi_barva' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_gb", "privzeto"=>"#1e73be") );
    register_setting( 'reading', 'zekom_gb' );
    add_settings_field( 'zekom_gbh',
      'Barva gumbov gumbov pozivnega okna (z miško čez)',
      array( &$this, 'nastavi_barva' ),
      'reading',
      'zekom_nastavitve',
      array("id"=>"zekom_gbh", "privzeto"=>"#ffffff") );
    register_setting( 'reading', 'zekom_gbh' );

  }

  //pogledi
  function zekom_nastavitve_zaslon() {
    ?>
    <p>Več informacij kako vašo spletno stran pripravite skladno z ZEKOM-1 si lahko preberete na <a href="http://www.arnes.si/pomoc-uporabnikom/gostovanje-virtualnih-streznikov/spletni-piskotki-in-sprememba-slovenske-zakonodaje.html" target="_blank">http://www.arnes.si/pomoc-uporabnikom/gostovanje-virtualnih-streznikov/spletni-piskotki-in-sprememba-slovenske-zakonodaje.html</a></p>
    <?php 
  }
  function nastavi_chx($a) {
    ?>
    <input name="<?php echo $a["id"]; ?>" id="<?php echo $a["id"]; ?>" type="checkbox" value="<?php echo get_option( $a["id"] ); ?>" class="code" <?php echo checked( 1, get_option( $a["id"] ), true )?>  />
    <?php 
   }
  function nastavi_text($a) {
    ?>
    <input name="<?php echo $a["id"]; ?>" type="text" class="code" value="<?php echo get_option( $a["id"] ); ?>" /> 
    <?php
  }
  function nastavi_stran($a) {
    wp_dropdown_pages(array(
        'selected' => get_option( $a["id"] ),
        'name' => $a["id"],
        'id' => $a["id"],
    ));
  }
  function nastavi_textarea($a) {
    ?>
    <textarea name="<?php echo $a["id"]; ?>" class="code" cols="40" rows="5"><?php echo get_option( $a["id"] ); ?></textarea> 
    <?php
  }

  function nastavi_barva($a)
  {
    ?>
    <input type="text" name="<?php echo $a["id"]; ?>" id="<?php echo $a["id"]; ?>" value="<?php echo get_option( $a["id"], $a["privzeto"] ); ?>" data-default-color="<?php echo $a["privzeto"] ?>" />

    <script>
    jQuery( document ).ready(function(){
        "use strict";
     
        if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
            jQuery( '#<?php echo $a["id"]; ?>' ).wpColorPicker();
        }

    });
    </script>
    <?php
  }
}
return new ZEKOM_Nastavitve();