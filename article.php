<?php
session_start();    //permet de récuperer les variables de cession d'une page à l'autre

require_once 'settings/bdd.inc.php';    //permet la connection à la base de données
require_once 'settings/init.inc.php';   //permet l'affichage des erreurs

//destruction de la session commentaire si existante
if (isset($_SESSION['commentaire']) and $_SESSION['commentaire'] == TRUE) {
    unset($_SESSION['commentaire']);
}


//Gestion de l'affichage du contenu du formulaire fonction de la présence ou non d'un id dans l'URL
if (isset($_GET['id'])) {   // vérifie l'existence d'un Id dans l'URL
    $id = $_GET['id'];  // déclaration de la variable id en récupérant l'id de l'URL

    //Id présent donc on récupére l'article correspondant
    $sth = $bdd->prepare("SELECT id, titre, texte, DATE_FORMAT (date, '%d/%m/%Y') as date_fr FROM articles WHERE id =$id"); //préparation de la requete pour récupération ID
    $sth->bindValue(':id', 1, PDO::PARAM_INT);  //Sécurise la valeur qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
    $sth->execute();    //execute la requete

    $tab_articles = $sth->fetchAll(PDO::FETCH_ASSOC);   //stock le résultat sous forme d'un tableau
    //print_r($tab_articles);  Affichage du tableau pour controle OK

    //insertion des champs du formulaire avec les éléments récupérés par la requéte
    $titre = $tab_articles [0] ['titre'];   //créé une variable titre récuperer dans le tableau (tableau à 2 niveaux donc [0] puis ['titre'] pour acceder notre variable titre
    $article = $tab_articles [0] ['texte']; //idem avec le contenu de l'article
    $bouton = "Modifier";   //variable modifiant la value du bouton, son affichage deviens donc modifier
    $case = "checked";  //variable permettant de pré-cocher la case par l'insertion de la class checked
    
    
    
    //si pas d'id dans l'URL, on envoi rien dans les champs du formulaire
} else {
    $tab_articles = null;
    $titre = "";    //Si pas d'ID, ce else va inserer des champs vides dans notre formulaire
    $article = "";
    $bouton = "Ajouter";    // et change l'intitulé de bouton en Ajouter car il s'agit d'un nouvel article
    $case = "";
    $id = "";
}


//Gestion de l'ajout d'un nouvel article
if (isset($_POST['Ajouter'])) { //condition permettant de lancer le php si le bouton submit a été cliqué
//      print_r($_FILES);   permet l'affiche de l'image sous la forme du tableau
//      exit(); stop le(s) script(s) d'affiche des données bruts
    $date_ajout = date("Y-m-d");    //fonction affichant la date
    $_POST['date'] = $date_ajout;
//    print_r($_POST);  affiche la date en données brut

    $_POST['publie'] = isset($_POST['publie']) ? 1 : 0; //condition ternaire controlant la présence d'une image lors du post 
//    print_r($_POST);  affiche toutes les entrées du tableau en données brut

    
    //Vérification que les champs du formulaire ont bien été complétés
    if($_POST['titre'] == NULL || $_POST['texte'] == NULL){?>
            <script type="text/javascript">
                    alert("Veuillez compléter tout les champs !");
                </script>
        <?php
        
    }else{
    
    if ($_FILES['image']['error'] == 0) {   //fonction permettant de tester la validité de l'image
        $sth = $bdd->prepare("INSERT INTO articles (titre, texte, publie, date) VALUES(:titre, :texte, :publie, :date)");   //préparation de la requete
        $sth->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR); //Sécurise les valeurs des variables qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
        $sth->bindValue(':texte', $_POST['texte'], PDO::PARAM_STR);
        $sth->bindValue(':publie', $_POST['publie'], PDO::PARAM_INT);
        $sth->bindValue(':date', $_POST['date'], PDO::PARAM_STR);
        $sth->execute();    //Permet l'execution de la requete
        $id = $bdd->lastInsertId();
        //echo '<br/> <b><u>' . $dernier_id . '</u></b>';    Permet de controle le dernier ID

        move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__) . "/img/$id.jpg");   //permet de déplacer les images du formulaire vers le dossier img, en renommant l'image par un nom associé à Id de l'article

        $_SESSION['ajout_article'] = TRUE;  //Création d'une session d'ajout d'article

        header("location: article.php");    //Recharge la page article en fin de processus
        
    } else {
        
        echo "Image erreur";    //Gestion des erreur de chargement d'image
        
    }
}
}


//Gestion de la modification d'un article
if (isset($_POST['Modifier'])) {    //si le bonton modifier a été cliqué
    $id_form = $_POST['id'];
    $sth = $bdd->prepare("UPDATE articles SET titre= :titre, texte=:texte WHERE id=$id_form");  //préparation de la requete de mise à jour
    $sth->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR); //Sécurise les valeurs des variables qui peut être introduite dans la base. Cette valeur est forcement numerique du fait du PARAM_INT
    $sth->bindValue(':texte', $_POST['texte'], PDO::PARAM_STR);
    $sth->execute();

    $_SESSION['modif_article'] = TRUE; //session de modif d'article ouverte pour gestion message
    header('Location: index.php'); //retour a la page Article.php
} 


//Affichage du formulaire
else {  // sinon lancer le html seul
    include_once 'includes/header.inc.php'; //renvoi à la page PHP incluant le header
    ?>



    <div class="span8"> <!-- mise en page du formulaire par la grille de Bootstrap-->
        
      
        
<!-- Gestion du message d'ajout d'un article -->
    <?php
    if (isset($_SESSION['ajout_article']) AND $_SESSION['ajout_article'] == TRUE) { // condition permettant l'affichage d'un message de confirmation de l'envoi de l'article
        ?>
            <div class = "alert alert-success text-center" role = "alert">  <!--Ajoute une alerte indiquant que l'article est bien chargé-->
                <strong>Félicitations</strong> Votre article a été ajouté!!!
            </div>
            <?php
        }
        ?>



        <form action="article.php" method="post" enctype="multipart/form-data" id="form_article">   <!-- Balise form pour le formulaire de création d'articles-->

            <div class="clearfix">
                <label for="titre">Titre</label>
                <div class="input"><input type="text" name="titre" id="titre" value="<?php echo $titre ?>"></div>   <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="clearfix">
                <label for="texte">Texte</label>
                <div class="input"><textarea name="texte" id="texte"><?php echo $article ?></textarea></div>    <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="clearfix">
                <label for="image">Image</label>
                <div class="input"><input type="file" name="image" id="image"></div>
            </div>

            <div class="clearfix">
                <label for="publie">Publié</label>
                <div class="input"><input type="checkbox" <?php echo $case ?> name="publie" id="publie"></div>  <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="clearfix">
                <div class="input"><input type="hidden" name="id" id="id" value="<?php echo $id ?>"></div>  <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->
            </div>

            <div class="form-actions">
                <input type="submit" name="<?php echo $bouton ?>" value="<?php echo $bouton ?>" class="btn btn-large btn-primary"></div>    <!-- insertion de php permettant de récuperer les données des articles pour les mettre dans les champs correspondants -->


        </form>

    </div>

    <?php
    include_once 'includes/menu.inc.php';
    include_once 'includes/footer.inc.php';
}


/* Index de mes variables :
 * 
 * $id = id de l'article
 * $titre = contenu du titre de l'article          
 * $article = contenu de l'article       
 * $bouton = pour modifier le texte du bouton du formulaire
 * $case = cocher ou non la case du formulaire
 * $date = date
 * 
 */