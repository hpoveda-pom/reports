<?php
// Incluir la función de datos
require_once 'get_report_data.php';

// Función para formatear nombres de columnas
function formatColumnName($name) {
    return ucfirst(str_replace('_', ' ', $name));
}

// Verificar si PhpSpreadsheet está disponible
$usePhpSpreadsheet = class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');

if ($usePhpSpreadsheet) {
    // Usar PhpSpreadsheet (librería profesional)
    require_once __DIR__ . '/vendor/autoload.php';
    
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Font;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    
    // Obtener parámetros GET directamente para la exportación
    $_GET['report'] = isset($_GET['report']) ? intval($_GET['report']) : 0;
    
    // Obtener datos del reporte
    $reportData = get_report_data();
    
    if (!$reportData) {
        die('Reporte no encontrado');
    }
    
    // Crear nuevo spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Título del reporte en fila 1
    $sheet->setCellValue('A1', $reportData['reportName']);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    // Fecha de generación en fila 2
    $fechaGenerado = date('d/m/Y H:i:s');
    $sheet->setCellValue('A2', 'Fecha generado: ' . $fechaGenerado);
    $sheet->getStyle('A2')->getFont()->setSize(10);
    $sheet->getStyle('A2')->getFont()->getColor()->setRGB('666666');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    // Obtener columnas dinámicamente desde los datos
    $columnNames = [];
    if (!empty($reportData['data'])) {
        // Obtener nombres de columnas del primer registro
        $columnNames = array_keys($reportData['data'][0]);
    } elseif (!empty($reportData['columns'])) {
        // Si no hay datos pero hay información de columnas, usarla
        $columnNames = $reportData['columns'];
    }
    
    // Encabezados en fila 3 (dinámicos)
    $col = 'A';
    foreach ($columnNames as $columnName) {
        $headerName = formatColumnName($columnName);
        $sheet->setCellValue($col . '3', $headerName);
        $sheet->getStyle($col . '3')->getFont()->setBold(true);
        $sheet->getStyle($col . '3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E0E0E0');
        $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $col++;
    }
    
    // Datos desde fila 4 (dinámicos)
    $row = 4;
    foreach ($reportData['data'] as $record) {
        $col = 'A';
        foreach ($columnNames as $columnName) {
            $value = isset($record[$columnName]) ? $record[$columnName] : '';
            $sheet->setCellValue($col . $row, $value);
            $col++;
        }
        $row++;
    }
    
    // Ajustar ancho de columnas dinámicamente
    $col = 'A';
    for ($i = 0; $i < count($columnNames); $i++) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }
    
    // Preparar descarga
    $filename = 'Reporte_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Escribir archivo
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
    
} else {
    // Alternativa simple usando headers PHP (CSV que Excel puede abrir)
    // O mejor aún, usar una implementación más simple pero que genere Excel real
    
    // Obtener parámetros GET directamente para la exportación
    $_GET['report'] = isset($_GET['report']) ? intval($_GET['report']) : 0;
    
    // Intentar usar una solución simple pero efectiva
    $reportData = get_report_data();
    
    if (!$reportData) {
        die('Reporte no encontrado');
    }
    
    // Generar Excel usando formato XML simple pero compatible
    $filename = 'Reporte_' . date('Y-m-d_H-i-s') . '.xls';
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Generar contenido Excel básico
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
    echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
    echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
    echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
    echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
    echo '<Worksheet ss:Name="Reporte">' . "\n";
    echo '<Table>' . "\n";
    
    // Fila 1: Título
    echo '<Row>' . "\n";
    echo '<Cell><Data ss:Type="String"><![CDATA[' . htmlspecialchars($reportData['reportName']) . ']]></Data></Cell>' . "\n";
    echo '</Row>' . "\n";
    
    // Fila 2: Fecha generado
    $fechaGenerado = date('d/m/Y H:i:s');
    echo '<Row>' . "\n";
    echo '<Cell ss:StyleID="fechaStyle"><Data ss:Type="String"><![CDATA[Fecha generado: ' . $fechaGenerado . ']]></Data></Cell>' . "\n";
    echo '</Row>' . "\n";
    
    // Fila vacía
    echo '<Row></Row>' . "\n";
    
    // Obtener columnas dinámicamente desde los datos
    $columnNames = [];
    if (!empty($reportData['data'])) {
        // Obtener nombres de columnas del primer registro
        $columnNames = array_keys($reportData['data'][0]);
    } elseif (!empty($reportData['columns'])) {
        // Si no hay datos pero hay información de columnas, usarla
        $columnNames = $reportData['columns'];
    }
    
    // Fila 4: Encabezados (dinámicos)
    echo '<Row>' . "\n";
    foreach ($columnNames as $columnName) {
        $headerName = formatColumnName($columnName);
        echo '<Cell ss:StyleID="headerStyle"><Data ss:Type="String"><![CDATA[' . htmlspecialchars($headerName) . ']]></Data></Cell>' . "\n";
    }
    echo '</Row>' . "\n";
    
    // Datos (dinámicos)
    foreach ($reportData['data'] as $record) {
        echo '<Row>' . "\n";
        foreach ($columnNames as $columnName) {
            $value = isset($record[$columnName]) ? $record[$columnName] : '';
            // Intentar determinar el tipo de dato
            if (is_numeric($value) && !is_string($value)) {
                echo '<Cell><Data ss:Type="Number">' . htmlspecialchars($value) . '</Data></Cell>' . "\n";
            } else {
                echo '<Cell><Data ss:Type="String"><![CDATA[' . htmlspecialchars($value) . ']]></Data></Cell>' . "\n";
            }
        }
        echo '</Row>' . "\n";
    }
    
    echo '</Table>' . "\n";
    
    // Estilos
    echo '<Styles>' . "\n";
    echo '<Style ss:ID="fechaStyle">' . "\n";
    echo '<Font ss:Size="10" ss:Color="#666666"/>' . "\n";
    echo '</Style>' . "\n";
    echo '<Style ss:ID="headerStyle">' . "\n";
    echo '<Font ss:Bold="1"/>' . "\n";
    echo '<Interior ss:Color="#E0E0E0" ss:Pattern="Solid"/>' . "\n";
    echo '</Style>' . "\n";
    echo '</Styles>' . "\n";
    
    echo '</Worksheet>' . "\n";
    echo '</Workbook>' . "\n";
    exit;
}
?>
