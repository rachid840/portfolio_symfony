<?php
require_once 'inc/init.inc.php';

// RESTE ELEMENTS POUR LE CALCUL DU PRIX TOTAL => requête avec INNER JOIN + calcul + affectation à $_SESSION + extract + affichage

/**
 * 1- Récupération des informations en BDD pour affichage des voyages
 */
$req_all_voyage = executeRequete("SELECT * FROM voyage");
// debug($res); // objet PDOStatement
// puis dans mon html je vais injecter la suite du traitement oour générer l'affichage : 
// (voir plus bas dans la <section> du if (isset($_GET['action']) && $_GET['action'] == 'choisir'))
// while($voyage = $reqAllVoyage->fetch(PDO::FETCH_ASSOC)) {
//      // debug($voyage); // objet PDOStatement
// }

/**
 * 2- Récupération de l'id du voyage dans l'url et requête pour affichage
 */
if ($_GET) {
    $id_voyage = $_GET['idv']; // variable de récupération de l'id dans l'url de la page
    // debug($id_voyage);          
    // echo __LINE__ . '<br>';


    // requête de récupération des informations en BDD du voyage correspondant à $id_voyage
    $req_fiche_voyage = executeRequete("SELECT * FROM voyage WHERE id_voyage = :id_voyage", array(
     ':id_voyage'   => $id_voyage
));
    // debug($req_fiche_voyage); // objet PDOStatement
    $fiche_voyage = $req_fiche_voyage->fetch(PDO::FETCH_ASSOC);
    // debug($fiche_voyage); // array
}

/**
 * 3- traitement du formulaire de réservation
 */
// vérification du formulaire de réservation
if ($_POST) {
      if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $msg .= '<div class="alert alert-danger">L\'email est incorrect.</div>'; // filter_var() avec le paramètre FILTER_VALIDATE_EMAIL permet de vérifier que la variable est bien de type email. Pour info, vous pouvez valider d'autres types : des adresses IP, des formats d'url... (voir la doc php.net)

      if (!isset($_POST['semaines']) || !is_numeric($_POST['semaines']) || strlen($_POST['semaines']) < 1 || strlen($_POST['semaines']) > 2) $msg .= '<div class="alert alert-danger">Le nombre de semaines est incorrect.</div>';

      if (!isset($_POST['participants']) || !is_numeric($_POST['participants']) || strlen($_POST['participants']) < 1 || strlen($_POST['participants']) > 2) $msg .= '<div class="alert alert-danger">Le nombre de participants est incorrect.</div>';

      // si les champs sont correctement remplis => insertion en BDD de la réservation
      if (empty($msg)) {
      
          $req_reservation = executeRequete("INSERT INTO reservation (id_voyage, email, semaines, participants, date_reservation) VALUES (:id_voyage, :email, :semaines, :participants, NOW())", array(
            ':id_voyage'        => $_POST['id_voyage'],
            ':email'            => $_POST['email'],
            ':semaines'         => $_POST['semaines'],
            ':participants'     => $_POST['participants'] // le NOW() à ne pas oublier est géré par le SGBD
      ));
      }
      // global $pdo;
      $last_reservation = $pdo->lastInsertId();
//      debug($last_reservation);
      // echo __LINE__ . '<br>';


      /**
       * 4- affichage des infos de réservation sur la base de lastInsertId()
       */
      // echo 'Dernier ID généré par la BDD : ' . $pdo->lastInsertId() . '<br>';

 /** on peut faire de plusieurs façons :
 * SOIT (1) :
 *   - une requête sur la table *reservation* avec lastInsertId()
 *   - la récupération dans $_POST de l'ID du voyage via un input *hidden*
  *  - on stocke le tout dans la super globale $_SESSION
  *  - on exploite $_SESSION pour le calcul du prix et le reste de l'affichage final
 * SOIT (2) :
 *  - une requête SQL imbriquée qui utilise le résultat d'une seconde requête
 */

//      $req_confirmation = executeRequete("SELECT * FROM reservation WHERE id_reservation = :id_reservation", array(
//            ':id_reservation'  => $last_reservation
//      ));
      $req_confirmation = executeRequete("SELECT r.*, v.* FROM reservation r INNER JOIN voyage v ON r.id_voyage = v.id_voyage WHERE r.id_reservation = :id_reservation", array(
           ':id_reservation'  => $last_reservation));
       //debug($req_confirmation);

      $confirmation = $req_confirmation->fetch(PDO::FETCH_ASSOC);
//      debug($confirmation);


    /**
     * Méthode 1
     */
//      $_SESSION['vacances'] = $confirmation;
//      $_SESSION['vacances']['prix_semaine'] = $_POST['prix_semaine'];
//
//    $facture_id = $_SESSION['vacances']['id_reservation'];
//    $facture_destination = $_SESSION['vacances']['destination'];
//    $facture_participants = $_SESSION['vacances']['participants'];
//    $facture_semaines = $_SESSION['vacances']['semaines'];
//    $facture_total = intval($facture_participants) * intval($facture_semaines) * intval($_POST['prix_semaine']);

    /**
     * Méthode 2
     */
    $facture_id = $confirmation['id_reservation'];
    $facture_destination = $confirmation['destination'];
    $facture_participants = $confirmation['participants'];
    $facture_semaines = $confirmation['semaines'];
    $facture_total = intval($facture_participants) * intval($facture_semaines) * intval($_POST['prix_semaine']);
    $date_reservation = new DateTime($confirmation['date_reservation']);
    $facture_date = $date_reservation->format('d/m/Y');

} // fin if ($_POST) 


?>

<!DOCTYPE html>
<html lang="fr">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>Phoenix Holidays</title>
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
     <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
     <link rel="stylesheet" href="#">

     <style>
          html{position: relative; min-height: 100%;}
          body{margin-bottom: 6vh; /* Margin bottom by footer height */}
          .col-2, .col-4, .col-8 {margin: 2vh 0;}
          .choisir {margin-top: 5vh; margin-bottom: 5vh;}
          .with-nav { margin-top: 10vh;}
          .footer{position: absolute; /*bottom: 0; */ width: 100%; height: 60px; /* Set the fixed height of the footer here */ line-height: 60px; /* Vertically center the text there */ background-color: #75c9c8; font-size: 1.373em;}
               /*END Sticky footer specific style*/
               /*margin bottom for the header*/
          /*header{margin-bottom: 10vh; }*/
          .container-fluid { padding-left: 0; padding-right: 0;}
          /*END argin bottom for the nav*/
          .carousel100 {max-height: 80vh;}
          .carousel10 {max-height: 30vh;}
          /* .row {margin-top: 2vh; margin-bottom: 2vh;} */
          .card-img-top {max-height: 13.5vh;}
          form > div.row {margin-top: 0vh; margin-bottom: 2vh;}
          #confirmer > .row{margin-top: 0vh; margin-bottom: 1.65vh;}
          .img-thumbnail {height: 10vh; }
          .row.card-deck {margin-bottom: 3.4vh;}
     </style>
</head>
<body>
     
     <!-- Navbar -->
<?php 
if (empty($_GET)) {
     ?>
     <nav class="navbar navbar-expand-lg fixed-top navbar-light" style="height: 10vh;">
<?php 
} else {
     ?>
     <nav class="navbar navbar-expand-lg fixed-top navbar-light" style="background-color: #75c9c8;">
<?php 
}
?>
          <div class="container">
               <a class="navbar-brand" href="workshop_2.php"> <i class="fab fa-phoenix-framework fa-2x"></i> </a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                         <li class="nav-item active">
                              <a class="nav-link" href="workshop_2.php">Phoenix <span class="sr-only">(current)</span></a>
                         </li>
                         <li class="nav-item">
                              <a class="nav-link" href="?action=choisir">Choisir une destination</a>
                         </li>
                         <li class="nav-item">
                              <a class="nav-link disabled" href="#">Payer</a>
                         </li>
                    </ul>
               </div>
          </div>
          <!-- End .container -->
     </nav>
     <!-- End Navbar -->

<?php 
if (empty($_GET)) {
     ?>
     <!-- carousel avec controls -->
     <div class="container-fluid">     
          <div id="#" class="carousel slide" data-ride="carousel">
               <div class="carousel-inner">
                    <div class="carousel-item active">
                         <img class="d-block w-100 img-fluid carousel100" src="img/caraibes1.jpg" alt="First slide">
                    </div>
                    <div class="carousel-item">
                         <img class="d-block w-100 img-fluid carousel100" src="img/maldives.jpg" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                         <img class="d-block w-100 img-fluid carousel100" src="img/maurice.jpg" alt="Third slide">
                    </div>
                    <div class="carousel-item">
                         <img class="d-block w-100 img-fluid carousel100" src="img/turkoise.jpg" alt="Third slide">
                    </div>
               </div>
               <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Précédent</span>
               </a>
               <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Suivant</span>
               </a>
          </div> <!-- fin carousel -->
          <div class="container choisir">
               <a href="?action=choisir" class="btn btn-outline-info btn-block">Choisir mon séjour tout de suite !</a>
          </div>  
     </div> <!-- fin .container-fluid -->
<?php 
} else {
     ?>
     <!-- carousel avec controls -->
     <div class="container with-nav">     
          <div id="#" class="carousel slide" data-ride="carousel">
               <div class="carousel-inner">
                    <div class="carousel-item active">
                         <img class="d-block w-100 img-fluid carousel10" src="img/caraibes1.jpg" alt="First slide">
                    </div>
                    <div class="carousel-item">
                         <img class="d-block w-100 img-fluid carousel10" src="img/maldives.jpg" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                         <img class="d-block w-100 img-fluid carousel10" src="img/maurice.jpg" alt="Third slide">
                    </div>
                    <div class="carousel-item">
                         <img class="d-block w-100 img-fluid carousel10" src="img/turkoise.jpg" alt="Third slide">
                    </div>
               </div>
               <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Précédent</span>
               </a>
               <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Suivant</span>
               </a>
          </div> <!-- fin carousel -->
<?php 
}
if (!empty($msg)) {
    echo '<p>' . $msg . '</p>';
}

if (!$_POST && isset($_GET['action']) && $_GET['action'] == 'choisir') {
     ?> 
          <section>
               <div class="row">
                    <?php
                    while($voyage = $req_all_voyage->fetch(PDO::FETCH_ASSOC)) {
                         // debug($voyage); // objet PDOStatement

                    echo '<div class="col-4">';
                         echo '<div class="card border-info">';
                              echo '<img class="card-img-top" src="' . $voyage['photo'] . '" alt="Card image cap">';
                              echo '<div class="card-body">';
                                   echo '<h4 class="card-title">' . $voyage['destination'] . '</h4>';
                                   echo '<p class="card-text">' . $voyage['presentation'] . '</p>';
                                   echo '<a href="?action=reserver&idv=' . $voyage['id_voyage'] . '" class="btn btn-outline-info btn-block">Réserver maintenant !</a>';
                              echo '</div>';
                         echo '</div>';
                    echo '</div>';
                    }
                    ?>
               </div>
          </section>
<?php 
// } elseif (!$_POST || $_POST['email'] == '' || $_POST['semaines'] == '' || $_POST['participants'] == ''  && (isset($_GET['action']) && $_GET['action'] == 'reserver')) {
} elseif ((!$_POST || !empty($msg)) && (isset($_GET['action']) && $_GET['action'] == 'reserver')) {
     ?>
          <section>
               <div class="row">
                    <div class="col-4">
                         <div class="card border-info">
                              <img class="card-img-top" src="<?php echo $fiche_voyage['photo']; ?>" alt="Card image cap">
                              <div class="card-body">
                                   <h4 class="card-title"><?php echo $fiche_voyage['destination']; ?></h4>
                                   <!-- <p class="card-text">Après les eaux calmes, partez à la découverte des spectaculaires cascades des gorges de la Falaise, à Trinité.</p> -->
                              </div>
                              <div class="card-footer alert-info">
                                   1 semaine / personne : <?php echo $fiche_voyage['prix']; ?> €
                              </div>
                         </div>
                    </div> <!-- end .col-4 -->

                    <div class="col-8">
                         <div class="card border-info">
                              <div class="card-header alert-info">
                                   <h4>Je complète mes informations de réservation &nbsp; <i class="fab fa-phoenix-framework"></i></h4>
                                    <!-- <div class="blockquote-footer"><i class="fab fa-phoenix-framework fa-2x"></i></div> -->
                              </div>
                              <div class="card-body">
                                   <form method="post" action="">
                                        <input type="hidden" id="id_voyage" name="id_voyage" value="<?php echo $id_voyage; ?>">
                                       <input type="hidden" id="prix_semaine" name="prix_semaine" value="<?php echo $fiche_voyage['prix']; ?>">
                                        <div class="row">
                                             <div class="col">
                                                  <input type="text" class="form-control" placeholder="Email de confirmation" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                                             </div>
                                        </div>
                                        <div class="row">
                                             <div class="col">
                                                  <input type="text" class="form-control" placeholder="Je pars combien de semaines ?" name="semaines" value="<?php echo $_POST['semaines'] ?? ''; ?>">
                                             </div>
                                             <div class="col">
                                                  <input type="text" class="form-control" placeholder="Nombre de vacanciers" name="participants" value="<?php echo $_POST['participants'] ?? ''; ?>">
                                             </div>
                                        </div>
                                        <input type="submit" value="Confirmer ma réservation" name="confirmer" class="btn btn-block btn-info">
                                   </form>
                              </div> <!-- end .card-body -->
                         </div> <!-- end .card -->
                    </div> <!-- end .col-8 -->

               </div> <!-- end .row -->
          </section>

          <section>
               <div class="row card-deck">
               <?php 
                  while($img_thumbnail = $req_all_voyage->fetch(PDO::FETCH_ASSOC)){
                        echo '<div class="card">';
                              echo '<img class="img-thumbnail" src="'. $img_thumbnail['photo'] . '" alt="' . $img_thumbnail['destination'] . '">';
                        echo '</div>';
                  }
               ?>
               </div> <!-- end .card-deck --> 
          </section>
<?php 
} 
// if ($_POST && !empty($_POST['email'])) { 
if ($_POST && empty($msg)) { 
     // debug($_POST);
// si mon internaute a réservé $_POST existe et est rempli => donc $msg est vide (cf. vérification des saisies du formulaire) je l'insère en BDD et dans ma $_SESSION - requêtes & traitements en haut du script

      // echo $last_reservation;
?>

            <section id="confirmer">
                  <div class="row">
                        <div class="col-12">
                              <div class="alert alert-info"><i class="fab fa-phoenix-framework"></i> &nbsp;  Récapitulatif de votre réservation pour <?php
                                  echo $facture_destination ?? ''; ?>
                              </div>
                        </div>
                  </div>

                  <div class="row">
                        <div class="col-2">
                              <div class="alert alert-dark" role="alert">Participant(s)</div>
                        </div>
                        <div class="col-4">
                              <div class="alert alert-dark" role="alert"><?php echo $facture_participants ?? ''; ?></div>
                        </div>
                        <div class="col-2">
                              <div class="alert alert-dark" role="alert">Commande</div>
                        </div>
                        <div class="col-4">
                              <div class="alert alert-dark" role="alert"><?php echo $facture_id ?? ''; ?></div>
                        </div>
                  </div> 

                  <div class="row">
                        <div class="col-2">
                              <div class="alert alert-dark" role="alert">Semaine(s) </div>
                        </div>
                        <div class="col-4">
                              <div class="alert alert-dark" role="alert"><?php echo $facture_semaines ?? ''; ?></div>
                        </div>
                        <div class="col-2">
                              <div class="alert alert-dark" role="alert">Total</div>
                        </div>
                        <div class="col-4">
                              <div class="alert alert-dark" role="alert"><?php echo number_format($facture_total, 2, ',', ' ') ?? ''; ?> €</div>
                        </div>
                  </div>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-dark text-center">Votre commande a été enregistrée le <?php echo $facture_date ?? ''; ?></div>
                    </div>
                </div>

                  <div class="row">
                        <div class="col-12">                      
                              <div class="alert alert-info" style="text-align: right;">Bon séjour &nbsp; <i class="fab fa-phoenix-framework"></i>
                        </div>
                  </div>  
            </section>
            <section>
               <div class="row card-deck">
                    <div class="card">
                         <img class="img-thumbnail" src="img/caraibes_martinique_boucaniers.jpg" alt="Card image cap">
                    </div>   
                    <div class="card">
                         <img class="img-thumbnail" src="img/sicile_kamarina.jpg" alt="Card image cap">
                    </div>   
                    <div class="card">
                         <img class="img-thumbnail" src="img/maldives_fino.jpg" alt="Card image cap">
                    </div>   
                    <div class="card">
                         <img class="img-thumbnail" src="img/maurice_albion.jpg" alt="Card image cap">
                    </div>   
                    <div class="card">
                         <img class="img-thumbnail" src="img/maldives_kani.jpg" alt="Card image cap">
                    </div>   
                    <div class="card">
                         <img class="img-thumbnail" src="img/grece_gregolimano.jpg" alt="Card image cap">
                    </div>   
               </div> <!-- end .card-deck --> 
          </section>
<?php
}
 ?>
     </div> <!-- fin .container -->
     <footer class="footer">
          <div class="container">
               <span class="text-muted"><i class="fas fa-umbrella-beach"></i>&nbsp Vos vacances de rêve ... &nbsp<i class="fas fa-sun"></i>&nbsp Plage ... &nbsp<i class="fas fa-city"></i>&nbsp Urbaine ... &nbsp<i class="fab fa-docker"></i>&nbsp Croisière ... &nbsp<i class="fas fa-image"></i>&nbsp Montagne ... &nbsp<i class="fas fa-euro-sign"></i>&nbsp A prix tout doux ... &nbsp<i class="fas fa-umbrella-beach"></i></span>
          </div>
     </footer>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"> </script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"> </script>
</body>
</html>