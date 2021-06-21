CREATE DATABASE IF NOT EXISTS phoenix;

USE phoenix;

DROP TABLE IF EXISTS voyage;
CREATE TABLE IF NOT EXISTS voyage
(
  id_voyage int(3) NOT NULL AUTO_INCREMENT,
  destination varchar(30) NOT NULL,
  presentation text NOT NULL,
  photo varchar(250) NOT NULL,
  prix int(4) NOT NULL,
  PRIMARY KEY (id_voyage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS reservation;
CREATE TABLE IF NOT EXISTS reservation
(
  id_reservation int(3) NOT NULL AUTO_INCREMENT,
  id_voyage int(3) DEFAULT NULL,
  email varchar(50) NOT NULL,
  semaines int(4) NOT NULL,
  participants int(4) NOT NULL,
  date_reservation datetime NOT NULL,
  PRIMARY KEY (id_reservation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO voyage
     (destination, presentation, photo, prix)
VALUES
     ('Les Boucaniers', 'Après les eaux
calmes, partez à la découverte des spectaculaires cascades des gorges de la Falaise, à Trinité.', 'img
/caraibes_martinique_boucaniers.jpg', 750),
     ('Kamarina', 'Bienvenue au pays
de l\'Etna où ruelles escarpées et places en fleurs s\'ouvrent sur la Méditerranée !', 'img
/sicile_kamarina.jpg', 510),
     ('Finohlu', 'Instants inoubliables sur
une île privative où luxe et charme naturel des Maldives s\'équilibrent à merveille.', 'img
/maldives_fino.jpg', 805),
     ('Albion sauvage', 'Au cœur de
l’Ile, un swing au golf, l\’adrénaline du trapèze volant ou la beauté des fonds marins ... que choisir ?', 'img
/maurice_albion.jpg', 630),
     ('Kani', 'Ile
-jardin posée sur des eaux turquoises, découvrez le paradis au cœur de l\'archipel des Maldives.', 'img
/maldives_kani.jpg', 798),
     ('Gregolimano', 'L\’île d\’Eubée est un oasis entre mer et oliviers ... plongez dans les eaux azures de la mer Egée ... en ski nautique', 'img
/grece_gregolimano.jpg', 535);
