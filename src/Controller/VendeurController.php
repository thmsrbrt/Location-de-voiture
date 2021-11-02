<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VendeurController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/Vendeur", name="vendeur_index")
     */
    public function index() {
        return $this->render('base.html.twig', ['controller_name' => 'VendeurController']);
    }

    /**
     * @Route("/Vendeur/connexion", name="vendeur_connexion")
     */
    public function connexionVendeur() {
        return $this->render('Vendeur/connexionVendeur.html.twig');
    }

    /**
     * @Route("Vendeur/valideConnexion", name="vendeur_valide_connexion")
     */
    public function valideConnexionVendeur(VehiculeRepository $vendeur) {
        $donnees['identifiant'] = htmlentities($_POST['identifiant']);
        $donnees['mdp'] = htmlentities($_POST['password']);

        if (!empty($donnees['identifiant']) and !empty($donnees['mdp'])) {
            if (!empty($vendeur->findOneBy(['identifiant' => $donnees['identifiant'], 'mdp' => $donnees['mdp']]))) {
                return $this->redirectToRoute('controller_show_vehicule');
            } else {
                $errors['connexion'] = true;
                return $this->render('Vendeur/connexionVendeur.html.twig', ['errors' => $errors]);
            }
        } else {
            $errors = true;
            return $this->render('Vendeur/connexionVendeur.html.twig', ['errors' => $errors]);
        }
    }
}