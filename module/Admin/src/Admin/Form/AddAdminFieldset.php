<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Admin\Form;


use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class AddAdminFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    private $entityManager;

    const LABEL_RELATED_WORKER = "Related Worker: ";
    const ERROR_USERNAME_EMPTY = "The username can't be empty.";
    const ERROR_USERNAME_INVALID_LENGTH = "The username length must be between between 4-15 characters long.";
    const ERROR_USERNAME_EXISTS = "The username already exists, please try another one.";
    const ERROR_USERNAME_INVALID_PATTERN = "The name can only contain letters, numbers, underscores and no spaces between.";
    const ERROR_PASSWORD_EMPTY = "The password can't be empty.";
    const ERROR_PASSWORD_INVALID_LENGTH = "The password length must be between between 4-20 characters long.";

    public function __construct($translator, $entityManager)
    {
        parent::__construct('admin');

        $this->translator = $translator;
        $this->entityManager = $entityManager;


        $this->add(array(
            'name' => 'username',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
        ));

        $this->add(
            array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'relatedWorker',
                'options' => array(
                    'object_manager' => $this->entityManager,
                    'empty_option' => $this->translator->translate('None'),
                    'target_class' => 'Worker\Entity\Worker',
                    'property' => 'surname',
                    'disable_inarray_validator' => true,
                    'label' => $this->translator->translate(self::LABEL_RELATED_WORKER)
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
            'username' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_USERNAME_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_USERNAME_INVALID_LENGTH)
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->entityManager->getRepository('Admin\Entity\Admin'),
                            'fields' => 'username',
                            'messages' => array(
                                'objectFound' => $this->translator->translate(self::ERROR_USERNAME_EXISTS)
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9_]{4,16}$/',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate(self::ERROR_USERNAME_INVALID_PATTERN)
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'password' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate(self::ERROR_PASSWORD_EMPTY)
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'min' => 4,
                            'max' => 20,
                            'messages' => array(
                                \Zend\Validator\StringLength::INVALID => $this->translator->translate(self::ERROR_PASSWORD_INVALID_LENGTH)
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'relatedWorker' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }


} 