<?php

namespace App\Models;

use CodeIgniter\Model;

class BooksModel extends Model
{
    protected $table = 'books';
    //protected $insertID = 0;
    //protected $DBGroup;
    //protected $returnType = 'array';
    //protected $useSoftDeletes = false;
    //protected $allowedFields = [];
    protected $useTimestamps = true;
    protected $allowedFields = ['judul', 'slug', 'penulis', 'penerbit', 'sampul'];
    //protected $dateFormat = 'datetime';
    //protected $createdField = 'created_at';
    //protected $updatedField = 'updated_at';
    public function getBuku($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }

        return $this->where(['slug' => $slug])->first();
    }
}