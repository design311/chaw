<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => 'Gebruikersnaam'));
        $builder->add('email', 'email', array('label' => 'E-mail'));
        $builder->add('password', 'repeated', array(
           'first_name'  => 'password',
           'second_name' => 'confirm',
           'first_options'  => array('label' => 'Wachtwoord'),
           'second_options' => array('label' => 'Bevestig wachtwoord'),
           'type'        => 'password',
        ));

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
        return 'userregister';
    }
}