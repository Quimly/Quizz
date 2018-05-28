<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

class TestController extends Controller
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
	    $user = $this->getDoctrine()->getRepository(User::class);

	    $users = $user->findAll();

	    return $this->render('test/index.html.twig', [
	    	'controller_name' => 'TestController', 'users' => $users
	    ]);

    }
}
