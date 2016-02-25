<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'cms-api' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/api',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                    ),
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'auth' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/auth',
                            'defaults' => array(
                                'controller'    => 'Auth',
                            ),
                        ),
                    ),
                    'note' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/note[/page/:page][/items-per-page/:items-per-page][/:id]',
                            'defaults' => array(
                                'controller'    => 'Note',
                            ),
                            'constraints' => array(
                                'items-per-page' => '[0-9]+',
                                'page'           => '[0-9]+',
                                'id'             => '[0-9]+'
                            ),
                        ),
                    ),
                ),
            ),
            'home' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/[:anything]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => Controller\IndexController::class,
            'Application\Controller\Auth' => Controller\AuthController::class,
            'Application\Controller\Note' => Controller\NoteController::class,
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'notes',
                'gc_maxlifetime' => 2500000,
            ),
        ),
        'save_handler' => 'SessionSaveHandler',
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            array(
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
            ),
        ),
    ),
);
