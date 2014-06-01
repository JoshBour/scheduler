<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Schedule\Form;


use Application\Form\BaseFieldset;
use Schedule\Entity\Exception;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;

class AddExceptionFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The add exception fieldset constructor
     */
    public function init()
    {
        parent::__construct('exception');

        $this->add(
            array(
                'name' => 'name',
                'type' => 'text',
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'color',
                'options' => array(
                    'value_options' => Exception::$colors,
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
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($this->getVocabulary()["ERROR_NAME_EMPTY"])
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
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($this->getVocabulary()["ERROR_COLOR_EMPTY"])
                            )
                        )
                    ),
                ),

            ),
        );
    }


} 