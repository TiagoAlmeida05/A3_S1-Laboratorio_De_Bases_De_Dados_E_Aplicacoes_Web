<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosting;

class AdminController extends Controller
{
    // US56: Gerir Ofertas de Emprego
    public function manageJobs() {
        $jobs = JobPosting::all();
        return view('admin.jobs', ['jobs' => $jobs]);
    }

    // US57: Gerir Conteúdo
    public function manageContent() {
        return "Olá Admin! Aqui vais ver denúncias e apagar conteúdo (US57).";
    }

    // US58: Editar Páginas Estáticas
    public function editPages() {
        return "Olá Admin! Aqui vais editar o 'Sobre Nós' (US58).";
    }
}