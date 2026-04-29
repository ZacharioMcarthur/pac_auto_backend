<?php

namespace App\Exports;

use App\Models\DemandeVehicule;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListeDemandeursExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateDebut;
    protected $dateFin;

    public function __construct($dateDebut = null, $dateFin = null)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin   = $dateFin;
    }

    /**
     * Récupération des données
     */
    public function collection()
    {
        $query = DemandeVehicule::with([
            'user.entite',
            'chauffeur.user',
            'vehicule'
        ]);

        if ($this->dateDebut && $this->dateFin) {
            $query->whereBetween('date_depart', [$this->dateDebut, $this->dateFin]);
        } elseif ($this->dateDebut) {
            $query->whereDate('date_depart', '>=', $this->dateDebut);
        } elseif ($this->dateFin) {
            $query->whereDate('date_depart', '<=', $this->dateFin);
        }

        return $query->get();
    }

    /**
     * Pour le Formatage des colonnes pour l'export
     */
    public function map($demande): array
    {
        return [
            $demande->date_depart ? Carbon::parse($demande->date_depart)->format('d/m/Y H:i') : '',
            $demande->user?->entite?->nom ?? '',
            $demande->chauffeur?->user?->nom ?? 'Non affecté',
            $demande->vehicule?->immatr ?? 'Non affecté',
            $demande->date_retour ? Carbon::parse($demande->date_retour)->format('d/m/Y H:i') : '',
            $demande->objet ?? '',
            $demande->statut ?? '',
        ];
    }

    /**
     * Titres des colonnes
     */
    public function headings(): array
    {
        return [
            'Date Départ',
            'Entité',
            'Chauffeur',
            'Véhicule',
            'Date Retour',
            'Objet',
            'Statut'
        ];
    }
}
