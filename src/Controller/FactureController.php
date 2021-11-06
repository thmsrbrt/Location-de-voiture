<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
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
     * @Route("Facture/client", name="client_facture")
     */
    public function factureClient(ClientRepository $client, FactureRepository $repository, SessionInterface $session){
        $id = $session->get('id');
        $client = $client->find($id);
        $factures = $repository->findBy(['idC' => $client]);

        return $this->render('Facture/factureClient.html.twig', ['factures' => $factures ]);
    }


}