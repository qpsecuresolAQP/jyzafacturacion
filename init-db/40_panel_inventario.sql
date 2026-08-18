CREATE OR REPLACE VIEW `vista_reporte_productos` AS
SELECT
    p.id AS producto_id,
    p.nombre AS nombre,
    p.codigo AS codigo,
    p.categoria_producto_id AS categoria_id,
    cp.nombre AS categoria_nombre,
    p.proveedor_id AS proveedor_id,
    pr.nombre AS proveedor_nombre,
    pr.whatsapp AS proveedor_whatsapp,
    pr.email AS proveedor_email,
    p.precio_compra AS precio_compra,
    p.precio AS precio_venta,
    (p.precio - p.precio_compra) AS margen_unitario,
    CASE WHEN p.precio_compra > 0
        THEN ROUND(((p.precio - p.precio_compra) / p.precio_compra) * 100, 2)
        ELSE 0
    END AS margen_porcentaje,
    p.stock AS stock,
    p.stock_minimo AS stock_minimo,
    (p.stock * p.precio_compra) AS valor_inventario,
    CASE
        WHEN p.stock <= 0 THEN 'AGOTADO'
        WHEN p.stock_minimo > 0 AND p.stock <= p.stock_minimo THEN 'BAJO'
        ELSE 'NORMAL'
    END AS estado_stock,
    p.estado AS activo,
    p.created AS created,
    p.modified AS modified
FROM productos p
LEFT JOIN categorias_productos cp ON cp.id = p.categoria_producto_id
LEFT JOIN proveedores pr ON pr.id = p.proveedor_id;

INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('PanelInventario', 'index', 'Ver panel de inventario', NOW(), NOW()),
('PanelInventario', 'exportarExcel', 'Exportar reporte de inventario (Excel)', NOW(), NOW()),
('PanelInventario', 'exportarPdf', 'Exportar reporte de inventario (PDF)', NOW(), NOW());
