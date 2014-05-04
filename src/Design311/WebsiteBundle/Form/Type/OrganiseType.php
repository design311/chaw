<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OrganiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dinner', new DinnerType());
        $builder->add('username', 'checkbox');
        $builder->add('address', new AddressType());
        $builder->add('Register', 'submit');
    }

    public function getName()
    {
        return 'organise';
    }
}