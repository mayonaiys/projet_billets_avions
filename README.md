# Canada Airline
Canada Airline est un site web de réservation de billets d'avions développé dans le cadre du projet de fin d'année des étudiants de CIR2 de l'ISEN Nantes 
et en collaboration avec les CIR2 de l'ISEN Rennes de l'année scolaire 2019-2020 (écoles appartenant au groupe ISEN Yncrea Ouest).
Les compétences évaluées du projet sont les suivantes : déploiement et gestion d'un serveur Web sur le cloud, création et manipulation d'une base 
de données, sécurité et protection des données, définition d'une interface client web.

### Languages de programmation et architecture
Il a été développé en HTML 5/CSS 3/JavaScript 1.8.5 et avec l'aide de Bootstrap v4.5.0 pour le côté client et en php pour le côté serveur. Les interactions entre client et serveur se font grâce à des requêtes ajax
et le serveur interagit avec une base de données mysql. L'architecture MVC (modèle vue contrôleur) a été utilisé pour la création de ce site, les vues clients interagissent donc avec un controller (php) qui interagit avec
la base de donnée.

### Description
Canada Airline est un site de réserveration de billets d'avions où la recherche de billets peut se faire en fonction du nom des villes
ou du code des aéroports. Il est aussi possible de choisir le nombre de passagers de plus et de moins de 4 ans (si un passager a moins de 4 ans
le prix de son billet (taxes comprises) est divisé par deux). Les vols disponibles sont affichés dans une liste ou il est possible de choisir le
vol qui nous intéresse. Lorsqu'un vol est séléctionné, il faut ensuite remplir les informations de chaque passagers. Lorsque la réservation est confirmée
le prix des billets est affiché pour chaque passager et le prix total est affiché en bas de page. Il est ensuite possible pour l'utilisateur de télécharger son billet au format pdf.

### Ajout en plus des exigeances
-Connexion avec un compte sur le site et possibilité d'inscription
-Panel utilisateur avec une liste des billets réservés
-Bannière avec des prix en réduction 
-Recherche avec un filtre supplémentaire de tranche de prix
-Enregistrement des réservations et des informations des passagers dans la base de donnée
-Possibilité d'appuyer sur un bouton pour télécharger et imprimer le billet en pdf
-Visualisation du trajet sur une map Google sur la page de confirmation

### Rejoindre le site
Le site web est accessible grâce au lien suivant : [Canada Airline](https://34.203.33.89/)
(Cependant, un nom d'utilisateur et un mot de passe sont demandés, il n'est donc pas possible pour tout le monde d'y accéder)


Auteurs : Antoine Massé, Jérémie Afsal Fazal, Charlotte Mougenot, Rémi Adde
ISEN Yncrea Ouest Nantes-Rennes CIR2 2019-2020

### License
[MIT](https://choosealicense.com/licenses/mit/)
