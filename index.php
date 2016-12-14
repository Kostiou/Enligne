<?php
session_start();
require_once 'settings/bdd.inc.php';        //permet la connection à la base de données
require_once 'settings/init.inc.php';       //permet l'affichage des erreurs
include_once 'includes/header.inc.php';     //renvoi à la page PHP incluant le header


//Destruction d'un session commentaire
if (isset($_SESSION['commentaire']) and $_SESSION['commentaire'] == TRUE) { //si une session de commentaire est ouverte
    unset($_SESSION['commentaire']);    //on la détruit
}



//Gestion de l'affichage d'article par page
$articlesParPage = 2;   // nombre d'article que je souhaite par page
$numeroPage = isset($_GET['p']) ? $_GET['p'] : 1;   // récupération du numero de la page dans l'URL
$index = (($numeroPage - 1) * $articlesParPage);    // calcul du numero d'index du début de la page

$nbreMessage = $bdd->prepare("SELECT COUNT(*) as nbArticles FROM articles WHERE publie = :publie"); // requete sql indiquant le nbre de message dans la table
$nbreMessage->bindValue(':publie', 1, PDO::PARAM_INT);  //Sécurise la valeur de la variable qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
$nbreMessage->execute();    //Permet l'execution de la requete

$nbArticles = $nbreMessage->fetchAll(PDO::FETCH_ASSOC); //On créé une variable dans laquelle on injecte le tableau des données
$nbreArticle = $nbArticles[0]['nbArticles'];    //Va chercher dans ce tableau le nombre d'articles

$nbredepages = ceil($nbreArticle / $articlesParPage);   //Affiche le nombre de page sous forme d'un entier

function indexDepart($numeroPage, $articlesParPage) {   // Fonction permettant le calcul de l'index en fonction de la page
    $index = (($numeroPage - 1) * $articlesParPage);
    return $index;
}

$indexDynamique = indexDepart($numeroPage, $articlesParPage);   //Utilisation de la fonction créée précedement

//Requete permettant l'affichage des articles selon les LIMIT des variables ci dessus
$sth = $bdd->prepare("SELECT id, titre, texte, DATE_FORMAT (date, '%d/%m/%Y') as date_fr FROM articles WHERE publie = :publie ORDER BY id ASC LIMIT $indexDynamique, $articlesParPage");    //préparation de la requete
$sth->bindValue(':publie', 1, PDO::PARAM_INT);  //Sécurise la valeur de la variable qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
$sth->execute();    //Permet l'execution de la requete

$tab_articles = $sth->fetchAll(PDO::FETCH_ASSOC);   //On créé une variable dans laquelle on injecte le tableau de données
//print_r($tab_articles);     Lance l'affichage du tableau en faisant appel à la variable créée précedement. Utile pour retrouver ces keys en supprimant  les // pour visualiser
?>



<div class="span7 hero-unit">

    
    <!-- Gestion des messages de connexion et de modification/suppression d'articles-->
    <?php
    if (isset($_COOKIE['sid']) AND isset($_SESSION['connexion']) AND $_SESSION['connexion'] == TRUE) {  // condition permettant de controler la présence du cookie de connexion
        ?>
        <div class = "alert alert-success text-center" role = "alert">  <!--Affichage d'un message de confirmation de connexion-->
            <strong>Félicitations</strong> Vous êtes connecté!!!
        </div>
        <?php
    }

    if (isset($_SESSION['modif_article']) AND $_SESSION['modif_article'] = TRUE) {  // condition permettant de controler la présence du cookie de connexion
        ?>
        <div class = "alert alert-success text-center" role = "alert">  <!--Affichage d'un message de confirmation de connexion-->
            <strong>Félicitations</strong> Votre article a été modifié !!!
        </div>
        <?php
    }
    
    if (isset($_SESSION['suppr']) AND $_SESSION['suppr'] = TRUE) {  // condition permettant de controler la présence du cookie de connexion
        ?>
        <div class = "alert alert-danger text-center" role = "alert">  <!--Affichage d'un message de confirmation de connexion-->
            Votre article a été supprimé !!!
        </div>
        <?php
        unset($_SESSION['suppr']);    //aprés affichage du message de suppression on détruit la session
    }

    
    
    //Affichage des articles
    foreach ($tab_articles as $value) { //boucle foreach et une boucle for, specifique à l'exploitation des tableaux
        ?>
    
        <h2><?php echo $value['titre']; ?></h2></br>    <!-- cet appel php permet de faire appel à la valeur 'titre' et l'inserer dans le h2 du HTML-->
        <div class="text-center">
            <img src="img/<?php echo $value['id']; ?>.jpg" width="250px" alt="titre"/>  <!-- ce PHP renvoi à une image stockée dans le dossier IMG et dont le n° titre.jpg correspond aux id-->
        </div></br>
        
        <p style="text-align: justify;"><?php echo $value['texte']; ?></p>  <!-- fait appel au texte de la base de donnée-->
        <p><em><u>Publié le : <?php echo $value['date_fr']; ?></u></em></p> <!-- cet appel php permet de faire appel à la valeur 'date' et l'inserer dans du texte HTML-->
        
        <div class="inline">
        <a href="commentaire.php?id=<?php echo $value['id']; ?>"><p>
                <button class="btn btn-success" type="button">Commentaires</button>
            </p></a></div>
        <?php
        
        if (isset($_COOKIE['sid'])AND isset($_SESSION['connexion']) AND $_SESSION['connexion'] == TRUE) {   //Donne l'accés à la modification des articles uniquement si la connexion est certifié par le cookie
            ?>
        <div class="inline">
            <a href="article.php?id=<?php echo $value['id']; ?>"><p>
                    <button class="btn btn-primary" type="button">Modifier cet article</button><!-- Ce lien redirige vers la page article en ajoutant l'id dans l'URL par la méthode GET   -->
                </p></a>
        </div>
        
        <div class="inline">
            <a href="suppression.php?id=<?php echo $value['id']; ?>"><p>
                    <button class="btn btn-danger" type="button">Supprimer cet article</button><!-- Ce lien redirige vers la page article en ajoutant l'id dans l'URL par la méthode GET   -->
                </p></a>
        </div>
            <?php
        }

        //Gestion de l'affichage des commentaires
        $id = $value['id']; //recuperation de l'id de l'article
        $req = $bdd->query("SELECT  COUNT(*) as NbCommentaires FROM commentaires WHERE id_billet=$id"); //requete recuperant les commentaires selon l'id de l'article
        $donnees = $req->fetch();
        $req->closeCursor();
        if ($donnees['NbCommentaires'] == 0) {
            ?>
            <p class="text-success">Il n'y a aucun commentaire sur cet article</p></br>  <!-- affichage du message si pas enore de commentaire  -->
            <?php
        } elseif ($donnees['NbCommentaires'] == 1) {
            ?>
            <p class="text-success">Il a 1 commentaire sur cet article</p></br>  <!-- commentaire au singulier si 1 commentaire -->
            <?php
        } else {
            ?>
            <p class="text-success">Il a <?php echo $donnees['NbCommentaires']; ?> commentaires sur cet article</p></br> <!-- affiche le nombre de comm' déja envoyés -->
            <?php
        }
    }
    ?>


    <!-- Gestion de l'affichage des numéros de pages pour la consultation des articles -->
    <div class="pagination pagination-centered">
        <ul>
            <li><a>Page : </a></li>
            <?php
            for ($i = 1; $i <= $nbredepages; $i++) {    //boucle for permettant la création de boutons en fonction du nombre de pages à afficher
                if ($numeroPage == $i) {    //ce if compare si la page actuelle affiché correspond au numéro du bouton
                    $ClassBouton = 'active';    // si la condition est vérifiée, alors il active la class "active" de Bootstrap
                } else {
                    $ClassBouton = '';  // sinon il laisse l'affichage normal sans class
                }
                ?>
                <li class="<?php echo $ClassBouton ?>"> <a href="index.php?p=<?= $i ?>"><?= $i ?></a> </li> <!-- boutons affichant le nombre de page avec notre variable classbouton activé ou non fonction de la page actuellement affiché  -->
                <?php
            }
            ?>
        </ul>
    </div>
</div>

<?php
include_once 'includes/menu.inc.php';   //renvoi à la page PHP incluant le menu
include_once 'includes/footer.inc.php'; //renvoi à la page PHP incluant le footer


//Destruction des sessions
unset($_SESSION['modif_article']);
if (isset($_SESSION['connexion']) and $_SESSION['connexion'] == FALSE) {    //destruction de la session connexion False (connexion non valide)
    unset($_SESSION['connexion']);
}
if (isset($_SESSION['ajout_article']) and $_SESSION['ajout_article'] == TRUE) { // destruction de la session d'ajout d'article
    unset($_SESSION['ajout_article']);
}
?>

<!--  Index de mes variables :
  
   $articlesParPage = nombre d'article que je souhaite afficher par page
   $numeroPage = numero de la page
   $index = correspond à l'article affiché fonction de la page affichée
   $nbredepages = nombre de pages à afficher dans le petit onglet inferieur
   function indexDepart = Fonction permettant le calcul de l'index en fonction de la page
   $value + [texte ou titre ou id ou date] = permet injecteur contenu des articles à l'affichage de la page
   $i = nombre de pages necessaire pour tous les articles.
   $ClassBouton = modifie le comportement des boutons pour aller aux pages fonction de la page affichée
-->