<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class FactureController
{
    public function __construct()
    {
    }

    /**
     * @Route("Facture/all", name="vendeur_show_all_facture")
     */
    public function index(){

        $this->render('');
    }

    /**
     * @Route("Facture/mesfacture", name="client_facture")
     */
    public function showAllFactures(){

    }



}