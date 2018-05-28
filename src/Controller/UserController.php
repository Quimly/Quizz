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

    }
}
