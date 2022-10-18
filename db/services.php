<?php

defined('MOODLE_INTERNAL') || die();

/*
  Moodle is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Moodle is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 */

$functions = [
    'mooges_get_category_from_course' => [
        'classname' => 'mooges',
        'methodname' => 'get_category_from_course',
        'classpath' => 'local/mooges/externallib.php',
        'description' => 'Get category id from course',
        'capabilities' => 'moodle/course:view',
        'type' => 'read',
    ],
    'mooges_get_roleid_by_shortname' => [
        'classname' => 'mooges',
        'methodname' => 'get_roleid_by_shortname',
        'classpath' => 'local/mooges/externallib.php',
        'description' => 'Get Role Id by shortname',
        'capabilities' => 'moodle/role:manage',
        'type' => 'read',
    ],
    'mooges_get_category_by_idnumber' => [
        'classname' => 'mooges',
        'methodname' => 'get_category_by_idnumber',
        'classpath' => 'local/mooges/externallib.php',
        'description' => 'Get Role Id by shortname',
        'capabilities' => 'moodle/category:manage',
        'type' => 'read',
    ],
    'mooges_get_users' => [
        'classname' => 'mooges',
        'methodname' => 'get_users',
        'classpath' => 'local/mooges/externallib.php',
        'description' => 'Get all users id',
        'capabilities' => 'moodle/user:editprofile',
        'type' => 'read',
    ],
    'mooges_get_stadistics' => [
        'classname' => 'mooges',
        'methodname' => 'get_stadistics',
        'classpath' => 'local/mooges/externallib.php',
        'description' => 'Get stadistics',
        'capabilities' => 'user:editprofile',
        'type' => 'read',
    ],
    'mooges_get_grades_by_userid' => [
        'classname' => 'mooges',
        'methodname' => 'get_grades_by_userid',
        'classpath' => 'local/mooges/externallib.php',
        'description' => 'Get grades',
        'capabilities' => 'user:editprofile',
        'type' => 'read',
    ],
    'mooges_get_badges_by_user_id' => [
        'classname' => 'mooges',
        'methodname' => 'get_badges_by_user_id',
        'classpath' => 'local/mooges/externallib.php',
        'description' => 'Get grades',
        'capabilities' => 'user:editprofile',
        'type' => 'read',
    ],
];

$services = [
    'MooGes API' => [
        'functions' => [
            'mooges_get_category_from_course',
            'mooges_get_roleid_by_shortname',
            'mooges_get_category_by_idnumber',
            'mooges_get_users_id',
            'mooges_get_stadistics',
            'mooges_get_grades_by_userid',
            'mooges_get_badges_by_user_id'
        ],
        'restrictedusers' => 1,
        'enabled' => 1,
        'shortname' => 'moogesapicalls'
    ]
];
