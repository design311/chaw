<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text');
        $builder->add('email', 'email');
        $builder->add('password', 'repeated', array(
           'first_name'  => 'password',
           'second_name' => 'confirm',
           'type'        => 'password',
        ));
        $builder->add('displayName', 'text', array('required' => false));
        $builder->add('description', 'textarea', array('required' => false));
        $builder->add('website', 'text', array('required' => false));
        $builder->add('facebook', 'text', array('required' => false));
        $builder->add('twitter', 'text', array('required' => false));
        $builder->add('googleplus', 'text', array('required' => false));
        $builder->add('address', new AddressType(), array('required' => false));
        $builder->add('file', 'file', array('required' => false));

        $builder->add('submit', 'submit', array('label' => 'Account aanmaken'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}