<?php
/**
 * Fix Duplicate Dropdown Data
 * 
 * Removes duplicate entries from vessel_types and flag_states tables,
 * reassigns foreign keys to the canonical (lowest ID) row,
 * and adds UNIQUE constraints to prevent future duplicates.
 * 
 * SECURITY: CLI only
 * Usage: php database/migrations/fix_duplicate_dropdowns.php
 */
if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    echo 'Page not found';
    exit;
}

// Load config - same logic as app/Config/Database.php
if (!function_exists('getEnvVar')) {
    function getEnvVar($keys, $default = '') {
        if (!is_array($keys)) $keys = [$keys];
        foreach ($keys as $key) {
            if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
            $val = getenv($key);
            if ($val !== false && $val !== '') return $val;
            if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
        }
        return $default;
    }
}

$isWindows = (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

if (!$isWindows) {
    // Docker / NAS UGreen
    $host = getEnvVar(['DB_HOST'], 'mysql');
    $user = getEnvVar(['DB_USER', 'DB_USERNAME'], 'root');
    $pass = getEnvVar(['DB_PASS', 'DB_PASSWORD'], 'rahasia123');
    $port = (int) getEnvVar(['DB_PORT'], 3306);
    $db   = getEnvVar(['ERP_DB_NAME', 'DB_DATABASE'], 'erp_db');
} else {
    // Windows / Laragon
    $host = getEnvVar(['DB_HOST'], 'localhost');
    $user = getEnvVar(['DB_USERNAME', 'DB_USER'], 'root');
    $pass = getEnvVar(['DB_PASSWORD', 'DB_PASS'], '');
    $port = (int) getEnvVar(['DB_PORT'], 3306);
    $db   = getEnvVar(['DB_DATABASE', 'ERP_DB_NAME'], 'erp_db');
}

echo "Connecting to {$host}:{$port} as {$user}...\n";
$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "=== Fix Duplicate Dropdown Data ===\n\n";

// ============================================
// 1. FIX VESSEL_TYPES DUPLICATES
// ============================================
echo "--- Fixing vessel_types ---\n";

// Find duplicates: names that appear more than once
$dupeQuery = "SELECT name, MIN(id) as keep_id, COUNT(*) as cnt 
              FROM vessel_types 
              GROUP BY name 
              HAVING cnt > 1";
$dupes = $conn->query($dupeQuery);

if ($dupes && $dupes->num_rows > 0) {
    while ($row = $dupes->fetch_assoc()) {
        $name = $row['name'];
        $keepId = $row['keep_id'];
        $count = $row['cnt'];
        echo "  Found {$count}x '{$name}' — keeping id={$keepId}\n";

        // Get all duplicate IDs (excluding the one we keep)
        $stmt = $conn->prepare("SELECT id FROM vessel_types WHERE name = ? AND id != ?");
        $stmt->bind_param('si', $name, $keepId);
        $stmt->execute();
        $result = $stmt->get_result();
        $dupeIds = [];
        while ($d = $result->fetch_assoc()) {
            $dupeIds[] = $d['id'];
        }
        $stmt->close();

        if (!empty($dupeIds)) {
            // Reassign vessels pointing to duplicate IDs
            $placeholders = implode(',', array_fill(0, count($dupeIds), '?'));
            $types = str_repeat('i', count($dupeIds));
            
            $updateSql = "UPDATE vessels SET vessel_type_id = ? WHERE vessel_type_id IN ({$placeholders})";
            $stmt = $conn->prepare($updateSql);
            $params = array_merge([$keepId], $dupeIds);
            $stmt->bind_param('i' . $types, ...$params);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected > 0) {
                echo "    Reassigned {$affected} vessel(s) to id={$keepId}\n";
            }

            // Delete duplicate rows
            $deleteSql = "DELETE FROM vessel_types WHERE name = ? AND id != ?";
            $stmt = $conn->prepare($deleteSql);
            $stmt->bind_param('si', $name, $keepId);
            $stmt->execute();
            $deleted = $stmt->affected_rows;
            $stmt->close();
            echo "    Deleted {$deleted} duplicate row(s)\n";
        }
    }
} else {
    echo "  No duplicates found.\n";
}

// Add UNIQUE constraint if not exists
$checkUnique = $conn->query("SHOW INDEX FROM vessel_types WHERE Key_name = 'unique_vessel_type_name'");
if ($checkUnique && $checkUnique->num_rows == 0) {
    if ($conn->query("ALTER TABLE vessel_types ADD UNIQUE INDEX unique_vessel_type_name (name)")) {
        echo "  Added UNIQUE constraint on vessel_types.name\n";
    } else {
        echo "  Warning: Could not add UNIQUE constraint: " . $conn->error . "\n";
    }
} else {
    echo "  UNIQUE constraint already exists.\n";
}

echo "\n";

// ============================================
// 2. FIX FLAG_STATES DUPLICATES
// ============================================
echo "--- Fixing flag_states ---\n";

$dupeQuery = "SELECT name, MIN(id) as keep_id, COUNT(*) as cnt 
              FROM flag_states 
              GROUP BY name 
              HAVING cnt > 1";
$dupes = $conn->query($dupeQuery);

if ($dupes && $dupes->num_rows > 0) {
    while ($row = $dupes->fetch_assoc()) {
        $name = $row['name'];
        $keepId = $row['keep_id'];
        $count = $row['cnt'];
        echo "  Found {$count}x '{$name}' — keeping id={$keepId}\n";

        // Get all duplicate IDs
        $stmt = $conn->prepare("SELECT id FROM flag_states WHERE name = ? AND id != ?");
        $stmt->bind_param('si', $name, $keepId);
        $stmt->execute();
        $result = $stmt->get_result();
        $dupeIds = [];
        while ($d = $result->fetch_assoc()) {
            $dupeIds[] = $d['id'];
        }
        $stmt->close();

        if (!empty($dupeIds)) {
            // Reassign vessels pointing to duplicate IDs
            $placeholders = implode(',', array_fill(0, count($dupeIds), '?'));
            $types = str_repeat('i', count($dupeIds));
            
            $updateSql = "UPDATE vessels SET flag_state_id = ? WHERE flag_state_id IN ({$placeholders})";
            $stmt = $conn->prepare($updateSql);
            $params = array_merge([$keepId], $dupeIds);
            $stmt->bind_param('i' . $types, ...$params);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected > 0) {
                echo "    Reassigned {$affected} vessel(s) to id={$keepId}\n";
            }

            // Delete duplicate rows
            $deleteSql = "DELETE FROM flag_states WHERE name = ? AND id != ?";
            $stmt = $conn->prepare($deleteSql);
            $stmt->bind_param('si', $name, $keepId);
            $stmt->execute();
            $deleted = $stmt->affected_rows;
            $stmt->close();
            echo "    Deleted {$deleted} duplicate row(s)\n";
        }
    }
} else {
    echo "  No duplicates found.\n";
}

// Add UNIQUE constraint if not exists
$checkUnique = $conn->query("SHOW INDEX FROM flag_states WHERE Key_name = 'unique_flag_state_name'");
if ($checkUnique && $checkUnique->num_rows == 0) {
    if ($conn->query("ALTER TABLE flag_states ADD UNIQUE INDEX unique_flag_state_name (name)")) {
        echo "  Added UNIQUE constraint on flag_states.name\n";
    } else {
        echo "  Warning: Could not add UNIQUE constraint: " . $conn->error . "\n";
    }
} else {
    echo "  UNIQUE constraint already exists.\n";
}

echo "\n";

// ============================================
// 3. FIX RANKS DUPLICATES
// ============================================
echo "--- Fixing ranks ---\n";

$dupeQuery = "SELECT name, department, MIN(id) as keep_id, COUNT(*) as cnt 
              FROM ranks 
              GROUP BY name, department 
              HAVING cnt > 1";
$dupes = $conn->query($dupeQuery);

if ($dupes && $dupes->num_rows > 0) {
    while ($row = $dupes->fetch_assoc()) {
        $name = $row['name'];
        $dept = $row['department'];
        $keepId = $row['keep_id'];
        $count = $row['cnt'];
        echo "  Found {$count}x '{$name}' ({$dept}) — keeping id={$keepId}\n";

        // Get all duplicate IDs
        $stmt = $conn->prepare("SELECT id FROM ranks WHERE name = ? AND department = ? AND id != ?");
        $stmt->bind_param('ssi', $name, $dept, $keepId);
        $stmt->execute();
        $result = $stmt->get_result();
        $dupeIds = [];
        while ($d = $result->fetch_assoc()) {
            $dupeIds[] = $d['id'];
        }
        $stmt->close();

        if (!empty($dupeIds)) {
            // Reassign contracts pointing to duplicate rank IDs
            $placeholders = implode(',', array_fill(0, count($dupeIds), '?'));
            $types = str_repeat('i', count($dupeIds));
            
            $updateSql = "UPDATE contracts SET rank_id = ? WHERE rank_id IN ({$placeholders})";
            $stmt = $conn->prepare($updateSql);
            $params = array_merge([$keepId], $dupeIds);
            $stmt->bind_param('i' . $types, ...$params);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected > 0) {
                echo "    Reassigned {$affected} contract(s) to rank id={$keepId}\n";
            }

            // Delete duplicate rows
            $deleteSql = "DELETE FROM ranks WHERE name = ? AND department = ? AND id != ?";
            $stmt = $conn->prepare($deleteSql);
            $stmt->bind_param('ssi', $name, $dept, $keepId);
            $stmt->execute();
            $deleted = $stmt->affected_rows;
            $stmt->close();
            echo "    Deleted {$deleted} duplicate row(s)\n";
        }
    }
} else {
    echo "  No duplicates found.\n";
}

// Add UNIQUE constraint if not exists
$checkUnique = $conn->query("SHOW INDEX FROM ranks WHERE Key_name = 'unique_rank_name_dept'");
if ($checkUnique && $checkUnique->num_rows == 0) {
    if ($conn->query("ALTER TABLE ranks ADD UNIQUE INDEX unique_rank_name_dept (name, department)")) {
        echo "  Added UNIQUE constraint on ranks(name, department)\n";
    } else {
        echo "  Warning: Could not add UNIQUE constraint: " . $conn->error . "\n";
    }
} else {
    echo "  UNIQUE constraint already exists.\n";
}

echo "\n";

// ============================================
// 4. VERIFY RESULTS
// ============================================
echo "--- Verification ---\n";

$result = $conn->query("SELECT id, name FROM vessel_types WHERE is_active = 1 ORDER BY name");
echo "  Vessel Types (" . $result->num_rows . "):\n";
while ($row = $result->fetch_assoc()) {
    echo "    [{$row['id']}] {$row['name']}\n";
}

$result = $conn->query("SELECT id, name FROM flag_states WHERE is_active = 1 ORDER BY name");
echo "  Flag States (" . $result->num_rows . "):\n";
while ($row = $result->fetch_assoc()) {
    echo "    [{$row['id']}] {$row['name']}\n";
}

$result = $conn->query("SELECT id, name, department FROM ranks WHERE is_active = 1 ORDER BY department, level");
echo "  Ranks (" . $result->num_rows . "):\n";
while ($row = $result->fetch_assoc()) {
    echo "    [{$row['id']}] {$row['name']} ({$row['department']})\n";
}

echo "\n✅ Done!\n";
$conn->close();
