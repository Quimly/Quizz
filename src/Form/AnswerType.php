<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class AnswerType extends AbstractType
{
	public function buildForm( FormBuilderInterface $builder, array $options )
	{
	    $builder
	    ->add('entitled', TextareaType::class, array(
		    'attr' => array(
			    'class' => 'ma-super-classe'
		    )
	    ))
	    ->add('correct', CheckboxType::class, array(
	        'required' => false,
	        'attr' => array(
		        'class' => 'autre-classe'
	        )
	    ))
	    ->add('explanation', TextareaType::class, array(
	        'required' => false,
	        'attr' => array(
		        'class' => 'autre-classe'
	        )
	    ))
	    ->add('image', ImageType::class)
	    ;


	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Answer::class,
		));
	}
}