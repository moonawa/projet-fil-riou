<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api",name="_api")
 */
class SecurityController extends AbstractFOSRestController
{
    /**
     * @Route("/register", name="register", methods={"Post"})
     */
    public function register(ValidatorInterface $validator, Request $request, UserpasswordEncoderInterface $passwordEncoder): Response{
        $user = new Utilisateur();
        $form=$this->createForm(UtilisateurType::class,$user);
        $form->handleRequest($request);
        $data=json_decode($request->getContent(),true);
        //if(!$data){
            $data=$request->request->all();
            $file=$request->files->all()['imageName'];
            $user->setImageFile($file);
       // }
        $form->submit($data);

        $errors = $validator->validate($user);
        if(count($errors) > 0){
            /*
            *Uses a __toString method on the $errors variable which is a
            *ConstrainViolationList object. This gives us nice string
            *for deugging.
            */
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }
        if($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $repo = $this->getDoctrine()->getRepository(Profil::class);
            $profil = $repo->find($data['Profil']);
            $user->setProfil($profil);

            if($profil->getLibelle()== "Super-admin"){
                $user->setRoles(['ROLE_Super_admin']);
            }
            elseif($profil->getLibelle()== "Caissier"){
                $user->setRoles(['ROLE_Caissier']);
            }
            elseif($profil->getLibelle()== "admin-Principal"){
                $user->setRoles(['ROLE_admin-Principal']);
            }
            elseif($profil->getLibelle()== "admin"){
                $user->setRoles(['ROLE_admin']);
            }
            elseif($profil->getLibelle()== "utilisateur"){
                $user->setRoles(['ROLE_utilisateur']);
            }          
            $user->setStatus('Actif')
                 ->setImageFile($file)
;
            $mba=$this->getUser()->getEntreprise();
            $user->setEntreprise($mba);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }
    
    //aller dans config -> packages -> packages  -> Security.yaml
    /**
     * @Route("/inscription", name="inscription", methods={"POST"})
     */
    public function inscriptionUtilisateur(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder, UserInterface $Userconnecte){
        /*
          Début variable utilisé frequement 
        */
        $libSupAdmi='Super-admin';
        $libCaissier='Caissier';
        $libAdmiPrinc='admin-Principal';
        $libAdmi='admin';
        $utilisateur='utilisateur';
        /*
          Fin variable utilisé frequement  
        */       
        $user=new Utilisateur();
        $form=$this->createForm(UtilisateurType::class,$user);
        
        $data=json_decode($request->getContent(),true);//Récupère une chaîne encodée JSON et la convertit en une variable PHP
        $form->submit($data);
        
        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $libelle=$user->getProfil()->getLibelle();

            $profilUserConnecte=$Userconnecte->getProfil()->getLibelle();

            if($libelle == $libSupAdmi   && $profilUserConnecte != $libSupAdmi   ||
               $libelle == $libCaissier  && $profilUserConnecte != $libSupAdmi   ||
               $libelle == $libAdmiPrinc && $profilUserConnecte != $libSupAdmi   ||
               $libelle == $libAdmi      && $profilUserConnecte != $libAdmiPrinc ||
               $libelle == $utilisateur  && $profilUserConnecte != $libAdmiPrinc 
            ){
                return $this->handleView($this->view(['impossible' => 'Votre profil ne vous permet pas de créer ce type d\'utilisateur'],Response::HTTP_CONFLICT));
            }
            else{
                $user->setRoles(['ROLE_'.$libelle]);
                if($libelle!=$libAdmiPrinc){//car si c'est l'admin principal on devra recuperer l'id de l'entreprise qui est sur le formulaire
                    $user->setEntreprise($Userconnecte->getEntreprise());//si ajout caissier il sera dans la même entreprise que le super-admin, si admin ou utilisateur il sera dans la même entreprise que l'admin-principal qui les a créé
                }
            }
            $user->setStatus('Actif');
            $manager->persist($user);
            $manager->flush();
            return $this->handleView($this->view(['status'=>'ok'],Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }
    
    /**
     *@Route("/connexion", name="api_login", methods={"POST"})
     */
    public function login(){ /*gerer dans config packages security.yaml*/}
    
   
}
    /*
        1 - Aller dans config -> packages -> fos_rest.yaml
        2 - Modifier le extend de cette classe par FOSRestController
        3 - Aller dans le UserType ajouter 'csrf_protection'=>false

        Pour authentification
        1 - Aller dans le fichier security.yaml
        2 - installer le bundle : composer require lexik/jwt-authentication-bundle
        3 - Lancer : mkdir -p config/jwt
        4 - Puis : openssl genrsa -out config/jwt/private.pem -aes256 4096
        5 - Un mot de passe et on confirme
        6 - Ensuite : openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem        
    */