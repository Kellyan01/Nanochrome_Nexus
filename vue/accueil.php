<?php include './vue/header.php'; ?>

<main id="accueil">
    <form id="formInscription" action="" method="POST">
        <h2>Inscription</h2>
        <fieldset>
            <label>Prénom</label>
            <input type="text" name="prenom_user" placeholder="John" required>
        </fieldset>

        <fieldset>
            <label>Nom</label>
            <input type="text" name="nom_user" placeholder="Doe" required>
        </fieldset>
        
        <fieldset>
            <label>Pseudo</label>
            <input type="text" name="pseudo_user" placeholder="Nick Name" required>
        </fieldset>

        <fieldset>
            <label>Mail</label>
            <input type="email" name="mail_user" placeholder="john@doe.com" pattern="[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})" required>
        </fieldset>

        <fieldset>
            <label>Mot de Passe</label>
            <input type="password" name="password_user" placeholder="Password1234" pattern="\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*" required>
            <p>Doit contenir au moins 8 caractères, dont : 1 lettre minuscule, 1 lettre majuscule, 1 chiffre et 1 caractère spécial</p>
        </fieldset>

        <fieldset>
            <label>Confirmez votre Mot de Passe</label>
            <input type="password" name="repeat_password_user" placeholder="Password1234" pattern="\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*" required>
        </fieldset>

        <input class="submit" type="submit" value="S'Inscrire">
        <div id="message"></div>
    </form>

    <form id="formConnexion" action="" method="POST">
        <h2>Connexion</h2>
        <fieldset>
            <label>Mail</label>
            <input type="email" name="mail_user_connexion" placeholder="john@doe.com" pattern="[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})" required>
        </fieldset>

        <fieldset>
            <label>Mot de Passe</label>
            <input type="password" name="password_user_connexion" placeholder="Password1234" pattern="\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*" required>
        </fieldset>
        <input class="submit" type="submit" value="Se Connecter">
    </form>
</main>

<div id="console"></div>

<?php include './vue/footer.php'; ?>
