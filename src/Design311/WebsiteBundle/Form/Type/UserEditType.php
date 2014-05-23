<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array('required' => false));
        $builder->add('displayName', 'text');
        $builder->add('email', 'email');
        
        $builder->add('description', 'textarea', array('required' => false));
        $builder->add('website', 'text', array('required' => false));
        $builder->add('facebook', 'text', array('required' => false));
        $builder->add('twitter', 'text', array('required' => false));
        $builder->add('googleplus', 'text', array('required' => false));

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