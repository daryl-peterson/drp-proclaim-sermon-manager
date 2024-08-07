<?php

namespace DRPPSM\Tests;

use DRPPSM\App;
use DRPPSM\Helper;
use DRPPSM\Interfaces\TextDomainInt;
use DRPPSM\TextDomain;

/**
 * Class description.
 *
 * @category
 *
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @since       1.0.0
 */
class TextDomainTest extends BaseTest {

	public TextDomainInt $obj;

	public function setup(): void {
		$this->obj = App::init()->get( TextDomainInt::class );
	}

	public function testLoadDomain() {
		$hook = Helper::get_key_name( TextDomain::INIT_KEY );
		do_action( $hook );
		$this->assertIsString( $hook );

		$result = $this->obj->switch_to_site_locale();
		$this->assertNull( $result );

		$result = $this->obj->restore_locale();
		$this->assertNull( $result );
	}
}
