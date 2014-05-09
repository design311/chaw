<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('recipe', 'textarea');
        $builder->add('cookingTime', 'integer');
        $builder->add('readyTime', 'integer');
        $builder->add('photos', new PhotoType());
        $builder->add('Recept toevoegen', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\Recipe'
        ));
    }

    public function getName()
    {
        return 'recipe';
    }
}