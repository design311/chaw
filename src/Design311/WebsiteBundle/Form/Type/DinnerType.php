<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DinnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', 'datetime');
        $builder->add('menu', 'textarea');
        $builder->add('maxinvitees', 'number');
        $builder->add('price', 'number');
        $builder->add('address_change', 'checkbox', array(
            'required' => false,
            'mapped' => false
            ));
        $builder->add('address', new AddressType());
        $builder->add('Register', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\Dinner'
        ));
    }

    public function getName()
    {
        return 'dinner';
    }
}