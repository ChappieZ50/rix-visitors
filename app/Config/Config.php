<?php

namespace App\Config;

class Config
{

    private static $path;
    private static $items;


    public static function get($string)
    {
        self::$path = __DIR__ . "/../../config/";
        $file = self::findFile($string);
        $values = self::getValues($file);
        if (is_array(self::$items)) {
            foreach (self::$items as $item) {
                if (isset($values[$item])) {
                    $values = $values[$item];
                }
            }
        }
        return $values;
    }

    public static function baseUrl()
    {
        $currentPath = $_SERVER['PHP_SELF'];
        $pathInfo = pathinfo($currentPath);
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https://' ? 'https://' : 'http://';
        return $protocol . $hostName . $pathInfo['dirname'] . "/";
    }

    public static function currentUrl()
    {
        $current = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $current;
    }

    private static function findFile($string)
    {
        $arr = explode('.', $string);
        if (isset($arr[0]) && file_exists(self::$path . $arr[0] . '.php')) {
            $fileName = $arr[0] . '.php';
            unset($arr[0]);
            self::$items = $arr;
            return self::$path . $fileName;
        }
        throw new \Exception('Config file not found');
    }

    private static function getValues($file)
    {
        $values = (include $file);
        return $values;
    }
}