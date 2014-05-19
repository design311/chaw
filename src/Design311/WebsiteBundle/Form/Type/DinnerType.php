<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DinnerType extends AbstractType
{
    private $metadata;

    public function __construct(Array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('date', 'datetime', array(
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
            ));
        $builder->add('menu', 'textarea');
        $builder->add('maxinvitees', 'integer');
        $builder->add('price', 'money');
        $builder->add('address_change', 'checkbox', array(
            'required' => false,
            'mapped' => false
            ));
        $builder->add('address', new AddressType(), array(
            'disabled' => true
            ));
        $builder->add('metafields', new MetaType($this->metadata), array(
            'mapped' => false
            ));

        $builder->add('Dinner organiseren', 'submit');
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