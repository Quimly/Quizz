<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class addAnswerType extends AbstractType
{
	public function buildForm( FormBuilderInterface $builder, array $options )
	{
		$builder

			->add('answers', CollectionType::class, array(
			    'entry_type' => AnswerType::class,
			    'entry_options' => array('label' => false),
				'allow_add' => true
			));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Question::class,
		));
	}
}