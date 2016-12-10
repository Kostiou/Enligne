<?php
session_start();
require_once 'settings/bdd.inc.php';        //permet la connection à la base de données
require_once 'settings/init.inc.php';       //permet l'affichage des erreurs
include_once 'includes/header.inc.php';     //renvoi à la page PHP incluant le headers
?>



<div class="span8"> <!-- mise en page du formulaire par la grille Bootstrap-->



    <!-- Gestion des messages d'alerte de création de compte, d'erreur de connexion et de déconnexion-->
    <?php
    if (isset($_SESSION['creationOk']) AND $_SESSION['creationOk'] == TRUE) {   // condition permettant l'affichage d'un message de confirmation de l'envoi de l'article
        ?>
        <div class = "alert alert-success text-center" role = "alert">  <!--Ajoute une alerte indiquant que le compte est bien créé-->
            <strong>Félicitations</strong> Votre compte a été créé!!!
        </div>
        <?php
    }
    ?>
    </br>
    <?php
    if (isset($_SESSION['connexion']) and $_SESSION['connexion'] == FALSE) {     //Vérifie que la session connexion existe mais qu'elle n'est pas valide
        ?>
        <div class="alert alert-error" role="alert">
            <strong>Erreur !!!</strong> Login et / ou mot de passe faux.</div>    <!-- Affichage message erreur connexion-->
        <?php
    }
    if (isset($_COOKIE['sid'])AND isset($_SESSION['connexion']) AND $_SESSION['connexion'] == TRUE) {   //Si le cookie est présent et que la session connexion est valide --> on déconnecte
        ?>
        <div class="alert alert-error" role="alert">
            <strong>Déconnexion OK</strong> A une prochaine fois
        </div>
        <?php
        setcookie('sid', time() - 3000);    //destruction du cookie par utilisation d'un timelaps négatif
        $_SESSION['connexion'] = FALSE; //passage de la session en déconnexion
    }
    ?>
            
            
            

    <!-- Formulaire de connexion -->
    <form action="connexion.php" method="post" enctype="multipart/form-data" id="form_article" name="connexion">

        <div class="clearfix">
            <label for="email">Email</label>
            <div class="email"><input type="email" name="email" id="email" placeholder="Votre Email"></div>
        </div>

        <div class="clearfix">
            <label for="mdp">Mot De Passe</label>
            <div class="input"><input type="password" name="mdp" id="mdp" placeholder="Votre Passe"></div>
        </div>

        <div class="clearfix">
            <input type="submit" name="envoicode" value="Envoyer" class="btn btn-large btn-primary"></div>
        </br>
        <div><p>Pas encore membre ? Inscrivez vous <a href="compte.php">ici</a></p></div> <!-- Lien vers la page de création de compte -->

    </form>

</div>


<?php
//Controle de l'existance d'un compte avec les données saisies par l'utilisateur
if (isset($_POST['envoicode'])) {   //Vérifie que le bouton Envoyer a été cliqué
    $conex = $bdd->prepare("SELECT * FROM utilisateurs WHERE email = :email AND mdp = :mdp");   //prépare la requete de comparaison dans la base 
    $conex->bindValue(':mdp', $_POST['mdp'], PDO::PARAM_STR);   //sécurise email et mdp
    $conex->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
    $conex->execute();  //Execute la requete

    $count = $conex->rowCount();    //Compte le résultat d'une correspondance dans la base
    $tab_conex = $conex->fetchAll(PDO::FETCH_ASSOC);
    //   print_r($tab_conex);    //Controle du contenu du tableau  ->OK

    //Validation de la connexion par la création d'un cookie et par l'ouverture d'une session
    if ($_POST['email'] == $tab_conex[0]['email'] && $_POST['mdp'] == $tab_conex[0]['mdp']) {   //vérifie la correspondance d'un mail et d'un mdp dans notre base
        $email = $tab_conex[0]['email'];    //création variable email récupéré dans notre base
        $sid = md5(time() . $email);    // création d'une variable sid unique à partir du mail et du temps par la fonction php md5
        echo $sid;

        $id = $tab_conex[0]['id'];
        $conex = $bdd->prepare("UPDATE utilisateurs SET sid='$sid' WHERE id='$id'");    //prépare une requete de mise à jour de la base
        $conex->execute();
        setcookie('sid', $sid, time() + 1800);  //création d'un cookie d'une durée de vie de 30 minutes
        header('Location: index.php');  //redirige vers l'index
        $_SESSION['connexion'] = TRUE;  //session validant une connexion
    } else {
        $_SESSION['connexion'] = FALSE; //sinon pas de correspondance mail+mdp donc la session de connexion est fausse
        header('Location: connexion.php');  //redirige vers la page connexion
    }
}
include_once 'includes/footer.inc.php';
unset($_SESSION['creationOk']); //destruction de la session de création de compte
?>

