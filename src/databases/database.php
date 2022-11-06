<?php

require_once(__DIR__ . '/../lib/sql.php');

$dropSql = 'DROP TABLE IF EXISTS hospital';

$createSql = <<<EOT
CREATE TABLE hospital(
    year INTEGER NOT NULL,
    average FLOAT NOT NULL,
    median FLOAT NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    category_id VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARACTER SET=utf8mb4
EOT;

$insertHospitalSql = <<<EOT
INSERT INTO hospital (
    year,
    average,
    median,
    category_name,
    category_id
) VALUES (
    2002,
    14.1,
    42.4,
    'hospital per 100km2',
    '#I0950102'
)
EOT;

$insertClinicSql = <<<EOT
INSERT INTO hospital (
    year,
    average,
    median,
    category_name,
    category_id
) VALUES (
    2000,
    15.5,
    55.4,
    'clinic per 100km2',
    '#I0950103'
);
EOT;

dropTable($dropSql);
createTable($createSql);
foreach ([$insertHospitalSql, $insertClinicSql] as $sql) {
    insertTable($sql);
}
