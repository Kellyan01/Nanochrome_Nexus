<?php 

include 'header.php';

include 'nav.php';
?>

<main>
    <h1>Supprimer compte ?</h1>
    <p>Souhaitez-vous vraiment supprimer votre compte ?</p>
    <form method="POST" action="">
        <fieldset>
            <label>Password</label><input name="passwordDelete" pattern="\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*" required>
        </fieldset>
        <input type="submit" value="Supprimer" name="delete">
        <a href="./nexus.php">Annuler</a>
    </form>
    <div id="message"></div>
</main>

<?php include 'footer.php'; ?>