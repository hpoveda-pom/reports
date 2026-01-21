<?php
function getFolderToggleUrl($folderId, $currentExpanded, $currentParams) {
    $params = $currentParams;
    $expanded = isset($params['expand']) ? explode(',', $params['expand']) : [];
    
    if ($currentExpanded) {
        // Colapsar: remover de expand
        $expanded = array_filter($expanded, function($f) use ($folderId) { return $f !== $folderId; });
    } else {
        // Expandir: agregar a expand
        if (!in_array($folderId, $expanded)) {
            $expanded[] = $folderId;
        }
    }
    
    if (!empty($expanded)) {
        $params['expand'] = implode(',', $expanded);
    } else {
        unset($params['expand']);
    }
    
    return '?' . http_build_query($params);
}

function isFolderExpanded($folderId, $expandedFolders, $reportIds) {
    return in_array($folderId, $expandedFolders) || !empty(array_intersect($reportIds, $expandedFolders));
}
?>
