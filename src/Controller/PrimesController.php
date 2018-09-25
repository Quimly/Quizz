<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PrimesController extends Controller
{
    /**
     * @Route("/primes", name="primes")
     */
    public function index()
    {
        return $this->render('primes/primes.html.twig');
    }
}
