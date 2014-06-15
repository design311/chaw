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
        $builder->add('title', 'text', array('label' => 'Titel'));
        $builder->add('date', 'datetime', array(
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'label' => 'Datum en tijdstip',
            'label_attr' => array('for' => 'dinner_date_date')
            ));
        $builder->add('menu', 'textarea');
        $builder->add('maxinvitees', 'integer', array('label' => 'Aantal gasten'));
        $builder->add('inviteonly', 'checkbox', array('label' => 'Enkel op uitnodiging', 'required' => false));
        $builder->add('price', 'money', array('currency' => false, 'label' => 'Prijs'));
        $builder->add('change_address', 'hidden', array(
            'mapped' => false,
            'data' => true
            ));
        $builder->add('address', new AddressType(), array('label' => 'Adres'));
        /*$builder->add('metafields', new MetaType($this->metadata), array(
            'mapped' => false
            ));*/
        $builder->add('diet', 'entity', array(
            'class' => 'Design311WebsiteBundle:Diet',
            'label'=> 'Eetgewoonte'
            ));
        $builder->add('photos', 'collection', array(
            'type' => new PhotoType(),
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'Foto\'s'
        ));

        $builder->add('submit', 'submit', array('label' => 'Dinner organiseren'));
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