<?php
require 'includes/db.php';
$c = db()->query('SELECT name FROM categories LIMIT 5')->fetchAll();
print_r($c);
