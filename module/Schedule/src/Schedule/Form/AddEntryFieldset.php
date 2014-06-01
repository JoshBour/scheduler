<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Schedule\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;

class AddEntryFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The add entry fieldset constructor
     */
    public function init()
    {
        parent::__construct('entry');

        $this->add(array(
            'name' => 'entryId',
            'type' => 'hidden'
        ));

        $this->add(
            array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'worker',
                'options' => array(
                    'object_manager' => $this->getEntityManager(),
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
                    'object_manager' => $this->getEntityManager(),
                    'empty_option' => $this->getTranslator()->translate($this->getVocabulary()["EMPTY_OPTION"]),
                    'target_class' => 'Schedule\Entity\Exception',
                    'property' => 'name',
                    'disable_inarray_validator' => true,
                    'label' => $this->getTranslator()->translate($this->getVocabulary()["LABEL_EXCEPTION"]) . " "
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
                    'label' => $this->getTranslator()->translate($this->getVocabulary()["LABEL_START_TIME"]) . " "
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
                    'label' => $this->getTranslator()->translate($this->getVocabulary()["LABEL_END_TIME"]) . " "
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
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($this->getVocabulary()["ERROR_START_TIME_EMPTY"])
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
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($this->getVocabulary()["ERROR_END_TIME_EMPTY"])
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