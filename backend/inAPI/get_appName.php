<?php


function getappName($id,$country,$code)
{
    include 'config.php';

    #TODO get by id not by name
    $sql = 'SELECT `Name` FROM `foreignapiservice` WHERE `Id_Foreign_Api` = ? and `country` = ? and `code` = ?';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id,$country,$code]);
    $res = $stmt->fetchAll();
    $appname = $res[0]['Name'];
    $stmt->closeCursor();
    return $appname;
}


