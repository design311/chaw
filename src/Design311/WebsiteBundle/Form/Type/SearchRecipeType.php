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
            'label' => 'Ingrediënten'
        ));
        $builder->add('category', 'entity', array(
            'class' => 'Design311WebsiteBundle:RecipeCategory',
            'required' => false,
            'empty_value' => 'Geen voorkeur',
            'label' => 'Categorie'
            ));
        $builder->add('diet', 'entity', array(
            'class' => 'Design311WebsiteBundle:Diet',
            'required' => false,
            'empty_value' => 'Geen voorkeur',
            'label'=> 'Eetgewoonte'
            ));
        $builder->add('submit', 'submit', array('label' => 'Recepten zoeken'));
    }

    public function getName()
    {
        return 'searchRecipe';
    }
}