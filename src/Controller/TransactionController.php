<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
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
     * @Route("/ajout/transaction", name="ajout_transaction")
    */
        public function ajout(Request $request)
        {
            $transaction = new Transaction();
            $form = $this->createForm(TransactionType::class, $transaction);
            $form->handleRequest($request);
            $values=$request->request->all();
            $form->submit($values);

            $transaction->setDateEnvoi();

            $c='MA'.rand(10000000,99999999);
            $codes=$c;
            $transaction->setCode($codes);

            $usere=$this->getUser();
            $transaction->setUserEmetteur($usere);

            $transaction->setDateReception();

            $userr=$this->getUser();
            $transaction->setUserRecepteur($userr);


               
        }

}


