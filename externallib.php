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

        //Parameter validation
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
     * @return array
     */
    public static function get_users_id()
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

    public static function get_users_id_parameters()
    {
        return new external_function_parameters([
            // 'idnumber' => new external_value(PARAM_TEXT, 'Idnumber from category')
        ]);
    }

    public static function get_users_id_returns()
    {
        return new external_multiple_structure(

            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'userid'),
                'username' => new external_value(PARAM_TEXT, 'username'),
                'firstname' => new external_value(PARAM_TEXT, 'first_name'),
                'lastname' => new external_value(PARAM_TEXT, 'last_name'),
                'email' => new external_value(PARAM_TEXT, 'email'),
                'city' => new external_value(PARAM_TEXT, 'city', 'VALUE_OPTIONAL'),
                //custom fields
                'profile_field_rut_lider' => new external_value(PARAM_TEXT, 'rut_lider', 'VALUE_OPTIONAL'),
                'profile_field_nombre_lider' => new external_value(PARAM_TEXT, 'nombre_lider', 'VALUE_OPTIONAL'),
                'profile_field_rut_jefatura' => new external_value(PARAM_TEXT, 'rut_jefatura', 'VALUE_OPTIONAL'),
                'profile_field_nombre_jefatura' => new external_value(PARAM_TEXT, 'nombre_jefatura', 'VALUE_OPTIONAL'),
                'profile_field_cargo' => new external_value(PARAM_TEXT, 'cargo', 'VALUE_OPTIONAL'),
                'profile_field_zona2' => new external_value(PARAM_TEXT, 'zona2', 'VALUE_OPTIONAL'),
                'profile_field_gerencia' => new external_value(PARAM_TEXT, 'gerencia', 'VALUE_OPTIONAL'),
                'profile_field_punto_de_venta' => new external_value(PARAM_TEXT, 'punto de venta', 'VALUE_OPTIONAL'),
                'profile_field_regional' => new external_value(PARAM_TEXT, 'regional', 'VALUE_OPTIONAL'),
                'profile_field_tipo_cargo' => new external_value(PARAM_TEXT, 'tipo_cargo', 'VALUE_OPTIONAL'),
                'profile_field_empresa' => new external_value(PARAM_TEXT, 'empresa', 'VALUE_OPTIONAL'),
                'profile_field_campana' => new external_value(PARAM_TEXT, 'campana', 'VALUE_OPTIONAL'),
                'profile_field_estado' => new external_value(PARAM_TEXT, 'estado', 'VALUE_OPTIONAL'),
                // 'profile_field_mibrillo' => new external_value(PARAM_TEXT, 'mibrillo', 'VALUE_OPTIONAL'),
                // 'profile_field_midatofreak' => new external_value(PARAM_TEXT, 'midatofreak', 'VALUE_OPTIONAL'),
                'profile_field_zona' => new external_value(PARAM_TEXT, 'zona', 'VALUE_OPTIONAL'),
                //access
                'firstaccess' => new external_value(PARAM_TEXT, 'Primer acceso', 'VALUE_OPTIONAL'),
                'lastaccess' => new external_value(PARAM_TEXT, 'Ultimo acceso', 'VALUE_OPTIONAL'),
                'lastlogin' => new external_value(PARAM_TEXT, 'Ultimo login', 'VALUE_OPTIONAL'),
                'currentlogin' => new external_value(PARAM_TEXT, 'Actual login', 'VALUE_OPTIONAL'),
                'timecreated' => new external_value(PARAM_TEXT, 'fecha de creacion de usuario', 'VALUE_OPTIONAL'),


            ])
        );
    }

    /**
     * Metodo que se encargara de volver algunas estadistricas de moodle
     * 
     * @return array
     */
    public function get_stadistic()
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

        // $totalGrades = 0;
        // $courseGrades = [];

        // foreach ($totalCourses as $course) {

        //     //obtener usuarios del curso
        //     $users =  get_enrolled_users(context_course::instance($course->id));
        //     $grade = grade_get_course_grades(context_course::instance($course->id), array_keys($users));
        //     array_push($courseGrades, $grade);
        // }

        // foreach ($courseGrades as $grade) {
        //     $totalGrades +=$item->grades[$userid] => $usercoursegrade;
        // }

        // var_dump($courseGrades);


        return [
            'total_users' => count($totalUsers),
            'total_access' => count($totalAccess),
            'total_cohorts' => count($totalCohorts),
            'total_courses' => count($totalCourses),
            // 'total_grades' => 1

        ];
    }

    public static function get_stadistic_parameters()
    {
        return new external_function_parameters([]);
    }

    public static function get_stadistic_returns()
    {
        return new external_single_structure([
            'total_users' => new external_value(PARAM_INT, 'Total users'),
            'total_access' => new external_value(PARAM_INT, 'Total access'),
            'total_cohorts' => new external_value(PARAM_INT, 'Total cohorts'),
            'total_courses' => new external_value(PARAM_INT, 'Total courses'),
            // 'total_grades' => new external_value(PARAM_INT, 'Total grades'),
        ]);
    }

    /**
     * Metodo que obtiene todos los cursos y sus notas por el id del usuario,
     * tecnicamente solo el grado final del libro de calificaciones
     * 
     * Solo devolera los cursos con notas, si no hay nota, no hay registro
     *
     * @param int $userid
     * @return array
     */
    public function get_grades_by_userid($userid)
    {
        global $DB;

        $params = self::validate_parameters(self::get_grades_by_userid_parameters(), ['userid' => $userid]);

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

    public function get_grades_by_userid_parameters()
    {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'Moodle user id')
        ]);
    }

    public function get_grades_by_userid_returns()
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
     * @return array
     */
    public function get_badges_by_user_id($userid)
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

    public function get_badges_by_user_id_parameters()
    {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'Moodle user id')
        ]);
    }

    public function get_badges_by_user_id_returns()
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
}
