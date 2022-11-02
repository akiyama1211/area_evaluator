<?php

require_once('readEnv.php');


function connectData(): mysqli
{
    $env = readEnv();
    $dbHost = $env[0];
    $dbDatabase = $env[1];
    $dbUsername = $env[2];
    $dbPassword = $env[3];
    $connectData = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
    if ($connectData->connect_error) {
        throw new RuntimeException('mysqli接続エラー:' . $connectData->connect_error);
    }
    return $connectData;
}

function createTable(string $sql): void
{

    $creator = connectData();
    $result = $creator->query($sql);
    if($result) {
        echo 'テーブルを作成しました' . PHP_EOL;
    } else {
        echo 'テーブルを作成できませんでした' . PHP_EOL;
    }
    $creator->close();
}

function insertTable(string $sql): void
{
    $creator = connectData();
    $result = $creator->query($sql);
    if($result) {
        echo 'レコードを作成しました' . PHP_EOL;
    } else {
        echo 'レコードを作成できませんでした' . PHP_EOL;
    }
    $creator->close();
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
