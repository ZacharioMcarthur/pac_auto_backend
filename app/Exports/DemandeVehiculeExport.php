<?php

namespace App\Exports;

use App\Models\DemandeVehicule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class DemandeVehiculeExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return DemandeVehicule::all();
    }

    public function map($demande): array
    {
        return [
            $demande->reference,
            $demande->objet,
            $demande->date,
            $demande->point_depart,
            $demande->point_destination,
            $demande->nbre_personnes,
            $demande->statut,
            $demande->date_depart,
            $demande->date_retour,
            Carbon::parse($demande->created_at)->locale('fr')->translatedFormat('F'), // Mois en français
        ];
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Objet',
            'Date de création',
            'Point de départ',
            'Point de destination',
            'Nombre de personnes',
            'Statut',
            'Date départ',
            'Date retour',
            'Mois',
        ];
    }
}
