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

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','GA@hash');

  GA@hash('create', '@gaid', @parametri);
  GA@hash('set', 'anonymizeIp', true);
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

  public function _generate_fingerprint($force = false) {


    if ($this->opcija("prst") || $force) {
      $finger = array(
        $_SERVER['HTTP_ACCEPT'],
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ,
        $_SERVER['HTTP_ACCEPT_ENCODING'] ,
        $_SERVER['HTTP_USER_AGENT'] ,
        $_SERVER['REMOTE_ADDR'],
      );
    }else{
      $finger = array(
        $_SERVER['SERVER_NAME'],
      );
    }
    return $this->_hashme( implode( "-", $finger ) );
  }

  public function vstavi_kodo()
  {
    global $post;
    $gaid= $this->opcija("gaid");
    $parametri = array();

    if (isset($_COOKIE["uporabnik_privolil"]) && $_COOKIE["uporabnik_privolil"] == "da") {
      $parametri["clientId"] = $this->_generate_fingerprint(true);
      $json = json_encode($parametri);
      echo $this->_construct_ga_code($gaid, $json);
    }else{
      $parametri["storage"] = "none";
      $parametri["clientId"] = $this->_generate_fingerprint();
      $json = json_encode($parametri);
      echo $this->_construct_ga_code($gaid, $json);
    }
    if (isset($post) && $post->ID == $this->opcija("url")) {
      return;
    }
    if (!isset($_COOKIE["uporabnik_privolil"])) {
      echo $this->nastavi_piskotke("index");
    }

  }

  function nastavi_piskotke($tip= "spremeni") {
    $code = file_get_contents(__DIR__."/js/prod/".$tip.".html");
    $js = file_get_contents(__DIR__."/js/prod/scripts/scripts.js");
    $code =  mb_eregi_replace( "#F7F7F7", $this->opcija("bg"), $code );
    $code =  mb_eregi_replace( "href=\"#\"", "href=\"".get_permalink($this->opcija("url"))."\"", $code );
    $code =  mb_eregi_replace( "rgb\(117, 117, 117\)", $this->opcija("fg"), $code );
    $code =  mb_eregi_replace( "#DD4814", $this->opcija("gb"), $code );
    $code =  mb_eregi_replace( "rgb\(142, 72, 194\)", $this->opcija("gbh"), $code );
    $code =  mb_eregi_replace( "<script src=\"scripts\/scripts.js\"><\/script>", "<script>".$js."</script>", $code );
    $code =  mb_eregi_replace( "Spletno mesto uporablja piškotke za zagotavljanje boljše uporabniške izkušnje in spremljanje statistike obiska. Z izborom opcije \"se strinjam\" se strinjate z uporabo piškotkov na tem spletnem mestu.", $this->opcija("opis"),$code );
    return $code;
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
