<?php

//Si on a pas de token, on demande à l'utilisateur de rentrer son username et son password
if (!file_exists('token.txt') || empty(file_get_contents('token.txt'))) {
    $token = getTokens();
}
else {
    //Si on a déjà un token, on le récupère
    $token = file_get_contents('token.txt');
}

//A présent on fait une nouvelle requête pour récupérer la liste des demandes en cours avec le token
$response = getResponse($token);

//Si la réponse contient un message d'erreur, on affiche le message d'erreur et on sort du script
while (str_contains($response, 'message')) {
    echo 'Erreur : ' . json_decode($response, true)['message'] . "\n";


    //On demande à l'utilisateur de rentrer son username et son password
    $token = getTokens();

    if ($token === null) {
        //On passe à la prochaine itération
        continue;
    }

    //On refait une requête avec le nouveau token
    $response = getResponse($token);
}

//On affiche la réponse en créant un tableau artificiel, avec un maximum de 8 caractères par cellule, si une cellule dépasse 8 caractères, elle sera tronquée
// On décode la réponse JSON en tableau PHP
$responseArray = json_decode($response, true);

// On affiche l'en-tête du tableau
echo 'Liste des demandes en cours : ' . "\n";
$keys = array_keys($responseArray[0]);
foreach ($keys as $key) {
    echo str_pad(substr($key, 0, 20), 20, ' ') . '|';
}
echo "\n";

//On affiche une ligne de séparation
foreach ($keys as $key) {
    echo str_pad('', 20, '-') . '|';
}
echo "\n";

// On parcourt chaque ligne du tableau
foreach ($responseArray as $row) {
    // On parcourt chaque cellule de la ligne
    foreach ($row as $key => $cell) {
        // On tronque la cellule à 15 caractères et on ajoute des espaces si nécessaire
        $row[$key] = str_pad(substr($cell, 0, 20), 20, ' ');
    }

    // On affiche la ligne tronquée
    echo implode('|', $row) . "\n";
}

/**
 * Fonction pour récupérer le token
 * @return mixed
 */
function getTokens()
{

    echo 'Bonjour, veuillez entrer votre username et votre password pour accéder à la liste des demandes en cours : ' . "\n";

    $url = 'http://localhost:8000/api/login_check';

    //On demande l'username
    echo 'Username : ';
    $username = trim(fgets(STDIN));

    //On demande le password
    echo 'Password : ';
    $password = trim(fgets(STDIN));

    //On crée un tableau associatif avec le username et le password
    $data = array('username' => $username, 'password' => $password);

    //On encode le tableau en JSON
    $data = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);

    if (isset(json_decode($response, true)['message'])) {
        return null;
    }
    $token = json_decode($response, true)['token'];

    //On stocke le token dans un fichier token.txt
    file_put_contents('token.txt', $token);

    return $token;
}

function getResponse($token)
{
    $url = 'http://localhost:8000/api/demandes';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    return $response;
}