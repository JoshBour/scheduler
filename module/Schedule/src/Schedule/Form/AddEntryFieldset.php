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

class AddEntryFieldset extends Fieldset implements InputFilterProviderInterface
{
    const ERROR_START_TIME_EMPTY = "The start time can't be empty";
    const ERROR_END_TIME_EMPTY = "The end time can't be empty";
    const ERROR_TOTAL_TIME_EMPTY = "The total time can't be empty";

    const LABEL_EXCEPTION = "Exception: ";
    const LABEL_START_TIME = "Start Time: ";
    const LABEL_END_TIME = "End Time: ";
    const LABEL_TOTAL_TIME = "Total Time: ";
    const LABEL_EXTRA_TIME = "Extra Time: ";

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    private $entityManager;

    public function __construct($translator, $entityManager)
    {
        parent::__construct('entry');

        $this->translator = $translator;
        $this->entityManager = $entityManager;

        $this->add(array(
            'name' => 'entryId',
            'type' => 'hidden'
        ));

        $this->add(
            array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'worker',
                'options' => array(
                    'object_manager' => $this->entityManager,
                    'target_class' => 'Worker\Entity\Worker',
                    'property' => 'surname',
                    'disable_inarray_validator' => true
                ),
            )
        );

        $this->add(
            array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'exception',
                'options' => array(
                    'object_manager' => $this->entityManager,
                    'empty_option' => $this->translator->translate('None'),
                    'target_class' => 'Schedule\Entity\Exception',
                    'property' => 'name',
                    'disable_inarray_validator' => true,
                    'label' => $this->translator->translate(self::LABEL_EXCEPTION)
                )
            )
        );

        $this->add(
            array(
                'name' => 'startTime',
                'type' => 'text',
                'attributes' => array(
                    'class' => 'datetimeInput'
                ),
                'options' => array(
                    'label' => $this->translator->translate(self::LABEL_START_TIME)
                )
            )
        );

        $this->add(
            array(
                'name' => 'endTime',
                'type' => 'text',
                'attributes' => array(
                    'class' => 'datetimeInput'
                ),
                'options' => array(
                    'label' => $this->translator->translate(self::LABEL_END_TIME)
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
            'worker' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'exception' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'startTime' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_START_TIME_EMPTY)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'endTime' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_END_TIME_EMPTY)
                            )
                        )
                    ),
                ),
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),

        );
    }


} 