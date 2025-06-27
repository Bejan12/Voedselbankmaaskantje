<?php
echo "=== DEBUG CLASS LOADING ===\n";

// Test bootstrap
require_once 'bootstrap.php';

echo "Classes loaded after bootstrap:\n";
$classes = get_declared_classes();
$userClasses = array_filter($classes, function($class) {
    return !str_starts_with($class, 'PHP') && 
           !str_starts_with($class, 'PDO') && 
           !str_starts_with($class, 'DateTime') &&
           !str_starts_with($class, 'Exception') &&
           !str_starts_with($class, 'Reflection');
});

foreach ($userClasses as $class) {
    echo "- $class\n";
}

echo "\nSpecific classes:\n";
echo "MagazijnvoorraadModel exists: " . (class_exists('MagazijnvoorraadModel') ? 'YES' : 'NO') . "\n";
echo "Magazijnvoorraad exists: " . (class_exists('Magazijnvoorraad') ? 'YES' : 'NO') . "\n";
echo "Database exists: " . (class_exists('Database') ? 'YES' : 'NO') . "\n";