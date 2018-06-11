<?php

namespace App\Controller;

use App\Form\EditUserType;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
	/**
	 * @Route("/register/", name="registration")
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);
			
			$user->setCreated(new \DateTime());
			$user->setUpdated(new \DateTime());

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			return $this->redirectToRoute('home');
		}

		return $this->render(
			'registration/register.html.twig',
			array('form' => $form->createView())
		);
	}

	/**
	 * @Route("/profile/edit/", name="userEdit")
	 */
	public function update(Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		$user = $this->getUser();
		$form = $this->createForm(EditUserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);

//			$user->setCreated(new \DateTime());
			$user->setUpdated(new \DateTime());

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			return $this->redirectToRoute('userEdit');
		}

		return $this->render(
			'user/edit.html.twig', array(
				'form' => $form->createView(),
				'user' => $user
			)
		);
	}
	
	/**
	 * @Route("/profile/myquizz/", name="userQuizz")
	 */
	public function getUserQuizz()
	{
	    $user = $this->getUser();
	    
	    return $this->render('user/quizz.html.twig', [
	        'controller_name' => 'TestController',
	        'user' => $user
	    ]);
	}

}
