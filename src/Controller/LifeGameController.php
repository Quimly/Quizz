<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LifeGameController extends Controller
{
    /**
     * @Route("/lifegame", name="life_game")
     */
    public function index()
    {
        //__Nombre de colonnes
        define('X', 30);
        //__Nombre de lignes
        define('Y', 30);
        //__ Taille minimale d'une cellule
        define('CELL_SIZE_MIN', 1);
        //__ Taille maximale d'une cellule
        define('CELL_SIZE_MAX', 20);

        //__ Nombre minimal en ms entre 2 tours de jeu
        define('SPEED_MIN', 1000);
        //__ Nombre maximal en ms entre 2 tours de jeu
        define('SPEED_MAX', 10);

        $request = Request::createFromGlobals();

        $X = is_numeric($request->request->get('nbCol', X)) ? $request->request->get('nbCol', X) : X;
        $Y = is_numeric($request->request->get('nbLine', Y)) ? $request->request->get('nbLine', Y) : Y;
        $cellSize = is_numeric($request->request->get('cellSize', 10)) && $request->request->get('cellSize', 10) >= CELL_SIZE_MIN && $request->request->get('cellSize', 5) <= CELL_SIZE_MAX ? $request->request->get('cellSize', 5) : 10;
        $speed = is_numeric($request->request->get('speed', 200)) && $request->request->get('speed', 200) >= SPEED_MAX && $request->request->get('speed', 200) <= SPEED_MIN ? $request->request->get('speed', 200) : 200;

        return $this->render('life_game/index.html.twig', [
            'controller_name' => 'LifeGameController',
            'X' => $X,
            'Y' => $Y,
            'cellSize' => $cellSize,
            'speed' => $speed
        ]);
    }
}
