<?php
/**
 * Configuración de reportes: cada ID tiene su tabla y consulta
 * La conexión se maneja centralmente en connections.php
 */
require_once __DIR__ . '/connections.php';

// Configuración de reportes: solo source, table y query
$reportsConfig = [
    // Análisis de Pagos
    1 => ['source' => 'mysql', 'table' => 'analisis_pago', 'query' => "SELECT * FROM analisis_pago"],
    2 => ['source' => 'mysql', 'table' => 'analisis_pago_legal', 'query' => "SELECT * FROM analisis_pago_legal"],
    
    // Avance Legal
    3 => ['source' => 'mysql', 'table' => 'avancelegalanualcartera', 'query' => "SELECT * FROM avancelegalanualcartera"],
    
    // Business Intelligence y Riesgo
    4 => ['source' => 'mysql', 'table' => 'bg_riesgopreescribirdav7', 'query' => "SELECT * FROM bg_riesgopreescribirdav7"],
    5 => ['source' => 'mysql', 'table' => 'bi_estrategia_operaciones', 'query' => "SELECT * FROM bi_estrategia_operaciones"],
    6 => ['source' => 'mysql', 'table' => 'bi_estrategias', 'query' => "SELECT * FROM bi_estrategias"],
    
    // Buró de Crédito
    7 => ['source' => 'mysql', 'table' => 'buropriorizado_actual', 'query' => "SELECT * FROM buropriorizado_actual"],
    8 => ['source' => 'mysql', 'table' => 'buropriorizado_historico', 'query' => "SELECT * FROM buropriorizado_historico"],
    
    // CDR
    9 => ['source' => 'mysql', 'table' => 'cdr', 'query' => "SELECT * FROM cdr"],
    
    // Central
    10 => ['source' => 'mysql', 'table' => 'central', 'query' => "SELECT * FROM central"],
    11 => ['source' => 'mysql', 'table' => 'resumen_central', 'query' => "SELECT * FROM resumen_central"],
    
    // Cierre y Desglose - Desglose Admin
    12 => ['source' => 'mysql', 'table' => 'ch_desgloceadmin', 'query' => "SELECT * FROM ch_desgloceadmin"],
    13 => ['source' => 'mysql', 'table' => 'ch_desgloceadmin_actual', 'query' => "SELECT * FROM ch_desgloceadmin_actual"],
    
    // Cierre y Desglose - Desglose Cierre
    14 => ['source' => 'mysql', 'table' => 'ch_desglocecierre', 'query' => "SELECT * FROM ch_desglocecierre"],
    15 => ['source' => 'mysql', 'table' => 'ch_desglocecierre_actual', 'query' => "SELECT * FROM ch_desglocecierre_actual"],
    
    // Cierre y Desglose - Escalones
    16 => ['source' => 'mysql', 'table' => 'ch_escalones_proyecto', 'query' => "SELECT * FROM ch_escalones_proyecto"],
    17 => ['source' => 'mysql', 'table' => 'ch_escalones_proyecto_actual', 'query' => "SELECT * FROM ch_escalones_proyecto_actual"],
    18 => ['source' => 'mysql', 'table' => 'ch_escalonesporcentuales_proyecto', 'query' => "SELECT * FROM ch_escalonesporcentuales_proyecto"],
    19 => ['source' => 'mysql', 'table' => 'ch_escalonesporcentuales_proyecto_actual', 'query' => "SELECT * FROM ch_escalonesporcentuales_proyecto_actual"],
    
    // Cierre y Desglose - Estados
    20 => ['source' => 'mysql', 'table' => 'ch_estados', 'query' => "SELECT * FROM ch_estados"],
    21 => ['source' => 'mysql', 'table' => 'ch_estados_actual', 'query' => "SELECT * FROM ch_estados_actual"],
    
    // Cierre y Desglose - Metas
    22 => ['source' => 'mysql', 'table' => 'ch_metas_proyecto', 'query' => "SELECT * FROM ch_metas_proyecto"],
    23 => ['source' => 'mysql', 'table' => 'ch_metas_proyecto_actual', 'query' => "SELECT * FROM ch_metas_proyecto_actual"],
    
    // Cierre y Desglose - Punto de Equilibrio
    24 => ['source' => 'mysql', 'table' => 'ch_ptoequilibrio_proyecto', 'query' => "SELECT * FROM ch_ptoequilibrio_proyecto"],
    25 => ['source' => 'mysql', 'table' => 'ch_ptoequilibrio_proyecto_actual', 'query' => "SELECT * FROM ch_ptoequilibrio_proyecto_actual"],
    
    // Cierre y Desglose - Totales
    26 => ['source' => 'mysql', 'table' => 'ch_totalesbanco', 'query' => "SELECT * FROM ch_totalesbanco"],
    27 => ['source' => 'mysql', 'table' => 'ch_totalesbanco_actual', 'query' => "SELECT * FROM ch_totalesbanco_actual"],
    28 => ['source' => 'mysql', 'table' => 'ch_totalescierre', 'query' => "SELECT * FROM ch_totalescierre"],
    29 => ['source' => 'mysql', 'table' => 'ch_totalescierre_actual', 'query' => "SELECT * FROM ch_totalescierre_actual"],
    
    // Gestores y Supervisores
    30 => ['source' => 'mysql', 'table' => 'ch_gestores', 'query' => "SELECT * FROM ch_gestores"],
    31 => ['source' => 'mysql', 'table' => 'ch_gestores_20250101', 'query' => "SELECT * FROM ch_gestores_20250101"],
    32 => ['source' => 'mysql', 'table' => 'ch_gestores_actual', 'query' => "SELECT * FROM ch_gestores_actual"],
    33 => ['source' => 'mysql', 'table' => 'ch_supervisores', 'query' => "SELECT * FROM ch_supervisores"],
    34 => ['source' => 'mysql', 'table' => 'ch_supervisores_20250101', 'query' => "SELECT * FROM ch_supervisores_20250101"],
    35 => ['source' => 'mysql', 'table' => 'ch_supervisores_actual', 'query' => "SELECT * FROM ch_supervisores_actual"],
    
    // Problemas y Conciliaciones
    36 => ['source' => 'mysql', 'table' => 'ch_girosconproblemas', 'query' => "SELECT * FROM ch_girosconproblemas"],
    37 => ['source' => 'mysql', 'table' => 'ch_girosconproblemas_actual', 'query' => "SELECT * FROM ch_girosconproblemas_actual"],
    38 => ['source' => 'mysql', 'table' => 'ch_pagosconproblemas', 'query' => "SELECT * FROM ch_pagosconproblemas"],
    39 => ['source' => 'mysql', 'table' => 'ch_pagosconproblemas_actual', 'query' => "SELECT * FROM ch_pagosconproblemas_actual"],
    40 => ['source' => 'mysql', 'table' => 'ch_pagosnoconciliados', 'query' => "SELECT * FROM ch_pagosnoconciliados"],
    41 => ['source' => 'mysql', 'table' => 'ch_pagosnoconciliados_actual', 'query' => "SELECT * FROM ch_pagosnoconciliados_actual"],
    
    // Servicios
    42 => ['source' => 'mysql', 'table' => 'ch_servimas', 'query' => "SELECT * FROM ch_servimas"],
    43 => ['source' => 'mysql', 'table' => 'ch_servimas_actual', 'query' => "SELECT * FROM ch_servimas_actual"],
    
    // Control
    44 => ['source' => 'mysql', 'table' => 'control_pagos', 'query' => "SELECT * FROM control_pagos"],
    45 => ['source' => 'mysql', 'table' => 'control_retenciones', 'query' => "SELECT * FROM control_retenciones"],
    
    // Coopeande
    46 => ['source' => 'mysql', 'table' => 'coopeande_rptdiario', 'query' => "SELECT * FROM coopeande_rptdiario"],
    
    // DAV
    47 => ['source' => 'mysql', 'table' => 'dav2abril', 'query' => "SELECT * FROM dav2abril"],
    
    // Estados de Cartera
    48 => ['source' => 'mysql', 'table' => 'est_cartera_aguinaldos20241004', 'query' => "SELECT * FROM est_cartera_aguinaldos20241004"],
    49 => ['source' => 'mysql', 'table' => 'est_cartera_aguinaldos20241021', 'query' => "SELECT * FROM est_cartera_aguinaldos20241021"],
    
    // Facturación
    50 => ['source' => 'mysql', 'table' => 'fact_cargaestadoscuenta', 'query' => "SELECT * FROM fact_cargaestadoscuenta"],
    
    // File Master
    51 => ['source' => 'mysql', 'table' => 'filemaster_dav_historico', 'query' => "SELECT * FROM filemaster_dav_historico"],
    
    // Gestión de Entregas
    52 => ['source' => 'mysql', 'table' => 'ge_entregas', 'query' => "SELECT * FROM ge_entregas"],
    53 => ['source' => 'mysql', 'table' => 'ge_entregas_contact', 'query' => "SELECT * FROM ge_entregas_contact"],
    
    // Indicadores
    54 => ['source' => 'mysql', 'table' => 'indicadores_gestoresdiario', 'query' => "SELECT * FROM indicadores_gestoresdiario"],
    
    // Licencias
    55 => ['source' => 'mysql', 'table' => 'lic_censa_cartera_20240823', 'query' => "SELECT * FROM lic_censa_cartera_20240823"],
    56 => ['source' => 'mysql', 'table' => 'lic_coopecaja_cartera_20250619', 'query' => "SELECT * FROM lic_coopecaja_cartera_20250619"],
    57 => ['source' => 'mysql', 'table' => 'lic_coopecaja_cartera_20250708', 'query' => "SELECT * FROM lic_coopecaja_cartera_20250708"],
    58 => ['source' => 'mysql', 'table' => 'lic_dav_cartera_20240425', 'query' => "SELECT * FROM lic_dav_cartera_20240425"],
    59 => ['source' => 'mysql', 'table' => 'lic_dav_cartera_20240425_detalle', 'query' => "SELECT * FROM lic_dav_cartera_20240425_detalle"],
    60 => ['source' => 'mysql', 'table' => 'lic_dav_cartera_20240425_proyeccion', 'query' => "SELECT * FROM lic_dav_cartera_20240425_proyeccion"],
    61 => ['source' => 'mysql', 'table' => 'lic_davivienda_cartera_20250704', 'query' => "SELECT * FROM lic_davivienda_cartera_20250704"],
    62 => ['source' => 'mysql', 'table' => 'lic_hawlet_cartera_20240918', 'query' => "SELECT * FROM lic_hawlet_cartera_20240918"],
    
    // Cierre Mensual
    63 => ['source' => 'mysql', 'table' => 'cierre_estadocuentames', 'query' => "SELECT * FROM cierre_estadocuentames"],
    
    // Revisión
    64 => ['source' => 'mysql', 'table' => 'rev_unicomer_recuperacion', 'query' => "SELECT * FROM rev_unicomer_recuperacion"],
    
    // Contact Center v1
    65 => [
        'source' => 'mysql',
        'table' => 'central',
        'query' => "SELECT DATE(calldate) AS fecha, COUNT(*) AS total_llamadas FROM central GROUP BY DATE(calldate) ORDER BY fecha DESC"
    ],
    66 => [
        'source' => 'mysql',
        'table' => 'central',
        'query' => "SELECT DATE(calldate) AS fecha, COUNT(*) AS total, SUM(disposition = 'ANSWERED') AS contestadas, SUM(disposition <> 'ANSWERED') AS no_contestadas, ROUND(SUM(disposition = 'ANSWERED') / COUNT(*) * 100, 2) AS asr_pct FROM central GROUP BY DATE(calldate)"
    ],
    67 => [
        'source' => 'mysql',
        'table' => 'central',
        'query' => "SELECT DATE(calldate) AS fecha, ROUND(AVG(CAST(billsec AS UNSIGNED)), 2) AS aht_segundos FROM central WHERE disposition = 'ANSWERED' GROUP BY DATE(calldate)"
    ],
    68 => [
        'source' => 'mysql',
        'table' => 'resumen_central',
        'query' => "SELECT c.Id_Usuario, ROUND(AVG(CAST(c.Duration AS UNSIGNED)), 2) AS duracion_promedio_seg FROM resumen_central c GROUP BY c.Id_Usuario ORDER BY duracion_promedio_seg DESC"
    ],
    69 => [
        'source' => 'mysql',
        'table' => 'resumen_central',
        'query' => "SELECT DATE(Fecha) AS fecha, Id_Usuario, COUNT(*) AS llamadas FROM resumen_central GROUP BY DATE(Fecha), Id_Usuario ORDER BY fecha DESC, llamadas DESC"
    ],
    70 => [
        'source' => 'mysql',
        'table' => 'central',
        'query' => "SELECT DATE(calldate) AS fecha, ROUND(SUM(disposition = 'ANSWERED') / COUNT(*) * 100, 2) AS contact_rate_pct FROM central GROUP BY DATE(calldate)"
    ],
    71 => [
        'source' => 'mysql',
        'table' => 'central',
        'query' => "SELECT src AS telefono, COUNT(*) AS intentos FROM central GROUP BY src HAVING COUNT(*) > 20 ORDER BY intentos DESC LIMIT 20"
    ],
    72 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT DATE(Fecha) AS fecha, COUNT(*) AS gestiones, SUM(Estado_Promesa = 'CUMPLIDA') AS promesas_cumplidas, ROUND(SUM(Estado_Promesa = 'CUMPLIDA') / COUNT(*) * 100, 2) AS efectividad_pct FROM coopeande_rptdiario GROUP BY DATE(Fecha)"
    ],
    73 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT Gestor, COUNT(*) AS gestiones, SUM(Estado_Promesa = 'CUMPLIDA') AS cumplidas, ROUND(SUM(Estado_Promesa = 'CUMPLIDA') / COUNT(*) * 100, 2) AS conversion_pct FROM coopeande_rptdiario GROUP BY Gestor ORDER BY conversion_pct DESC"
    ],
    74 => [
        'source' => 'mysql',
        'table' => 'central',
        'query' => "SELECT DATE(c.calldate) AS fecha, COUNT(*) AS total_llamadas, SUM(c.disposition = 'ANSWERED') AS contestadas, ROUND(AVG(CAST(c.billsec AS UNSIGNED)), 2) AS aht, ROUND(SUM(c.disposition = 'ANSWERED') / COUNT(*) * 100, 2) AS contact_rate FROM central c GROUP BY DATE(c.calldate)"
    ],
    
    // Contact Center v2
    75 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT DATE(Fecha) AS fecha, COUNT(*) AS gestiones, SUM(Accion IN ('CONTACTO', 'CONTACTO EFECTIVO')) AS contactos, ROUND(SUM(Accion IN ('CONTACTO', 'CONTACTO EFECTIVO')) / COUNT(*) * 100, 2) AS contactabilidad_pct FROM coopeande_rptdiario GROUP BY DATE(Fecha)"
    ],
    76 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT DATE(Fecha) AS fecha, SUM(Estado_Promesa IS NOT NULL) AS promesas, SUM(Estado_Promesa = 'CUMPLIDA') AS cumplidas, ROUND(SUM(Estado_Promesa = 'CUMPLIDA') / NULLIF(SUM(Estado_Promesa IS NOT NULL),0) * 100, 2) AS cumplimiento_pct FROM coopeande_rptdiario GROUP BY DATE(Fecha)"
    ],
    77 => [
        'source' => 'mysql',
        'table' => 'control_pagos',
        'query' => "SELECT DATE(FechaPago) AS fecha, ROUND(SUM(CAST(Monto AS DECIMAL(18,2))),2) AS recuperado FROM control_pagos GROUP BY DATE(FechaPago)"
    ],
    78 => [
        'source' => 'mysql',
        'table' => 'control_pagos',
        'query' => "SELECT Gestor, ROUND(SUM(CAST(Monto AS DECIMAL(18,2))),2) AS recuperado FROM control_pagos GROUP BY Gestor ORDER BY recuperado DESC"
    ],
    79 => [
        'source' => 'mysql',
        'table' => 'central',
        'query' => "SELECT ROUND(COUNT(*) / NULLIF(SUM(disposition = 'ANSWERED'),0), 2) AS intentos_por_contacto FROM central"
    ],
    80 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT ROUND(SUM(Accion NOT IN ('CONTACTO','CONTACTO EFECTIVO')) / COUNT(*) * 100, 2) AS sin_contacto_pct FROM coopeande_rptdiario"
    ],
    81 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT ROUND(AVG(DATEDIFF(p.FechaPago, g.Fecha)),2) AS dias_promedio_pago FROM coopeande_rptdiario g JOIN control_pagos p ON g.Operacion = p.Expediente WHERE g.Estado_Promesa = 'CUMPLIDA'"
    ],
    82 => [
        'source' => 'mysql',
        'table' => 'control_pagos',
        'query' => "SELECT Gestor, SUM(CAST(Monto AS DECIMAL(18,2))) AS recuperado FROM control_pagos GROUP BY Gestor ORDER BY recuperado DESC"
    ],
    83 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT DATE(Fecha) AS fecha, Gestor, ROUND(COUNT(*) / NULLIF(COUNT(DISTINCT HOUR(Hora_Registro)),0),2) AS gestiones_por_hora FROM coopeande_rptdiario GROUP BY DATE(Fecha), Gestor"
    ],
    84 => [
        'source' => 'mysql',
        'table' => 'coopeande_rptdiario',
        'query' => "SELECT ROUND(SUM(Estado_Promesa = 'INCUMPLIDA') / NULLIF(SUM(Estado_Promesa IS NOT NULL),0) * 100, 2) AS reincidencia_pct FROM coopeande_rptdiario"
    ],
    
    // POM Reportes v2 (SQL Server) - Copia de reportes 1-64
    // Análisis de Pagos
    85 => ['source' => 'sqlserver', 'table' => 'analisis_pago', 'query' => "SELECT * FROM [analisis_pago]"],
    86 => ['source' => 'sqlserver', 'table' => 'analisis_pago_legal', 'query' => "SELECT * FROM [analisis_pago_legal]"],
    
    // Avance Legal
    87 => ['source' => 'sqlserver', 'table' => 'avancelegalanualcartera', 'query' => "SELECT * FROM [avancelegalanualcartera]"],
    
    // Business Intelligence y Riesgo
    88 => ['source' => 'sqlserver', 'table' => 'bg_riesgopreescribirdav7', 'query' => "SELECT * FROM [bg_riesgopreescribirdav7]"],
    89 => ['source' => 'sqlserver', 'table' => 'bi_estrategia_operaciones', 'query' => "SELECT * FROM [bi_estrategia_operaciones]"],
    90 => ['source' => 'sqlserver', 'table' => 'bi_estrategias', 'query' => "SELECT * FROM [bi_estrategias]"],
    
    // Buró de Crédito
    91 => ['source' => 'sqlserver', 'table' => 'buropriorizado_actual', 'query' => "SELECT * FROM [buropriorizado_actual]"],
    92 => ['source' => 'sqlserver', 'table' => 'buropriorizado_historico', 'query' => "SELECT * FROM [buropriorizado_historico]"],
    
    // CDR
    93 => ['source' => 'sqlserver', 'table' => 'cdr', 'query' => "SELECT * FROM [cdr]"],
    
    // Central
    94 => ['source' => 'sqlserver', 'table' => 'central', 'query' => "SELECT * FROM [central]"],
    95 => ['source' => 'sqlserver', 'table' => 'resumen_central', 'query' => "SELECT * FROM [resumen_central]"],
    
    // Cierre y Desglose - Desglose Admin
    96 => ['source' => 'sqlserver', 'table' => 'ch_desgloceadmin', 'query' => "SELECT * FROM [ch_desgloceadmin]"],
    97 => ['source' => 'sqlserver', 'table' => 'ch_desgloceadmin_actual', 'query' => "SELECT * FROM [ch_desgloceadmin_actual]"],
    
    // Cierre y Desglose - Desglose Cierre
    98 => ['source' => 'sqlserver', 'table' => 'ch_desglocecierre', 'query' => "SELECT * FROM [ch_desglocecierre]"],
    99 => ['source' => 'sqlserver', 'table' => 'ch_desglocecierre_actual', 'query' => "SELECT * FROM [ch_desglocecierre_actual]"],
    
    // Cierre y Desglose - Escalones
    100 => ['source' => 'sqlserver', 'table' => 'ch_escalones_proyecto', 'query' => "SELECT * FROM [ch_escalones_proyecto]"],
    101 => ['source' => 'sqlserver', 'table' => 'ch_escalones_proyecto_actual', 'query' => "SELECT * FROM [ch_escalones_proyecto_actual]"],
    102 => ['source' => 'sqlserver', 'table' => 'ch_escalonesporcentuales_proyecto', 'query' => "SELECT * FROM [ch_escalonesporcentuales_proyecto]"],
    103 => ['source' => 'sqlserver', 'table' => 'ch_escalonesporcentuales_proyecto_actual', 'query' => "SELECT * FROM [ch_escalonesporcentuales_proyecto_actual]"],
    
    // Cierre y Desglose - Estados
    104 => ['source' => 'sqlserver', 'table' => 'ch_estados', 'query' => "SELECT * FROM [ch_estados]"],
    105 => ['source' => 'sqlserver', 'table' => 'ch_estados_actual', 'query' => "SELECT * FROM [ch_estados_actual]"],
    
    // Cierre y Desglose - Metas
    106 => ['source' => 'sqlserver', 'table' => 'ch_metas_proyecto', 'query' => "SELECT * FROM [ch_metas_proyecto]"],
    107 => ['source' => 'sqlserver', 'table' => 'ch_metas_proyecto_actual', 'query' => "SELECT * FROM [ch_metas_proyecto_actual]"],
    
    // Cierre y Desglose - Punto de Equilibrio
    108 => ['source' => 'sqlserver', 'table' => 'ch_ptoequilibrio_proyecto', 'query' => "SELECT * FROM [ch_ptoequilibrio_proyecto]"],
    109 => ['source' => 'sqlserver', 'table' => 'ch_ptoequilibrio_proyecto_actual', 'query' => "SELECT * FROM [ch_ptoequilibrio_proyecto_actual]"],
    
    // Cierre y Desglose - Totales
    110 => ['source' => 'sqlserver', 'table' => 'ch_totalesbanco', 'query' => "SELECT * FROM [ch_totalesbanco]"],
    111 => ['source' => 'sqlserver', 'table' => 'ch_totalesbanco_actual', 'query' => "SELECT * FROM [ch_totalesbanco_actual]"],
    112 => ['source' => 'sqlserver', 'table' => 'ch_totalescierre', 'query' => "SELECT * FROM [ch_totalescierre]"],
    113 => ['source' => 'sqlserver', 'table' => 'ch_totalescierre_actual', 'query' => "SELECT * FROM [ch_totalescierre_actual]"],
    
    // Gestores y Supervisores
    114 => ['source' => 'sqlserver', 'table' => 'ch_gestores', 'query' => "SELECT * FROM [ch_gestores]"],
    115 => ['source' => 'sqlserver', 'table' => 'ch_gestores_20250101', 'query' => "SELECT * FROM [ch_gestores_20250101]"],
    116 => ['source' => 'sqlserver', 'table' => 'ch_gestores_actual', 'query' => "SELECT * FROM [ch_gestores_actual]"],
    117 => ['source' => 'sqlserver', 'table' => 'ch_supervisores', 'query' => "SELECT * FROM [ch_supervisores]"],
    118 => ['source' => 'sqlserver', 'table' => 'ch_supervisores_20250101', 'query' => "SELECT * FROM [ch_supervisores_20250101]"],
    119 => ['source' => 'sqlserver', 'table' => 'ch_supervisores_actual', 'query' => "SELECT * FROM [ch_supervisores_actual]"],
    
    // Problemas y Conciliaciones
    120 => ['source' => 'sqlserver', 'table' => 'ch_girosconproblemas', 'query' => "SELECT * FROM [ch_girosconproblemas]"],
    121 => ['source' => 'sqlserver', 'table' => 'ch_girosconproblemas_actual', 'query' => "SELECT * FROM [ch_girosconproblemas_actual]"],
    122 => ['source' => 'sqlserver', 'table' => 'ch_pagosconproblemas', 'query' => "SELECT * FROM [ch_pagosconproblemas]"],
    123 => ['source' => 'sqlserver', 'table' => 'ch_pagosconproblemas_actual', 'query' => "SELECT * FROM [ch_pagosconproblemas_actual]"],
    124 => ['source' => 'sqlserver', 'table' => 'ch_pagosnoconciliados', 'query' => "SELECT * FROM [ch_pagosnoconciliados]"],
    125 => ['source' => 'sqlserver', 'table' => 'ch_pagosnoconciliados_actual', 'query' => "SELECT * FROM [ch_pagosnoconciliados_actual]"],
    
    // Servicios
    126 => ['source' => 'sqlserver', 'table' => 'ch_servimas', 'query' => "SELECT * FROM [ch_servimas]"],
    127 => ['source' => 'sqlserver', 'table' => 'ch_servimas_actual', 'query' => "SELECT * FROM [ch_servimas_actual]"],
    
    // Control
    128 => ['source' => 'sqlserver', 'table' => 'control_pagos', 'query' => "SELECT * FROM [control_pagos]"],
    129 => ['source' => 'sqlserver', 'table' => 'control_retenciones', 'query' => "SELECT * FROM [control_retenciones]"],
    
    // Coopeande
    130 => ['source' => 'sqlserver', 'table' => 'coopeande_rptdiario', 'query' => "SELECT * FROM [coopeande_rptdiario]"],
    
    // DAV
    131 => ['source' => 'sqlserver', 'table' => 'dav2abril', 'query' => "SELECT * FROM [dav2abril]"],
    
    // Estados de Cartera
    132 => ['source' => 'sqlserver', 'table' => 'est_cartera_aguinaldos20241004', 'query' => "SELECT * FROM [est_cartera_aguinaldos20241004]"],
    133 => ['source' => 'sqlserver', 'table' => 'est_cartera_aguinaldos20241021', 'query' => "SELECT * FROM [est_cartera_aguinaldos20241021]"],
    
    // Facturación
    134 => ['source' => 'sqlserver', 'table' => 'fact_cargaestadoscuenta', 'query' => "SELECT * FROM [fact_cargaestadoscuenta]"],
    
    // File Master
    135 => ['source' => 'sqlserver', 'table' => 'filemaster_dav_historico', 'query' => "SELECT * FROM [filemaster_dav_historico]"],
    
    // Gestión de Entregas
    136 => ['source' => 'sqlserver', 'table' => 'ge_entregas', 'query' => "SELECT * FROM [ge_entregas]"],
    137 => ['source' => 'sqlserver', 'table' => 'ge_entregas_contact', 'query' => "SELECT * FROM [ge_entregas_contact]"],
    
    // Indicadores
    138 => ['source' => 'sqlserver', 'table' => 'indicadores_gestoresdiario', 'query' => "SELECT * FROM [indicadores_gestoresdiario]"],
    
    // Licencias
    139 => ['source' => 'sqlserver', 'table' => 'lic_censa_cartera_20240823', 'query' => "SELECT * FROM [lic_censa_cartera_20240823]"],
    140 => ['source' => 'sqlserver', 'table' => 'lic_coopecaja_cartera_20250619', 'query' => "SELECT * FROM [lic_coopecaja_cartera_20250619]"],
    141 => ['source' => 'sqlserver', 'table' => 'lic_coopecaja_cartera_20250708', 'query' => "SELECT * FROM [lic_coopecaja_cartera_20250708]"],
    142 => ['source' => 'sqlserver', 'table' => 'lic_dav_cartera_20240425', 'query' => "SELECT * FROM [lic_dav_cartera_20240425]"],
    143 => ['source' => 'sqlserver', 'table' => 'lic_dav_cartera_20240425_detalle', 'query' => "SELECT * FROM [lic_dav_cartera_20240425_detalle]"],
    144 => ['source' => 'sqlserver', 'table' => 'lic_dav_cartera_20240425_proyeccion', 'query' => "SELECT * FROM [lic_dav_cartera_20240425_proyeccion]"],
    145 => ['source' => 'sqlserver', 'table' => 'lic_davivienda_cartera_20250704', 'query' => "SELECT * FROM [lic_davivienda_cartera_20250704]"],
    146 => ['source' => 'sqlserver', 'table' => 'lic_hawlet_cartera_20240918', 'query' => "SELECT * FROM [lic_hawlet_cartera_20240918]"],
    
    // Cierre Mensual
    147 => ['source' => 'sqlserver', 'table' => 'cierre_estadocuentames', 'query' => "SELECT * FROM [cierre_estadocuentames]"],
    
    // Revisión
    148 => ['source' => 'sqlserver', 'table' => 'rev_unicomer_recuperacion', 'query' => "SELECT * FROM [rev_unicomer_recuperacion]"]
];

/**
 * Obtener configuración de un reporte específico
 */
function getReportConfig($reportId) {
    global $reportsConfig;
    return isset($reportsConfig[$reportId]) ? $reportsConfig[$reportId] : null;
}
?>
