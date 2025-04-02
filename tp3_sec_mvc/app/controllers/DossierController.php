<?php
// contrôleur pour la gestion des dossiers médicaux

require_once __DIR__ . '/../../config/config.php';
require_once MODELS_PATH . 'DossierModel.php';

class DossierController
{
    private $dossierModel;

    // constructeur 
    public function __construct($db)
    {
        $this->dossierModel = new DossierModel($db);
    }

    // Afficher la liste des dossiers
    public function index()
    {
        try {
            // récupération du filtre de nom 
            $filtreNom = isset($_GET['filtre-nom']) ? $_GET['filtre-nom'] : '';

            // récupération des dossiers filtrés
            $dossiers = $this->dossierModel->filtrerDossiers($filtreNom);

            // calcul de l'âge moyen
            $ageMoyen = $this->dossierModel->calculerAgeMoyen($dossiers);

            // chargement de la vue avec les données nécessaires
            loadView('dossier/index', [
                'dossiers' => $dossiers,
                'filtreNom' => $filtreNom,
                'ageMoyen' => $ageMoyen
            ]);
        } catch (Exception $e) {
            $_SESSION['message'] = "erreur: " . $e->getMessage();
            loadView('dossier/index', ['dossiers' => [], 'filtreNom' => '', 'ageMoyen' => 0]);
        }
    }

    // méthode pour ajouter un dossier
    public function ajouterDossier()
    {
        // vérification si l'utilisateur est connecté
        requireLogin();

        try {
            // récupération des champs du formulaire
            $numeroDossier = $_POST['numero-dossier'];
            $nomClient = $_POST['nom-client'];
            $ageClient = $_POST['age-client'];
            $dateDiagnostic = $_POST['date-diagnostic'];
            $diagnostic = $_POST['diagnostic'];

            // ajout du dossier via le modele
            $result = $this->dossierModel->ajouterDossier($numeroDossier, $nomClient, $ageClient, $dateDiagnostic, $diagnostic);

            if ($result) {
                $_SESSION['message'] = "dossier ajouté avec succès.";
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "erreur: " . $e->getMessage();
        }

        // redirection vers la page principale
        header("Location: " . BASE_URL . "/");
        exit();
    }

    // méthode pour supprimer un dossier
    public function supprimerDossier($id)
    {
        // vérification si l'utilisateur est connecté
        requireLogin();

        try {
            // suppression du dossier via le modèle
            $result = $this->dossierModel->supprimerDossier($id);

            if ($result) {
                $_SESSION['message'] = "dossier supprimé avec succès.";
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "erreur: " . $e->getMessage();
        }

        // redirection vers la page principale
        header("Location: " . BASE_URL . "/");
        exit();
    }

    // méthode pour ajouter un diagnostic
    public function ajouterDiagnostic()
    {
        // vérification si l'utilisateur est connecté
        requireLogin();

        try {
            // récupération des champs du formulaire
            $dossierId = $_POST['dossier_id'];
            $dateDiagnostic = $_POST['date-diagnostic'];
            $diagnostic = $_POST['diagnostic'];

            // ajout du diagnostic via le modèle
            $result = $this->dossierModel->ajouterDiagnostic($dossierId, $dateDiagnostic, $diagnostic);

            if ($result) {
                $_SESSION['message'] = "diagnostic ajouté avec succès.";
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "erreur: " . $e->getMessage();
        }

        // redirection vers la page principale
        header("Location: " . BASE_URL . "/");
        exit();
    }
}
