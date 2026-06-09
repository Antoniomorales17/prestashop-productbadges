# AGENTS.md - Reglas de contexto para IA

Este archivo documenta las instrucciones aplicadas al trabajar con asistentes de IA en este repositorio. Su objetivo es limitar las sugerencias a una solucion compatible con la prueba tecnica de Blinders Group y con PrestaShop 1.7.

## Stack objetivo

- **PrestaShop:** 1.7.8.11 como version de referencia.
- **PHP:** 7.4. No usar constructor property promotion, union types, attributes ni funciones exclusivas de PHP 8.
- **Base de datos:** MySQL 5.7 con tablas InnoDB.
- **Dependencias:** no introducir Composer ni librerias JS externas salvo justificacion explicita.

## Reglas arquitectonicas

- El modulo debe llamarse `productbadges`.
- El archivo raiz `productbadges.php` debe actuar como instalador, configurador y despachador de hooks.
- La logica CRUD de Back Office debe residir en `AdminProductBadgesController`.
- El acceso a datos de badges debe pasar por `ProductBadge`, heredando de `ObjectModel`.
- La asignacion producto-badge debe mantener una relacion muchos a muchos mediante tabla intermedia.
- El modulo debe instalar y desinstalar limpiamente tablas, hooks, configuraciones y pestanas admin.

## Seguridad obligatoria

- Castear IDs a `(int)` antes de usarlos.
- Sanear valores SQL con APIs nativas de PrestaShop, `DbQuery`, `Db::getInstance()` y `pSQL()` cuando aplique.
- Validar server-side, no depender solo del formulario HTML.
- Escapar toda variable impresa en Smarty con `|escape:'html':'UTF-8'`.
- Validar colores con `Validate::isColor`.
- Limitar la posicion de una badge a `left` o `right`.

## Flujo de revision humana

- No aceptar bloques generados por IA sin leerlos.
- Probar sintaxis PHP en el contenedor Docker antes de entregar.
- Revisar especialmente plantillas Smarty, instalacion/desinstalacion y consultas SQL.
- Documentar en `IA.md` los errores relevantes detectados en outputs de IA.

## Uso en este proyecto

Estas reglas se usaron como contexto durante la revision final con Codex y como guia de comprobacion para ajustar el modulo antes de la entrega.
