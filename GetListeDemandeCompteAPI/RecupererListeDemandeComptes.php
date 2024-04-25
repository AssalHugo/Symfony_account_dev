<?php

//Si on a pas de token, on demande à l'utilisateur de rentrer son username et son password
if (!file_exists('token.txt') || empty(file_get_contents('token.txt'))) {
    $token = getTokens();
}
else {
    //Si on a déjà un token, on le récupère
    $token = file_get_contents('token.txt');
}

//A présent on fait une requête pour récupérer la liste des demandes en cours avec le token
if ($token !== null) {
    $response = getResponse($token, getBonUrl($token));
}
else {
    $response = null;
}

//Si la réponse contient un message d'erreur, on affiche le message d'erreur et on sort du script
while ($response === null || str_contains($response, 'message')) {
    if ($response === null) {
        echo 'Erreur : Impossible de récupérer les demandes en cours' . "\n";
    }
    else{
        echo 'Erreur : ' . json_decode($response, true)['message'] . "\n";
    }

    //On demande à l'utilisateur de rentrer son username et son password
    $token = getTokens();

    if ($token === null) {
        //On passe à la prochaine itération
        continue;
    }

    //On refait une requête avec le nouveau token
    $response = getResponse($token, getBonUrl($token));
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

//On crée un tableau avec les différents éléments de la réponse
$reponseAAPi = [];



// On parcourt chaque ligne du tableau
foreach ($responseArray as $row) {
    //Si la derniere colonne de la ligne contient la valeur 'C' on renvoie une 'C_V' avec l'id de la requete à l'API pour dire que le compte a été validé
    if ($row["etat_Système"] === 'C') {
        //Une fois sur deux on donne C_V, pour simuler que le compte a été validé et l'autre fois on donne C_E pour simuler que le compte a été refusé
        $rand = rand(0, 1);
        if ($rand === 0) {
            $reponseAAPi[$row['id']] = 'C_V';
        }
        else {
            $reponseAAPi[$row['id']] = 'C_E';
        }
    }
    else if ($row["etat_Système"] === 'U') {
        //Une fois sur deux on donne U_V, pour simuler que le compte a été validé et l'autre fois on donne U_E pour simuler que le compte a été refusé
        $rand = rand(0, 1);
        if ($rand === 0) {
            $reponseAAPi[$row['id']] = 'U_V';
        }
        else {
            $reponseAAPi[$row['id']] = 'U_E';
        }
    }

    // On parcourt chaque cellule de la ligne
    foreach ($row as $key => $cell) {
        // On tronque la cellule à 15 caractères et on ajoute des espaces si nécessaire
        $row[$key] = str_pad(substr($cell, 0, 20), 20, ' ');
    }

    // On affiche la ligne tronquée
    echo implode('|', $row) . "\n";
}

//On envoie la réponse à l'API
$ch = curl_init(getBonUrl($token));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token, 'Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($reponseAAPi));

$response = curl_exec($ch);

if (isset(json_decode($response, true)['message'])){
    echo json_decode($response, true)['message'] . "\n";
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

    //Si l'username ou le password est vide, on sort de la fonction
    if (empty($username) || empty($password)) {
        return null;
    }

    //On crée un tableau associatif avec le username et le password
    $data = array('username' => $username, 'password' => $password);

    //On encode le tableau en JSON
    $data = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);

    if (isset(json_decode($response, true)['message']) || empty($response)) {
        return null;
    }
    $token = json_decode($response, true)['token'];

    //On stocke le token dans un fichier token.txt
    file_put_contents('token.txt', $token);

    return $token;
}

function getResponse($token, $url = 'http://localhost:8000/api/demandes')
{

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    return curl_exec($ch);
}

function getBonUrl($token){
    //On décode le token en base 64, pour regarder quel est le rôle de l'utilisateur, si le role est ROLE_API_MDP, on fait une autre requête pour récupérer les demandes avec les mots de passe

    $tokenDecoded = base64_decode(explode('.', $token)[1]);
    $tokenDecoded = json_decode($tokenDecoded, true);
    if (in_array('ROLE_API_MDP', $tokenDecoded['roles'])) {
        return 'http://localhost:8000/api/demandesMdp';
    }
    else {
        return 'http://localhost:8000/api/demandes';
    }
}