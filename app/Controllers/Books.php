<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookModel;
use App\Models\CategoryModel;
use App\Models\CategoryBookModel;

class Books extends BaseController
{
    private $book;
    public function __construct()
    {
        $this->book = new BookModel();
    }

    public function index()
    {
        $result = [];
        $categoriesBooksModel = new CategoryBookModel();
        $categoryModel = new CategoryModel();
        foreach ($this->book->where('is_available', 1)->findAll() as $book) {
            $categoriesRelationship = [];
            foreach ($categoriesBooksModel->where('book_id', $book->id)->findAll() as $categoryBookModel) {
                $category = $categoryModel->find($categoryBookModel->category_id);
                $categoriesRelationship[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description
                ];
            }
            
            $result[] = [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->author,
                'publishedAt' => date("d-m-Y", strtotime($book->published_at)),
                'categories' => $categoriesRelationship
            ];
        }

        return $this->response->setStatusCode(200)->setJSON($result);
    }

    public function show($id)
    {
        $result = [];
        $categoriesBooksModel = new CategoryBookModel();
        $categoryModel = new CategoryModel();
        $book = $this->book->find($id);

        $categoriesRelationship = [];
        foreach ($categoriesBooksModel->where('book_id', $book->id)->findAll() as $categoryBookModel) {
            $category = $categoryModel->find($categoryBookModel->category_id);
            $categoriesRelationship[] = [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description
            ];
        }

        $result = [
            'id' => $book->id,
            'name' => $book->name,
            'author' => $book->author,
            'publishedAt' => date("d-m-Y", strtotime($book->published_at)),
            'categories' => $categoriesRelationship
        ];

        return $this->response->setStatusCode(200)->setJSON($result);
    }

    public function store()
    {
        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|regex_match[/^([a-z ])+$/i]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ],
            'author' => [
                'label' => 'autor',
                'rules' => 'required|regex_match[/^([a-z ])+$/i]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ],
            'publishedAt' => [
                'label' => 'fecha de publicación',
                'rules' => 'required|regex_match[/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) 00:00:00$/i]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'regex_match' => 'El {field} {value} debe ser una fecha valida.'
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        if (!isset(request()->getJson()->categories)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['categories' => "La categoría es obligatoría."]]);
        }

        if (count(request()->getJson()->categories) == 0) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['categories' => "Se debe envíar por lo menos una categoría."]]);
        }

        $categoryModel = new CategoryModel();
        foreach (request()->getJson()->categories as $category) {
            if ($categoryModel->find($category) === null) {
                return $this->response->setStatusCode(409)->setJSON(["message" => ['categories' => "Una o más categorias no existen."]]);
            }
        }
        
        $bookId = $this->book->insert(request()->getJson());
        $this->book->update($bookId, ['published_at' => request()->getJson()->publishedAt]);
        $categoryBook = new CategoryBookModel();
        foreach (request()->getJson()->categories as $category) {
            $categoryBook->insert(['category_id' => $category, 'book_id' => $bookId]);
        }

        return $this->response->setStatusCode(201);
    }

    public function update($id)
    {
        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|regex_match[/^([a-z ])+$/i]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ],
            'author' => [
                'label' => 'autor',
                'rules' => 'required|regex_match[/^([a-z ])+$/i]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ],
            'publishedAt' => [
                'label' => 'fecha de publicación',
                'rules' => 'required|regex_match[/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) 00:00:00$/i]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'regex_match' => 'El {field} {value} debe ser una fecha valida.'
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        if (!isset(request()->getJson()->categories)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['categories' => "La categoría es obligatoría."]]);
        }

        if (count(request()->getJson()->categories) == 0) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['categories' => "Se debe envíar por lo menos una categoría."]]);
        }

        $categoryModel = new CategoryModel();
        foreach (request()->getJson()->categories as $category) {
            if ($categoryModel->find($category) === null) {
                return $this->response->setStatusCode(409)->setJSON(["message" => ['categories' => "Una o más categorias no existen."]]);
            }
        }
        
        $this->book->update($id, request()->getJson());
        $this->book->update($id, ['published_at' => request()->getJson()->publishedAt]);
        $categoryBook = new CategoryBookModel();
        $categoryBook->where('book_id', $id)->delete();
        foreach (request()->getJson()->categories as $category) {
            $categoryBook->insert(['category_id' => $category, 'book_id' => $id]);
        }

        return $this->response->setStatusCode(204);
    }

    public function delete($id)
    {
        $book = $this->book->find($id);
        if ($book === null) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['book' => 'El campo identificador del libro debe ser válido.']]);
        }

        if (!$book->is_available) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['book' => 'No puedes eliminar el libro, no se encuentra disponible.']]);
        }

        $categoryBook = new CategoryBookModel();
        $categoryBook->where('book_id', $id)->delete();

        $this->book->delete([$id]);
        return $this->response->setStatusCode(204);
    }
}
