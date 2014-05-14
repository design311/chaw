<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchRecipeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ingredients', 'entity', array(
            'class' => 'Design311WebsiteBundle:Ingredient',
            'property' => 'name',
            'multiple'  => true,
        ));
        $builder->add('Zoeken', 'submit');
    }

    public function getName()
    {
        return 'searchRecipe';
    }
}