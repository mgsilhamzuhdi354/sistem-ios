<?php
/**
 * Simple script to set UI mode to modern
 * Run this from browser: http://localhost/PT_indoocean/PT_indoocean/erp/set_ui_mode.php?mode=modern
 */

session_start();

$mode = $_GET['mode'] ?? 'classic';

if (in_array($mode, ['classic', 'modern'])) {
    $_SESSION['ui_mode'] = $mode;
    echo "UI Mode set to: " . $mode;
    echo "<br><br>";
    echo "<a href='/PT_indoocean/PT_indoocean/erp/crews'>Go to Crews</a> | ";
    echo "<a href='/PT_indoocean/PT_indoocean/erp/crews/1'>Go to Crew Profile #1</a> | ";
    echo "<a href='/PT_indoocean/PT_indoocean/erp/crews/skill-matrix'>Go to Skill Matrix</a>";
} else {
    echo "Invalid mode. Use 'classic' or 'modern'";
}
