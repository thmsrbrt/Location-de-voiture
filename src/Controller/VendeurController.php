<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\Vendeur;
use App\Form\VehiculeType;
use App\Repository\ClientRepository;
use App\Repository\FactureRepository;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class VendeurController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/Vendeur", name="vendeur_index")
     * @Route("/Vendeur/Vehicule", name="vendeur_vehicule")
     */
    public function index(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->redirectToRoute('vendeur_connexion');
    }

    /**
     * @Route("/Vendeur/Services", name="vendeur_services")
     */
    public function services() {
        return $this->render('Vendeur/vendeurAllService.html.twig');
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
    public function valideConnexionVendeur(SessionInterface $session, Vendeur $vendeur) {
        $donnees['identifiant'] = htmlentities($_POST['identifiant']);
        $donnees['mdp'] = htmlentities($_POST['password']);

        if (!empty($donnees['identifiant']) and !empty($donnees['mdp'])) {
            $vendeurRepository = $this->getDoctrine()->getRepository(Vendeur::class);
            if (!empty($vendeurRepository->findOneBy(['identifiant' => $donnees['identifiant'], 'password' => $donnees['mdp']]))) {
                $this->setSessions($session, $vendeur);
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
        $vehicules = $vehiculeRepository->findByEtat(1);
        $donnees = $this->getDonneesVehicules($vehicules);
        //dd($donnees);
        $data['nbVehicule'] = count($vehicules);
        $data['nbTotal'] = count($vehiculeRepository->findAll());

        return $this->render('Vendeur/Vehicule/vendeurVehiculeDisponible.html.twig', ['vehicules' => $donnees, 'data' => $data] );
    }

    /**
     * @Route("/Vendeur/Vehicule/indisponible", name="vendeur_vehicule_indisponible")
     */
    public function vehiculeIndisponible(FactureRepository $factureRepository, ClientRepository $clientRepository, VehiculeRepository $vehiculeRepository) {
        $vehicules = $this->getDoctrine()->getRepository(Vehicule::class)->findByEtat(0);
        $donnees = $this->getDonneesVehicules($vehicules);
        $data['nbVehicule'] = count($vehicules);
        $data['nbTotal'] = count($vehiculeRepository->findAll());
        $client = $factureRepository->findBy(['idV' => $vehicules[0]->getId()]);
        //dd($facture);
        /*
        for ($i=0; $i < count($donnees); $i++) {
            $a = $clientRepository->find($client[$i]->getIdC())->getId();
            $donnees[$i]['idC'] = $a;
            $donnees[$i]['NomClient'] = $clientRepository->find($client[$i]->getIdC())->getNom();
        }
        */

        return $this->render('Vendeur/Vehicule/vendeurVehiculeIndisponible.html.twig', ['vehicules' => $donnees, 'data' => $data, 'clients' => $client]);
    }

    /**
     * @Route("/Vendeur/Vehicule/add", name="vendeur_vehicule_add")
     */
    public function addVehicule(Request $request, SluggerInterface $slugger) {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            $caracteres = $form->get('caracteres')->getData();
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
            //dd($caracteres);
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            $texte = "{".(new UnicodeString($caracteres))
                ->replace("\r\n", ' , ')
                ->replace(' : ', " => ")."}";
            //dd($texte);
            $texte = $serializer->serialize($texte, 'json');
            //dd($texte);

            $jsonCaracteres = json_encode($texte);
            //dd($jsonCaracteres);
            $jsonCaracteres = $serializer->serialize($texte, 'json');

            $vehicule->setCaracteres($texte);
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

    /**
     * @Route("/Vendeur/logout", name="vendeur_logout")
     */
    public function logout(SessionInterface $session) {
        $session->clear();
        return $this->render('Vendeur/connexionVendeur.html.twig');
    }

    /**
     * Fonction pour enregistrer une session vendeur
     */
    private function setSessions(SessionInterface $session, Vendeur $vendeur) {
        $session->clear();
        $session->set('id', $vendeur->getId());
        $session->set('nom', $vendeur->getNom());
        $session->set('identifiant', $vendeur->getIdentifiant());
        $session->set('password', $vendeur->getPassword());
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