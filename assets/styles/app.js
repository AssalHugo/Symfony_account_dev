// Fonction pour mettre à jour le rôle
function updateRole() {
    var userId = document.getElementById('changer_role_user').value;
    fetch('/admin/' + userId + '/role')
        .then(response => response.text())
        .then(role => {
            //On retire les "" du role
            role = role.replace(/"/g, '');
            document.getElementById('changer_role_role').value = role;
        });
}

// Écouteur d'événement pour le changement de l'utilisateur
document.getElementById('changer_role_user').addEventListener('change', updateRole);

// Mise à jour du rôle lors du chargement de la page
window.addEventListener('load', updateRole);