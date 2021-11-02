<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\isEmpty;


class ClientController extends AbstractController {
    public function __construct(){

    }

    public function index(){
        return $this->render('base.html.twig', ['controller_name' => 'ClientController']);
    }

    /**
     * @Route("/Client/inscription", name="client_inscription")
     */
    public function inscriptionClient(){
        return $this->render('Client/inscriptionClient.html.twig');
    }

    /**
     * @Route("/Client/connexion", name="client_connexion")
     */
    public function connexionClient(){
        return $this->render('Client/connexionClient.html.twig');
    }

    /**
     * @Route("/Client/edit", name="client_edit")
     */
    public function editClient(ClientRepository $client){
        //$donnees = $client->findOneBy();
        //dd($donnees);
        $donnees = null;
        return $this->render('Client/editClient.html.twig', ['donnees' => $donnees]);
    }

    /**
     * @Route("Client/{id}/suppression", name="client_suppression")
     */
    public function suppressionClient( $id){
        $entityManager = $this->getDoctrine()->getManager();
        $client = $entityManager->getRepository(Client::class)->find($id);

        if (!$client){
            throw $this->createNotFoundException('client non trouve'.$id);
        }

        if (isEmpty($client)){
            $entityManager->remove($client);
            $entityManager->flush();
        }
        return $this->redirectToRoute('controller_show_vehicule');
    }
/*
    /**
     * @Route("Client/valideConnexion", name="client_valide_connexion")

    public function valideConnexionClient(){
        $donnees['email'] = htmlentities($_POST['email']);
        $donnees['password'] = htmlentities($_POST['password']);

        if (!empty($donnees['email']) and !empty($donnees['password'])){
            if (empty($this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $donnees['email'], 'mdp' => $donnees['password']]))) {
                $errors['connexion'] = "Email ou mot de passe incorrect";
                return $this->render('Client/connexionClient.html.twig', ['donnees' => $donnees, 'errors' => $errors]);
            } else
                return $this->redirectToRoute('controller_show_vehicule');
        } else
            return $this->render('Client/connexionClient.html.twig', ['donnees' => $donnees]);
    }
*/

    /**
     * @Route("Client/valideConnexion", name="client_valide_connexion")
     */
    public function valideConnexionClient(ClientRepository $client){
        $donnees['email'] = htmlentities($_POST['email']);
        $donnees['password'] = htmlentities($_POST['password']);

        if (!empty($donnees['email']) and !empty($donnees['password'])){
            if (!empty($client->findOneBy(['email' => $donnees['email'], 'mdp' => $donnees['password']]))) {
                return $this->redirectToRoute('controller_show_vehicule');
            } else {
                $errors['connexion'] = "Email ou mot de passe incorrect";
                //dd($donnees);
                return $this->render('Client/connexionClient.html.twig', ['donnees' => $donnees, 'errors' => $errors]);
            }
        } else
            return $this->render('Client/connexionClient.html.twig', ['donnees' => $donnees]);
    }

    /**
     * @Route("Client/valideFormClient", name="client_valide_form_client")
     */
    public function validateInscriptionClient()
    {
        $donnees['nom'] = htmlentities($_POST['nom']);
        $donnees['prenom'] = htmlentities($_POST['prenom']);
        $donnees['pseudo'] = htmlentities($_POST['pseudo']);
        $donnees['email'] = htmlentities($_POST['email']);
        $donnees['password'] = htmlentities($_POST['password']);
        $donnees['passwordVerify'] = htmlentities($_POST['passwordVerify']);

        $errors = $this->valideFormClient($donnees);

        if (empty($errors)) {
            $entityManager = $this->getDoctrine()->getManager();
            $new_client = new Client();
            $new_client->setNom($donnees['nom']);
            $new_client->setPrenom($donnees['prenom']);
            if (!empty($donnees['pseudo']))
                $new_client->setPseudo($donnees['pseudo']);
            $new_client->setEmail($donnees['email']);
            $new_client->setMdp($donnees['password']);
            $entityManager->persist($new_client);
            $entityManager->flush();

            // ne pas oublier l'enregistrment pour le cookie
            return $this->redirectToRoute('controller_show_vehicule');// rettour vers l'accuil en gros mais en étant connecté

        }
        return $this->render('Client/inscriptionClient.html.twig', ['donnees' => $donnees, 'errors' => $errors]);
    }

    public function valideFormClient($donnees):array {
        $errors = array();
        $good = "Correct !!";
        if (!preg_match('/^[A-Za-z ]{2,}$/', $donnees['nom']))
            $errors['nom'] = "Le nom doit comporter 2 caractères uniquement avec des lettres";
        else
            $errors['nom'] = $good;

        if (!preg_match('/^[A-Za-z ]{2,}$/', $donnees['prenom']))
            $errors['prenom'] = "Le nom doit comporter 2 caractères uniquement avec des lettres";
        else
            $errors['prenom'] = $good;

        if (!empty($donnees["pseudo"]) and $donnees['pseudo'] != ''){
            if (!empty($this->getDoctrine()->getRepository(Client::class)->findOneBy(['pseudo' => $donnees['pseudo']])))
                $errors['pseudo'] = "Ce pseudo à déjà été utilisé";
            else
                $errors['pseudo'] = $good;
        }

        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $donnees['password']))
            $errors['password'] = "Mot de passe non sécurité, il doit contenir 8 caractères minimum et avoir un caractère spécial parmi ! @ # $%";
        else
            $errors['password'] = $good;

        if (!preg_match("%@%", $donnees["email"]))
            $errors['email'] = "Le format de l'email est incorrect";
        else
            $errors['email'] = $good;

        if ($donnees['password'] != $donnees['passwordVerify'])
            $errors['passwordVerify'] = "Les deux mots de passe ne sont pas cohérent";
        else
            $errors['passwordVerify'] = $good;

        return $errors;
    }
}