<?php

namespace App\Controller;

use App\Form\UtilisateurType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Utilisateur;


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
        if ($form->isSubmitted() && $form->isValid()) {
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
}
