<?php
class DossierModel
{
    private $db;

    // constructeur 
    public function __construct($db)
    {
        $this->db = $db;
    }

    // méthode pour récupérer tous les dossiers
    public function getAllDossiers()
    {
        try {
            // requête pour récupérer tous les dossiers triés par numéro
            $query = "SELECT * FROM dossiers ORDER BY numeroDossier";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $dossiers = [];

            while ($row = $stmt->fetch()) {
                // récupération des diagnostics pour ce dossier
                $queryDiag = "SELECT * FROM diagnostics WHERE dossier_id = :dossier_id ORDER BY date";
                $stmtDiag = $this->db->prepare($queryDiag);
                $stmtDiag->execute([':dossier_id' => $row['id']]);

                $diagnostics = [];
                while ($diag = $stmtDiag->fetch()) {
                    $diagnostics[] = [
                        'date' => $diag['date'],
                        'texte' => $diag['texte']
                    ];
                }

                // construction du tableau final des dossiers avec leurs diagnostics
                $dossiers[] = [
                    'id' => $row['id'],
                    'numeroDossier' => $row['numeroDossier'],
                    'nomClient' => $row['nomClient'],
                    'ageClient' => $row['ageClient'],
                    'diagnostics' => $diagnostics
                ];
            }

            return $dossiers;
        } catch (PDOException $e) {
            throw new Exception("erreur lors de la récupération des dossiers : " . $e->getMessage());
        }
    }

    // méthode pour filtrer les dossiers par nom
    public function filtrerDossiers($filtreNom)
    {
        $dossiers = $this->getAllDossiers();

        if ($filtreNom !== '') {
            // filtre les dossiers dont le nom contient la chaîne recherchée (insensible à la casse)
            return array_filter($dossiers, function ($dossier) use ($filtreNom) {
                return stripos($dossier['nomClient'], $filtreNom) !== false;
            });
        }

        return $dossiers;
    }

    // méthode pour ajouter un dossier
    public function ajouterDossier($numeroDossier, $nomClient, $ageClient, $dateDiagnostic, $diagnostic)
    {
        try {
            // début de la transaction pour garantir l'intégrité des données
            $this->db->beginTransaction();

            // insertion du dossier dans la table dossiers
            $query = "INSERT INTO dossiers (numeroDossier, nomClient, ageClient) VALUES (:numeroDossier, :nomClient, :ageClient)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':numeroDossier' => $numeroDossier,
                ':nomClient' => $nomClient,
                ':ageClient' => $ageClient
            ]);

            // récupération de l'ID du dossier inséré
            $dossierId = $this->db->lastInsertId();

            // insertion du diagnostic initial dans la table diagnostics
            $query = "INSERT INTO diagnostics (dossier_id, date, texte) VALUES (:dossier_id, :date, :texte)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':dossier_id' => $dossierId,
                ':date' => $dateDiagnostic,
                ':texte' => $diagnostic
            ]);

            // validation de la transaction
            $this->db->commit();

            return true;
        } catch (PDOException $e) {
            // annulation de la transaction en cas d'erreur
            $this->db->rollBack();
            throw new Exception("erreur lors de l'ajout du dossier : " . $e->getMessage());
        }
    }

    // méthode pour supprimer un dossier
    public function supprimerDossier($id)
    {
        try {
            // la suppression du dossier entraînera aussi la suppression des diagnostics (ON DELETE CASCADE)
            $query = "DELETE FROM dossiers WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("erreur lors de la suppression du dossier : " . $e->getMessage());
        }
    }

    // méthode pour ajouter un diagnostic
    public function ajouterDiagnostic($dossierId, $dateDiagnostic, $diagnostic)
    {
        try {
            // insertion du nouveau diagnostic dans la table diagnostics
            $query = "INSERT INTO diagnostics (dossier_id, date, texte) VALUES (:dossier_id, :date, :texte)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':dossier_id' => $dossierId,
                ':date' => $dateDiagnostic,
                ':texte' => $diagnostic
            ]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("erreur lors de l'ajout du diagnostic : " . $e->getMessage());
        }
    }

    // méthode pour calculer l'âge moyen des patients
    public function calculerAgeMoyen($dossiers)
    {
        if (count($dossiers) === 0) return 0; // évite la division par zéro

        $ageTotal = 0;
        foreach ($dossiers as $dossier) {
            $ageTotal += $dossier['ageClient'];
        }

        return number_format($ageTotal / count($dossiers), 2); // formatte le résultat à 2 décimales
    }
}
