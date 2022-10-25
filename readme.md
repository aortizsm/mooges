## MooGes Plugin V0.1

Un plugin que extiende/simplifica algunos métodos apirest de Moodle haciéndolo más simples.  Útil para el desarrollo de orquestadoras de Moodle.

Este plugin intenta resolver algunas cosas que no hace el apirest provisto por Moodle, para el caso de algunas instalaciones de Moodle (como por ejemplo: hosting) donde la infraestructura es limitada. Este plugin extiende el API de Moodle creando las siguientes:

- mooges_get_category_from_course
- mooges_get_roleid_by_shortname
- mooges_get_category_by_idnumber
- mooges_get_users
- mooges_get_stadistics
- mooges_get_grades_by_user_id
- mooges_get_badges_by_user_id

# Recomendaciones
El plugin [https://github.com/llagerlof/MoodleRest](MoodleRest) desarrollado por [https://github.com/llagerlof](llagerlof) es muy bueno a la hora de interactuar con Moodle simplificando mucho las cosas.

# MooGes Plugin Instalacion
Se debe instalar bajo el nombre de "mooges" dentro de la carpeta "local"

## mooges_get_category_from_course
Obtiene la categoría de un curso únicamente, extrayendo el campo 'category' dentro del mismo curso, recibiendo por argumento el 'courseid'. 

### Uso
```php
$moodleRest->request('mooges_get_category_from_course', ['courseid' => 2]);
```
### Respuesta
```php
array (size=1)
  'category' => int 1
```

## mooges_get_roleid_by_shortname
Obtiene la id del role global, extrayendo el campo 'shortname' dentro de role, recibiendo por argumento el 'shortname'. La idea detrás de este método es obtener el id 

### Uso
```php
$moodleRest->request('mooges_get_roleid_by_shortname', ['shortname' => 'student']);
```
### Respuesta
```php
array (size=2)
  'id' => int 5
  'shortname' => string 'student' (length=7)
```

## mooges_get_category_by_idnumber
Obtiene la id de la category, extrayendo el campo 'id' dentro de la categoria, recibiendo por argumento el 'idnumber'. Ideal para ser usado en la automatización de creación de cursos

### Uso
```php
$moodleRest->request('mooges_get_category_by_idnumber', ['idnumber' => 'ciclo1']);
```
### Respuesta
```php
array (size=7)
  'id' => int 1
  'name' => string 'Categoría 1' (length=12)
  'description' => string '' (length=0)
  'parent' => int 0
  'sortorder' => int 10000
  'visible' => int 1
  'visibleold' => int 1
```

## mooges_get_users
Método que obtiene a todos los usuarios activos incluyendo campos personalizados. No recibe parámetros (debe ser ajustado según las reglas de negocio).

### Uso
```php
$moodleRest->request('mooges_get_users');
```
### Respuesta
```php
array (size=349)
  0 => 
    array (size=11)
      'id' => int 2
      'username' => string 'admin40' (length=7)
      'firstname' => string 'Alberto' (length=13)
      'lastname' => string 'Ortiz' (length=7)
      'email' => string 'asd@ads.cl' (length=10)
      'city' => string '' (length=0)
      'firstaccess' => string '1666038308' (length=10)
      'lastaccess' => string '1666728403' (length=10)
      'lastlogin' => string '1666714467' (length=10)
      'currentlogin' => string '1666714556' (length=10)
      'timecreated' => string '0' (length=1)
  1 => 
    array (size=11)
      'id' => int 3
      'username' => string 'agustina figueroa' (length=17)
      'firstname' => string 'Nombre de' (length=18)
      'lastname' => string 'Prueba' (length=15)
      'email' => string 'nombre.prueba@ip.cl' (length=25)
      'city' => string 'Los Angeles' (length=16)
      'firstaccess' => string '1394649247' (length=10)
      'lastaccess' => string '1664901709' (length=10)
      'lastlogin' => string '1664828866' (length=10)
      'currentlogin' => string '1664886406' (length=10)
      'timecreated' => string '1388664754' (length=10)
  2 => 
  //
```

## mooges_get_stadistics
Método que obtiene algunas estadísticas sobre Moodle:

- Total de usuarios
- Total de accesos de día
- Total de cohortes
- Total de cursos activos
- Total de notas

### Uso
```php
$moodleRest->request('mooges_get_stadistics');
```

### Respuesta
```php
array (size=5)
  'total_users' => int 1396
  'total_access' => int 1
  'total_cohorts' => int 0
  'total_courses' => int 3
  'total_grades' => int 24
```

## mooges_get_grades_by_user_id
Metodo que obtiene las notas total del libro de calificaciones de todos los cursos de un usuario, recibiendo por parametros el id de usuarios. Incluye también los cursos matriculados sin notas

### Uso
```php
$moodleRest->request('mooges_get_grades_by_user_id', ['userid' => 223] );
```

### Respuesta
```php
array (size=2)
  0 => 
    array (size=2)
      'courseid' => int 1
      'grade' => int 0
  1 => 
    array (size=2)
      'courseid' => int 2
      'grade' => int 100
```

## mooges_get_badges_by_user_id
Este metodo obtiene todas las insginias del usuario en todos los cursos.

### Uso
```php
$moodleRest->request('mooges_get_badges_by_user_id', ['userid' => 223] );
```

### Respuesta
```php
array (size=2)
  0 => 
    array (size=7)
      'badgetid' => 1,
      'name' => 'Insignia demo 1',
      'description' => 'Insignia demo 1',
      'version' => '1',
      'point' => '2',
      'type' => 'trofeo',
      'visible' => true,
  1 => 
    array (size=2)
      'badgetid' => 2,
      'name' => 'Insignia demo 2',
      'description' => 'Insignia demo 2',
      'version' => '1',
      'point' => '2',
      'type' => 'trofeo',
      'visible' => true,
```