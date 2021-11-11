<?php 

include 'header.php';

include 'nav.php';
?>

<main id="nexus">
    <h1>Nexus</h1>
    <button class="submit" id="updateProfil">Modifier Profil</button>
    <button class="submit" id="updatePassword">Modifier Mot de Passe</button>
    <button class="submit"><a id="deleteProfil" href="delete.php">Supprimer Profil</a></button>
    <section id="profil">
        <img src="./img/default_avatar.png" alt="avatar">
        <ul>
            <li id="nom"></li>
            <li id="prenom"></li>
            <li id="pseudo"></li>
            <li id="mail"></li>
        </ul>
    </section>
    <div>
        <form id="modifForm" action="" method="POST" style="display: none;">
            <fieldset><label>Nom</label><input id="modif_nom" name="modif_nom" required></fieldset>
            <fieldset><label>Prénom</label><input id="modif_prenom" name="modif_prenom" required></fieldset>
            <fieldset><label>Pseudo</label><input id="modif_pseudo" name="modif_pseudo" required></fieldset>
            <fieldset><label>Mail</label><input id="modif_mail" name="modif_mail" pattern="[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})" required></fieldset>
            <input class="submit" type="submit" value="Modifier">
        </form>
        <form id="modifPassword" action="" method="POST" style="display: none;">
            <fieldset><label>Ancien Mot de Passe</label><input id="old_password" name="old_password" pattern="\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*" required></fieldset>
            <fieldset><label>Nouveau Mot de Passe</label><input id="new_password" name="new_password" pattern="\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*" required></fieldset>
            <fieldset><label>Répétez Mot de Passe</label><input id="repeat_password" name="repeat_password" pattern="\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*" required></fieldset>
            <input class="submit" type="submit" value="Modifier">
        </form>
        <button class="submit" id="cancelUpdate" style="display:none;">Annuler</button>
        <div id="message"></div>
    </div>
</main>

<?php include 'footer.php'; ?>