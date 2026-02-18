<?php 
$contentFile = (isset($uiMode) && $uiMode === 'modern') ? 'crewing/applications/content_modern' : 'crewing/applications/content';
$content = $contentFile;
include APPPATH . 'Views/layouts/crewing.php'; 
?>
