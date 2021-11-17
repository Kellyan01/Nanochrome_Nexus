<?php 

include 'header.php';

include 'nav.php';
?>

<h1>Messagerie</h1>
<div id="mailbox">
    <aside>
        <button>Ecrire un Message</button>
        <section>
            <h2>Message reçu</h2>
            <div id="reception"></div>
        </section>
        <section>
            <h2>Message envoyé</h2>
            <div id="envoie"></div>
        </section>
    </aside>
    <main>
        <form id="ecriture" action="" method="POST">
            <fieldset>
                <label>Pseudo du Destinataire</label>
                <input name="destinataire" required>
            </fieldset>
            <fieldset>
                <label>Sujet</label>
                <input name="sujet" required>
            </fieldset>
            <textarea  rows="10" cols="38" name="corps" required></textarea>
            <input class="submit" type="submit" value="Envoyer" name="envoyer">
            <div id="messageMail"></div>
        </form>

        <div id="lecture">
            <div id="auteur"></div>
            <div id="sujet"></div>
            <div id="corps"></div>
        </div>
    </main>
</div>
<div id="message"></div>

<?php include 'footer.php'; ?>