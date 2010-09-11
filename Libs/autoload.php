<?php
/**
 * phpjobseeker
 *
 * Copyright (C) 2009 Kevin Benton - kbenton at bentonfam dot org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 * 
 */

function __autoload($class_name) {
    switch (true) {
        case ( preg_match('/Dao(Base|)$/', $class_name) ) :
            require_once 'Libs/Dao/' . $class_name . '.php' ;
            break ;
        case ( preg_match('/View(Base|)$/', $class_name) ) :
            require_once 'Libs/Views/' . $class_name . '.php' ;
            break ;
        default :
            require_once 'Libs/' . $class_name . '.php' ;
            break ;
    } // END OF switch (true)
}
