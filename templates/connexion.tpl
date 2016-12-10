<div class="span8">                                 <!-- mise en page du formulaire par la grille Bootstrap-->

        {if isset($connexion) AND  $connexion == FALSE}
        <!-- Le message de notification de connexion -->
    {/if}
    }
    <form action="connexion.php" method="post" enctype="multipart/form-data" id="form_article" name="connexion">  <!-- Appel un formulaire pour créer se connecter-->

        <div class="clearfix">
            <label for="email">Email</label>
            <div class="email"><input type="email" name="email" id="email" value="Votre Email"></div>
        </div>

        <div class="clearfix">
            <label for="mdp">Mot De Passe</label>
            <div class="input"><input type="password" name="mdp" id="mdp" value="Votre Passe"></div>
        </div>

        <div class="clearfix">
            <input type="submit" name="envoyer" value="Envoyer" class="btn btn-large btn-primary"></div>

    </form>

</div>