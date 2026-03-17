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
            'codigo' => 'nullable|max:50', 
            'nombre' => 'required|max:255', 
            'descripcion' => 'nullable|max:2000',
            'img_path' => 'nullable|image|mimes:png,jpg,jpeg,webp,avif,gif,svg,bmp',
            'imagenes_extra' => 'nullable|array|max:5',
            'imagenes_extra.*' => 'nullable|image|mimes:png,jpg,jpeg,webp,avif,gif,svg,bmp',
            'marca_id' => 'nullable', 
            'presentacione_id' => 'nullable', 
            'categoria_id' => 'nullable',
            'color' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'genero' => 'nullable|in:Hombre,Mujer,Unisex',
            'precio' => 'nullable|numeric|min:0'
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
            'nombre.required'       => 'El nombre del producto es obligatorio.',
            'nombre.max'            => 'El nombre no puede superar los 255 caracteres.',
            'codigo.max'            => 'El código no puede superar los 50 caracteres.',
            'descripcion.max'       => 'La descripción no puede superar los 2000 caracteres.',
            'img_path.image'        => 'El archivo debe ser una imagen válida.',
            'img_path.mimes'        => 'La imagen debe ser de tipo: png, jpg, jpeg, webp, gif, svg o bmp.',
            'precio.numeric'        => 'El precio debe ser un número.',
            'precio.min'            => 'El precio no puede ser negativo.',
            'genero.in'             => 'El género debe ser Hombre, Mujer o Unisex.',
        ];
    }
}
