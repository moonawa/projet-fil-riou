<?php

namespace App\Controller;

use App\Form\CompteType;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/api",name="_api")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
    */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);
        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];

        $form->submit($data);
        if ($form->isSubmitted()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_Super_admin']);
            $user->setUpdatedAt(new \DateTime);
            $user->setImageFile($file);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->handleView($this->view(['status'=>'ok'],Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }

    /**
    * @Route("/alloue/user", name="alloue_compte", methods={"Post"})
    */    
    public function allouer(ValidatorInterface $validator, UtilisateurRepository $utilisateur, Request $request, EntityManagerInterface $entityManager)
    {
        $user = new Utilisateur();
        $form = $this->createForm(CompteType::class, $user);
        $form->handleRequest($request);
        $data=$request->request->all();
        $form->submit($data);
        
        $id = $user->getId();
    }
 
}
