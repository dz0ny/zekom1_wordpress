<?php
use Codeception\Util\Stub;

class ZekomAdminTest extends \Codeception\TestCase\Test
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
        $this->plugin = new ZEKOM_Nastavitve;

    }

    protected function _after() {

    }

    // tests
    public function testCeZEKOM_NastavitveClassObstaja() {

        $this->assertTrue( class_exists( "ZEKOM_Nastavitve" ) );
    }

    public function testCeSeZazeneObZagonuWPAdmin() {
        global $wp_test_expectations;
        $this->assertTrue( class_exists( "ZEKOM_Nastavitve" ) );
        $this->assertTrue( isset( $wp_test_expectations["actions"]["admin_init"][1] ) );
        $this->assertTrue( $wp_test_expectations["actions"]["admin_init"][1] == "nastavitve_zekom_admin" );
        _reset_wp();
    }

    public function testCeSoNastavitve() {
        global $wp_test_expectations;
        $this->plugin->nastavitve_zekom_admin();
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_gaid"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_prst"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_opis"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_url"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_bg"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_fg"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_gb"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_gbh"][1] == "reading" );
        _reset_wp();
    }
}