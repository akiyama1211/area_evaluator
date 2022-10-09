<?php

function connectData(): mysqli
{
    $connectData = new mysqli('db', 'test_user', 'pass', 'test_database');
    if ($connectData->connect_error) {
        throw new RuntimeException('mysqli接続エラー:' . $connectData->connect_error);
    }
    return $connectData;
}

function getData(string $sql): array
{
    $getter = connectData();
    $result = $getter->query($sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $getter->close();
    return $data;
}

function createData(string $sql, array $values): void
{
    $creator = connectData();
    // $sql = 'INSERT INTO employees (
    //         average,
    //         median
    //     ) VALUES (' . implode(',', array_fill(0, count($values), '?')) . ')';
    $stmt = $creator->prepare($sql);
    $types = judgeType($values);
    $typeString = implode('', $types);
    $stmt->bind_param($typeString, ...$values);
    $stmt->execute();
    $stmt->close();
    $creator->close();
}

function deleteData(string $sql, array $check): void
{
    $deleter = connectData();
    // $sql = 'DELETE FROM employees WHERE id IN (' . implode(',', array_fill(0, count($check), '?')) . ')';
    $stmt = $deleter->prepare($sql);
    $types = judgeType($check);
    $typeString = implode('', $types);
    $stmt->bind_param($typeString, ...$check);
    $stmt->execute();
    $stmt->close();
    $deleter->close();
}

function judgeType(array $arr): array
{
    $types = [];
    foreach($arr as $value) {
        $type = gettype($value);
        if ($type === 'integer') {
            $types[] = 'i';
        } elseif ($type === 'string') {
            $types[] = 's';
        } elseif ($type === 'float' || $type === 'double') {
            $types[] = 'd';
        }
    }
    return $types;
}
