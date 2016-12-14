<?php
session_start();
require_once 'settings/bdd.inc.php';        //permet la connection à la base de données
require_once 'settings/init.inc.php';       //permet l'affichage des erreurs

if (isset($_GET['id'])) {   // vérifie l'existence d'un Id dans l'URL
    $id = $_GET['id'];  // déclaration de la variable id en récupérant l'id de l'URL

    //Suppression de l'article dont l'id est dans l'URL
    $supprArt = $bdd->prepare("DELETE FROM articles WHERE id=$id"); //préparation de la requete de suppression
    $supprArt->execute(); //execution de la réquete;
    
    //Suppression des commentaires correspondant à l'id_billet
    $supprCom =$bdd->prepare("DELETE FROM commentaires WHERE id_billet=$id"); //préparation de la requete suppression
    $supprCom->execute(); //execution de la réquete;
    
    $_SESSION['suppr'] = TRUE;  //session validant la suppression
    
    header("location: index.php");    //Recharge la page index en fin de processus
} else {
    header("location: index.php");    //Renvoi vers la page article
}