<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array('required' => false, 'label' => 'Nieuwe profielafbeelding uploaden'));
        $builder->add('displayName', 'text', array('label' => 'Weergavenaam'));
        $builder->add('email', 'email', array('label' => 'E-mail'));
        
        $builder->add('description', 'textarea', array('required' => false, 'label' => 'Korte voorstelling'));
        $builder->add('website', 'text', array('required' => false));
        $builder->add('facebook', 'text', array('required' => false));
        $builder->add('twitter', 'text', array('required' => false));
        $builder->add('googleplus', 'text', array('required' => false, 'label' => 'Google+'));

        $builder->add('submit', 'submit', array('label' => 'Opslaan'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'useredit';
    }
}