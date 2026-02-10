<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'codigo' => 'nullable|max:50', // Removed unique:productos,codigo to avoid SQLite pragma error
            'nombre' => 'required|max:255', // Removed unique:productos,nombre to avoid SQLite pragma error
            'descripcion' => 'nullable|max:255',
            'img_path' => 'nullable|image|mimes:png,jpg,jpeg,webp,avif,gif,svg,bmp',
            'marca_id' => 'nullable|exists:marcas,id',
            'presentacione_id' => 'nullable|exists:presentaciones,id',
            'categoria_id' => 'nullable|exists:categorias,id'
        ];
    }

    public function attributes()
    {
        return [
            'marca_id' => 'marca',
            'presentacione_id' => 'presentación',
            'categoria_id' => 'categoría'
        ];
    }

    public function messages()
    {
        return [
           // 'codigo.required' => 'Se necesita un campo código'
        ];
    }
}
