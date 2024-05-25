<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home | Unipdu Press'
            // 'tes' => ['satu', 'dua', 'tiga']
        ];

        return view('pages/home', $data);
    }
    public function about()
    {
        $data = [
            'title' => 'Halaman About'
        ];

        echo view('pages/about', $data);
    }
    public function contact()
    {
        $data = [
            'title' => 'Contact Us',
            'alamat' => [
                [
                    'tipe' => 'Rumah',
                    'alamat' => 'Desa Godong Kecamatan Gudo',
                    'kota' => 'Jombang'
                ],
                [
                    'tipe' => 'Kantor',
                    'alamat' => 'Kompleks Ponpes Darul Ulum Peterongan',
                    'kota' => 'Jombang'
                ]
            ]
        ];

        echo view('pages/contact', $data);
    }


}
