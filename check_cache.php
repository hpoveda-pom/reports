<?php
/**
 * Script para verificar y limpiar cachés de PHP/Apache
 */

// Headers para evitar cache del navegador
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificador de Cache - POM Reportes</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #1e1e1e;
            color: #ffffff;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #ffffff;
            border-bottom: 2px solid #3d3d3d;
            padding-bottom: 10px;
        }
        h2 {
            color: #ffffff;
            margin-top: 30px;
            border-bottom: 1px solid #3d3d3d;
            padding-bottom: 5px;
        }
        .section {
            background-color: #252525;
            border: 1px solid #3d3d3d;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 10px;
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #808080;
        }
        .value {
            color: #ffffff;
        }
        .success {
            color: #4caf50;
        }
        .warning {
            color: #ff9800;
        }
        .error {
            color: #f44336;
        }
        .btn {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 10px 5px;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .file-list {
            background-color: #1e1e1e;
            border: 1px solid #3d3d3d;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            max-height: 300px;
            overflow-y: auto;
        }
        .file-item {
            padding: 5px;
            border-bottom: 1px solid #3d3d3d;
        }
        .file-item:last-child {
            border-bottom: none;
        }
        pre {
            background-color: #1e1e1e;
            border: 1px solid #3d3d3d;
            border-radius: 4px;
            padding: 10px;
            overflow-x: auto;
            color: #d0d0d0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificador de Cache - POM Reportes</h1>
        
        <?php
        // Verificar OPcache
        $opcacheEnabled = function_exists('opcache_get_status');
        $opcacheStatus = null;
        if ($opcacheEnabled) {
            $opcacheStatus = opcache_get_status(false);
        }
        
        // Verificar extensiones
        $extensions = [
            'mysqli' => extension_loaded('mysqli'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'opcache' => extension_loaded('Zend OPcache')
        ];
        
        // Obtener información de archivos clave
        $keyFiles = [
            'config.php',
            'get_report_data.php',
            'get_report_data_ajax.php',
            'index.php'
        ];
        
        $fileInfo = [];
        foreach ($keyFiles as $file) {
            $filePath = __DIR__ . '/' . $file;
            if (file_exists($filePath)) {
                $fileInfo[$file] = [
                    'exists' => true,
                    'size' => filesize($filePath),
                    'modified' => filemtime($filePath),
                    'modified_date' => date('Y-m-d H:i:s', filemtime($filePath)),
                    'realpath' => realpath($filePath)
                ];
            } else {
                $fileInfo[$file] = ['exists' => false];
            }
        }
        
        // Verificar contenido de config.php para asegurar que no hay dummy
        $configContent = '';
        $hasDummyInConfig = false;
        if (file_exists(__DIR__ . '/config.php')) {
            $configContent = file_get_contents(__DIR__ . '/config.php');
            $hasDummyInConfig = (stripos($configContent, 'dummy') !== false || stripos($configContent, 'Dummy') !== false);
        }
        
        // Verificar contenido de get_report_data.php
        $hasDummyInData = false;
        $hasDummyFunction = false;
        if (file_exists(__DIR__ . '/get_report_data.php')) {
            $dataContent = file_get_contents(__DIR__ . '/get_report_data.php');
            $hasDummyInData = (stripos($dataContent, 'dummy') !== false || stripos($dataContent, 'Dummy') !== false);
            $hasDummyFunction = (stripos($dataContent, 'function get_data_dummy') !== false || stripos($dataContent, 'function getDataDummy') !== false);
        }
        
        // Información del servidor
        $serverInfo = [
            'PHP Version' => phpversion(),
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'Script Name' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
            'Current Time' => date('Y-m-d H:i:s'),
            'Timezone' => date_default_timezone_get()
        ];
        ?>
        
        <!-- Información del Servidor -->
        <div class="section">
            <h2>📊 Información del Servidor</h2>
            <div class="info-grid">
                <?php foreach ($serverInfo as $label => $value): ?>
                <div class="label"><?php echo htmlspecialchars($label); ?>:</div>
                <div class="value"><?php echo htmlspecialchars($value); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- OPcache Status -->
        <div class="section">
            <h2>⚡ Estado de OPcache</h2>
            <?php if ($opcacheEnabled): ?>
                <?php if ($opcacheStatus): ?>
                    <div class="info-grid">
                        <div class="label">Estado:</div>
                        <div class="value success">✅ OPcache está ACTIVO</div>
                        <div class="label">Caché Habilitado:</div>
                        <div class="value"><?php echo $opcacheStatus['opcache_enabled'] ? '✅ Sí' : '❌ No'; ?></div>
                        <div class="label">Archivos en Cache:</div>
                        <div class="value"><?php echo number_format($opcacheStatus['opcache_statistics']['num_cached_scripts'] ?? 0); ?></div>
                        <div class="label">Hits:</div>
                        <div class="value"><?php echo number_format($opcacheStatus['opcache_statistics']['hits'] ?? 0); ?></div>
                        <div class="label">Misses:</div>
                        <div class="value"><?php echo number_format($opcacheStatus['opcache_statistics']['misses'] ?? 0); ?></div>
                        <div class="label">Hit Rate:</div>
                        <div class="value">
                            <?php 
                            $hits = $opcacheStatus['opcache_statistics']['hits'] ?? 0;
                            $misses = $opcacheStatus['opcache_statistics']['misses'] ?? 0;
                            $total = $hits + $misses;
                            $hitRate = $total > 0 ? round(($hits / $total) * 100, 2) : 0;
                            echo $hitRate . '%';
                            ?>
                        </div>
                        <div class="label">Memoria Usada:</div>
                        <div class="value"><?php echo number_format($opcacheStatus['memory_usage']['used_memory'] / 1024 / 1024, 2); ?> MB</div>
                        <div class="label">Memoria Libre:</div>
                        <div class="value"><?php echo number_format($opcacheStatus['memory_usage']['free_memory'] / 1024 / 1024, 2); ?> MB</div>
                    </div>
                    
                    <?php if ($opcacheStatus['opcache_enabled']): ?>
                    <form method="post" style="margin-top: 15px;">
                        <button type="submit" name="clear_opcache" class="btn btn-danger">🗑️ Limpiar OPcache</button>
                    </form>
                    <?php endif; ?>
                    
                    <?php
                    // Limpiar OPcache si se solicitó
                    if (isset($_POST['clear_opcache']) && function_exists('opcache_reset')) {
                        if (opcache_reset()) {
                            echo '<div class="value success" style="margin-top: 10px;">✅ OPcache limpiado exitosamente. Recargando página...</div>';
                            echo '<script>setTimeout(function(){ location.reload(); }, 2000);</script>';
                        } else {
                            echo '<div class="value error" style="margin-top: 10px;">❌ Error al limpiar OPcache</div>';
                        }
                    }
                    ?>
                <?php else: ?>
                    <div class="value warning">⚠️ OPcache está instalado pero no disponible para obtener estado</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="value">ℹ️ OPcache no está habilitado o no está disponible</div>
            <?php endif; ?>
        </div>
        
        <!-- Extensions -->
        <div class="section">
            <h2>🔌 Extensiones PHP</h2>
            <div class="info-grid">
                <?php foreach ($extensions as $ext => $loaded): ?>
                <div class="label"><?php echo htmlspecialchars($ext); ?>:</div>
                <div class="value <?php echo $loaded ? 'success' : 'error'; ?>">
                    <?php echo $loaded ? '✅ Instalada' : '❌ No instalada'; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- File Information -->
        <div class="section">
            <h2>📁 Información de Archivos Clave</h2>
            <?php foreach ($fileInfo as $file => $info): ?>
                <?php if ($info['exists']): ?>
                    <div class="file-item">
                        <strong><?php echo htmlspecialchars($file); ?></strong><br>
                        <small>
                            Tamaño: <?php echo number_format($info['size'] / 1024, 2); ?> KB<br>
                            Última modificación: <?php echo htmlspecialchars($info['modified_date']); ?> (<?php echo time() - $info['modified']; ?> segundos atrás)<br>
                            Ruta: <?php echo htmlspecialchars($info['realpath']); ?>
                        </small>
                    </div>
                <?php else: ?>
                    <div class="file-item error">
                        <strong><?php echo htmlspecialchars($file); ?></strong> - ❌ No existe
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <!-- Verificación de Código Dummy -->
        <div class="section">
            <h2>🔍 Verificación de Código Dummy</h2>
            <div class="info-grid">
                <div class="label">Referencias a "dummy" en config.php:</div>
                <div class="value <?php echo $hasDummyInConfig ? 'error' : 'success'; ?>">
                    <?php echo $hasDummyInConfig ? '⚠️ Se encontraron referencias a "dummy"' : '✅ No se encontraron referencias'; ?>
                </div>
                
                <div class="label">Referencias a "dummy" en get_report_data.php:</div>
                <div class="value <?php echo $hasDummyInData ? 'error' : 'success'; ?>">
                    <?php echo $hasDummyInData ? '⚠️ Se encontraron referencias a "dummy"' : '✅ No se encontraron referencias'; ?>
                </div>
                
                <div class="label">Función get_data_dummy existe:</div>
                <div class="value <?php echo $hasDummyFunction ? 'error' : 'success'; ?>">
                    <?php echo $hasDummyFunction ? '⚠️ La función todavía existe' : '✅ La función fue eliminada'; ?>
                </div>
            </div>
            
            <?php if ($hasDummyInConfig || $hasDummyInData || $hasDummyFunction): ?>
                <div class="value error" style="margin-top: 15px;">
                    ⚠️ Se encontraron referencias a código dummy. Esto podría explicar por qué sigues viendo datos dummy.
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Configuración Actual -->
        <div class="section">
            <h2>⚙️ Verificación de Configuración Actual</h2>
            <?php
            if (file_exists(__DIR__ . '/config.php')) {
                require_once __DIR__ . '/config.php';
                
                echo '<h3>Configuración de Reportes:</h3>';
                echo '<div class="file-list">';
                
                if (isset($reportsConfig)) {
                    foreach ($reportsConfig as $reportId => $config) {
                        $source = $config['source'] ?? 'no configurado';
                        $color = ($source === 'mysql') ? 'success' : 'error';
                        echo '<div class="file-item">';
                        echo "<strong>Reporte {$reportId}:</strong> ";
                        echo "<span class='{$color}'>Fuente: {$source}</span><br>";
                        
                        if ($source === 'mysql') {
                            echo "<small>Base de datos: " . htmlspecialchars($config['database'] ?? 'N/A') . "</small><br>";
                            echo "<small>Tabla: " . htmlspecialchars($config['table'] ?? 'N/A') . "</small>";
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<div class="value error">❌ No se pudo cargar $reportsConfig</div>';
                }
                
                echo '</div>';
            } else {
                echo '<div class="value error">❌ config.php no existe</div>';
            }
            ?>
        </div>
        
        <!-- Acciones -->
        <div class="section">
            <h2>🔧 Acciones</h2>
            <form method="post" style="display: inline;">
                <button type="submit" name="clear_all_cache" class="btn btn-danger">🗑️ Limpiar Todo el Cache</button>
            </form>
            <a href="index.php" class="btn">🏠 Volver al Inicio</a>
            
            <?php
            if (isset($_POST['clear_all_cache'])) {
                echo '<div style="margin-top: 15px;">';
                
                // Limpiar OPcache
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                    echo '<div class="value success">✅ OPcache limpiado</div>';
                }
                
                // Limpiar cache de estadísticas
                if (function_exists('clearstatcache')) {
                    clearstatcache(true);
                    echo '<div class="value success">✅ Cache de archivos limpiado</div>';
                }
                
                echo '<div class="value success">✅ Todos los cachés fueron limpiados. Recarga la página.</div>';
                echo '<script>setTimeout(function(){ location.reload(); }, 2000);</script>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Debug Info -->
        <div class="section">
            <h2>🐛 Información de Debug</h2>
            <details>
                <summary style="cursor: pointer; color: #007bff;">Ver contenido de config.php (primeras 50 líneas)</summary>
                <pre><?php 
                if (file_exists(__DIR__ . '/config.php')) {
                    $lines = file(__DIR__ . '/config.php');
                    echo htmlspecialchars(implode('', array_slice($lines, 0, 50)));
                }
                ?></pre>
            </details>
            <br>
            <details>
                <summary style="cursor: pointer; color: #007bff;">Ver búsqueda de "dummy" en get_report_data.php</summary>
                <pre><?php 
                if (file_exists(__DIR__ . '/get_report_data.php')) {
                    $content = file_get_contents(__DIR__ . '/get_report_data.php');
                    // Buscar líneas con "dummy"
                    $lines = explode("\n", $content);
                    $dummyLines = [];
                    foreach ($lines as $num => $line) {
                        if (stripos($line, 'dummy') !== false) {
                            $dummyLines[] = ($num + 1) . ': ' . htmlspecialchars(trim($line));
                        }
                    }
                    if (empty($dummyLines)) {
                        echo "✅ No se encontraron líneas con 'dummy'";
                    } else {
                        echo "⚠️ Se encontraron " . count($dummyLines) . " líneas con 'dummy':\n\n";
                        echo implode("\n", $dummyLines);
                    }
                }
                ?></pre>
            </details>
        </div>
    </div>
</body>
</html>
