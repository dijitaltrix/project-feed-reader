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
function path($str=null) {
	return sprintf("%s/%s", BASE_PATH, ltrim($str, "/"));
}

