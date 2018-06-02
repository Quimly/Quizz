<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\QuizzType;
use App\Entity\Quizz;

class QuizzController extends Controller
{
    /**
     * @Route("profile/quizz/", name="createQuizz")
     */
    public function createQuizz(Request $request)
    {
        $quizz = new Quizz();
        $form = $this->createForm(QuizzType::class, $quizz);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {  
            $quizz->setUser($this->getUser());
            $quizz->setCreated(new \DateTime());
            $quizz->setUpdated(new \DateTime());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quizz);
            $entityManager->flush();
            
            return $this->redirectToRoute('userQuizz');
        }
        
        return $this->render(
            'quizz/index.html.twig',
            array('form' => $form->createView())
            );
    }
    
    /**
     * @Route("profile/quizz/{id}", name="editQuizz", requirements={"page"="\d+"})
     */
    public function editQuizz($id)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $quizz = $entityManager->getRepository(Quizz::class)->find($id);
        
        if (!$quizz) {
            throw $this->createNotFoundException(
                'No quizz found for id '.$id
                );
        } else if ($quizz->getUser() != $this->getUser()){
            throw $this->createNotFoundException(
                'This user doesn\'t own this quizz'
                );
        }
        
        return $this->render(
            'quizz/edit.html.twig',[
                'quizz' => $quizz       
            ]);
    }
}
