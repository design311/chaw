<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('label' => 'Titel'));
        $builder->add('recipeingredients', 'collection', array(
            'type' => new RecipeIngredientType(),
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'Ingrediënten',
        ));
        $builder->add('recipe', 'textarea', array('label' => 'Recept'));
        $builder->add('cookingTime', 'integer', array('label' => 'Bereidingstijd (min)'));
        $builder->add('readyTime', 'integer', array('label' => 'Totale tijd (min)'));
        $builder->add('aantalPersonen', 'integer');
        $builder->add('category', 'entity', array('class' => 'Design311WebsiteBundle:RecipeCategory', 'label' => 'Categorie'));
        $builder->add('diet', 'entity', array(
            'class' => 'Design311WebsiteBundle:Diet',
            'label'=> 'Eetgewoonte'
            ));
        $builder->add('photos', 'collection', array(
            'type' => new PhotoType(),
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'Foto\'s'
        ));
        $builder->add('submit', 'submit', array('label' => 'Recept toevoegen'));
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