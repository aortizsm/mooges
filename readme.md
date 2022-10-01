# MooGes Plugin
## Un plugin que extiende los metodo apirest de Moodle

Este plugin debe resolver todas que no hace el api de Moodle. Lo que hace este
plugin es extender el API de Moodle creando las siguientes endpoint:

- mooges_get_category
- mooges_get_roleid_by_shortname
- mooges_get_category_by_idnumber

## mooges_get_categories
Obtiene la categoria de un curso unicamente, extrayendo el campo 'category' dentro del mismo curso, recibiendo por argumento el 'courseid'

## mooges_get_roleid_by_shortname
Obtiene la id del role, extrayendo el campo 'shortname' dentro de role, recibiendo por argumento el 'shortname'

## mooges_get_category_by_idnumber
Obtiene la id de la category, extrayendo el campo 'id' dentro de role, recibiendo por argumento el 'idnumber'

# MooGes Plugin Instalacion
Se debe instalar bajo el nombre de "mooges" dentro de la carpeta "local"