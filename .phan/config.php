<?php
/**
 * Phan static analysis configuration file.
 * @see https://github.com/phan/phan/blob/master/.phan/config.php
 */

return [
    "directory_list" => [
        "src/",
        "vendor/",
    ],
    "exclude_analysis_directory_list" => [
        "vendor/",
    ],
    "target_php_version" => "7.0",
];
