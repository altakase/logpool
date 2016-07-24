<?php

namespace lib;
require_once(dirname(__FILE__) . '/Constants.php');
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

class Database
{
    /**
     * Initialize ORM from config.inc.php
     */
    public static function Initialize()
    {
        \ORM::configure('sqlite:' . BASE_DIR . '/db/' . CONFIG_SQLITE_FILE_NAME);
    }

    /**
     * @param int $page
     * @param int $count
     * @param int $total_count
     * @param int $per_page
     * @return array
     */
    public static function getPagerInfo($page, $count, $total_count, $per_page = Constants::ITEMS_PER_PAGE)
    {
        return array(
            'page' => $page,
            'count' => $count,
            'first' => max(($page - 1) * $per_page, 1),
            'last' => ($page - 1) * $per_page + $count,
            'total_count' => $total_count,
            'next_page' => $page * $per_page > $total_count ? null : $page + 1,
            'previous_page' => $page <= 1 ? null : $page - 1
        );
    }
}
