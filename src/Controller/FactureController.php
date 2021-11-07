<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\FactureRepository;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("Facture/", name="vendeur_show_all_facture")
     */
    public function index(){
        return $this->redirectToRoute('controller_show_vehicule');
    }

    /**
     * @Route("Facture/client", name="client_facture")
     */
    public function factureClient(ClientRepository $client, FactureRepository $factureRepository, SessionInterface $session, VehiculeRepository $vehiculeRepository){
        $id = $session->get('id');
        if ($id != null) {
            $client = $client->findOneBy(['id' => $id]);
            //dd($client);
            $factures = $factureRepository->findBy(['idC' => $id]);
            //dd($factures);
            $data['nbFacture'] = count($factures);
            $data['vehiculeDisponible'] = count($vehiculeRepository->findByEtat(1));
            $data['totalFacture'] = 0;
            foreach ($factures as $facture)
                $data['totalFacture'] += $facture->getValeur();

            return $this->render('Facture/factureClient.html.twig', ['factures' => $factures, 'client' => $client, 'data' => $data]);
        } else
            return $this->redirectToRoute('client_connexion');
    }


}