<?php

namespace App\Controller;

use DateTime;
use App\Entity\Depot;
use App\Entity\Transaction;
use App\Form\TransactionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    */
        public function ajout(ValidatorInterface $validator,Request $request, EntityManagerInterface $entityManager)
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

            $usere=$this->getUser();
            $transaction->setUserEmetteur($usere);

            $transaction->setDateReception(new \DateTime());

            $userr=$this->getUser();
            $transaction->setUserRecepteur($userr);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transaction);
            $entityManager->flush();
            return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
            }
        return $this->handleView($this->view($form->getErrors()));
               
        }

}


