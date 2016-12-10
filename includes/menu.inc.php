<?php


//Gestion de l'insertion de commentaires
if (isset($_POST['post_commentaire'])) {    //Vérifie si le bouton post relatif au commentaire a été cliqué
    
    
    //Si l'un des champs est vide on affiche un message d'erreur par un javascript
    if($_POST['auteur'] == NULL || $_POST['commentaire'] == NULL){?>
            <script type="text/javascript">
                    alert("Veuillez compléter tout les champs !");
                </script>
        <?php
        
    }else{
    
    //Sinon on insert le commentaire
    $date_commentaire = date("Y-m-d");  //fonction affichant la date
    $sth = $bdd->prepare("INSERT INTO commentaires (id_billet, auteur, commentaire, date_commentaire) VALUES(:id_billet, :auteur, :commentaire, :date_commentaire)");   //préparation de la requete d'insertion du commentaire avec les champs du formulaire commentaire
    $sth->bindValue(':id_billet', $_POST['id'], PDO::PARAM_STR);    //Sécurisation des valeurs des variables qui vont être introduites dans la base.
    $sth->bindValue(':auteur', $_POST['auteur'], PDO::PARAM_STR);
    $sth->bindValue(':commentaire', $_POST['commentaire'], PDO::PARAM_STR);
    $sth->bindValue(':date_commentaire', $date_commentaire, PDO::PARAM_STR);
    $sth->execute();    //execution de la réquete

    header('Location: commentaire.php?id=' . $id);  //recharge la page commentaire.php en reprenant l'id de l'article que l'on commente
}
}
?>



<!-- AFFICHAGE DU MENU -->
<nav class="span3">      
    
    <h2 class="text-center">Menu</h2></br>
    
    
    <!-- Moteur de recherche -->
    <form action="recherche.php" method="get" enctype="multipart/form-data" id="form_recherche" class="text-center">    <!-- Formulaire de recherche d'article dans notre site-->
        <div class="clearfix">
            <div class="input"><input type="text" name="recherche" id="recherche" class="input-medium search-query" placeholder="Votre recherche..."/></div></br>   <!-- Utilisation d'une classe de Bootstrap -->
        </div>

        <div class="form-inline">
            <input type="submit" name="rechercher" value="Rechercher sur le blog" class="btn btn-small btn-primary">
        </div>
    </form>
    
    
    
    <ul class="nav nav-list">
        <li class="nav-header"><a href="index.php">Accueil</a></li>     <!-- Lien vers l'index du site -->
        

<!-- Gestion de l'affichage des lien vers la modification d'article et la déconnexion-->        
<?php
if (isset($_COOKIE['sid']) AND isset($_SESSION['connexion']) AND $_SESSION['connexion'] == TRUE) {  // condition permettant de controler la présence du cookie de connexion
    ?>
            <li class="nav-header"><a href="article.php">Rédiger un article</a></li>    <!--Si connexion OK alors on accede à la page de rédaction d'articles -->
            <li class="nav-header"><a href="connexion.php">Déconnexion</a></li> <!--ainsi qu'à un lien de déconnexion -->
    <?php
    
    
  // Sinon affichage du lien pour se connecter
} else {
    ?>
            <li class="nav-header"><a href="connexion.php">Se connecter</a></li>  <!--Sinon on accede uniquement à la page de connexion -->
            <?php
        }
        
        
        //Affichage d'un message indiquant le statut de la connexion
        if (isset($_COOKIE['sid']) AND isset($_SESSION['connexion']) AND $_SESSION['connexion'] == TRUE) {        // condition permettant de controler la présence du cookie de connexion
            ?>
            <li class="nav-header">Vous êtes connecté</li>      <!-- Message indiquant la connexion -->
            <?php
        } else {
            ?>
            <li class="nav-header">Vous n'êtes pas connecté</li>  <!-- ou non -->
            <?php
        }

        
        // si on accede par le bouton commentaire, un session "commentaire" sera ouverte permettant l'affichage d'un formulaire de dépot de commentaire
        if (isset($_SESSION['commentaire']) AND $_SESSION['commentaire'] = TRUE) {  
            ?>
            </br>
            </br>
            <div><h5>Envie de laisser un commentaire sur cet article ?</h5></div>


            <!-- Formulaire pour laisser un commentaire -->
            <form action="commentaire.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" id="post_commentaire" name="commentaire"> <!-- redirection fonction de l'id pour afficher de nouveau le même article que celui que l'on commente -->

                <div class="clearfix">
                    <label for="prenom">Prénom :</label>
                    <div class="input"><input type="text" name="auteur" id="nom" placeholder="Votre Prénom"></div>   <!-- envoi du prénom -->
                </div>

                <div class="clearfix">
                    <label for="commentaire">Commentaire</label>
                    <div class="input"><textarea name="commentaire" id="commentaire" placeholder="Votre commentaire"></textarea></div>   <!-- commentaire -->
                </div>

                <div class="clearfix">
                    <div class="input"><input type="hidden" name="id" id="id" value="<?php echo $id ?>"></div>  <!-- Champs caché permettant de poster l'id de l'article pour l'utiliser pour afficher le même article lors de la validation du formulaire (c.f balise form) -->

                    <div class="clearfix">
                        <input type="submit" name="post_commentaire" value="Laisser un commentaire" class="btn btn-small btn-primary"></div>
    <?php
}
?>
                </nav>
            </div>