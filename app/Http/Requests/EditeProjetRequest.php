<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditeProjetRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'montant'=>'required',
            'description'=>'required',
            // 'user_id'=>'required',
            // 'projet_id'=>'required'
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
                            'montant.required'=>'veuillez definnir un montant',
                            'description.required'=>'Description requis!',
                        ];
           }
}
