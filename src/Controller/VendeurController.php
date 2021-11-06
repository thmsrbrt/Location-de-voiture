<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\ClientRepository;
use App\Repository\FactureRepository;
use App\Repository\VehiculeRepository;
use App\Repository\VendeurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class VendeurController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/Vendeur", name="vendeur_index")
     */
    public function index() {
        return $this->redirectToRoute('vendeur_connexion');
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
    public function valideConnexionVendeur(VendeurRepository $vendeur) {
        $donnees['identifiant'] = htmlentities($_POST['identifiant']);
        $donnees['mdp'] = htmlentities($_POST['password']);

        if (!empty($donnees['identifiant']) and !empty($donnees['mdp'])) {
            if (!empty($vendeur->findOneBy(['identifiant' => $donnees['identifiant'], 'password' => $donnees['mdp']]))) {
                return $this->redirectToRoute('vendeur_vehicule_disponible');
            } else {
                $errors['connexion'] = true;
                return $this->render('Vendeur/connexionVendeur.html.twig', ['errors' => $errors]);
            }
        } else {
            $errors = true;
            return $this->render('Vendeur/connexionVendeur.html.twig', ['errors' => $errors]);
        }
    }

    /**
     * @Route("/Vendeur/Vehicule/disponible", name="vendeur_vehicule_disponible")
     */
    public function vehiculeDisponible(VehiculeRepository $vehiculeRepository) {
        $donnees = $vehiculeRepository->findByEtat(1);
        //dd($donnees);
        $data['nbVehicule'] = count($donnees);
        $data['nbTotal'] = count($vehiculeRepository->findAll());

        return $this->render('Vendeur/Vehicule/vendeurVehiculeDisponible.html.twig', ['vehicules' => $donnees, 'data' => $data] );
    }

    /**
     * @Route("/Vendeur/Vehicule/indisponible", name="vendeur_vehicule_indisponible")
     */
    public function vehiculeIndisponible(FactureRepository $factureRepository, ClientRepository $clientRepository, VehiculeRepository $vehiculeRepository) {
        $donnees = $vehiculeRepository->findByEtat(0);
        $data['nbVehicule'] = count($donnees);
        $data['nbTotal'] = count($vehiculeRepository->findAll());

        $client = null;

        return $this->render('Vendeur/Vehicule/vendeurVehiculeIndisponible.html.twig', ['vehicules' => $donnees, 'data' => $data, 'clients' => $client]);
    }

    //fonction qui permet ajouter des vehicules
    /**
     * @Route("/Vendeur/Vehicule/add", name="vendeur_vehicule_add")
     */
    public function addVehicule(Request $request, SluggerInterface $slugger) {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('vehicule_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $chemin = '/Images/Voitures/'.$newFilename;
                $vehicule->setPhoto($chemin);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($vehicule);
            $em->flush();
            return $this->redirectToRoute('vendeur_vehicule_disponible');
        }

        return $this->render('Vendeur/Vehicule/vendeurAddVehicule.html.twig', array('formvehicule' => $form->createView()));
    }

    /**
     * @Route("/Vendeur/Vehicule/rendreIndisponible/{id}", name="vendeur_vehicule_rendre_indisponible")
     */
    public function rendreIndisponible(Vehicule $vehicule)
    {
        $vehicule->setEtat(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($vehicule);
        $em->flush();
        return $this->redirectToRoute('vendeur_vehicule_disponible');
    }

    /**
     * @Route("/Vendeur/Vehicule/rendreDisponible/{id}", name="vendeur_vehicule_rendre_disponible")
     */
    public function rendreDisponible(Vehicule $vehicule)
    {
        $vehicule->setEtat(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($vehicule);
        $em->flush();
        return $this->redirectToRoute('vendeur_vehicule_indisponible');
    }

    /**
     * @Route("/Vendeur/Vehicule/supprimer/{id}", name="vendeur_vehicule_supprimer")
     */
    public function supprimer(Vehicule $vehicule)
    {
        $em = $this->getDoctrine()->getManager();
        $namePhoto = (new UnicodeString($vehicule->getPhoto()))
            ->replace('/Images/Voitures/', '');
        $path = $this->getParameter('vehicule_directory').'/'.$namePhoto;
        try {
            unlink($path);
            echo "\nsuppression réussie !\n";
        } catch (FileException $e) {
            echo "\nerreur lors de la suppression de la photo\n";
        }
        $em->remove($vehicule);
        $em->flush();

        return $this->redirectToRoute('vendeur_vehicule_disponible');
    }

}