**********************************************************************************
************************         Blog de Thomas         **************************
**********************************************************************************

Lors de l'arriver sur le blog, il y a plusieurs possibilités :
    
    - Visionner les articles deja présents sur le blog
    - Vous inscrire, si vous n'avez pas de compte
    - Vous connecter si vous etes inscrit
    - Rechercher un article à l'aide du moteur de recherche
    - Laisser un commentaire sur un article
    
Si vous n'étes pas inscrit, vous n'avez pas la possibilité de rédiger ni d'éditer d'articles.

-- Rediger un article --

Lorsque que vous rédiger un article, vous pouvez mettre une photo grace à  l'upload d'image.



*********************************************************************************
***********************         Espace développement       **********************
*********************************************************************************

Pour que le code soit un peu plus clair, dans chaque page j'ai fais des includes vers le header, footer et menu.



index.php = Page avec tous les articles présent en base de données

footer.php = C'est le bas de la page

header.php = C'est le haut de la page avec les appels css, et si un utilisateur est connecter, il affiche le prenom de la personne connecter et l'adresse de connexion

menu.php = Cette page gere la barre de navigation avec les boutons de connexion, le moteur de recherche. Si l'utilisateur est connecter on affiche le bouton déconnexion

article.php = C'est le php qui gére l'ajout et la modification dans la base des articles, par un fomulaire

commentaire.php = C'est le php qui gére l'affichage d'un message et de ses commentaires

compte.php = Page pour se créer un compte

connexion.php = Page gérant la connexion au blog

recherche.php = Gére l'affichage du résultat de la recherche par le moteur de recherche du menu



Lorsqu'on s'inscrit, on fait appel a la page : compte.php
Nous avons les champs Nom, Prenom, E-mail et mot de passe