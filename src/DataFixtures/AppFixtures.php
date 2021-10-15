<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Facture;
use App\Entity\Vehicule;
use App\Entity\Vendeur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
        $this->load_Vehicules($manager);
        $this->load_Clients($manager);
        $this->load_Vendeurs($manager);
        $this->load_Facture($manager);
    }

    public function load_Vehicules($manager) {
        $tab_vehicules = [
            ['id'=>1, "type" => "Peugeot 508", "nombre" => "12", "caractere" => "noir ererere", "photo" => "/Images/Voitures/508-noir-perla-nera.jpg", "etat" => "true"],
            ['id'=>2, "type" => "Peugeot 3008", "nombre" => "12", "caractere" => "rouge eeeee", "photo" => "/Images/Voitures/peugeot-3008-bmw-q3-115.jpg", "etat" => "true"],
            ['id'=>3, "type" => "Peugeot 5008", "nombre" => "12", "caractere" => "bleu magnétique", "photo" => "/Images/Voitures/peugeot-5008-2009styp-016b.jpg", "etat" => "true"],
            ['id'=>4, "type" => "Peugeot 208", "nombre" => "12", "caractere" => "jaune eeeee", "photo" => "/Images/Voitures/Peugeot_208_GT_Line.jpeg", "etat" => "true"],
            ['id'=>5, "type" => "DS", "nombre" => "12", "caractere" => "la ds du garage", "photo" => "/Images/Voitures/S0-salon-de-geneve-2020-ds-presente-la-ds9-622618.jpg", "etat" => "true"],

        ];
        foreach ($tab_vehicules as $vehicule) {
            $new_vehicule = new Vehicule();
            $new_vehicule->setType($vehicule["type"]);
            $new_vehicule->setNombre($vehicule["nombre"]);
            $new_vehicule->setCaractere($vehicule["caractere"]);
            $new_vehicule->setPhoto($vehicule["photo"]);
            $new_vehicule->setEtat($vehicule["etat"]);
            $manager->persist($new_vehicule);
            echo "vehicule : ".$new_vehicule."\n";
        }
        $manager->flush();
    }

    public function load_Clients($manager) {
        $tab_clients = [
            ['id' => 1, "nom" => "Thomas", "prenom" => "Robert", "pseudo" => "", "mdp" => "1234567890", "email" => "thomas.robert@email.com", "adresse" => "Paris1"],
            ['id' => 2, "nom" => "Quentin", "prenom" => "Robert", "pseudo" => "quidhuitre", "mdp" => "1212", "email" => "thomas.robert@email.com", "adresse" => "Rue du ranelaght Paris"],
            ['id' => 3, "nom" => "David", "prenom" => "Robert", "pseudo" => "dav", "mdp" => "2424", "email" => "thomas.robert@email.com", "adresse" => "Paris2"],
        ];
        foreach ($tab_clients as $client) {
            $new_client = new Client();
            $new_client->setNom($client["nom"]);
            $new_client->setPrenom($client["prenom"]);
            $new_client->setPseudo($client["pseudo"]);
            $new_client->setMdp($client["mdp"]);
            $new_client->setEmail($client["email"]);
            $new_client->setAdresse($client["adresse"]);
            $manager->persist($new_client);
            echo "client : ".$new_client."\n";
        }
        $manager->flush();
    }

    public function load_Vendeurs($manager){
        $tab_vendeurs = [
            ['id' => 1, "nom" => "Eric", "identifiant" => "1956", "mdp" => "eric1234"],
            ['id' => 2, "nom" => "Isa", "identifiant" => "3", "mdp" => "bambou"],
        ];
        foreach ($tab_vendeurs as $vendeur) {
            $new_vendeur = new Vendeur();
            $new_vendeur->setNom($vendeur["nom"]);
            $new_vendeur->setIdentifiant($vendeur["identifiant"]);
            $new_vendeur->setMdp($vendeur["mdp"]);
            $manager->persist($new_vendeur);
            echo "vendeur : ".$new_vendeur."\n";
        }
        $manager->flush();
    }

    public function load_Facture($manager) {
        $tab_factures = [
            ['id' => 1, 'idC' => 1, 'idV' => 4, "dateD" => "2020-1-1", "dateF" => "2021-1-1", "etatR" => "1", "valeur" => 4000 ],
            ['id' => 2, 'idC' => 3, 'idV' => 1, "dateD" => "2020-1-30", "dateF" => "2020-9-1", "etatR" => "0", "valeur" => 4000 ],
            ['id' => 3, 'idC' => 2, 'idV' => 5, "dateD" => "2020-2-15", "dateF" => "2020-5-1", "etatR" => "1", "valeur" => 4000 ],
        ];
        foreach ($tab_factures as $facture) {
            $new_facture = new Facture();
            $new_facture->setIdC($manager->getRepository(Client::class)->find($facture["idC"]));
            $new_facture->setIdC($manager->getRepository(Vehicule::class)->find($facture["idV"]));
            $new_facture->setDateD(\DateTime::createFromFormat('Y-m-d', $facture["dateD"]));
            $new_facture->setDateF(\DateTime::createFromFormat('Y-m-d',$facture["dateF"]));

            $new_facture->setEtatR($facture["etatR"]);
            $new_facture->setValeur($facture["valeur"]);
            $manager->persist($new_facture);
            echo "facture : " .$new_facture."\n";
        }
        $manager->flush();
    }
}
