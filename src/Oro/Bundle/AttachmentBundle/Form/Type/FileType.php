<?php

namespace Oro\Bundle\AttachmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class FileType extends AbstractType
{
    const NAME = 'oro_file';

    /**
     * @var EventSubscriberInterface
     */
    protected $eventSubscriber;

    /**
     * @param EventSubscriberInterface $eventSubscriber
     */
    public function setEventSubscriber(EventSubscriberInterface $eventSubscriber)
    {
        $this->eventSubscriber = $eventSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [];
        if ($options['checkEmptyFIle']) {
            $constraints = [
                new NotBlank()
            ];
        }

        $builder->add(
            'file',
            'file',
            [
                'label' => 'oro.attachment.file.label',
                'required'  => $options['checkEmptyFIle'],
                'constraints' => $constraints
            ]
        );

        $builder->addEventSubscriber($this->eventSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Oro\Bundle\AttachmentBundle\Entity\File',
                'checkEmptyFIle' => false,
            ]
        );
    }
}
