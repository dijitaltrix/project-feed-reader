<?php
/**
 * Override the default Slim3 error handling 
 */
unset($container['notFoundHandler']);
unset($container['notAllowedHandler']);
unset($container['phpErrorHandler']);
unset($container['errorHandler']);