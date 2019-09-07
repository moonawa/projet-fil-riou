<?php

namespace App\Controller;

use DateTime;
use App\Entity\Depot;
use App\Entity\Tarif;
use App\Entity\Compte;
use App\Form\RetraitType;
use App\Entity\Transaction;

use App\Form\TransactionType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Route("/api",name="_api")
 */
class TransactionController extends AbstractFOSRestController 
{
    /**
     * @Route("/transaction", name="transaction")
     */
    public function index()
    {
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    public function depot (Request $request)
    {
        $depot = new Depot();

        $form = $this->createForm(Depottype::class,$depot);
        $data=json_decode($request->getContent(),true);
        $form->submit($data);
        
        if($form->isSubmitted())
        {
            $dep=$this->getDoctrine()->getManager();
            $dep->persist($depot);
            $dep->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors));        
    }
    /**
     * @Route("/ajout/transaction", name="ajout_transaction", methods={"Post"})
     * @Security("has_role('ROLE_utilisateur)")
    */    
        public function ajout(Request $request, EntityManagerInterface $entityManager)
        {
            $transaction = new Transaction();
            $form = $this->createForm(TransactionType::class, $transaction);
            $form->handleRequest($request);
            $values=$request->request->all();
            $form->submit($values);
            if($form->isSubmitted()){
            $transaction->setDateEnvoi(new \DateTime());

            $c='MA'.rand(10000000,99999999);
            $codes=$c;
            $transaction->setCode($codes);

            $user = $this->getUser();
            
            $transaction->setUserEmetteur($user);
                        
                // recuperer la valeur du frais
                $repository=$this->getDoctrine()->getRepository(Tarif::class);
                $commission=$repository->findAll();

                //recuperer la valeur du montant saisie
                $montant=$transaction->getMontant();

                //Verifier si le montant est disponible en solde 
               $comptes=$this->getUser()->getCompte();
                if($transaction->getMontant() >= $comptes->getSolde()){
                    return $this->json([
                        'message18' => 'votre solde( '.$comptes->getSolde().') ne vous permez pas d\'effectuer cet envoie'
                    ]);
                    } 
                
                // trouver les frais qui correspond au montant
                foreach ($commission as $values ) {
                    $values->getBorneInferieure();
                    $values->getBorneSuperieure();
                    $values->getValeur();
                if($montant >= $values->getBorneInferieure() &&  $montant <= $values->getBorneSuperieure()){
                    $valeur=$values->getValeur();
    
                }

                }
                $transaction->setFrais($valeur);

                // repartition des commissions 
                $moon=($valeur*40)/100;
                $envoie=($valeur*20)/100;
                $etat=($valeur*30)/100;
                $retrait=($valeur*10)/100;

                // dimunition du monatnt envoyé au niveau du solde et ajout de la commission pour wari
                $comptes->setSolde($comptes->getSolde()-$transaction->getMontant()+ $envoie);

                $transaction->setCommissionEmetteur($envoie);
                $transaction->setCommissionRecepteur($retrait);
                
                $transaction->setTaxesEtat($etat);
                $transaction->setCommissionWari($moon);

               
                $transaction->setEtat('envoye');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transaction);
            $entityManager->flush();

            //return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
            $data = [
                'status' => 201,
                'message' => 'Le transfert a été effectué'
            ];
    
            return new JsonResponse($data, 201);
            }
        //return $this->handleView($this->view($form->getErrors()));
               
        }
        /**
         * @Route("/retrait/transaction", name="retrait_transaction", methods={"Post"})
         * @Security("has_role('ROLE_utilisateur)")
        */
        public function retrait(ValidatorInterface $validator, TransactionRepository $transaction, Request $request, EntityManagerInterface $entityManager)
        {
            $retrait = new Transaction();
            $form = $this->createForm(RetraitType::class, $retrait);
            $form->handleRequest($request);
            $values=$request->request->all();
            $form->submit($values);
            $codes = $retrait->getCode();

            $code = $transaction ->findOneBy(['Code'=>$codes]);
            $statut=$code->getEtat();
            if(!$code){
                return new Response('Ce code est invalide ',Response::HTTP_CREATED);
            }
            
            if($code==$codes && $statut=="retire" ){
                return new Response('Le code est déja retiré',Response::HTTP_CREATED);
            }
            else
            {
                $code->setDateReception(new \DateTime());
                $userr = $this->getUser();
                $code->setUserRecepteur($userr);
                $code->setNciRecepteur($values['NciRecepteur']);

                $code->setEtat('retrait');

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($code);
                $entityManager->flush();

                //return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
                $data = [
                    'status' => 201,
                    'message' => 'Le retrait a été effectué'
                ];
        
                return new JsonResponse($data, 201);
            }
        }
       

    /**
     * @Route("/ind", name="listentreprise", methods={"GET"})
     */
    public function lister(EntrepriseRepository $entrepriseRepository, SerializerInterface $serializer)
    {
        $entreprises = $entrepriseRepository->findAll();
        
        $data = $serializer->serialize($entreprises, 'json');

        return new Response($data, 200, [
            'Content-Type'=>'application/json'
        ]);
    }

}


