<?php

namespace Oro\Bundle\FilterBundle\Tests\Unit\Form\Type\Filter;

use Oro\Bundle\FilterBundle\Form\Type\Filter\ChoiceFilterType;
use Oro\Bundle\FilterBundle\Form\Type\Filter\FilterType;
use Oro\Bundle\FilterBundle\Tests\Unit\Fixtures\CustomFormExtension;
use Oro\Bundle\FilterBundle\Tests\Unit\Form\Type\AbstractTypeTestCase;
use Oro\Component\Testing\Unit\PreloadedExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceFilterTypeTest extends AbstractTypeTestCase
{
    /**
     * @var ChoiceFilterType
     */
    private $type;

    protected function setUp()
    {
        $translator = $this->createMockTranslator();
        $this->type = new ChoiceFilterType($translator);
        $this->formExtensions[] = new CustomFormExtension(array(new FilterType($translator)));
        $this->formExtensions[] = new PreloadedExtension([$this->type], []);

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected function getTestFormType()
    {
        return $this->type;
    }

    public function testGetName()
    {
        $this->assertEquals(ChoiceFilterType::NAME, $this->type->getName());
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptionsDataProvider()
    {
        return array(
            array(
                'defaultOptions' => array(
                    'field_type' => ChoiceType::class,
                    'field_options' => array(),
                    'operator_choices' => array(
                        ChoiceFilterType::TYPE_CONTAINS => 'oro.filter.form.label_type_contains',
                        ChoiceFilterType::TYPE_NOT_CONTAINS => 'oro.filter.form.label_type_not_contains',
                    ),
                    'populate_default' => false,
                    'default_value' => null,
                    'null_value' => null,
                    'class' => null
                )
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function bindDataProvider()
    {
        return array(
            'empty' => array(
                'bindData' => array(),
                'formData' => array('type' => null, 'value' => null),
                'viewData' => array(
                    'value' => array('type' => null, 'value' => null),
                )
            ),
            'predefined value choice' => array(
                'bindData' => array('value' => 1),
                'formData' => array('type' => null, 'value' => 1),
                'viewData' => array(
                    'value' => array('type' => null, 'value' => 1),
                ),
                'customOptions' => array(
                    'field_options' => array(
                        'choices' => array(1 => 'One', 2 => 'Two')
                    ),
                )
            ),
            'invalid value choice' => array(
                'bindData' => array('value' => 3),
                'formData' => array('type' => null),
                'viewData' => array(
                    'value' => array('type' => null, 'value' => 3),
                ),
                'customOptions' => array(
                    'field_options' => array(
                        'choices' => array(1 => 'One')
                    ),
                )
            ),
            'multiple choices' => array(
                'bindData' => array('value' => array(1, 2)),
                'formData' => array('type' => null, 'value' => array(1, 2)),
                'viewData' => array(
                    'value' => array('type' => null, 'value' => array(1, 2)),
                ),
                'customOptions' => array(
                    'field_options' => array(
                        'multiple' => true,
                        'choices' => array(1 => 'One', 2 => 'Two', 3 => 'Three')
                    ),
                )
            ),
            'invalid multiple choices' => array(
                'bindData' => array('value' => array(3, 4)),
                'formData' => array('type' => null),
                'viewData' => array(
                    'value' => array('type' => null, 'value' => array(3, 4)),
                ),
                'customOptions' => array(
                    'field_options' => array(
                        'multiple' => true,
                        'choices' => array(1 => 'One', 2 => 'Two', 3 => 'Three')
                    ),
                )
            ),
        );
    }
}
