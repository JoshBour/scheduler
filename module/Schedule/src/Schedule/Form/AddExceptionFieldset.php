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

        $this->add(
            array(
                'name' => 'referencedDate',
                'type' => 'text',
                'attributes' => array(
                    'class' => 'datetimeInput'
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
            'referencedDate' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }


} 