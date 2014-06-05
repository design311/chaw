<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('mails', 'text', array('label' => 'E-mails'));
        $builder->add('message', 'textarea', array('label' => 'Uitnodiging'));
        $builder->add('submit', 'submit', array('label' => 'Verzenden'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        /*$resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\Dinner'
        ));*/
    }

    public function getName()
    {
        return 'mail';
    }
}