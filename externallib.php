<?php

defined('MOODLE_INTERNAL') || die;

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

require_once($CFG->libdir . "/externallib.php");
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->libdir . '/badgeslib.php';
require_once($CFG->dirroot . "/local/mooges/lib.php");
require_once($CFG->dirroot . "/user/profile/lib.php");
require_once($CFG->dirroot . "/grade/querylib.php");

/**
 * Descripción of mooges
 *
 * Este plugin exitende el restful api de moodle agregando y ajustando
 * las necesidades de los desarrollos web sobre moodle de devlearning.cl
 * 
 * @author Alberto Ortiz Acevedo <aortizsm@gmail.com>
 */
class mooges extends external_api
{

    /**
     * Metodo que obtiene el id de la categoria dentro de un curso.
     * 
     * @global object $DB
     * @param int $courseid
     * @return array
     */
    public static function get_category_from_course($courseid)
    {

        global $DB;

        $params = self::validate_parameters(self::get_category_from_course_parameters(), ['courseid' => $courseid]);

        $context = context_system::instance();
        require_capability('moodle/course:view', $context);

        $sql = "SELECT category FROM {course} WHERE id = :courseid";
        $record = $DB->get_record_sql($sql, ['courseid' => $params['courseid']], MUST_EXIST);
        return (array) $record;
    }

    public static function get_category_from_course_parameters()
    {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Category id from course')
        ]);
    }

    public static function get_category_from_course_returns()
    {
        return new external_single_structure([
            'category' => new external_value(PARAM_INT, 'Category id'),
        ]);
    }

    /**
     * Metodo que retorna el roleid por medio del shortname
     * 
     * @param string $shortname
     * @return array welcome message
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     */
    public static function get_roleid_by_shortname($shortname = 'teacher')
    {
        global $DB;

        $params = self::validate_parameters(self::get_roleid_by_shortname_parameters(), ['shortname' => $shortname]);

        $context = context_system::instance();
        require_capability('moodle/role:manage', $context);

        $role = $DB->get_record('role', ['shortname' => $params['shortname']]);
        if ($role === false) {
            throw new moodle_exception('notexist', 'Invalid shortname');
        }

        return ['id' => $role->id, 'shortname' => $role->shortname];
    }

    public static function get_roleid_by_shortname_parameters()
    {
        return new external_function_parameters(
            ['shortname' => new external_value(PARAM_TEXT, 'role shortname')]
        );
    }

    public static function get_roleid_by_shortname_returns()
    {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'role id'),
            'shortname' => new external_value(PARAM_TEXT, 'shortname'),
        ]);
    }

    /**
     * Metodo que retorna el id de la categoria por medio del idnumber
     * 
     * @global object $DB
     * @param int $idnumber
     * @author Alberto Ortiz Acevedo <alberto@develearning.cl>
     * @return array
     */
    public static function get_category_by_idnumber($idnumber)
    {

        global $DB;

        $params = self::validate_parameters(self::get_category_by_idnumber_parameters(), ['idnumber' => $idnumber]);

        $context = context_system::instance();
        require_capability('moodle/category:manage', $context);

        $sql = "SELECT id, name, description, parent, sortorder, visible, visibleold FROM {course_categories} WHERE idnumber = :idnumber";
        $record = $DB->get_record_sql($sql, ['idnumber' => $params['idnumber']], MUST_EXIST);
        return (array) $record;
    }

    public static function get_category_by_idnumber_parameters()
    {
        return new external_function_parameters([
            'idnumber' => new external_value(PARAM_TEXT, 'Idnumber from category')
        ]);
    }

    public static function get_category_by_idnumber_returns()
    {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Instance id'),
            'name' => new external_value(PARAM_TEXT, 'fullname category'),
            'description' => new external_value(PARAM_TEXT, 'Description fullname category'),
            'parent' => new external_value(PARAM_INT, 'Category top'),
            'sortorder' => new external_value(PARAM_INT, 'Category sortorder'),
            'visible' => new external_value(PARAM_INT, 'Category visibility'),
            'visibleold' => new external_value(PARAM_INT, 'Category visibility'),
        ]);
    }

    /**
     * Metodo que obtiene a todos los usuarios activos con sus campos personalizados
     *
     * @global object $DB
     * @return array
     * @author Alberto Ortiz Acevedo <alberto@develearning.cl>
     */
    public static function get_users()
    {
        global $DB;

        $users = array();

        $context = context_system::instance();
        require_capability('moodle/user:editprofile', $context);

        $sql = "SELECT id FROM {user} WHERE suspended = false and deleted = 0 and id <> 1";
        $record = $DB->get_records_sql($sql);

        foreach ($record as $row) {

            $user = $DB->get_record('user', ['id' => $row->id]);
            profile_load_data($user);
            array_push($users, $user);
        }

        return $users;
    }

    public static function get_users_parameters()
    {
        return new external_function_parameters([]);
    }

    public static function get_users_returns()
    {
        return new external_multiple_structure(

            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'userid'),
                'username' => new external_value(PARAM_TEXT, 'username'),
                'firstname' => new external_value(PARAM_TEXT, 'first_name'),
                'lastname' => new external_value(PARAM_TEXT, 'last_name'),
                'email' => new external_value(PARAM_TEXT, 'email'),
                'city' => new external_value(PARAM_TEXT, 'city', 'VALUE_OPTIONAL'),

                //access
                'firstaccess' => new external_value(PARAM_TEXT, 'Primer acceso', 'VALUE_OPTIONAL'),
                'lastaccess' => new external_value(PARAM_TEXT, 'Ultimo acceso', 'VALUE_OPTIONAL'),
                'lastlogin' => new external_value(PARAM_TEXT, 'Ultimo login', 'VALUE_OPTIONAL'),
                'currentlogin' => new external_value(PARAM_TEXT, 'Actual login', 'VALUE_OPTIONAL'),
                'timecreated' => new external_value(PARAM_TEXT, 'fecha de creacion de usuario', 'VALUE_OPTIONAL'),

                //custom fields
                // 'profile_field_rut_lider' => new external_value(PARAM_TEXT, 'rut_lider', 'VALUE_OPTIONAL'),
            ])
        );
    }

    /**
     * Metodo que se encargara de volver algunas estadistricas de moodle
     * 
     * @global object $DB
     * @author Alberto Ortiz Acevedo <alberto@develearning.cl>
     * @return array
     */
    public function get_stadistics()
    {
        global $DB;

        $now = new DateTime();
        $start = clone $now;
        $start->setTime(00, 00, 00);
        $end = clone $now;
        $end->setTime(23, 59, 59);

        $context = context_system::instance();
        require_capability('moodle/user:editprofile', $context);

        $totalUsers = $DB->get_records_sql("SELECT id FROM {user} WHERE suspended = false");
        $totalAccess = $DB->get_records_sql(
            "SELECT id FROM {user} WHERE lastaccess >= :start AND lastaccess <= :end AND suspended = 0",
            [
                'start' => $start->getTimestamp(),
                'end' => $end->getTimestamp()
            ]
        );
        $totalCohorts = $DB->get_records_sql("SELECT id FROM {cohort}");
        $totalCourses = $DB->get_records_sql("SELECT id FROM {course} where visible = true");
        $gradeFoundCounter = 0;

        foreach ($totalCourses as $course) {

            $grades = grade_get_course_grades($course->id);

            foreach ($grades as $grade => $item) {

                if (!strcmp($item->str_grade, '-') == 0) {
                    $gradeFoundCounter++;
                }
            }
        }

        return [
            'total_users' => count($totalUsers),
            'total_access' => count($totalAccess),
            'total_cohorts' => count($totalCohorts),
            'total_courses' => count($totalCourses),
            'total_grades' => $gradeFoundCounter
        ];
    }

    public static function get_stadistics_parameters()
    {
        return new external_function_parameters([]);
    }

    public static function get_stadistics_returns()
    {
        return new external_single_structure([
            'total_users' => new external_value(PARAM_INT, 'Total users'),
            'total_access' => new external_value(PARAM_INT, 'Total access'),
            'total_cohorts' => new external_value(PARAM_INT, 'Total cohorts'),
            'total_courses' => new external_value(PARAM_INT, 'Total courses'),
            'total_grades' => new external_value(PARAM_INT, 'Total grades'),
        ]);
    }

    /**
     * Metodo que obtiene todos los cursos y sus notas por el id del usuario,
     * tecnicamente solo el grado final del libro de calificaciones
     * 
     * Solo devolera los cursos con notas, si no hay nota, no hay registro
     *
     * @param int $userid
     * @global object $DB
     * @author Alberto Ortiz Acevedo <alberto@develearning.cl>
     * @return array
     */
    public static function get_grades_by_user_id($userid)
    {
        global $DB;

        $params = self::validate_parameters(self::get_grades_by_user_id_parameters(), ['userid' => $userid]);

        $context = context_system::instance();
        require_capability('moodle/user:editprofile', $context);
        $courses = $DB->get_records_sql("SELECT id FROM {course} where visible = true");

        $grades = array();

        foreach ($courses as $course) {

            $contextCourse = context_course::instance($course->id);

            $user = $DB->get_record('user', ['id' => $userid]);

            if (is_enrolled($contextCourse, $user)) {

                $grade = grade_get_course_grade($user->id, $course->id);

                // if (!strcmp($grade->str_grade, '-') == 0) {
                array_push($grades, [
                    'courseid' => $course->id,
                    'grade' => (float) $grade->str_grade
                ]);
                // }
            }
        }

        return $grades;
    }

    public static function get_grades_by_user_id_parameters()
    {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'Moodle user id')
        ]);
    }

    public static function get_grades_by_user_id_returns()
    {
        return new external_multiple_structure(
            new external_single_structure([
                'courseid' => new external_value(PARAM_INT, 'courseid'),
                'grade' => new external_value(PARAM_FLOAT, 'grade')
            ])
        );
    }

    /**
     * Metodo que hace más simple consultar las insignias de usuarios
     *
     * @param int $userid
     * @author Alberto Ortiz Acevedo <alberto@develearning.cl>
     * @return array
     */
    public static function get_badges_by_user_id($userid)
    {
        global $DB;

        $badgesArray = array();

        $params = self::validate_parameters(self::get_badges_by_user_id_parameters(), ['userid' => $userid]);

        $context = context_system::instance();
        require_capability('moodle/user:editprofile', $context);

        $badges = badges_get_user_badges($params['userid']);

        foreach ($badges as $badge) {

            array_push($badgesArray, [
                'badgetid' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'version' => $badge->version,
                'point' => $badge->version,
                'type' => $badge->imagecaption,
                'visible' => $badge->visible
            ]);
        }
        return $badgesArray;
    }

    public static function get_badges_by_user_id_parameters()
    {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'Moodle user id')
        ]);
    }

    public static function get_badges_by_user_id_returns()
    {
        return new external_multiple_structure(
            new external_single_structure([
                'badgetid' => new external_value(PARAM_INT, 'badgetid'),
                'name' => new external_value(PARAM_TEXT, 'name'),
                'description' => new external_value(PARAM_TEXT, 'description'),
                'version' => new external_value(PARAM_TEXT, 'name'),
                'point' => new external_value(PARAM_TEXT, 'point'),
                'type' => new external_value(PARAM_TEXT, 'Type'),
                'visible' => new external_value(PARAM_BOOL, 'visible'),
            ])
        );
    }

    /**
     * Metodo que revisa si existe el enrolamiento en base a los parametros
     * 
     * Si no encuentra el enroll con el role indicado devolvera false, asi como
     * tambien, si no existe el role en primera instancia, devolvera false
     *
     * @param string $username
     * @param string $shortname
     * @param string $role
     * @author Alberto Ortiz Acevedo <alberto@develearning.cl>
     * @return boolean
     */
    public function is_enrollment_exists($username, $shortname, $role)
    {
        global $DB;

        //obtenemos el roleid desde el shortname del role
        $roleId = $DB->get_record_sql("SELECT id FROM {role} WHERE shortname LIKE :role", ['role' => $role]);

        if (empty($roleId)) {
            return ['exists' => false];
        }

        $params = self::validate_parameters(self::is_enrollment_exists_parameters(), [
            'username' => $username,
            'shortname' => $shortname,
            'role' => $roleId->id
        ]);

        $isEnroll = $DB->count_records_sql(
            "SELECT count(*)
            FROM {role_assignments} ra, {user} u, {course} c, {context} cxt
            WHERE ra.userid = u.id
            AND ra.contextid = cxt.id
            AND cxt.contextlevel = 50
            AND cxt.instanceid = c.id
            AND u.username LIKE :username
            AND c.shortname LIKE :shortname
            AND roleid = :roleid",
            [
                'username' => $params['username'],
                'shortname' => $params['shortname'],
                'roleid' => $params['role']
            ]
        );

        if ($isEnroll > 0) {
            return ['exists' => true];
        } else {
            return ['exists' => false];
        }
    }

    public static function is_enrollment_exists_parameters()
    {
        return new external_function_parameters([
            'username' => new external_value(PARAM_TEXT, 'username user'),
            'shortname' => new external_value(PARAM_TEXT, 'shortname course'),
            'role' => new external_value(PARAM_TEXT, 'shortname role'),
        ]);
    }

    public static function is_enrollment_exists_returns()
    {
        return new external_single_structure([
            'exists' => new external_value(PARAM_BOOL, 'True si existe'),
        ]);
    }
}
