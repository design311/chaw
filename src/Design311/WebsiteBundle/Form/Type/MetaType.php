<?php 

namespace Design311\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MetaType extends AbstractType
{
    private $metadata;

    public function __construct(Array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->metadata as $meta) {
            $choices = [];
            foreach ($meta->getChildren() as $child) {
                $choices[] = $child->getValue();
            }

            if (strtolower($meta->getName()) == 'leeftijd') {
                $builder->add($meta->getName(), 'choice', array(
                    'choices' => $choices,
                    'multiple' => true,
                    'expanded' => true,
                    'data' => range(0, count($choices)-1)
               ));
            }
            else{
                $builder->add($meta->getName(), 'choice', array(
                    'choices' => $choices
                ));
            }

        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Design311\WebsiteBundle\Entity\DinnerMeta'
        ));
    }

    public function getName()
    {
        return 'dinnerMeta';
    }
}