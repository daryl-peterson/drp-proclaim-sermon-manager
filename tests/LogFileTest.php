<?php

namespace DRPSermonManager\Tests;

use DRPSermonManager\LogFile;

/**
 * Log file test.
 *
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 */
class LogFileTest extends BaseTest
{
    public LogFile $obj;

    public function setup(): void
    {
        $this->obj = new LogFile();
    }

    public function testTruncate()
    {
        $file = '/tmp/truncate.log';

        @unlink($file);
        $result = $this->obj->checkFileSize($file);
        $this->assertNull($result);
    }
}
