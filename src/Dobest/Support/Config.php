<?php
namespace Dobest\Support;

/**
*
*/
class Config
{
    public static $config;

    private function __construct(array $config = [])
    {
        self::$config = $config;
    }

    /**
     * init set config value
     * @param  array  $config
     * @return void
     */
    public static function initConfig(array $config = [])
    {
        self::$config = $config;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public static function has($key)
    {
        return isset(self::$config[$key]);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if(self::has($key)){
            return self::$config[$key];
        }
        return $default;
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */
    public static function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                self::$config[$innerKey] = $innerValue;
            }
        } else {
            self::$config[$key] = $value;
        }
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public static function prepend($key, $value)
    {
        $array = self::get($key);

        array_unshift($array, $value);

        self::set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public static function push($key, $value)
    {
        $array = self::get($key);

        $array[] = $value;

        self::set($key, $array);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public static function all()
    {
        return self::$config;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public static function offsetExists($key)
    {
        return self::has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public static function offsetGet($key)
    {
        return self::get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public static function offsetSet($key, $value)
    {
        self::set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public static function offsetUnset($key)
    {
        self::set($key, null);
    }
}