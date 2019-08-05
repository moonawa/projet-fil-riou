<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $motDePass='$argon2id$v=19$m=65536,t=4,p=1$ca6ytbpiXsp0Kv1qAZx5Dg$cjKyjGntnEXLcESRos5sR2uvd401aX3mR5wM89IiPY0';
        $actif='Actif';
        $profilSup=new Profil();
        $profilSup->setLibelle('Super-admin');
        $manager->persist($profilSup);
        
        $profilCaiss=new Profil();
        $profilCaiss->setLibelle('Caissier');
        $manager->persist($profilCaiss);
        
        $profilAdP=new Profil();
        $profilAdP->setLibelle('admin-Principal');
        $manager->persist($profilAdP);
        
        $profilAdm=new Profil();
        $profilAdm->setLibelle('admin');
        $manager->persist($profilAdm);
        
        $profilUtil=new Profil();
        $profilUtil->setLibelle('utilisateur');
        $manager->persist($profilUtil);
        

        $wari=new Entreprise();
                $wari->setRaisonSociale('MoonAwa')
                    ->setNinea(strval(rand(150000000,979999999)))
                    ->setAdresse('Mermoz')
                    ->setSolde(1000000)
                    ->setStatus($actif) 
                    ->setEmail('awandiayesene7@gmail.com')
                    ->setTelephone(rand(770000000,779999999));
        $manager->persist($wari);

        $SupUser=new Utilisateur();
        $SupUser->setUsername('Moonawa') 
             ->setRoles(['ROLE_Super-admin'])
             ->setPassword($motDePass)
             ->setConfirmPassword($motDePass)
             ->setEntreprise($wari)
             ->setNom('Awa Ndiaye Sene')
             ->setEmail($wari->getEmail())
             ->setTelephone($wari->getTelephone())
             ->setNci(strval(rand(150000000,979999999)))
             ->setStatus($actif)
             ->setProfil($profilSup) 
             ->setPhoto('image.jpg'); 
        $manager->persist($SupUser);

        /* $faker = \Faker\Factory::create('fr_FR');
        for($i=0;$i<=5;$i++){
            $entreprise=new Entreprise();
            //$entreprise->setNumeroCompte(strval(rand(150000000,979999999)))
            $entreprise->setRaisonSociale($faker->company)
                        ->setNinea(strval(rand(150000000,979999999)))
                        ->setAdresse($faker->streetAddress)
                        ->setSolde(rand(1000000,10000000))
                        ->setStatus($actif)
                        ->setEmail('awa'.rand(1, 15).'@gmail.com')
                        ->setTelephone(rand(770000000,779999999));
            $manager->persist($entreprise);
            
                       
            for($j=1;$j<5;$j++){
                $user=new Utilisateur();
                $user->setUsername($faker->userName)
                    ->setPassword($motDePass)
                    ->setConfirmPassword($motDePass)
                    ->setEntreprise($entreprise)
                    ->setNom($faker->name)                    
                    ->setNci(strval(rand(150000000,979999999)))
                    ->setStatus($actif)
                    ->setPhoto('http://lightexhibit.org/bio_images_sm/IMG_0134_720.jpg');

                if($j==1){
                    
                    $user->setProfil($profilAdP)
                        ->setRoles(['ROLE_admin-Principal'])
                        ->setEmail($entreprise->getEmail())
                        ->setTelephone($entreprise->getTelephone()); 
                }
                else if($j==2 || $j==3){
                    $user->setProfil($profilAdm)
                     ->setRoles(['ROLE_admin'])
                     ->setEmail(rand(10, 15).'@gmail.com')
                    ->setTelephone(strval(rand(150000000,979999999))); 
                }
                else{
                    $user->setProfil($profilUtil)
                     ->setRoles(['ROLE_utilisateur'])
                     ->setEmail('ma'.rand(2, 15).'@gmail.com')
                    ->setTelephone(strval(rand(150000000,979999999))); ; 
                }
                
                $manager->persist($user);
            }
            $manager->flush();
        }
        //Pour les testes fonctionnels
        $UserSimpl=new Utilisateur();
        $UserSimpl->setUsername('utilisateur1')
             ->setRoles(['ROLE_Utilisateur']) 
             ->setPassword($motDePass)
             ->setConfirmPassword($motDePass)
             ->setEntreprise($wari)
             ->setNom('utilisateur1')
             ->setEmail('utilisateur1@gmail.com')
             ->setTelephone(rand(770000000,779999999))
             ->setNci(strval(rand(150000000,979999999)))
             ->setStatus($actif)
             ->setProfil($profilUtil)
             ->setPhoto('http://lightexhibit.org/bio_images_sm/IMG_0134_720.jpg'); 
        $manager->persist($UserSimpl);

        $UserCaissier=new Utilisateur();
        $UserCaissier->setUsername('caissier1')
             ->setRoles(['ROLE_Caissier'])
             ->setPassword($motDePass)
             ->setConfirmPassword($motDePass)
             ->setEntreprise($wari)
             ->setNom('caissier1')
             ->setEmail('caissier1@gmail.com')
             ->setTelephone(rand(770000000,779999999))
             ->setNci(strval(rand(150000000,979999999)))
             ->setStatus($actif)
             ->setProfil($profilCaiss)
             ->setPhoto('http://lightexhibit.org/bio_images_sm/IMG_0134_720.jpg'); 
        $manager->persist($UserCaissier);

        $UseradminPinci=new Utilisateur();
        $UseradminPinci->setUsername('admPrincipale1')
             ->setRoles(['ROLE_admin-Principal'])
             ->setPassword($motDePass)
             ->setConfirmPassword($motDePass)
             ->setEntreprise($wari)
             ->setNom('admPrincipale1')
             ->setEmail('admPrincipale1@gmail.com')
             ->setTelephone(rand(770000000,779999999))
             ->setNci(strval(rand(150000000,979999999)))
             ->setStatus($actif)
             ->setProfil($profilAdP)
             ->setPhoto('http://lightexhibit.org/bio_images_sm/IMG_0134_720.jpg'); 
        $manager->persist($UseradminPinci);*/
        $manager->flush();
    } 
}
