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
        _reset_wp();
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
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_dnt"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_opis"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_url"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_bg"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_fg"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_gb"][1] == "reading" );
        $this->assertTrue( $wp_test_expectations["wp_settings_fields"]["zekom_gbh"][1] == "reading" );
        _reset_wp();
    }

    public function testOznaciPolje()
    {
        ob_start();
        $this->plugin->nastavi_chx(array('id' => "@ID"));
        $code = ob_get_clean();
        $this->assertContains("@ID", $code);
    }
    public function testInputPolje()
    {
        ob_start();
        $this->plugin->nastavi_text(array('id' => "@ID"));
        $code = ob_get_clean();
        $this->assertContains("@ID", $code);
    }
    public function testTextareaPolje()
    {
        ob_start();
        $this->plugin->nastavi_textarea(array('id' => "@ID"));
        $code = ob_get_clean();
        $this->assertContains("@ID", $code);
    }
    public function testBarvaPolje()
    {
        ob_start();
        $this->plugin->nastavi_barva(array('id' => "@ID", 'privzeto'=> "@BABAFF"));
        $code = ob_get_clean();
        $this->assertContains("@ID", $code);
        $this->assertContains("@BABAFF", $code);
    }
}