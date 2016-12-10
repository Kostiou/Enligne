<?php

session_start();

require_once 'settings/bdd.inc.php';        //permet la connection à la base de données
require_once 'settings/init.inc.php';       //permet l'affichage des erreurs
include_once 'includes/header.inc.php';     //renvoi à la page PHP incluant le header



//Gestion de la récupération de l'article correspondant à l'id de l'URL
if (isset($_GET['id'])) {   // vérifie l'existence d'un Id dans l'URL
    $id = $_GET['id'];  // injecte l'id de l'URL dans une variable
    $_SESSION['commentaire'] = TRUE;    // Création d'une session relative au commentaire

    $sth = $bdd->prepare("SELECT id, titre, texte, DATE_FORMAT (date, '%d/%m/%Y') as date_fr FROM articles WHERE id =$id"); //préparation de la requete pour récupération d'un article fonction de l'id dans l'URL
    $sth->bindValue(':id', 1, PDO::PARAM_INT);  //Sécurise la valeur qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
    $sth->execute();    //execute la requete

    $tab_articles = $sth->fetchAll(PDO::FETCH_ASSOC);   //stock le résultat sous forme d'un tableau

    $titre = $tab_articles [0] ['titre'];   //créé une variable titre récuperer dans le tableau (tableau à 2 niveaux donc [0] puis ['titre'] pour acceder notre variable titre
    $article = $tab_articles [0] ['texte']; //idem avec le contenu de l'article
    $date_article = $tab_articles [0] ['date_fr']; //récupération de la date de l'article
}
?>


<!-- HTML gérant l'affichage de l'article -->
<div class="span7 hero-unit">   

    <h2><?php echo $titre; ?></h2></br> <!-- cet appel php permet de faire appel à la valeur 'titre' et l'inserer dans le h2 du HTML-->
    <div class="text-center">
        
        <img src="img/<?php echo $id; ?>.jpg" width="250px" alt="titre"/>   <!-- ce PHP renvoi à une image stockée dans le dossier IMG et dont le n° titre.jpg correspond aux id-->
        
    </div></br>
    
    <p style="text-align: justify;"><?php echo $article; ?></p> <!-- fait appel au texte de la base de donnée-->
    
    <p><em><u>Publié le : <?php echo $date_article; ?></u></em></p> <!-- cet appel php permet de faire d'afficher la 'date' et l'inserer dans du texte HTML-->
    </br>

    


<!-- Message d'information sur le nombre de commentaires sur l'article  -->    
<?php
$req = $bdd->query("SELECT  COUNT(*) as NbCommentaires FROM commentaires WHERE id_billet=$id"); //requete comptant le nombre de commentaire sur l'id de l'article
$donnees = $req->fetch();
$req->closeCursor();

//Affichage de differents messages fonction du nombre d'article
if ($donnees['NbCommentaires'] == 0) {  //si 0 article
    ?>
        <h2 class="text-success">Il n'y a aucun commentaire sur cet article</h2> 
        <?php
        
    } elseif ($donnees['NbCommentaires'] == 1) { //si un article, commentaire au singulier
        ?>
        <h2 class="text-success">Commentaire</h2>
        <?php
        
    } else {    //pour le reste (donc >1) on affiche commentaire au pluriel
        ?>
        <h2 class="text-success">Commentaires</h2>
        <?php
    }

    
    
    $sth->closeCursor();    // libère le curseur pour la prochaine requête

//Récupération des commentaires
    $sth = $bdd->prepare('SELECT auteur, commentaire, DATE_FORMAT(date_commentaire, \'%d/%m/%Y\') AS date_commentaire_fr FROM commentaires WHERE id_billet = ? ORDER BY id ASC');
    $sth->execute(array($_GET['id']));

    while ($donnees = $sth->fetch()) {  //boucle affichant tous les commentaires
        ?>

        <p><strong class="text-info"><?php echo htmlspecialchars($donnees['auteur']); ?></strong> le <?php echo $donnees['date_commentaire_fr']; ?></p>
        <p><?php echo nl2br(htmlspecialchars($donnees['commentaire'])); ?></p>

    <?php
} // Fin de la boucle des commentaires

$sth->closeCursor();    // libère le curseur pour la prochaine requête
?>

</div>


<?php
include_once 'includes/menu.inc.php';       //renvoi à la page PHP incluant le menu
include_once 'includes/footer.inc.php';     //renvoi à la page PHP incluant le footer