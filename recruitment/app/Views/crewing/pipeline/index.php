<?php 
$contentFile = (isset($uiMode) && $uiMode === 'modern') ? 'crewing/pipeline/content_modern' : 'crewing/pipeline/content';
$content = $contentFile;
include APPPATH . 'Views/layouts/crewing.php'; 
?>
