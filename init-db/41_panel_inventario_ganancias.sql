INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('PanelInventario', 'reporteGanancias', 'Ver reporte de ganancia por ventas de productos', NOW(), NOW()),
('PanelInventario', 'exportarGananciasExcel', 'Exportar reporte de ganancia de productos (Excel)', NOW(), NOW()),
('PanelInventario', 'exportarGananciasPdf', 'Exportar reporte de ganancia de productos (PDF)', NOW(), NOW());
