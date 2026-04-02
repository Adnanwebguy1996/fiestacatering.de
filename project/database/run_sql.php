<?php
$db = new PDO('sqlite:database/database.sqlite');
$sql = file_get_contents('database/setup_custom_tables.sql');
$db->exec($sql);
echo "Tables created successfully.";
