<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use PhpParser\JsonDecoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        $vehicules = $this->getDoctrine()->getRepository(Vehicule::class)->findAll();
        if ($session != null)
            $info = true;
        else
            $info = false;
        $donnees = $this->getDonneesVehicules($vehicules);
        //dd($donnees);
        return $this->render('Vehicule/showVehicule.html.twig', ['vehicules' => $donnees ,'info' => $info]);
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

    /**
     * @param array $vehicules
     * @return array
     */
    public function getDonneesVehicules(array $vehicules): array
    {
        $i = 0;
        foreach ($vehicules as $vehicule) {
            $donnees[$i]['id'] = $vehicule->getId();
            $donnees[$i]['name'] = $vehicule->getName();
            $donnees[$i]['photo'] = $vehicule->getPhoto();
            $donnees[$i]['etat'] = $vehicule->getEtat();
            $eee = json_decode($vehicule->getCaracteres());
            $donnees[$i]['caracteres'] = $eee[0];
            $i++;
        }
        return $donnees;
    }

}