<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FlappyBirdController extends Controller
{
    /**
     * @Route("flappy/", name="flappy")
     */
    public function index()
    {
        return $this->render('flappy/index.html.twig', [
            'controller_name' => 'FlappyBirdController',
        ]);
    }
}
