<?php
use Codeception\Util\Stub;

class ZekomTest extends \Codeception\TestCase\Test
{
    /**
     *
     *
     * @var \Plugin
     */
    private $plugin;
    /**
     *
     *
     * @var \CodeGuy
     */
    protected $codeGuy;

    protected function _before() {
        _reset_wp();
        $this->plugin = new ZEKOM;

    }

    protected function _after() {

    }

    // tests
    public function testCeZEKOMClassObstaja() {

        $this->assertTrue( class_exists( "ZEKOM" ) );
    }

    public function testCeSeZazeneObZagonuWP() {
        global $wp_test_expectations;
        $this->assertTrue( class_exists( "ZEKOM" ) );
        $this->assertTrue( isset( $wp_test_expectations["actions"]["wp_footer"][1] ) );
        $this->assertTrue( $wp_test_expectations["actions"]["wp_footer"][1] == "vstavi_kodo" );
        _reset_wp();
    }

    public function testCeZaznamDNT() {
        $_SERVER['HTTP_DNT'] = 1 ;
        $this->assertTrue( $this->plugin->_is_dnt() );
        unset( $_SERVER['HTTP_DNT'] );
    }

    public function testCeNezaznamDNT() {
        unset( $_SERVER['HTTP_DNT'] );
        $this->assertFalse( $this->plugin->_is_dnt() );
    }

    public function testCeOpcijaVsebujeImeClassa() {
        update_option("zekom_test_opcija", "DOMAČICA");
        $this->assertEquals( "DOMAČICA", $this->plugin->opcija("test_opcija") );
        _reset_wp();
    }

    public function testHashFunkcije() {
        $this->assertEquals( "565586621674444657", $this->plugin->_hashme( "ABC" ) );
    }

    public function testHashFunkcijeDaVednoVrne() {
        $this->assertEquals( "565586632079043445", $this->plugin->_hashme() );
    }

    public function testPravilenAnonID() {
        $this->assertEquals( "565582976131091899", $this->plugin->_generate_fingerprint() );
    }

    public function testPravilenPrstID() {
        update_option( "zekom_prst", true);
        $this->assertEquals( "565586265826893684", $this->plugin->_generate_fingerprint() );
        _reset_wp();
    }
    public function testPravilenPrstIDPrisiljen() {
        update_option( "zekom_prst", false);
        $this->assertEquals( "565586265826893684", $this->plugin->_generate_fingerprint(true) );
        _reset_wp();
    }
    public function testUstvariGAKodo() {
        $this->assertContains( "GA565582976131091899('create', 'UA-123456', {'storage': 'none'});", $this->plugin->_construct_ga_code( "UA-123456" ) );
    }

    public function testyoutubeNocokie() {
        $html = '<iframe width="1280" height="720" src="http://www.youtube.com/embed/JAV_YmtvVm0?rel=0" frameborder="0" allowfullscreen></iframe>';
        $fixed_html = '<iframe width="1280" height="720" src="//www.youtube-nocookie.com/embed/JAV_YmtvVm0?rel=0" frameborder="0" allowfullscreen></iframe>';

        $f = $this->plugin->embed_oembed_html_youtube_fix(
            $html,
            "",
            "",
            ""
        );
        $this->assertEquals( $fixed_html, $f );
    }

    public function testNastaviPiskoteVrneHTML() {
        update_option( "zekom_bg", "@ozadje");
        update_option( "zekom_fg", "@pisava");
        update_option( "zekom_gb", "@gumb");
        update_option( "zekom_gbh", "@gumb_hover");
        $code = $this->plugin->nastavi_piskotke();
        $this->assertNotContains( "scripts/scripts.js", $code );
        $this->assertContains( "@ozadje", $code) ;
        $this->assertContains( "@pisava", $code );
        $this->assertContains( "@gumb", $code );
        $this->assertContains( "@gumb_hover", $code );
        $this->assertContains( 'Se ne strinjam',  $code );
        $this->assertContains( 'Se strinjam',  $code );
    }

    public function testVstaviKodoVrneHTMLPozivCeUporabikNiSprejelPotrdila() {
        update_option( "zekom_gaid", "@GAID");
        ob_start();
        $this->plugin->vstavi_kodo();
        $code = ob_get_clean();
        $this->assertContains( "@GAID",  $code );
        $this->assertContains( '"storage":"none"',  $code );
        $this->assertContains( 'Nastavitve',  $code );
        $this->assertContains( 'Se strinjam',  $code );
    }

    public function testVstaviKodoVrneHTMLPozivCeUporabikJeSprejelPotrdilo() {
        update_option( "zekom_gaid", "@GAID");
        $_COOKIE["uporabnik_privolil"] = "da";
        ob_start();
        $this->plugin->vstavi_kodo();
        $code = ob_get_clean();
        $this->assertContains( "@GAID",  $code );
        $this->assertNotContains( '"storage":"none"',  $code );
        $this->assertNotContains( 'Nastavitve',  $code );
        $this->assertNotContains( 'Se strinjam',  $code );
    }
}
