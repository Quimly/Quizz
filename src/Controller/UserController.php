<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $uManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setPseudo('newUser');
        $user->setAge(55);

        $uManager->persist($user);

        $uManager->flush();

	    return new Response('Saved new user with id '.$user->getId());
    }
}
