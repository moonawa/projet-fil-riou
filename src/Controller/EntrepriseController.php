<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Profil;
use App\Form\DepotType;
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
     * @Route("/add/entreprise", name="add_entreprises", methods={"POST"})
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator){
        
        $values = json_decode($request->getContent());

        $entreprise = new Entreprise();
        $entreprise->setRaisonSociale($values->RaisonSociale);
        $entreprise->setNinea($values->Ninea);
        $entreprise->setAdresse($values->Adresse);
        $entreprise->setSolde($values->Solde);
        $entreprise->setStatus("Actif");
        $entreprise->setEmail($values->email);
        $entreprise->setTelephone($values->telephone);

        $user = new Utilisateur();
        $user->setUsername($values->username);
        $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
        $user->setEmail($entreprise->getEmail());
        $user->setNom($values->nom);
        $user->setTelephone($entreprise->getTelephone());
        $user->setStatus("Actif");
        $user->setNci($values->Nci);
        $user->setRoles(["'ROLE_Super-admin'"]);
        $user->setPhoto($values->photo);
        $user->setEntreprise($entreprise);
        $form->submit($info);$form->submit($info);
        $repos=$this->getDoctrine()->getRepository(Profil::class);
        $Profil=$repos->find($values->Profil);
        $user->setProfil($Profil);

        $compte = new Compte();
       //$compte = new Utilisateur();
        $compte->setNoCompte($values->no_compte);
        $compte->setSolde($values->solde);
        $compte->setEntreprise($entreprise);
        
        $entityManager->persist($compte);$form->submit($info);
        $entityManager->persist($user);
        $entityManager->persist($entreprise);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $data = [
            'status' => 201,
            'message' => 'L\'utilisateur a été créé'
        ];

        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/form/entreprise", name="add_entreprises", methods={"POST"})
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
     * @Route("/ajout/entreprises", name="ajout_entreprise", methods={"POST"})
    */
    public function put(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {       
        $entreprise = $serializer->deserialize($request->getContent(), Entreprise::class,'json');
        $errors = $validator->validate($entreprise);
        if(count($errors)) {
            $errors = $serializer->serialize($errors,'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($entreprise);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'message' => 'L\'entreprise a été bien enregistré'
        ];
        return new JsonResponse($data, 201);
    }
    /**
    * @Route("/entreprises/{id}", name="update_entreprise", methods={"PUT"})
    */ 
    public function update(Request $request, SerializerInterface $serializer, Entreprise $entreprise, ValidatorInterface $validator, EntityManagerInterface $entityManager)
        {
            $entrepriseUpdate = $entityManager->getRepository(Entreprise::class)->find($entreprise->getId());
            $data = json_decode($request->getContent());
            foreach ($data as $key => $value){
                if($key && !empty($value)) {
                    $name = ucfirst($key);
                    $setter = 'set'.$name;
                    $entrepriseUpdate->$setter($value);
                }
            }
            $errors = $validator->validate($entrepriseUpdate);
            if(count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->flush();
            $data = [
                'status' => 200,
                'message' => 'L\'entreprise a bien été mis à jour'
            ];
            return new JsonResponse($data);
        }
    /**
    * @Route("/bloque/entreprises/{id}", name="bloque_entreprise", methods={"PUT"})
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
}