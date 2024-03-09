<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserBookModel;
use App\Models\BookModel;

class Users extends BaseController
{
    private $user;
    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function index()
    {
        $result = [];
        $bookModel = new BookModel();
        $usersBooksModel = new UserBookModel();
        foreach ($this->user->findAll() as $user) {
            $booksRelationship = [];
            foreach ($usersBooksModel->where('user_id', $user->id)->findAll() as $userBookModel) {
                $book = $bookModel->find($userBookModel->book_id);
                $booksRelationship[] = [
                    'id' => $book->id,
                    'name' => $book->name,
                    'author' => $book->author,
                    'publishedAt' => $book->published_at,
                ];
            }

            $result[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'books' => $booksRelationship
            ];
        }
        
        return $this->response->setStatusCode(200)->setJSON($result);
    }

    public function show($id)
    {
        $result = [];
        $bookModel = new BookModel();
        $usersBooksModel = new UserBookModel();
        $user = $this->user->find($id);
        $result = [];
        $booksRelationship = [];
        foreach ($usersBooksModel->where('user_id', $user->id)->findAll() as $userBookModel) {
            $book = $bookModel->find($userBookModel->book_id);
            $booksRelationship[] = [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->author,
                'publishedAt' => $book->published_at,
            ];
        }

        $result = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'books' => $booksRelationship
        ];
        
        return $this->response->setStatusCode(200)->setJSON($result);
    }

    public function store()
    {
        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                ],
            ]
        ];
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'min_length[5]|max_length[150]|regex_match[/^([a-zA-Z ])+$/]',
                'errors' => [
                    'min_length' => 'El {field} es debe tener minimo 5 caracteres.',
                    'max_length' => 'El {field} es debe tener máximo 150 caracteres.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ],
            'email' => [
                'label' => 'correo electrónico',
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'valid_email' => 'El {field} debe ser válido.',
                    'is_unique' => 'El {field} ya existe.',
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        return $this->response->setStatusCode(201)->setJSON($this->user->find($this->user->insert(request()->getJson())));
    }

    public function update($id)
    {
        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|min_length[5]|max_length[150]|regex_match[/^([a-zA-Z ])+$/]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'min_length' => 'El {field} es debe tener minimo 5 letras.',
                    'max_length' => 'El {field} es debe tener máximo 150 caracteres.',
                    'regex_match' => 'El {field} debe ser texto.'
                ],
            ],
            'email' => [
                'label' => 'correo electrónico',
                'rules' => 'required|valid_email|is_unique[users.email,id,{id}]',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'valid_email' => 'El {field} debe ser válido.',
                    'is_unique' => 'El {field} ya existe.'
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        $this->user->update($id, request()->getJson());
        return $this->response->setStatusCode(204);
    }

    public function delete($id)
    {
        $usersBooksModel = new UserBookModel();
        $user = $this->user->find($id);
        if ($user === null) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['id' => 'El campo identificador del usuario debe ser válido.']]);
        }
        if (count($usersBooksModel->where('user_id', $user->id)->findAll()) > 0) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['id' => 'No puedes eliminar al usuario, tiene un préstamo de libro.']]);
        }

        $this->user->delete([$id]);
        return $this->response->setStatusCode(204);
    }
    
    public function borrowedBook($id)
    {
        $rules = [
            'bookId' => [
                'label' => 'campo identificador del libro',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'numeric' => 'El {field} debe ser numerico.'
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        $book = new BookModel();
        $book = $book->find(request()->getJson()->bookId);
        if ($book === null) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['bookId' => 'El libro no existe.']]);
        }

        if (!$book->is_available) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['bookId' => 'El libro no se encuentra disponible.']]);
        }

        $userBook = new UserBookModel();
        $userBook->insert(['book_id' => request()->getJson()->bookId, 'user_id' => $id]);

        $book = new BookModel();
        $book->update(request()->getJson()->bookId, ['is_available' => 0]);

        return $this->response->setStatusCode(204);
    }

    public function getBackBorrowedBook($id)
    {
        $rules = [
            'bookId' => [
                'label' => 'campo identificador del libro',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'El {field} es obligatorio.',
                    'numeric' => 'El {field} debe ser numerico.'
                ],
            ]
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(409)->setJSON(["message" => $this->validator->getErrors()]);
        }

        $book = new BookModel();
        $book = $book->find(request()->getJson()->bookId);
        if ($book === null) {
            return $this->response->setStatusCode(409)->setJSON(["message" => ['bookId' => 'El libro no existe.']]);
        }

        $userBook = new UserBookModel();
        if (!$book->is_available && count($userBook->where('book_id', request()->getJson()->bookId)->where('user_id', $id)->findAll()) > 0) {
            $userBook->where('book_id', request()->getJson()->bookId)->where('user_id', $id)->delete();

            $book = new BookModel();
            $book->update(request()->getJson()->bookId, ['is_available' => 1]);

            return $this->response->setStatusCode(204);
        }

        return $this->response->setStatusCode(409)->setJSON(["message" => ['bookId' => 'El libro no se encuentra disponible.']]);
    }
}
