<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    // Metodo Index - Redireciona para a rota /menu
    public function index()
    {
        return redirect('/menu');
    }

}