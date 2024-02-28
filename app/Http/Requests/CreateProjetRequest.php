<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProjetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
       // le fonction validator pour valider les champ du formulaire
       public function rules(): array
       {
           return [
            'nom'=>'required',
             'objectif'=>'required',
             'description'=>'required',
             'echeance'=>'required',
             'budget'=>'required',
         
            
           ];
       }
   
       public function failedValidation(Validator $validator){
                    throw new HttpResponseException( response()->json([
                       'success'=>false,
                       'error'=>true,
                       'message'=>'Erreur de validation',
                       'errorsList' =>$validator->errors()
                    ]));
       }
   
   // fonction pour traduire les message d'ereur en francais
              public function messages()
              {
                           return [
                               'nom.required' => 'le nom est requis',
                               'object.required' => 'l\'objectif es requis',
                               'description.required' => 'la description est requis',
                               'echeance.required' => 'l\'echeance est requis',
                               'budget.required' => 'le budget est requis',
                              
                           ];
              }
}
