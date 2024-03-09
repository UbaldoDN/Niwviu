<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\CategoryBookModel;

class Categories extends BaseController
{
    private $category;
    public function __construct()
    {
        $this->category = new CategoryModel();
    }

    public function index()
    {
        return $this->response->setStatusCode(200)->setJSON($this->category->findAll());
    }

    public function store()
    {
        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|min_length[5]|regex_match[/^([a-zA-Z ])+$/]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'min_length' => 'El {field} es debe tener minimo 5 letras.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        return $this->response->setStatusCode(201)->setJSON($this->category->find($this->category->insert(request()->getJson())));
    }

    public function update($id)
    {
        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|min_length[5]|regex_match[/^([a-zA-Z ])+$/]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'min_length' => 'El {field} es debe tener minimo 5 letras.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        $this->category->update($id, request()->getJson());
        return $this->response->setStatusCode(204);
    }

    public function delete($id)
    {
        $categoryBook = new CategoryBookModel();
        if (count($categoryBook->where('category_id', $id)->findAll()) > 0) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['category' => 'No puedes eliminar la categoría, se encuentra relacionado a un libro.']]);
        }
        $this->category->delete([$id]);
        return $this->response->setStatusCode(204);
    }
}
