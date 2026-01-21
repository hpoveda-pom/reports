<?php
/**
 * Función centralizada para obtener nombres de reportes
 * Esto evita duplicación de código
 */
function get_report_names() {
    return [
        // Análisis de Pagos
        1 => ['name' => 'Análisis de Pago', 'folder' => 'Análisis de Pagos'],
        2 => ['name' => 'Análisis de Pago Legal', 'folder' => 'Análisis de Pagos'],
        
        // Avance Legal
        3 => ['name' => 'Avance Legal Anual de Cartera', 'folder' => 'Avance Legal'],
        
        // Business Intelligence y Riesgo
        4 => ['name' => 'Riesgo Pre-escribir DAV7', 'folder' => 'Business Intelligence y Riesgo'],
        5 => ['name' => 'Estrategia de Operaciones', 'folder' => 'Business Intelligence y Riesgo'],
        6 => ['name' => 'Estrategias', 'folder' => 'Business Intelligence y Riesgo'],
        
        // Buró de Crédito
        7 => ['name' => 'Buró Priorizado Actual', 'folder' => 'Buró de Crédito'],
        8 => ['name' => 'Buró Priorizado Histórico', 'folder' => 'Buró de Crédito'],
        
        // CDR
        9 => ['name' => 'CDR', 'folder' => 'CDR'],
        
        // Central
        10 => ['name' => 'Central', 'folder' => 'Central'],
        11 => ['name' => 'Resumen Central', 'folder' => 'Central'],
        
        // Cierre y Desglose - Desglose Admin
        12 => ['name' => 'Desglose Admin', 'folder' => 'Cierre y Desglose'],
        13 => ['name' => 'Desglose Admin Actual', 'folder' => 'Cierre y Desglose'],
        
        // Cierre y Desglose - Desglose Cierre
        14 => ['name' => 'Desglose Cierre', 'folder' => 'Cierre y Desglose'],
        15 => ['name' => 'Desglose Cierre Actual', 'folder' => 'Cierre y Desglose'],
        
        // Cierre y Desglose - Escalones
        16 => ['name' => 'Escalones Proyecto', 'folder' => 'Cierre y Desglose'],
        17 => ['name' => 'Escalones Proyecto Actual', 'folder' => 'Cierre y Desglose'],
        18 => ['name' => 'Escalones Porcentuales Proyecto', 'folder' => 'Cierre y Desglose'],
        19 => ['name' => 'Escalones Porcentuales Proyecto Actual', 'folder' => 'Cierre y Desglose'],
        
        // Cierre y Desglose - Estados
        20 => ['name' => 'Estados', 'folder' => 'Cierre y Desglose'],
        21 => ['name' => 'Estados Actual', 'folder' => 'Cierre y Desglose'],
        
        // Cierre y Desglose - Metas
        22 => ['name' => 'Metas Proyecto', 'folder' => 'Cierre y Desglose'],
        23 => ['name' => 'Metas Proyecto Actual', 'folder' => 'Cierre y Desglose'],
        
        // Cierre y Desglose - Punto de Equilibrio
        24 => ['name' => 'Punto de Equilibrio Proyecto', 'folder' => 'Cierre y Desglose'],
        25 => ['name' => 'Punto de Equilibrio Proyecto Actual', 'folder' => 'Cierre y Desglose'],
        
        // Cierre y Desglose - Totales
        26 => ['name' => 'Totales Banco', 'folder' => 'Cierre y Desglose'],
        27 => ['name' => 'Totales Banco Actual', 'folder' => 'Cierre y Desglose'],
        28 => ['name' => 'Totales Cierre', 'folder' => 'Cierre y Desglose'],
        29 => ['name' => 'Totales Cierre Actual', 'folder' => 'Cierre y Desglose'],
        
        // Gestores y Supervisores
        30 => ['name' => 'Gestores', 'folder' => 'Gestores y Supervisores'],
        31 => ['name' => 'Gestores 2025-01-01', 'folder' => 'Gestores y Supervisores'],
        32 => ['name' => 'Gestores Actual', 'folder' => 'Gestores y Supervisores'],
        33 => ['name' => 'Supervisores', 'folder' => 'Gestores y Supervisores'],
        34 => ['name' => 'Supervisores 2025-01-01', 'folder' => 'Gestores y Supervisores'],
        35 => ['name' => 'Supervisores Actual', 'folder' => 'Gestores y Supervisores'],
        
        // Problemas y Conciliaciones
        36 => ['name' => 'Giros con Problemas', 'folder' => 'Problemas y Conciliaciones'],
        37 => ['name' => 'Giros con Problemas Actual', 'folder' => 'Problemas y Conciliaciones'],
        38 => ['name' => 'Pagos con Problemas', 'folder' => 'Problemas y Conciliaciones'],
        39 => ['name' => 'Pagos con Problemas Actual', 'folder' => 'Problemas y Conciliaciones'],
        40 => ['name' => 'Pagos No Conciliados', 'folder' => 'Problemas y Conciliaciones'],
        41 => ['name' => 'Pagos No Conciliados Actual', 'folder' => 'Problemas y Conciliaciones'],
        
        // Servicios
        42 => ['name' => 'Servimas', 'folder' => 'Servicios'],
        43 => ['name' => 'Servimas Actual', 'folder' => 'Servicios'],
        
        // Control
        44 => ['name' => 'Control de Pagos', 'folder' => 'Control'],
        45 => ['name' => 'Control de Retenciones', 'folder' => 'Control'],
        
        // Coopeande
        46 => ['name' => 'Reporte Diario Coopeande', 'folder' => 'Coopeande'],
        
        // DAV
        47 => ['name' => 'DAV Abril', 'folder' => 'DAV'],
        
        // Estados de Cartera
        48 => ['name' => 'Estado Cartera Aguinaldos 2024-10-04', 'folder' => 'Estados de Cartera'],
        49 => ['name' => 'Estado Cartera Aguinaldos 2024-10-21', 'folder' => 'Estados de Cartera'],
        
        // Facturación
        50 => ['name' => 'Carga Estados de Cuenta', 'folder' => 'Facturación'],
        
        // File Master
        51 => ['name' => 'File Master DAV Histórico', 'folder' => 'File Master'],
        
        // Gestión de Entregas
        52 => ['name' => 'Gestión de Entregas', 'folder' => 'Gestión de Entregas'],
        53 => ['name' => 'Gestión de Entregas Contacto', 'folder' => 'Gestión de Entregas'],
        
        // Indicadores
        54 => ['name' => 'Indicadores Gestores Diario', 'folder' => 'Indicadores'],
        
        // Licencias
        55 => ['name' => 'Licencia CENSA Cartera 2024-08-23', 'folder' => 'Licencias'],
        56 => ['name' => 'Licencia Coopecaja Cartera 2025-06-19', 'folder' => 'Licencias'],
        57 => ['name' => 'Licencia Coopecaja Cartera 2025-07-08', 'folder' => 'Licencias'],
        58 => ['name' => 'Licencia DAV Cartera 2024-04-25', 'folder' => 'Licencias'],
        59 => ['name' => 'Licencia DAV Cartera Detalle 2024-04-25', 'folder' => 'Licencias'],
        60 => ['name' => 'Licencia DAV Cartera Proyección 2024-04-25', 'folder' => 'Licencias'],
        61 => ['name' => 'Licencia Davivienda Cartera 2025-07-04', 'folder' => 'Licencias'],
        62 => ['name' => 'Licencia Hawlet Cartera 2024-09-18', 'folder' => 'Licencias'],
        
        // Cierre Mensual
        63 => ['name' => 'Cierre Estado de Cuenta Mensual', 'folder' => 'Cierre Mensual'],
        
        // Revisión
        64 => ['name' => 'Revisión Unicomer Recuperación', 'folder' => 'Revisión'],
        
        // Contact Center v1
        65 => ['name' => 'Volumen de Llamadas por Día', 'folder' => 'Contact Center v1'],
        66 => ['name' => 'Llamadas Contestadas vs No Contestadas (ASR)', 'folder' => 'Contact Center v1'],
        67 => ['name' => 'AHT - Average Handle Time', 'folder' => 'Contact Center v1'],
        68 => ['name' => 'Duración Promedio por Asesor', 'folder' => 'Contact Center v1'],
        69 => ['name' => 'Llamadas por Asesor por Día', 'folder' => 'Contact Center v1'],
        70 => ['name' => 'Contact Rate', 'folder' => 'Contact Center v1'],
        71 => ['name' => 'Top Números Más Contactados', 'folder' => 'Contact Center v1'],
        72 => ['name' => 'Efectividad de Gestión', 'folder' => 'Contact Center v1'],
        73 => ['name' => 'Conversión por Asesor', 'folder' => 'Contact Center v1'],
        74 => ['name' => 'KPI Ejecutivo Diario', 'folder' => 'Contact Center v1'],
        
        // Contact Center v2
        75 => ['name' => 'Contactos Efectivos vs Intentos', 'folder' => 'Contact Center v2'],
        76 => ['name' => 'Promesas Generadas vs Cumplidas', 'folder' => 'Contact Center v2'],
        77 => ['name' => 'Recovery Rate Diario', 'folder' => 'Contact Center v2'],
        78 => ['name' => 'Recovery Rate por Gestor', 'folder' => 'Contact Center v2'],
        79 => ['name' => 'Intentos Promedio por Contacto Exitoso', 'folder' => 'Contact Center v2'],
        80 => ['name' => 'Gestiones sin Contacto (%)', 'folder' => 'Contact Center v2'],
        81 => ['name' => 'Tiempo Promedio entre Contacto y Pago', 'folder' => 'Contact Center v2'],
        82 => ['name' => 'Pareto 80/20 de Recuperación', 'folder' => 'Contact Center v2'],
        83 => ['name' => 'Productividad Real (Gestiones por Hora)', 'folder' => 'Contact Center v2'],
        84 => ['name' => 'Tasa de Reincidencia', 'folder' => 'Contact Center v2'],
        
        // POM Reportes v2 (SQL Server) - Copia de reportes 1-64
        // Análisis de Pagos
        85 => ['name' => 'Análisis de Pago', 'folder' => 'POM Reportes v2 - Análisis de Pagos'],
        86 => ['name' => 'Análisis de Pago Legal', 'folder' => 'POM Reportes v2 - Análisis de Pagos'],
        
        // Avance Legal
        87 => ['name' => 'Avance Legal Anual de Cartera', 'folder' => 'POM Reportes v2 - Avance Legal'],
        
        // Business Intelligence y Riesgo
        88 => ['name' => 'Riesgo Pre-escribir DAV7', 'folder' => 'POM Reportes v2 - Business Intelligence y Riesgo'],
        89 => ['name' => 'Estrategia de Operaciones', 'folder' => 'POM Reportes v2 - Business Intelligence y Riesgo'],
        90 => ['name' => 'Estrategias', 'folder' => 'POM Reportes v2 - Business Intelligence y Riesgo'],
        
        // Buró de Crédito
        91 => ['name' => 'Buró Priorizado Actual', 'folder' => 'POM Reportes v2 - Buró de Crédito'],
        92 => ['name' => 'Buró Priorizado Histórico', 'folder' => 'POM Reportes v2 - Buró de Crédito'],
        
        // CDR
        93 => ['name' => 'CDR', 'folder' => 'POM Reportes v2 - CDR'],
        
        // Central
        94 => ['name' => 'Central', 'folder' => 'POM Reportes v2 - Central'],
        95 => ['name' => 'Resumen Central', 'folder' => 'POM Reportes v2 - Central'],
        
        // Cierre y Desglose - Desglose Admin
        96 => ['name' => 'Desglose Admin', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        97 => ['name' => 'Desglose Admin Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        
        // Cierre y Desglose - Desglose Cierre
        98 => ['name' => 'Desglose Cierre', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        99 => ['name' => 'Desglose Cierre Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        
        // Cierre y Desglose - Escalones
        100 => ['name' => 'Escalones Proyecto', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        101 => ['name' => 'Escalones Proyecto Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        102 => ['name' => 'Escalones Porcentuales Proyecto', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        103 => ['name' => 'Escalones Porcentuales Proyecto Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        
        // Cierre y Desglose - Estados
        104 => ['name' => 'Estados', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        105 => ['name' => 'Estados Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        
        // Cierre y Desglose - Metas
        106 => ['name' => 'Metas Proyecto', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        107 => ['name' => 'Metas Proyecto Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        
        // Cierre y Desglose - Punto de Equilibrio
        108 => ['name' => 'Punto de Equilibrio Proyecto', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        109 => ['name' => 'Punto de Equilibrio Proyecto Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        
        // Cierre y Desglose - Totales
        110 => ['name' => 'Totales Banco', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        111 => ['name' => 'Totales Banco Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        112 => ['name' => 'Totales Cierre', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        113 => ['name' => 'Totales Cierre Actual', 'folder' => 'POM Reportes v2 - Cierre y Desglose'],
        
        // Gestores y Supervisores
        114 => ['name' => 'Gestores', 'folder' => 'POM Reportes v2 - Gestores y Supervisores'],
        115 => ['name' => 'Gestores 2025-01-01', 'folder' => 'POM Reportes v2 - Gestores y Supervisores'],
        116 => ['name' => 'Gestores Actual', 'folder' => 'POM Reportes v2 - Gestores y Supervisores'],
        117 => ['name' => 'Supervisores', 'folder' => 'POM Reportes v2 - Gestores y Supervisores'],
        118 => ['name' => 'Supervisores 2025-01-01', 'folder' => 'POM Reportes v2 - Gestores y Supervisores'],
        119 => ['name' => 'Supervisores Actual', 'folder' => 'POM Reportes v2 - Gestores y Supervisores'],
        
        // Problemas y Conciliaciones
        120 => ['name' => 'Giros con Problemas', 'folder' => 'POM Reportes v2 - Problemas y Conciliaciones'],
        121 => ['name' => 'Giros con Problemas Actual', 'folder' => 'POM Reportes v2 - Problemas y Conciliaciones'],
        122 => ['name' => 'Pagos con Problemas', 'folder' => 'POM Reportes v2 - Problemas y Conciliaciones'],
        123 => ['name' => 'Pagos con Problemas Actual', 'folder' => 'POM Reportes v2 - Problemas y Conciliaciones'],
        124 => ['name' => 'Pagos No Conciliados', 'folder' => 'POM Reportes v2 - Problemas y Conciliaciones'],
        125 => ['name' => 'Pagos No Conciliados Actual', 'folder' => 'POM Reportes v2 - Problemas y Conciliaciones'],
        
        // Servicios
        126 => ['name' => 'Servimas', 'folder' => 'POM Reportes v2 - Servicios'],
        127 => ['name' => 'Servimas Actual', 'folder' => 'POM Reportes v2 - Servicios'],
        
        // Control
        128 => ['name' => 'Control de Pagos', 'folder' => 'POM Reportes v2 - Control'],
        129 => ['name' => 'Control de Retenciones', 'folder' => 'POM Reportes v2 - Control'],
        
        // Coopeande
        130 => ['name' => 'Reporte Diario Coopeande', 'folder' => 'POM Reportes v2 - Coopeande'],
        
        // DAV
        131 => ['name' => 'DAV Abril', 'folder' => 'POM Reportes v2 - DAV'],
        
        // Estados de Cartera
        132 => ['name' => 'Estado Cartera Aguinaldos 2024-10-04', 'folder' => 'POM Reportes v2 - Estados de Cartera'],
        133 => ['name' => 'Estado Cartera Aguinaldos 2024-10-21', 'folder' => 'POM Reportes v2 - Estados de Cartera'],
        
        // Facturación
        134 => ['name' => 'Carga Estados de Cuenta', 'folder' => 'POM Reportes v2 - Facturación'],
        
        // File Master
        135 => ['name' => 'File Master DAV Histórico', 'folder' => 'POM Reportes v2 - File Master'],
        
        // Gestión de Entregas
        136 => ['name' => 'Gestión de Entregas', 'folder' => 'POM Reportes v2 - Gestión de Entregas'],
        137 => ['name' => 'Gestión de Entregas Contacto', 'folder' => 'POM Reportes v2 - Gestión de Entregas'],
        
        // Indicadores
        138 => ['name' => 'Indicadores Gestores Diario', 'folder' => 'POM Reportes v2 - Indicadores'],
        
        // Licencias
        139 => ['name' => 'Licencia CENSA Cartera 2024-08-23', 'folder' => 'POM Reportes v2 - Licencias'],
        140 => ['name' => 'Licencia Coopecaja Cartera 2025-06-19', 'folder' => 'POM Reportes v2 - Licencias'],
        141 => ['name' => 'Licencia Coopecaja Cartera 2025-07-08', 'folder' => 'POM Reportes v2 - Licencias'],
        142 => ['name' => 'Licencia DAV Cartera 2024-04-25', 'folder' => 'POM Reportes v2 - Licencias'],
        143 => ['name' => 'Licencia DAV Cartera Detalle 2024-04-25', 'folder' => 'POM Reportes v2 - Licencias'],
        144 => ['name' => 'Licencia DAV Cartera Proyección 2024-04-25', 'folder' => 'POM Reportes v2 - Licencias'],
        145 => ['name' => 'Licencia Davivienda Cartera 2025-07-04', 'folder' => 'POM Reportes v2 - Licencias'],
        146 => ['name' => 'Licencia Hawlet Cartera 2024-09-18', 'folder' => 'POM Reportes v2 - Licencias'],
        
        // Cierre Mensual
        147 => ['name' => 'Cierre Estado de Cuenta Mensual', 'folder' => 'POM Reportes v2 - Cierre Mensual'],
        
        // Revisión
        148 => ['name' => 'Revisión Unicomer Recuperación', 'folder' => 'POM Reportes v2 - Revisión']
    ];
}
?>
