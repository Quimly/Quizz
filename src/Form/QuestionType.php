<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class QuestionType extends AbstractType
{
	public function buildForm( FormBuilderInterface $builder, array $options )
	{
		$builder

			->add('entitled', TextType::class)
			->add('image', ImageType::class)
			->add('answers', CollectionType::class, array(
			    'entry_type' => AnswerType::class,
			    'entry_options' => array('label' => false),
			));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Question::class,
		));
	}
}