<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclure le fichier connectParam.php pour les informations de connexion à la base de données
    include('../libs/connect_params.php');

    try {
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les logements du propriétaire
        $sql = "SELECT * FROM alhaiz_breizh._logement WHERE logement_statut_ligne = true";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $logements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
        // Connexion à la base de données avec PDO
        $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        // Récupérer les valeurs du formulaire
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id']; 
        }


        function trier($logement1, $logement2) {
            if (isset($_POST['tri']) && $_POST['tri'] == 'croissant') {
                $prix1 = $logement1['logement_prix_nuit_base'];
                $prix2 = $logement2['logement_prix_nuit_base'];
                return ($prix1 < $prix2) ? -1 : 1;
            } elseif (isset($_POST['tri']) && $_POST['tri'] == 'decroissant') {
                $prix1 = $logement1['logement_prix_nuit_base'];
                $prix2 = $logement2['logement_prix_nuit_base'];
                return ($prix1 > $prix2) ? -1 : 1;
            } else {
                // Aucun tri sélectionné, pas de changement dans l'ordre
                return 0;
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tri'])) {
            usort($logements, 'trier');
        }
        
        function filtrerInput($logement) {
            $prixMin = isset($_POST['prix_min']);
            $prixMax = isset($_POST['prix_max']);
            $surfaceMin = isset($_POST['surface_min']);
            $surfaceMax = isset($_POST['surface_max']);
            $litsMin = isset($_POST['lits_min']);
                        
            if(isset($_POST['prix_min'])){
                if(($_POST['prix_min']) == ($_POST['prix_max'])){
                    $logementHasPrixMin = true;
                    $logementHasPrixMax = true;
                }else{
                    $logementHasPrixMin = $logement['logement_prix_nuit_base'] >= $_POST['prix_min'];
                    $logementHasPrixMax = $logement['logement_prix_nuit_base'] <= $_POST['prix_max'];
                }
            }

            if(isset($_POST['surface_min'])){
                if(($_POST['surface_min']) == ($_POST['surface_max'])){
                    $logementHasSurfaceMin = true;
                    $logementHasSurfaceMax = true;
                }else{
                    $logementHasSurfaceMin = $logement['logement_surface'] >= $_POST['surface_min'];
                    $logementHasSurfaceMax = $logement['logement_surface'] <= $_POST['surface_max'];
                }
            }

            if(isset($_POST['lits_min'])){
                if(($_POST['lits_min']) == 0){
                    $logementHasLitsMin = true;
                }else{
                    $logementHasLitsMin = $logement['logement_nb_lit'] >= $_POST['lits_min'];
                }
            }
                        
            // Condition pour filtrer les logements
            $prixMinCondition = !$prixMin || $logementHasPrixMin;
            $prixMaxCondition = !$prixMax || $logementHasPrixMax;
            $surfaceMinCondition = !$surfaceMin || $logementHasSurfaceMin;
            $surfaceMaxCondition = !$surfaceMax || $logementHasSurfaceMax;
            $litsMinCondition = !$litsMin || $logementHasLitsMin;
        
            // Retourne true si les deux conditions sont remplies
            return $prixMinCondition && $prixMaxCondition && $surfaceMinCondition && $surfaceMaxCondition && $litsMinCondition;
        }
        

        function filtrerType($logement) {
            $maison = isset($_POST['choix_maison']);
            $appartement = isset($_POST['choix_appartement']);
            $atypique = isset($_POST['choix_atypique']);

            $type = $logement['logement_nature'];
        
            $logementHasMaison = $type == 'Maison';
            $logementHasAppartement = $type == 'Appartement';
            $logementHasAtypique = $type !== 'Maison' && $type !== 'Appartement';
        
            // Condition pour filtrer les logements
            $maisonCondition = !$maison || $logementHasMaison;
            $appartementCondition = !$appartement || $logementHasAppartement;
            $atypiqueCondition = !$atypique || $logementHasAtypique;
        
            // Retourne true si les deux conditions sont remplies
            return $maisonCondition && $appartementCondition && $atypiqueCondition;
        }

        function filtrerDepartements($logement) {
            $cotedarmor = isset($_POST['choix_cotedarmor']);
            $finistere = isset($_POST['choix_finistere']);
            $illeetvilaine = isset($_POST['choix_illeetvilaine']);
            $morbihan = isset($_POST['choix_morbihan']);
        
            $deuxPremiersChiffres = substr($logement["logement_code_postal"], 0, 2);

            $logementHasCoteDArmor = $deuxPremiersChiffres == '22';
            $logementHasFinistere = $deuxPremiersChiffres == '29';
            $logementHasIlleEtVilaine = $deuxPremiersChiffres == '35';
            $logementHasMorbihan = $deuxPremiersChiffres == '56';
        
            // Condition pour filtrer les logements
            $coteDArmorCondition = !$cotedarmor || $logementHasCoteDArmor;
            $finistereCondition = !$finistere || $logementHasFinistere;
            $illeEtVilaineCondition = !$illeetvilaine || $logementHasIlleEtVilaine;
            $morbihanCondition = !$morbihan || $logementHasMorbihan;
        
            // Retourne true si les deux conditions sont remplies
            return $coteDArmorCondition && $finistereCondition && $illeEtVilaineCondition && $morbihanCondition;
        }
    

        function filtrerEquipements($logement) {
            $tvFilter = isset($_POST['choix_tv']);
            $machineLaverFilter = isset($_POST['choix_machine_a_laver']);
            $laveVaisselleFilter = isset($_POST['choix_lave_vaiselle']);
            $wifiFilter = isset($_POST['choix_wifi']);
        
            $logementHasTv = $logement['equipement_tv'] == true;
            $logementHasMachineLaver = $logement['equipement_machine_a_laver'] == true;
            $logementHasLaveVaisselle = $logement['equipement_lave_vaisselle'] == true;
            $logementHasWifi = $logement['equipement_wifi'] == true;
        
            // Condition pour filtrer les logements
            $tvCondition = !$tvFilter || $logementHasTv;
            $machineLaverCondition = !$machineLaverFilter || $logementHasMachineLaver;
            $laveVaisselleCondition = !$laveVaisselleFilter || $logementHasLaveVaisselle;
            $wifiCondition = !$wifiFilter || $logementHasWifi;
        
            // Retourne true si les deux conditions sont remplies
            return $tvCondition && $machineLaverCondition && $laveVaisselleCondition && $wifiCondition;
        }

        function filtrerServices($logement) {
            $linge = isset($_POST['choix_linge']);
            $menage = isset($_POST['choix_menage']);
            $transport = isset($_POST['choix_transport']);
            $animaux = isset($_POST['choix_animaux']);
            $personne_sup = isset($_POST['choix_personne_sup']);
        
            $logementHasLinge = $logement['service_linge'] == true;
            $logementHasMenage = $logement['service_menage'] == true;
            $logementHasTransport = $logement['service_transport'] == true;
            $logementHasAnimaux = $logement['service_animaux_domestique'] == true;
            $logementHasPersonneSup = $logement['service_personne_sup'] == true;
        
            // Condition pour filtrer les logements
            $lingeCondition = !$linge || $logementHasLinge;
            $menageCondition = !$menage || $logementHasMenage;
            $transportCondition = !$transport || $logementHasTransport;
            $animauxCondition = !$animaux || $logementHasAnimaux;
            $personneSupCondition = !$personne_sup || $logementHasPersonneSup;
        
            // Retourne true si les deux conditions sont remplies
            return $lingeCondition && $menageCondition && $transportCondition && $animauxCondition && $personneSupCondition;
        }

        function filtrerInstallations($logement) {
            $climatisation = isset($_POST['choix_climatisation']);
            $piscine = isset($_POST['choix_piscine']);
            $jacuzzi = isset($_POST['choix_jacuzzi']);
            $hammam = isset($_POST['choix_hammam']);
            $sauna = isset($_POST['choix_sauna']);
        
            $logementHasClimatisation = $logement['installation_climatisation'] == true;
            $logementHasPiscine = $logement['installation_piscine'] == true;
            $logementHasJacuzzi = $logement['installation_jacuzzi'] == true;
            $logementHasHammam = $logement['installation_hammam'] == true;
            $logementHasSauna = $logement['installation_sauna'] == true;
        
            // Condition pour filtrer les logements
            $climatisationCondition = !$climatisation || $logementHasClimatisation;
            $piscineCondition = !$piscine || $logementHasPiscine;
            $jacuzziCondition = !$jacuzzi || $logementHasJacuzzi;
            $hammamCondition = !$hammam || $logementHasHammam;
            $saunaCondition = !$sauna || $logementHasSauna;
        
            // Retourne true si les deux conditions sont remplies
            return $climatisationCondition && $piscineCondition && $jacuzziCondition && $hammamCondition && $saunaCondition;
        }

        function filtrerAmenagements($logement) {
            $jardin = isset($_POST['choix_jardin']);
            $balcon = isset($_POST['choix_balcon']);
            $parkingPublic = isset($_POST['choix_parking_public']);
            $parkingPrive = isset($_POST['choix_parking_prive']);
            $terrasse = isset($_POST['choix_terrasse']);
        
            $logementHasJardin = $logement['amenagement_jardin'] == true;
            $logementHasBalcon = $logement['amenagement_balcon'] == true;
            $logementHasParkingPublic = $logement['amenagement_parking_public'] == true;
            $logementHasParkingPrive = $logement['amenagement_parking_prive'] == true;
            $logementHasTerrasse = $logement['amenagement_terrasse'] == true;
        
            // Condition pour filtrer les logements
            $jardinCondition = !$jardin || $logementHasJardin;
            $logementCondition = !$balcon || $logementHasBalcon;
            $parkingPublicCondition = !$parkingPublic || $logementHasParkingPublic;
            $parkingPriveCondition = !$parkingPrive || $logementHasParkingPrive;
            $terrasseCondition = !$terrasse || $logementHasTerrasse;
        
            // Retourne true si les deux conditions sont remplies
            return $jardinCondition && $logementCondition && $parkingPublicCondition && $parkingPriveCondition && $terrasseCondition;
        }
        
        // Condition pour filtrer les logements en fonction des critères du formulaire
        $logementsFiltres = array_filter($logements, function ($logement) {
            return filtrerInput($logement) && filtrerType($logement) && filtrerDepartements($logement) &&
                   filtrerEquipements($logement) && filtrerServices($logement) &&
                   filtrerInstallations($logement) && filtrerAmenagements($logement);
        });

        // Stocker les logements filtrés dans la session
        $_SESSION['logementsFiltres'] = $logementsFiltres;

        
    $events = array();    
    
    $json_data = json_encode(array('success' => true, 'events' => $logementsFiltres), JSON_PRETTY_PRINT);
    echo $json_data;
        exit();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}
?>