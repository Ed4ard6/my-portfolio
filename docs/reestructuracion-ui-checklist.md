# Checklist de reestructuración UI/UX (pendiente de confirmación)

Este listado resume todo lo solicitado para validar alcance antes de implementar cambios.

## 1) Navegación y acciones de administrador
- [ ] Quitar/renombrar botón **Administradores** por una acción más clara: **Crear usuario** o **Gestionar usuarios**.
- [ ] Reubicar el toggle de modo admin en esquina superior para evitar confusiones de cierre de sesión accidental.
- [ ] Resaltar visualmente el botón **Cerrar sesión** para distinguirlo del resto de acciones.

## 2) Recuperación de contraseña
- [ ] Eliminar texto/botón de “web administrador” en recuperación de contraseña.
- [ ] Dejar una sola acción de retorno: **Volver**.
- [ ] Centrar y normalizar espaciado entre divisor y botones.
- [ ] Renombrar “Generar enlace de recuperación” a un texto más corto y claro (por ejemplo: **Enviar enlace**).

## 3) Formularios de registro y autenticación
- [ ] Hacer inputs del formulario de registro más compactos.
- [ ] Ajustar input de correo para que no robe protagonismo visual al título/label.
- [ ] Revisar que en modales no aparezca “Volver” si ya existe “Cancelar”.

## 4) Proyectos, historial y listas (paginación)
- [ ] Agregar paginación en la sección **Historial de cambios**.
- [ ] Agregar paginación en la sección de **Proyectos**.
- [ ] Revisar todas las listas del panel y definir paginación consistente (tamaño por página + controles).

## 5) Tecnologías al crear/editar proyectos
- [ ] Cambiar selector de tecnologías a UI de **tarjetas/etiquetas seleccionables**.
- [ ] Implementar patrón tipo checkbox/toggle visual: al seleccionar, cambia color y estado.
- [ ] Mantener comportamiento claro en responsive para pantallas pequeñas.

## 6) Responsive y modales
- [ ] Corregir layout en móviles (capturas muestran desbordes y botones mal ubicados).
- [ ] Normalizar posición de botones: **Cancelar** y **Guardar cambios** de forma consistente.
- [ ] Eliminar mezclas de tamaños/colores y centralizar reglas de estilos de botones.

## 7) Estilo visual general
- [ ] Mejorar estilos en modo light (contrastes, bordes, legibilidad).
- [ ] Revisión integral de coherencia visual en todo el proyecto antes de seguir con nuevas funciones.

## 8) Botón “Contáctame”
- [ ] Hacer el botón más interactivo (hover/animación llamativa).
- [ ] Añadir detalle visual distintivo (ejemplo: microinteracción/emoji/icono) sin afectar accesibilidad.

## Propuesta de orden de implementación
1. **Base de diseño**: normalización global de botones + tokens de espaciado.
2. **Navegación/admin**: botones de cabecera, toggle y cerrar sesión.
3. **Auth/recuperación**: formularios y flujo de recuperación.
4. **Tecnologías/proyectos**: selector visual + paginación en proyectos.
5. **Historial y listas restantes**: paginación total.
6. **Responsive + light mode**: ajustes finales y QA visual.

## Criterios de validación (QA)
- [ ] Mobile 320px–480px sin desbordes horizontales.
- [ ] Botoneras consistentes (alineación, color, jerarquía y tamaño).
- [ ] Flujos de auth claros y sin duplicidad de acciones.
- [ ] Listas con paginación funcional y estado vacío.
- [ ] Contraste aceptable en dark/light.

---
Si confirmas este checklist, lo convierto en plan de ejecución por etapas con entregables pequeños y verificables.
