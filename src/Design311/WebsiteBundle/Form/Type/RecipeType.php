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
        ));
        $builder->add('recipe', 'textarea');
        $builder->add('cookingTime', 'integer');
        $builder->add('readyTime', 'integer');
        $builder->add('aantalPersonen', 'integer');
        $builder->add('category', 'entity', array('class' => 'Design311WebsiteBundle:RecipeCategory'));
        $builder->add('diet', 'entity', array(
            'class' => 'Design311WebsiteBundle:Diet',
            'label'=> 'Eetgewoonte'
            ));
        $builder->add('photos', 'collection', array(
            'type' => new PhotoType(),
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
        ));
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