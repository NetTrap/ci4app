<?php

namespace App\Controllers;

use App\Models\BooksModel;

class Books extends BaseController
{
    protected $bukuModel;

    public function __construct()
    {
        $this->bukuModel = new BooksModel();
    }

    public function index()
    {
        // $buku = $this->bukuModel->findAll();

        $data = [
            'title' => 'Daftar Buku',
            // 'buku' => $buku
            'buku' => $this->bukuModel->getBuku()
        ];

        // konek db tanpa model
        //$db = \Config\Database::connect();
        //$books = $db->query("SELECT * FROM books");
        //foreach ($books->getResultArray() as $row) {
        //  d($row);
        //}

        //$booksModel = new\App\Models\BooksModels();
        //$booksModel = new BooksModel();

        return view('books/index', $data);
    }


    public function detail($slug)
    {
        // $buku = $this->bukuModel->where(['slug' => $slug])->first();
        $buku = $this->bukuModel->getBuku($slug);
        // pindah ke data
        $data = [
            'title' => 'Detail Buku',
            'buku' => $this->bukuModel->getBuku($slug)
        ];

        // jika buku tidak ada di tabel
        if (empty($data['buku'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Buku ' . $slug . ' Tidak ditemukan.');
        }

        return view('books/detail', $data);
    }

    public function create()
    {
        // session();
        $data = [
            'title' => 'Form Tambah Data Buku',
            // 'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation()
            'validation' => \Config\Services::validation()
        ];

        return view('books/create', $data);
    }

    public function save()
    {
        // validasi input
        if (
            !$this->validate([
                'judul' => [
                    'rules' => 'required|is_unique[books.judul]',
                    'errors' => [
                        'required' => '{field} buku harus diisi.',
                        'is_unique' => '{field} buku sudah terdaftar'
                    ]
                ],
                'sampul' => [
                    'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran gambar terlalu besar',
                        'is_image' => 'Yang anda pilih bukan gambar',
                        'mime_in' => 'Yang anda pilih bukan gambar'
                    ]
                ]
            ])
        ) {
            // session()->setFlashdata('validation' ?? \Config\Services::validation());
            return redirect()->to('/books/create')->withInput();
            // $validation = \Config\Services::validation();
            // return redirect()->to('/books/create')->withInput()->with('validation', $validation);
        }

        // $this->request->getVar('judul');

        // ambil file gambar
        $gambarSampul = $this->request->getFile('sampul');
        // apakah tidak ada gambar yang diupload
        if ($gambarSampul->getError() == 4) {
            $namaSampul = 'default.png';
        } else {
            // generate nama sampul random
            $namaSampul = $gambarSampul->getRandomName();
            // pindahkan file ke folder img
            $gambarSampul->move('img', $namaSampul);
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->bukuModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');

        return redirect()->to('/books');
    }

    public function delete($id)
    {
        // cari nama gambar berdasarkan id
        $buku = $this->bukuModel->find($id);

        // cek jika file gambar default
        if ($buku['sampul'] != 'default.png') {

            // hapus gambar
            unlink('img/' . $buku['sampul']);
        }

        $this->bukuModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus');
        return redirect()->to('/books');
    }

    public function edit($slug) // Menerima parameter slug
    {
        // session();

        $data = [
            'title' => 'Form Edit Data Buku',
            'validation' => \Config\Services::validation(),
            'buku' => $this->bukuModel->getBuku($slug) //Mengirim data yang dipilih
        ];

        return view('books/edit', $data);
    }

    public function update($id)
    {
        // $bukulama = $this->bukuModel->getBuku($this->request->getVar('slug'));
        // $bukulama = $this->bukuModel->getBuku($id);
        $bukulama = $this->bukuModel->find($id);

        // validasi judul
        if ($bukulama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[books.judul]';
        }

        // validasi penulis
        if ($bukulama['penulis'] == $this->request->getVar('penulis')) {
            $rule_penulis = 'required';
        } else {
            $rule_penulis = 'required|is_unique[books.penulis]';
        }

        // validasi penerbit
        if ($bukulama['penerbit'] == $this->request->getVar('penerbit')) {
            $rule_penerbit = 'required';
        } else {
            $rule_penerbit = 'required|is_unique[books.penerbit]';
        }

        // validasi input
        if (
            !$this->validate([
                'judul' => [
                    'rules' => $rule_judul,
                    'errors' => [
                        'required' => '{field} buku harus diisi.',
                        'is_unique' => '{field} buku sudah terdaftar'
                    ]
                ],
                'penulis' => [
                    'rules' => $rule_penulis,
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'is_unique' => '{field} sudah terdaftar'
                    ]
                ],
                'penerbit' => [
                    'rules' => $rule_penerbit,
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'is_unique' => '{field} sudah terdaftar'
                    ]
                ],
                'sampul' => [
                    'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        // 'uploaded' => 'Pilih gambar yang sesuai',
                        'max_size' => 'Ukuran gambar terlalu besar',
                        'is_image' => 'Yang anda pilih bukan gambar',
                        'mime_in' => 'Yang anda pilih bukan gambar'
                    ]
                ]
            ])
        ) {
            // session()->setFlashdata('validation' ?? \Config\Services::validation());
            // return redirect()->to('/books/edit')->withInput();
            // $validation = \Config\Services::validation();
            // return redirect()->to('/books/edit/' . $this->request->getVar('slug'))->withInput()->with('validation', $validation);
            return redirect()->to('/books/edit/' . $this->request->getVar('slug'))->withInput();
        }

        // ambil file gambar
        $gambarSampul = $this->request->getFile('sampul');

        // cek gambar, apakah tidak ada gambar yang diupload
        if ($gambarSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            // generate nama sampul random
            $namaSampul = $gambarSampul->getRandomName();
            // pindahkan file ke folder img
            $gambarSampul->move('img', $namaSampul);
            //  hapus file
            unlink('img/' . $this->request->getVar('sampulLama'));
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->bukuModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');

        return redirect()->to('/books');
    }

}