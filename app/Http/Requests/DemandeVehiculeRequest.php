<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandeVehiculeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'objet' => 'required',
            'point_depart' => 'required',
            'point_destination' => 'required',
            'nbre_personnes' => 'required',
            'escales' => '',
            'user_id' => 'required',
            'motif' => 'required',
            'type_vehicule' => 'required',
            'date_depart' => 'required',
            'heure_depart' => 'required',
        ];
    }
}
