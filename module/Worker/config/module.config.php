<?php
namespace Worker;

return array(
    'doctrine' => array(
        'driver' => array(
            'entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'entity',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'workers' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/workers',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
                            )
                        )
                    ),
                    'remove' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/remove',
                            'defaults' => array(
                                'action' => 'remove'
                            )
                        )
                    )

                )
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'worker_add_form' => __NAMESPACE__ . '\Factory\WorkerAddFormFactory'
        ),
        'invokables' => array(
            'worker_service' => __NAMESPACE__ . '\Service\Worker'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Index' => __NAMESPACE__ . '\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
