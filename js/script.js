/*--------------------------------
            PAGE NEXUS
--------------------------------*/
document.addEventListener("DOMContentLoaded", function(event) {
    let btn_UpdateProfil = document.getElementById('updateProfil');
    let btn_CancelUpdate = document.getElementById('cancelUpdate');
    let modifForm = document.getElementById('modifForm');
    let btn_UpdatePassword = document.getElementById('updatePassword');
    //let btn_CancelUpdatePassword = document.getElementById('cancelUpdatePassword');
    let modifPassword = document.getElementById('modifPassword');

    btn_UpdateProfil.addEventListener("click", function(event) {
        modifForm.setAttribute('style', 'display: bloc;');
        btn_CancelUpdate.setAttribute('style', 'display: inline;');
    });

    btn_CancelUpdate.addEventListener("click", function(event) {
        modifForm.setAttribute('style', 'display: none;');
        modifPassword.setAttribute('style', 'display: none;');
        btn_CancelUpdate.setAttribute('style', 'display: none;');
    });
    btn_UpdatePassword.addEventListener("click", function(event) {
        modifPassword.setAttribute('style', 'display: bloc;');
        btn_CancelUpdate.setAttribute('style', 'display: inline;');
    });

    /*btn_CancelUpdatePassword.addEventListener("click", function(event) {
        modifPassword.setAttribute('style', 'display: none;');
    });*/
});