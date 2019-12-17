<?php

/*
 *	Path helpers
 */

/**
 * Returns the absolute path to the root of the application
 * optionally appended by $str
 *
 * @param string $str
 * @return string
 */
function path($str=null)
{
    return sprintf("%s/%s", BASE_PATH, ltrim($str, "/"));
}
/**
 * Shortcut to App\Filter class
 * This could be considered dangerous to put here as it may encourage 'loose' use
 * of input checking, rather than enforcing 'checkpoints' in the models and controllers
 *
 * @param string $var
 * @param string $filter
 * @return mixed
 */
function filter($var, $type)
{
    $filter = new App\Filter();
    if (method_exists($filter, $type)) {
        return $filter->$type($var);
    }

    throw new Exception("Cannot filter '$type'");
}
