/*--------------------------------
            PAGE NEXUS
--------------------------------*/
document.addEventListener("DOMContentLoaded", function(event) {
    if (document.getElementById('nexus')) {
        let btn_UpdateProfil = document.getElementById('updateProfil');
        let btn_CancelUpdate = document.getElementById('cancelUpdateProfil');
        let modifForm = document.getElementById('modifForm');
        let btn_UpdatePassword = document.getElementById('updatePassword');
        let btn_CancelUpdatePassword = document.getElementById('cancelUpdatePassword');
        let modifPassword = document.getElementById('modifPassword');

        btn_UpdateProfil.addEventListener("click", function(event) {
            modifForm.setAttribute('style', 'height: auto; overflow: hidden; padding: 1rem;');
        });

        btn_CancelUpdate.addEventListener("click", function(event) {
            modifForm.setAttribute('style', 'height: 0; overflow: hidden; padding: 0;');
        });

        btn_UpdatePassword.addEventListener("click", function(event) {
            modifPassword.setAttribute('style', 'height: auto; overflow: hidden; padding: 1rem;');
        });

        btn_CancelUpdatePassword.addEventListener("click", function(event) {
            modifPassword.setAttribute('style', 'height: 0; overflow: hidden; padding: 0;');
        });
    }
});