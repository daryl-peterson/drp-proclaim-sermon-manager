<?php

namespace DRPSermonManager;

use DRPSermonManager\Core\Interfaces\LogFormatterInterface;
use DRPSermonManager\Core\Interfaces\NoticeInterface;
use DRPSermonManager\Core\Interfaces\OptionsInterface;
use DRPSermonManager\Core\Interfaces\PluginInterface;
use DRPSermonManager\Core\Interfaces\RequirementsInterface;

/**
 * App configuration.
 *
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 */
class AppConfig
{
    public static function get(): array
    {
        return [
            LogFormatterInterface::class => function () {
                return new LogFormatter();
            },

            NoticeInterface::class => function () {
                return Notice::getInstance();
            },

            OptionsInterface::class => function () {
                return Options::getInstance();
            },

            PluginInterface::class => function () {
                return new Plugin();
            },

            RequirementsInterface::class => function (NoticeInterface $notice) {
                return new Requirements($notice);
            },

            AdminPage::class => function () {
                return new AdminPage();
            },
        ];
    }
}
