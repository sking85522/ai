<?php
/**
 * SciPHP Framework v2.0 — Single Entry Point
 * 
 * Just include this file to get access to all 15 libraries:
 *   require_once 'modules/index.php';
 * 
 * Then use any module:
 *   use NumPHP\NumPHP as np;
 *   use MLPHP\MLPHP as ml;
 *   etc.
 */

// Load the central autoloader which reads modules.php and loads all registered modules
require_once __DIR__ . '/autoload.php';