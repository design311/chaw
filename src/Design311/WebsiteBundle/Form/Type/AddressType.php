<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street','text', array('label' => 'Straat'));
        $builder->add('number','text', array('label' => 'Nummer'));
        $builder->add('zipcode','text', array('label' => 'Postcode'));
        $builder->add('city','text', array('label' => 'Plaats'));
        $builder->add('country','text', array('label' => 'Land'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\Address'
        ));
    }

    public function getName()
    {
        return 'address';
    }
}