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
}
