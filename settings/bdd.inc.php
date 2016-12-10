<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=u850706227_costi;charset=utf8', 'u850706227_costi', '9nzM3pcKRm');
    $bdd->exec('set names utf8');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}