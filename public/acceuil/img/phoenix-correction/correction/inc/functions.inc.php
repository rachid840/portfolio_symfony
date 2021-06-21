<?php

/**
 * ***************************** FONCTIONS DE DEBUG ******************************
 */

// fonction d'affichage d'un print_r() [2ème paramètre = 1] et d'un var_dump() [2ème paramètre = 2] avec balise <pre>
// function debug($param, $exit = 2)
// {
//      if ($exit === 1) {
//           echo '<pre style="background-color: #d5ecd4 ; padding: 10vh 5vh;">';
//           echo '<strong>print_r($param)</strong> <br>';
//           print_r($param);
//           echo '</pre>';
//      } elseif ($exit === 2) {
//           echo '<pre style="background-color: #ebd4cb; padding: 10vh 5vh;">';
//           echo '<strong>var_dump($param)</strong> <br>';
//           var_dump($param);
//           echo '</pre>';
//      }
// }


function debug($var, $mode = 1) // si je ne fournit que le 1er argument par défaut à l'exécution il prendra la valeur de la 2nd variable déclarée en argument
{
	// COULEUR DU FOND ALEATOIRE
	// $tab_couleur = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f');
	$tab_couleur = array('DA9F93', 'F2D5F8', 'DDBDD5', 'B8E1FF', 'F7C59F', '7EB09B', 'C8FFBE', 'CBAC88', 'EDFF7A', '5DD9C1', '8FD694', 'C0DA74', 'BEEDAA', 'FFD275', 'C6C8EE');
	// echo '<pre>'; var_dump($tab_couleur); echo '</pre>';
	$code_couleur = $tab_couleur[rand(0, 15)];

	// for ($i = 0; $i < 6; $i++) {
	// 	$code_couleur .= $tab_couleur[rand(0, 15)]; // ou $code_couleur = $code_couleur . $tab_couleur[rand(0, 15)];
	// }
	// rand() est une fonction prédéfinie permettant de récupérer une valeur aléatoire contenue entre deux entiers
	
	// RECUPERATION DES INFORMATIONS SUR LE FICHIER DANS LEQUEL ON APPELLE LA FONCTION
	$trace = debug_backtrace(); // la fonction debug_backtrace retourne un tableau ARRAY contenant des informations telles que la ligne et le fichier où est exécutée cette fonction
	// echo '<hr />'; var_dump($trace); echo '<hr />';
	$trace = array_shift($trace); // retire le 1er élément du tableau et réordonne tous les éléments pour qu'il n'y ait pas de vide
	
	// AFFICHAGE
	echo '<div style="background-color: #'. $code_couleur .' ; padding: 10vh;">';
	if ($mode === 1) {
		echo '<p>VAR_DUMP() demandé dans le fichier : ' . $trace['file'] . ' à la ligne ' . $trace['line'] . '</p>';
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	} else {
		echo '<p>PRINT_R() demandé dans le fichier : ' . $trace['file'] . ' à la ligne: ' . $trace['line'] . '</p>';
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}

	echo '</div>';
}
// si on passe un seul argument  debug($arg); => var_dump
// si on passe deux arguments et que le 2nd argument n'est pas 1  debug($arg, $arg2); => print_r


/**
 * ***************************** FONCTION DE REQUETE ******************************
 */
// EXEMPLE DE REQUÊTE SQL $membre = $executeRequete("SELECT * FROM membre WHERE pseudo = :pseudo", array(':pseudo' => $_POST['pseudo']));

function executeRequete($requete, $param = array())
{
    if (!empty($param)) { // si j'ai bien reçu un array rempli (non vide), je peux faire la foreach dessus pour transformer les caractères spéciaux en entités HTML
        // en PHP si le tableau est vide la foreach génère une erreur car elle va tenter de parcourir le tableau même vide
        foreach ($param as $indice => $valeur) {
            $param[$indice] = htmlspecialchars($valeur, ENT_QUOTES); // pour éviter les injections CSS et JS
        }
    }

    global $pdo; // permet d'avoir accès (à l'intérieur de la fonction) à la variable $pdo définie dans l'espace global (à l'extérieur de la fonction)

    $resultat = $pdo->prepare($requete); // on prépare la requête fournie lors de l'appel de la fonction
     $resultat->execute($param); // on exécute en liant les marqueurs aux valeurs qui se trouvent dans l'array $param fourni lors de l'appel de la fonction, et comme execute() fonctionne même si on ne lui passe pas d'argument si mon array $param est vide il n'y aura pas d'erreur

     return $resultat; // on retourne l'objet PDOStatement à l'endroit ou la fonction executeRequete() est appelée
}
