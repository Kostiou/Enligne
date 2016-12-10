<?php
session_start();
require_once 'settings/bdd.inc.php';    //permet la connection à la base de données
require_once 'settings/init.inc.php';   //permet l'affichage des erreurs
include_once 'includes/header.inc.php'; //renvoi à la page PHP incluant le headers

unset($_SESSION['connexion']);  //destruction de la session connexion



//Requete permettant la création de nouveaux comptes
if (isset($_POST['creercompte'])) { //condition permettant de lancer le php si le bouton submit a été cliqué
    
    //Script vérifiant si tous les champs sont complétés
    if($_POST['nom'] == NULL || $_POST['prenom'] == NULL || $_POST['email'] == NULL|| $_POST['mdp'] == NULL ){?>
            <script type="text/javascript">
                    alert("Veuillez compléter tout les champs !");
                </script>
        <?php
        
    }else{
    $sth = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, mdp) VALUES(:nom, :prenom, :email, :mdp)"); //préparation de la requete d'insertion pour la création de comptes
    $sth->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
    $sth->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
    $sth->bindValue(':email', $_POST['email'], PDO::PARAM_STR); //Sécurise les valeurs des variables qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
    $sth->bindValue(':mdp', $_POST['mdp'], PDO::PARAM_STR);
    $sth->execute();    //Permet l'execution de la requete

    $_SESSION['creationOk'] = TRUE; //Création d'une session de validation de création de compte

    header("location: connexion.php");  //Redirige vers l'index en fin de processus
}
}
?>


<!-- Formulaire HTML de création de compte -->

<div class="span8">

    <div><p>Veuillez remplir ce formulaire d'inscription pour la création de votre compte</p></div>

    <form action="compte.php" method="post" enctype="multipart/form-data" id="form_article" name="compte">  

        <div class="clearfix">
            <label for="nom">Nom</label>
            <div class="input"><input type="text" name="nom" id="nom" placeholder="Votre Nom"></div>   
        </div>

        <div class="clearfix">
            <label for="prenom">Prénom</label>
            <div class="input"><input type="text" name="prenom" id="prenom" placeholder="Votre Prénom"></div>
        </div>

        <div class="clearfix">
            <label for="email">Email</label>
            <div class="email"><input type="email" name="email" id="email" placeholder="Votre Email"></div>
        </div>

        <div class="clearfix">
            <label for="mdp">Mot De Passe</label>
            <div class="input"><input type="password" name="mdp" id="mdp" placeholder="Votre Passe"></div>
        </div>

        <div class="clearfix">
            <input type="submit" name="creercompte" value="Créer mon Compte" class="btn btn-large btn-primary"></div>

</div>



<?php
if (isset($_SESSION['creationOk']) AND $_SESSION['creationOk'] == TRUE) {   // condition permettant l'affichage du menu, fonction de la validation de création du compte
include_once 'includes/menu.inc.php';
}
include_once 'includes/footer.inc.php';
?>