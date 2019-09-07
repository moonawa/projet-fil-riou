<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Profil;
use App\Form\DepotType;
use App\Form\CompteType;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use App\Form\EntrepriseType;
use App\Form\UtilisateurType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
*/
class EntrepriseController extends AbstractController
{
    /**
     * @Route("/{id}", name="show_entreprise", methods={"GET"})
    */
    /* public function show(EntrepriseRepository $entrepriseRepository, SerializerInterface $serializer)
    {
        $entreprises = $entrepriseRepository->find($entreprises->getId());
        $data = $serializer->serialize($entreprises,'json',[
            'groups' => ['show']
        ]);
        return new Response($data,200,[
            'Content-Type' => 'application/json'
        ]);
    } */

    /**
     * @Route("/liste", name="listerentreprise", methods={"GET"})
     * @Security("has_role('ROLE_Super-admin')")
     */
    public function lister(EntrepriseRepository $entrepriseRepository, SerializerInterface $serialize)
    {
        $entreprises = $entrepriseRepository->findAll();
       
        
        $data = $serialize->serialize($entreprises, 'json',[
            'groups' => ['show']
        ]);

        return new Response($data, 200, [
            'Content-Type'=>'application/json'
        ]);
    }

    /**
     * @Route("/form/entreprise", name="add_entreprises", methods={"POST"})
     * @Security("has_role('ROLE_Super-admin')")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder):Response
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $data = $request->request->All();
        $form->submit($data);
        $entityManager->persist($entreprise);
        $entityManager->flush();
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entreprise);
        $entityManager->flush();
        return new Response('entreprise ajoute', Response::HTTP_CREATED);
    }
    
    /**
    * @Route("/bloque/entreprises/{id}", name="bloque_entreprise", methods={"PUT"})
    * @Security("has_role('ROLE_Super-admin')")
    */ 
    public function bloque(Request $request, SerializerInterface $serializer, Entreprise $entreprise, ValidatorInterface $validator, EntityManagerInterface $entityManager, ObjectManager $manager)
    {
        if($entreprise->getRaisonSociale()=='Moonawa'){
            return new Response('Impossible de bloqué ce partenaire', 409, [
                'Content-Type' => 'application/json'
            ]);
        }
        elseif($entreprise->getStatus() == "Actif"){
            $entreprise->setStatus("bloqué");
            $reponse= new Response('Partenaire bloqué', 200, [
                'Content-Type' => 'application/json'
            ]);            
        }
        elseif($entreprise->getStatus() == "Bloque"){
            $entreprise->setStatus("Actif");
            $reponse= new Response('Partenaire débloqué', 200, [
                'Content-Type' => 'application/json'
            ]);
        }
        $manager->persist($entreprise);
        $manager->flush();
        return $reponse;
    }  
    /**
    * @Route("/depot/entreprise", name="depot_entreprise", methods={"POST"})
    * @Security("has_role('ROLE_Caissier')")
    */
    public function depot (Request $request, UserInterface $Userconnecte)
    {
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $data=json_decode($request->getContent(),true);
        $form->submit($data);
        if($form->isSubmitted() && $form->isValid())
        {           
           $depot->setDate(new \DateTime());
           $depot->setCaissier($Userconnecte);
           $entreprise=$depot->getEntreprise();
           $entreprise->setSolde($entreprise->getSolde()+$depot->getMontant());
           $manager=$this->getDoctrine()->getManager();
           $manager->persist($entreprise);
           $manager->persist($depot);
           $manager->flush();
           $data = [
               'status' => 201,
               'message' => 'Le depot a bien été effectué '
           ];
           return new JsonResponse($data, 201);
        }
        return new JsonResponse($this->view($form->getErrors()), 500);
    }
    /**
     * @Route("/list", name="listeruser", methods={"GET"})
     * @Security("has_role('ROLE_admin')")
     */
    public function list(UtilisateurRepository $utilisateurRepository, SerializerInterface $serialize)
    {
        $users = $utilisateurRepository->findAll();
           
        $data = $serialize->serialize($users, 'json',[
            'groups' => ['show']
        ]);

        return new Response($data, 200, [
            'Content-Type'=>'application/json'
        ]);
    }

    /** 
     * @Route("/ajout", name="admin_utilisateur_compte", methods={"POST"})
     * @Security("has_role('ROLE_Super-admin')")
     */
    public function tout(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $data = $request->request->all();
        $form->submit($data);
        $entreprise->setStatus('Actif');
        
        $entityManager->persist($entreprise);
        $entityManager->flush();
        //recuperation de l'id du entreprise//
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $part = $repository->find($entreprise->getId());

        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $data = $request->request->all();
        $form->submit($data);
        $compte->setSolde($part->getSolde());
        /* $num = rand(100, 999);
        $number=$num."";
        $compte->setNumCompte($number); */
        $nocompte='MA'.rand(10000,99999);       
        $compte->setNoCompte($nocompte);
        $compte->setEntreprise($part);
        $entityManager = $this->getDoctrine()->getManager();
    
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);
        $form->submit($data);
        $user->setRoles(["ROLE_Super-admin"]);
        $user->setEntreprise($part);
        $user->setStatus("Actif")
             ->setEmail($part->getEmail());
        
             /* $profil = new Profil();
             ->setProfil($profil)
 */
             $user->setTelephone($part->getTelephone());
        $user->setPassword($encoder->encodePassword($user,
                             $form->get('plainPassword')->getData()
                            ));
        $file=$request->files->all()['imageName'];

        $user->setImageFile($file);                    
        //$user->setPassword($hash);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($compte);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response('Ajout d\'un entreprise de son user et d\'un compte pour ce dernier , fait', Response::HTTP_CREATED);
    }
}