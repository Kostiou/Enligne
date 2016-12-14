<?php
session_start();
require_once 'settings/bdd.inc.php';        //permet la connection à la base de données
require_once 'settings/init.inc.php';       //permet l'affichage des erreurs

if (isset($_GET['id'])) {   // vérifie l'existence d'un Id dans l'URL
    $id = $_GET['id'];  // déclaration de la variable id en récupérant l'id de l'URL

    //Id présent donc on récupére l'article correspondant
    $supprArt = $bdd->prepare("DELETE FROM articles WHERE id=$id"); //préparation de la requete pour récupération ID
    $supprArt->execute(); //execution de la réquete;
    
    
    $supprCom =$bdd->prepare("DELETE FROM commentaires WHERE id_billet=$id"); //préparation de la requete pour récupération ID
    $supprCom->execute(); //execution de la réquete;
    
    $_SESSION['suppr'] = TRUE;  //session validant la suppression
    
    header("location: index.php");    //Recharge la page article en fin de processus
} else {
    header("location: index.php");    //Recharge la page article en fin de processus
}