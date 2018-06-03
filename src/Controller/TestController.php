<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

class TestController extends Controller
{
    /**
     * @Route("/test/", name="test")
     */
    public function index()
    {
	    $users = $this->getDoctrine()->getRepository(User::class);

		$user = $users->find(24);

	    return $this->render('test/index.html.twig', [
		    'controller_name' => 'TestController', 'user' => $user
	    ]);
    }
    
    /**
     * @Route("/profile/roleadmin/", name="setadmin")
     */
    public function addAdmin()
    {
        $user = $this->getUser();
        
        $user->setRole(array('ROLE_ADMIN'));
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($user);
        
        $entityManager->flush();
        
        return $this->render('test/echo.html.twig', [
           
        ]);
    }
    
}

