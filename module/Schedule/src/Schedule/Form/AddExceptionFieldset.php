<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Schedule\Form;


use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AddExceptionFieldset extends Fieldset implements InputFilterProviderInterface
{
    const ERROR_NAME_EMPTY = "The name can't be empty";
    const ERROR_COLOR_EMPTY = "The color can't be empty";

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    private $entityManager;

    public function __construct($translator, $entityManager)
    {
        parent::__construct('exception');

        $this->translator = $translator;
        $this->entityManager = $entityManager;

        $this->add(
            array(
                'name' => 'name',
                'type' => 'text',
            )
        );

        $colors = array('0' => 'aqua', '1' => 'black', '2' => 'blue', '3' => 'fuchsia', '4' => 'gray', '5' => 'green', '6' => 'lime', '7' => 'maroon',
            '8' => 'navy', '9' => 'olive', '10' => 'orange', '11' => 'purple',
            '12' => 'red', '13' => 'silver', '14' => 'teal', '15' => 'white', '16' => 'yellow');

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'color',
                'options' => array(
                    'value_options' => $colors,
                ),
                'attributes' => array(
                    'class' => 'colorSelect'
                )
            )
        );

    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_NAME_EMPTY)
                            )
                        )
                    ),
                ),
            ),
            'color' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_COLOR_EMPTY)
                            )
                        )
                    ),
                ),

            ),
        );
    }


} 