<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Worker\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;

class AddWorkerFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The add worker fieldset constructor
     */
    public function init()
    {
        parent::__construct('worker');

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'surname',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'email',
        ));

        $this->add(array(
            'name' => 'position',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'address',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'firstTelephone',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'secondTelephone',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'hireDate',
            'type' => 'text',
            'attributes' => array(
                'class' => 'datetimeInput'
            ),
        ));

        $this->add(array(
            'name' => 'releaseDate',
            'type' => 'text',
            'attributes' => array(
                'class' => 'datetimeInput'
            ),
        ));

        $this->add(array(
            'name' => 'workHours',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'salary',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'notes',
            'type' => 'textarea',
        ));
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
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'surname' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($this->getVocabulary()["ERROR_SURNAME_EMPTY"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'email' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                EmailAddress::INVALID_FORMAT => $this->getTranslator()->translate($this->getVocabulary()["ERROR_EMAIL_INVALID"]),
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'position' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'address' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'firstTelephone' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'secondTelephone' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'hireDate' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'releaseDate' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'workHours' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'salary' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'notes' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            )
        );
    }


} 