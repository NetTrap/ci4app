<?php

namespace App\Models;

use CodeIgniter\Model;

class PenulisModel extends Model
{
  protected $table = 'penulis';
  protected $useTimestamps = true;
  protected $allowedFields = ['nama', 'alamat'];

  public function search($kataKunci)
  {
    // $builder = $this->table('penulis');
    // $builder->like('name', $kataKunci);

    // return $builder;

    return $this->table('penulis')->like('nama', $kataKunci)->orLike('alamat', $kataKunci);
  }
}