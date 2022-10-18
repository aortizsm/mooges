## Un plugin que extiende/simplifica algunos métodos apirest de Moodle haciéndolo más simples.  Útil para el desarrollo de orquestadoras de Moodle

Este plugin intenta resolver algunas cosas que no hace el apirest provisto por Moodle, para el caso de algunas instalaciones de Moodle (como por ejemplo: hosting) donde la infraestructura es limitada. Este plugin extiende el API de Moodle creando las siguientes:

- mooges_get_category_from_course
- mooges_get_roleid_by_shortname
- mooges_get_category_by_idnumber
- mooges_get_users
- mooges_get_stadistics
- mooges_get_grades_by_userid

# MooGes Plugin Instalacion
Se debe instalar bajo el nombre de "mooges" dentro de la carpeta "local"

## mooges_get_category_from_course
Obtiene la categoría de un curso únicamente, extrayendo el campo 'category' dentro del mismo curso, recibiendo por argumento el 'courseid'.

## mooges_get_roleid_by_shortname
Obtiene la id del role global, extrayendo el campo 'shortname' dentro de role, recibiendo por argumento el 'shortname'. La idea detrás de este método es obtener el id 

## mooges_get_category_by_idnumber
Obtiene la id de la category, extrayendo el campo 'id' dentro de role, recibiendo por argumento el 'idnumber'. Ideal para ser usado en la automatización de creación de cursos

## mooges_get_users
Método que obtiene a todos los usuarios activos incluyendo los campos personalizados. No recibe parámetros (debe ser ajustado según las reglas de negocio)

## mooges_get_stadistics
Método que obtiene algunas estadísticas sobre Moodle:

- Total de usuarios
- Total de accesos de día
- Total de cohortes
- Total de cursos activos
- Total de notas

```php
array (size=5)
  'total_users' => int 1396
  'total_access' => int 1
  'total_cohorts' => int 0
  'total_courses' => int 3
  'total_grades' => int 24
```

## mooges_get_grades_by_userid
Método que obtiene la nota total del libro de calificaciones de todos los cursos de un usuario, recibiendo por parámetros el id de usuarios.

## mooges_get_users
Metodo que obtiene a todos los usuarios activos incluyendo los campos personalizados. No recibe parametros (debe ser ajustado segun las reglas de negocio)

## mooges_get_stadistics
Metodo que obtiene algunas estadisticas sobre moodle:

- Total de usuarios
- Total de accesos de día
- Total de cohortes
- Total de cursos activos
- Total de notas

## mooges_get_grades_by_userid
Metodo que obtiene las notas total del libro de calificaciones de todos los cursos de un usuario, recibiendo por parametros el id de usuarios. Incluye también los cursos matriculados sin notas

## mooges_get_badges_by_user_id
Este metodo obtiene todas las insginias