<?php
/**
 * Base test case for all WooCommerce tests.
 *
 * @package WooCommerce\Tests
 */

/**
 * WC Unit Test Case.
 *
 * Provides WooCommerce-specific setup/tear down/assert methods, custom factories,
 * and helper functions.
 *
 * @since 2.2
 */
class WC_Unit_Test_Case extends WP_HTTP_TestCase {

	/**
	 * Holds the WC_Unit_Test_Factory instance.
	 *
	 * @var WC_Unit_Test_Factory
	 */
	protected $factory;

	/**
	 * Setup test case.
	 *
	 * @since 2.2
	 */
	public function setUp() {

		parent::setUp();

		// Add custom factories.
		$this->factory = new WC_Unit_Test_Factory();

		// Setup mock WC session handler.
		add_filter( 'woocommerce_session_handler', array( $this, 'set_mock_session_handler' ) );

		$this->setOutputCallback( array( $this, 'filter_output' ) );

		// Register post types before each test.
		WC_Post_types::register_post_types();
		WC_Post_types::register_taxonomies();
	}

	/**
	 * Set up class unit test.
	 *
	 * @since 3.5.0
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		// Terms are deleted in WP_UnitTestCase::tearDownAfterClass, then e.g. Uncategorized product_cat is missing.
		WC_Install::create_terms();
	}

	/**
	 * Mock the WC session using the abstract class as cookies are not available.
	 * during tests.
	 *
	 * @since  2.2
	 * @return string The $output string, sans newlines and tabs.
	 */
	public function set_mock_session_handler() {
		return 'WC_Mock_Session_Handler';
	}

	/**
	 * Strip newlines and tabs when using expectedOutputString() as otherwise.
	 * the most template-related tests will fail due to indentation/alignment in.
	 * the template not matching the sample strings set in the tests.
	 *
	 * @since 2.2
	 *
	 * @param string $output The captured output.
	 * @return string The $output string, sans newlines and tabs.
	 */
	public function filter_output( $output ) {

		$output = preg_replace( '/[\n]+/S', '', $output );
		$output = preg_replace( '/[\t]+/S', '', $output );

		return $output;
	}

	/**
	 * Throws an exception with an optional message and code.
	 *
	 * Note: can't use `throwException` as that's reserved.
	 *
	 * @since 3.3-dev
	 * @param string $message Optional. The exception message. Default is empty.
	 * @param int    $code    Optional. The exception code. Default is empty.
	 * @throws Exception Containing the given message and code.
	 */
	public function throwAnException( $message = null, $code = null ) {
		$message = $message ? $message : "We're all doomed!";
		throw new Exception( $message, $code );
	}
}
