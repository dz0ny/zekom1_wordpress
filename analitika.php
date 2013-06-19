<?php
/*
 * Plugin Name: ZEKOM-1 Analitika, Wordpress
 * Version: 1.0
 * Author: dz0ny
 */

/**
* Sledi uporabniku in sporoča piškotke na in je skladen z ZEKOM-1
*/

require_once("views/admin.php");

class ZEKOM {

  public static $gacode = <<<EOD
  <script type="text/javascript">
  !function(r){"use strict";var t=function(r){var t=Array.prototype.forEach,e=Array.prototype.map;this.each=function(r,e,n){if(null!==r)if(t&&r.forEach===t)r.forEach(e,n);else if(r.length===+r.length){for(var a=0,h=r.length;h>a;a++)if(e.call(n,r[a],a,r)==={})return}else for(var o in r)if(r.hasOwnProperty(o)&&e.call(n,r[o],o,r)==={})return},this.map=function(r,t,n){var a=[];return null==r?a:e&&r.map===e?r.map(t,n):(this.each(r,function(r,e,h){a[a.length]=t.call(n,r,e,h)}),a)},r&&(this.hasher=r)};t.prototype={get:function(){var r=[];r.push(navigator.userAgent),r.push(navigator.language),r.push(@hash),r.push(screen.colorDepth),r.push((new Date).getTimezoneOffset()),r.push(!!window.sessionStorage),r.push(!!window.localStorage);var t=this.map(navigator.plugins,function(r){var t=this.map(r,function(r){return[r.type,r.suffixes].join("~")}).join(",");return[r.name,r.description,t].join("::")},this).join(";");return r.push(t),this.hasher?this.hasher(r.join("###"),31):this.murmurhash3_32_gc(r.join("###"),31)},murmurhash3_32_gc:function(r,t){var e,n,a,h,o,i,s,c;for(e=3&r.length,n=r.length-e,a=t,o=3432918353,i=461845907,c=0;n>c;)s=255&r.charCodeAt(c)|(255&r.charCodeAt(++c))<<8|(255&r.charCodeAt(++c))<<16|(255&r.charCodeAt(++c))<<24,++c,s=4294967295&(65535&s)*o+((65535&(s>>>16)*o)<<16),s=s<<15|s>>>17,s=4294967295&(65535&s)*i+((65535&(s>>>16)*i)<<16),a^=s,a=a<<13|a>>>19,h=4294967295&5*(65535&a)+((65535&5*(a>>>16))<<16),a=(65535&h)+27492+((65535&(h>>>16)+58964)<<16);switch(s=0,e){case 3:s^=(255&r.charCodeAt(c+2))<<16;case 2:s^=(255&r.charCodeAt(c+1))<<8;case 1:s^=255&r.charCodeAt(c),s=4294967295&(65535&s)*o+((65535&(s>>>16)*o)<<16),s=s<<15|s>>>17,s=4294967295&(65535&s)*i+((65535&(s>>>16)*i)<<16),a^=s}return a^=r.length,a^=a>>>16,a=4294967295&2246822507*(65535&a)+((65535&2246822507*(a>>>16))<<16),a^=a>>>13,a=4294967295&3266489909*(65535&a)+((65535&3266489909*(a>>>16))<<16),a^=a>>>16,a>>>0}},r.Fingerprint=t}(window);
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','GA@hash');

  GA@hash('create', '@gaid', @parametri);
  GA@hash('forceSSL', true);
  GA@hash('send', 'pageview');

  </script>
EOD;
  
  function __construct() {

    add_action( 'wp_footer', array( &$this, 'vstavi_kodo' ), 9999 );
    add_filter( 'embed_oembed_html', array( &$this, 'embed_oembed_html_youtube_fix' ), 9999, 4 );
    add_shortcode( "nastavi_piskotke", array( &$this, 'nastavi_piskotke_cb' ) );
  }

  function embed_oembed_html_youtube_fix( $html, $url, $attr, $post_ID ) {
    $html = preg_replace( "/\.youtube\./", ".youtube-nocookie.", $html );
    return preg_replace( "/http:/", "", $html );
  }

  public function opcija($key)
  {
    return get_option( strtolower(get_class($this)."_".$key), false );
  }

  public function _generate_fingerprint() {

    return $this->_hashme( $this->_get_ip() );
  }
  public function _get_ip() {
      $ip = $_SERVER['REMOTE_ADDR'];
   
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
          $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }
   
      return $ip;
  }

  public function vstavi_kodo()
  {
    global $post;
    $gaid= $this->opcija("gaid");
    if (empty($gaid) || ($this->opcija("dnt") && $this->_is_dnt()) ) {
      return;
    }
    if (isset($_COOKIE["uporabnik_privolil"]) && $_COOKIE["uporabnik_privolil"] == "da") {
      $json = "{'clientId': new Fingerprint().get()}";
      echo $this->_construct_ga_code($gaid, $json);
    }else{
      $json = "{'storage': 'none', 'clientId': new Fingerprint().get()}";
      echo $this->_construct_ga_code($gaid, $json);
    }

    // Ne prikaži pogleda ker se uporabi shortcode
    if (isset($post) && ($post->ID == $this->opcija("url")) ) {
      return;
    }

    if (!isset($_COOKIE["uporabnik_privolil"])) {
      echo $this->nastavi_piskotke("index");
    }

  }

  function nastavi_piskotke($tip= "spremeni") {
    if ( false === ( $code = get_transient( 'cookie_law_cache' ) ) ) {
      $code = file_get_contents(__DIR__."/js/prod/".$tip.".html");
      $js = file_get_contents(__DIR__."/js/prod/scripts/scripts.js");
      $code =  mb_eregi_replace( "#F7F7F7", $this->opcija("bg"), $code );
      $code =  mb_eregi_replace( "href=\"#\"", "href=\"".get_permalink($this->opcija("url"))."\"", $code );
      $code =  mb_eregi_replace( "rgb\(117, 117, 117\)", $this->opcija("fg"), $code );
      $code =  mb_eregi_replace( "#DD4814", $this->opcija("gb"), $code );
      $code =  mb_eregi_replace( "rgb\(142, 72, 194\)", $this->opcija("gbh"), $code );
      $code =  mb_eregi_replace( "<script src=\"scripts\/scripts.js\"><\/script>", "<script>".$js."</script>", $code );
      $code =  mb_eregi_replace( "Spletno mesto uporablja piškotke za zagotavljanje boljše uporabniške izkušnje in spremljanje statistike obiska. Z izborom opcije \"se strinjam\" se strinjate z uporabo piškotkov na tem spletnem mestu.", $this->opcija("opis"),$code );
      set_transient( 'cookie_law_cache', $code, 60*60*24*30 );//30 dni
      return $code;
    }else{
      get_transient( 'cookie_law_cache' );
    }
      
  }

  function nastavi_piskotke_cb() {
    return $this->nastavi_piskotke($tip= "spremeni");
  }

  public function _is_dnt() {
    return isset( $_SERVER['HTTP_DNT'] ) && $_SERVER['HTTP_DNT']==1;
  }

  public function _construct_ga_code( $gaid = False, $parametri = "{'storage': 'none'}" ) {
    $code =  mb_eregi_replace( "@hash", $this->_generate_fingerprint(), self::$gacode );
    $code =  mb_eregi_replace( "@parametri", $parametri, $code );
    return mb_eregi_replace( "@gaid", $gaid, $code );
  }

  public function _hashme( $value = "" ) {
    $a = strlen( $value );
    $b = 0xff1 * 1984;
    $c = $value;
    $a -= ( $b + $c );
    $a ^= ( $c >> 13 );
    $b -= ( $c + $a );
    $b ^= ( $a << 8 );
    $c -= ( $a + $b );
    $c ^= ( $b >> 13 );
    $a -= ( $b + $c );
    $a ^= ( $c >> 12 );
    $b -= ( $c + $a );
    $b ^= ( $a << 16 );
    $c -= ( $a + $b );
    $c ^= ( $b >> 5 );
    $a -= ( $b + $c );
    $a ^= ( $c >> 3 );
    $b -= ( $c + $a );
    $b ^= ( $a << 10 );
    $c -= ( $a + $b );
    $c ^= ( $b >> 15 );
    return abs( $c );
  }

}

add_action( 'init', create_function( '', 'return new ZEKOM();' ) );
