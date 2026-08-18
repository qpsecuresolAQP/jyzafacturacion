<?php
/**
 * @var \App\View\AppView $this
 * @var array $medicamentos
 */
// Mapear medicamentos para retornar solo los campos necesarios
$medicamentosArray = array_map(function($med) {
    return [
        'id' => $med->id,
        'codigo' => $med->codigo,
        'nombre' => $med->nombre,
        'concentracion' => $med->concentracion ?? ''
    ];
}, $medicamentos);

echo json_encode(['medicamentos' => $medicamentosArray]);
