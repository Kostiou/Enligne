<?php
session_start();
require_once 'settings/bdd.inc.php';        //permet la connection à la base de données
require_once 'settings/init.inc.php';       //permet l'affichage des erreurs
include_once 'includes/header.inc.php';     //renvoi à la page PHP incluant le header

if (isset($_SESSION['commentaire']) and $_SESSION['commentaire'] == TRUE) { //Vérifie si une session commentaire existe
    unset($_SESSION['commentaire']);    //et la détruit
}

//requete de recherche dans la base par LIKE et le mot recherché encadré de %
if (isset($_GET['recherche'])) {    //on recherche le paramétre 'recherche' dans l'URL
    $recherche = $_GET['recherche'];    //on injecte dans une nouvelle variable le paramétre de l'URL 
    $sth = $bdd->prepare("SELECT id, titre, texte, DATE_FORMAT (date, '%d/%m/%Y') as date_fr FROM articles WHERE (titre LIKE :recherche OR texte LIKE :recherche)");    //On prépare une requete SQL de recherche de termes avec LIKE dans les champs titres et texte
    $sth->bindValue(':recherche', "%$recherche%", PDO::PARAM_STR);  //sécurisation de la variable. On encadre de modulo la variable recherche pour trouver les occurances avant et aprés le terme recherché
    $sth->execute();    //on execute la requete

    $count = $sth->rowCount();  //Compte le nombre d'occurence de la recherche

    $tab_result = $sth->fetchAll(PDO::FETCH_ASSOC); //stock le résultat sous forme d'un tableau
//    print_r($tab_result); OK !!   Controle de l'affichage du résultat de la requete
}
?>

<!-- Affichage du résultat de la recherche-->
<div class="span7 hero-unit">

    <?php
    //Si au moins une correspondance est trouvée
    if ($count >= 1) {  //si au moins un article correspond à la recherche
        ?><div class = "alert alert-success text-center" role = "alert">   <!--Message indiquant le nombre d'articles trouvés sur la recherche-->
            <?php echo $count ?> résultat(s) trouvé(s) pour <strong>"<?php echo $recherche ?>"</strong>
        </div>   <?php
        foreach ($tab_result as $value) {   //boucle foreach et une boucle for, specifique à l'exploitation des tableaux et affichant tous les résultats
            ?>

            <h2><?php echo $value['titre']; ?></h2></br>    <!-- cet appel php permet de faire appel à la valeur 'titre' et l'inserer dans le h2 du HTML-->
            <div class="text-center">    
                <img src="img/<?php echo $value['id']; ?>.jpg" width="250px" alt="titre"/>  <!-- ce PHP renvoi à une image stockée dans le dossier IMG et dont le n° titre.jpg correspond aux id-->
            </div></br>  

            <p style="text-align: justify;"><?php echo $value['texte']; ?></p>  <!-- fait appel au texte de la base de donnée-->
            <p><em><u>Publié le : <?php echo $value['date_fr']; ?></u></em></p> <!-- cet appel php permet de faire appel à la valeur 'date' et l'inserer dans du texte HTML-->


            <div class="inline">
                <a href="commentaire.php?id=<?php echo $value['id']; ?>"><p> <!-- Affichage bouton commentaire-->
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
            <?php
        }
    }

    //Si pas de correspondance, affichage d'un message
} else {
    ?><div class="alert alert-error" role="alert">   <!--Message indiquant qu'il n'y a pas de résultat correspondant à la recherche-->
            <strong>Désolé !!! </strong>Aucun résultat trouvé pour <strong>"<?php echo $recherche ?>"</strong>
        </div>   <?php
    }
    ?>
</div>


<?php
include_once 'includes/menu.inc.php';       //renvoi à la page PHP incluant le menu
include_once 'includes/footer.inc.php';     //renvoi à la page PHP incluant le footer
?>  