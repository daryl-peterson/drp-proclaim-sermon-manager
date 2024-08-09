<?php
/**
 * Image sizes test.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM\Tests;

use DRPPSM\HooksUtils;
use DRPPSM\ImageSizes;
use DRPPSM\Logging\Logger;

/**
 * Image sizes test.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class ImageSizesTest extends BaseTest {

	/**
	 * ImageSizes object.
	 *
	 * @var ImageSizes
	 */
	protected ImageSizes $obj;

	/**
	 * This method is called before each test.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function setup(): void {
		$this->obj = ImageSizes::init();
	}

	/**
	 * Test hooks were registered.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function test_register() {
		$result = $this->obj->register();
		$this->assertTrue( $result );

		// add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
		$obj    = HooksUtils::init();
		$result = $obj->find_hook_callback_instances( 'after_setup_theme', array( ImageSizes::class, 'add_image_sizes' ) );

		$count = count( $result );
		$this->assertGreaterThan( 0, $count );
		Logger::debug( $result );
	}

	/**
	 * Test add image sizes.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function test_add_image_sizes() {
		$result = $this->obj->add_image_sizes();
		$this->assertTrue( $result );

		$sizes = $this->obj->get_sizes();
		$this->assertIsArray( $sizes );

		foreach ( $sizes as $name => $value ) {
			$result = has_image_size( $name );
			$this->assertTrue( $result );
		}
	}
}