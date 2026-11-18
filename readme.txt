=== Feed de Botones Simple ===
Contributors: Dsantycam
Tags: buttons, shortcode, bio link, links, feed
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Crea feeds de botones tipo bio link mediante shortcode, con configuración visual por feed y por botón.

== Description ==

Feed de Botones Simple permite crear feeds de botones desde el panel de WordPress y mostrarlos en cualquier página o entrada mediante shortcode.

Características principales:

- Custom Post Type para administrar feeds
- Botones repetibles dentro de cada feed
- Soporte para imagen o icono por botón
- Descripción y enlace opcionales
- Colores individuales por botón
- Layout global por feed
- Orden manual con drag and drop
- Shortcode por ID o por slug
- Vista previa dentro del admin
- Duplicado rápido de feeds

Ejemplos de shortcode:

[fdbs_feed id="123"]

[fdbs_feed slug="mi-feed"]

[fdbs_feed id="123" columns="2" show_title="no"]

== Installation ==

1. Sube la carpeta del plugin a /wp-content/plugins/
2. Activa el plugin desde el panel de WordPress
3. Ve a "Feeds" en el admin
4. Crea un feed nuevo
5. Agrega botones y copia el shortcode
6. Pega el shortcode en una página o entrada

== Frequently Asked Questions ==

= ¿Necesito una página especial para cada feed? =

No. Los feeds se muestran mediante shortcode en cualquier contenido compatible.

= ¿Puedo usar imágenes en los botones? =

Sí. Puedes seleccionar imágenes desde la biblioteca multimedia de WordPress.

= ¿Puedo usar iconos? =

Sí. El plugin incluye un selector con más de 90 iconos (Lucide).

= ¿Puedo mostrar el feed sin título? =

Sí. Usa el atributo show_title="no" en el shortcode.

== Changelog ==

= 1.2.0 =
* Duplicar botones y separadores dentro del feed
* Separadores de sección con 10 diseños (línea, degradado, guiones, punteado, doble, onda, ornamento, tres puntos, etiqueta, solo espacio), texto opcional, color, grosor, ancho, alineación y espacio arriba/abajo
* Minimizar / expandir cada elemento, o todos a la vez; el estado se recuerda al guardar
* Resumen en la cabecera de cada elemento (título, enlace, icono, estado inactivo)
* Actualizaciones automáticas desde GitHub
* Corrige el panel de iconos, que aparecía siempre abierto y con los iconos aplastados

= 1.1.0 =
* Primera versión estable del plugin
* Administración de feeds con CPT
* Botones repetibles
* Imágenes, iconos, colores y enlaces
* Layout global por feed
* Vista previa en admin
* Duplicado de feeds
* Shortcodes por ID y slug