<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IngredientType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\Ingredient'
        ));
    }

    public function getName()
    {
        return 'ingredient';
    }
}