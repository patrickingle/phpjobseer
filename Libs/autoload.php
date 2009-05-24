<?php
/**
 * Created on Apr 29, 2009 by kbenton
 *
 */

function __autoload($class_name) {
    require_once 'Libs/' . $class_name . '.php';
}
