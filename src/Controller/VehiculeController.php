<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class VehiculeController extends AbstractController
{

    public function __construct()
    {
    }

    /**
     * @Route("/Vehicule", name="vehicule_index")
     */
    public function index(){
        return $this->redirectToRoute('controller_show_vehicule');
    }

    /**
     * @Route("/", name="controller_show_vehicule")
     */
    public function showAllVehicules(SessionInterface $session){
        $vehicule = $this->getDoctrine()->getRepository(Vehicule::class)->findAll();
        if ($session != null)
            $info = true;
        else
            $info = false;
        return $this->render('Vehicule/showVehicule.html.twig', ['vehicules' => $vehicule, 'info' => $info]);
    }

    /**
     * @Route("/Vehicule/panier", name="panier_vehicule")
     */
    public function showPanierVehicules(SessionInterface $session, VehiculeRepository $vehiculeRepository){
        $panier = $session->get('panier',  []);
        $donnees = [];
        foreach($panier as $id => $quantity) {
            $donnees[] = [
                'vehicule' => $vehiculeRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $this->render('Vehicule/panierVehicule.html.twig', ['Vehicules' => $donnees]);
    }

    /**
     * @Route("Vehicule/panier/add/{id}", name="panier_add")
     */
    public function addVehiculePanier($id , SessionInterface $session){
        $panier = $session->get('panier', []);

        if (!empty($panier[$id]))
            $panier[$id]++;
        else
            $panier[$id] = 1;

        $session->set('panier', $panier);
        //dd($session->get('panier'));
        $this->redirectToRoute("controller_show_vehicule");
    }

    /**
     * @Route("/Vehicule/panier/remove/{id}", name="panier_remove")
     */
    public function removeVehiculePanier($id, SessionInterface $session) {
        $panier = $session->get('panier',  []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute("panier_vehicule");
    }

}