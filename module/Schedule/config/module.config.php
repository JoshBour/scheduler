<?php
namespace Schedule;

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
            'schedule_add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/schedule/add',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'add'
                    )
                )
            ),
            'schedule_save' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/schedule/save',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'save'
                    )
                )
            ),
            'schedule_remove' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/schedule/remove',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'remove'
                    )
                )
            ),
            'schedule' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/schedule[/from/:startDate/to/:endDate]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'list',
                    ),
                ),
            ),
            'schedule_export' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/schedule/export[/from/:startDate/to/:endDate]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'export',
                    ),
                ),
            ),
            'exceptions' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/exceptions',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Exception',
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
            'changelogs' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/changelog[/from/:startDate/to/:endDate]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Changelog',
                        'action' => 'list',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'entry_add_form' => __NAMESPACE__ . '\Factory\EntryAddFormFactory',
            'exception_add_form' => __NAMESPACE__ . '\Factory\ExceptionAddFormFactory'
        ),
        'invokables' => array(
            'entry_service' => __NAMESPACE__ . '\Service\Entry',
            'exception_service' => __NAMESPACE__ . '\Service\Exception',
            'changelog_service' => __NAMESPACE__ . '\Service\Changelog'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Index' => __NAMESPACE__ . '\Controller\IndexController',
            __NAMESPACE__ . '\Controller\Exception' => __NAMESPACE__ . '\Controller\ExceptionController',
            __NAMESPACE__ . '\Controller\Changelog' => __NAMESPACE__ . '\Controller\ChangelogController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'entry' => __DIR__ . '/../view/partial/entry.phtml'
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
