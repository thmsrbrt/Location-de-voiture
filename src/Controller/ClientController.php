<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class ClientController extends AbstractController {

    public function __construct(){}

    /**
     * @Route("/Client", name="client_error")
     */
    public function index(){
        return $this->redirectToRoute('controller_show_vehicule');
    }

    /**
     * @Route("/Client/inscription", name="client_inscription")
     */
    public function inscriptionClient(SessionInterface $session){
        if ($session != null)
            $session->clear();

        return $this->render('Client/inscriptionClient.html.twig');
    }

    /**
     * @Route("/Client/connexion", name="client_connexion")
     */
    public function connexionClient(SessionInterface $session){
        if ($session != null)
            $session->clear();
        return $this->render('Client/connexionClient.html.twig');
    }

    /**
     * @Route("/Client/edit", name="client_edit")
     */
    public function editClient(ClientRepository $client, SessionInterface $session){
        $donnees = $client->find($session->get('id'));
        return $this->render('Client/editClient.html.twig', ['donnees' => $donnees]);
    }

    /**
     * @Route("Client/{id}/suppression", name="client_suppression")
     */
    public function suppressionClient(ClientRepository $client, SessionInterface $session){
        $entityManager = $this->getDoctrine()->getManager();
        $id = $session->get('id');
        $client = $client->find($id);

        if (!$client){
            throw $this->createNotFoundException('client non trouve'.$id);
        } else {
            $entityManager->remove($client);
            $entityManager->flush();
        }
        return $this->redirectToRoute('controller_show_vehicule');
    }

    /**
     * @Route("/Client/deconnection", name="client_logout")
     */
    public function logout(SessionInterface $session) {
        if ($session != null) {
            $session->clear();
        }
        return $this->redirectToRoute('controller_show_vehicule');
    }

    /**
     * @Route("Client/valideConnexion", name="client_valide_connexion")
     */
    public function valideConnexionClient(ClientRepository $client, SessionInterface $session)
    {
        $donnees['email'] = htmlentities($_POST['email']);
        $donnees['password'] = htmlentities($_POST['password']);

        $errors = $this->valideFormConnexion($donnees);

        if (empty($errors['errors'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $donnees['email']]);
            if ($client != null) {
                if ($client->getPassword() == $donnees['password']) {
                    $this->setSession($session, $client);
                    return $this->redirectToRoute('controller_show_vehicule');
                } else
                    $errors['connexion'] = "Mot de passe incorrect";
            } else
                $errors['connexion'] = "Email incorrect";
        }
        return $this->render('Client/connexionClient.html.twig', ['donnees' => $donnees, 'errors' => $errors]);
    }

    /**
     * @Route("Client/valideFormClient", name="client_valide_form_client")
     */
    public function validateInscriptionClient(SessionInterface $session)
    {
        $donnees['nom'] = htmlentities($_POST['nom']);
        $donnees['prenom'] = htmlentities($_POST['prenom']);
        $donnees['pseudo'] = htmlentities($_POST['pseudo']);
        $donnees['email'] = htmlentities($_POST['email']);
        $donnees['password'] = htmlentities($_POST['password']);
        $donnees['passwordVerify'] = htmlentities($_POST['passwordVerify']);

        $errors = $this->valideFormClient($donnees);
        if (empty($errors['errors'])) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($entityManager->getRepository(Client::class)->findOneBy(['email' => $donnees['email']]) == null)
                $new_client = new Client();
            else
                $new_client = $entityManager->getRepository(Client::class)->findOneBy(['email' => $donnees['email']]);

            $new_client->setNom($donnees['nom']);
            $new_client->setPrenom($donnees['prenom']);
            if (!empty($donnees['pseudo']))
                $new_client->setPseudo($donnees['pseudo']);
            $new_client->setEmail($donnees['email']);
            $new_client->setPassword($donnees['password']);
            $entityManager->persist($new_client);
            $entityManager->flush();
            $this->setSession($session, $new_client);
            return $this->redirectToRoute('controller_show_vehicule');
        }
        return $this->render('Client/inscriptionClient.html.twig', ['donnees' => $donnees, 'errors' => $errors]);
    }

    public function valideFormClient(array $donnees):array {
        $errors = array();
        $good = "Correct !!";
        if (!preg_match('/^[A-Za-z ]{2,}$/', $donnees['nom'])) {
            $errors['errors'] = 1;
            $errors['nom'] = "Le nom doit comporter 2 caractères uniquement avec des lettres";
        }else
            $errors['nom'] = $good;

        if (!preg_match('/^[A-Za-z ]{2,}$/', $donnees['prenom'])) {
            $errors['errors'] = 1;
            $errors['prenom'] = "Le nom doit comporter 2 caractères uniquement avec des lettres";
        }else
            $errors['prenom'] = $good;

        if (!empty($donnees["pseudo"]) and $donnees['pseudo'] != '') {
            if (!empty($this->getDoctrine()->getRepository(Client::class)->findOneBy(['pseudo' => $donnees['pseudo']]))){
                $errors['errors'] = 1;
                $errors['pseudo'] = "Ce pseudo à déjà été utilisé";
        }else
                $errors['pseudo'] = $good;
        }

        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,20}$/', $donnees['password'])){
            $errors['errors'] = 1;
            $errors['password'] = "Mot de passe non sécurité, il doit contenir 8 caractères minimum et avoir un caractère spécial parmi ! @ # $%";
        } else
            $errors['password'] = $good;

        if (!preg_match("%@%", $donnees["email"])) {
            $errors['errors'] = 1;
            $errors['email'] = "Le format de l'email est incorrect";
        } else
            $errors['email'] = $good;

        if ($donnees['password'] != $donnees['passwordVerify']) {
            $errors['errors'] = 1;
            $errors['passwordVerify'] = "Les mots de passe ne sont pas identiques";
        } else
            $errors['passwordVerify'] = $good;

        return $errors;
    }

    private function valideFormConnexion(array $donnees):array
    {
        $errors = array();
        $good = "Correct !!";
        if (!preg_match("%@%", $donnees["email"])) {
            $errors['errors'] = 1;
            $errors['email'] = "Le format de l'email est incorrect";
        } else
        $errors['email'] = $good;

        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,20}$/', $donnees['password'])){
            $errors['errors'] = 1;
            $errors['password'] = "Mot de passe non sécurité, il doit contenir 8 caractères minimum et avoir un caractère spécial parmi ! @ # $%";
        } else
            $errors['password'] = $good;

        return $errors;
    }

    private function setSession(SessionInterface $session, Client $client) {
        $session->clear();
        $session->set('id', $client->getId());
        $session->set('nom', $client->getNom());
        $session->set('prenom', $client->getPrenom());
        $session->set('pseudo', $client->getPseudo());
        $session->set('email', $client->getEmail());
        $session->set('password', $client->getPassword());
    }
}