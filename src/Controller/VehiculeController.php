<?php

namespace App\Controller;

use App\Entity\Vehicule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('base.html.twig', ['controller_name' => 'VehiculeController']);
    }

    /**
     * @Route("/", name="controller_show_vehicule")
     */
    public function showAllVehicules(){
        $vehicule = $this->getDoctrine()->getRepository(Vehicule::class)->findAll();
        //dd($vehicule);
        return $this->render('Vehicule/showVehicule.html.twig', ['vehicules' => $vehicule]);
    }

    /**
     * @Route("/Vehicule/panier", name="panier_vehicule")
     */
    public function showPanierVehicules(){

    }

    /**
     * @Route("/Vehicule/locations", name="locations_vehicule")
     */
    public function showVehiculeEnLocations(){

    }

    /**
     *
     *
     *
     * formulaire ajout modification de véjhicule par les admin
     */
    public function vendeurVehicule(){

    }

    /**
     * @Route("/Véhicule/deleted/{id}", name="deleted_vehicule")
     */
    public function vendeurDeleteVehicule(){

    }



}