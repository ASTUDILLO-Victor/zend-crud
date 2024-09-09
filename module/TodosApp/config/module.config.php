<?php
namespace TodosApp;

use Laminas\Router\Http\Segment;
use TodosApp\Controller\ToDoController;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
//La siguiente sección es nueva y debe ser agregada a tu archivo
   'router' => [
       'routes' => [
           'todo-app' => [
               'type' => Segment::class,
               'options' => [
                   'route' => '/todo-app[/:action[/:id]]',//la ruta y lo que esta entre[] es opcional tanto la action como la id
                   'constraints' => [
                       'action' => '[a-zA-Z][a-zA-Z0-9_-]*',// esto son las parametros establecidos para los action 
                       'id' => '[1-9]\d*',// y el parametro establecido para las id 
                   ],
                   'defaults' => [
                       'controller' => ToDoController::class,
                       'action' => 'index'// por default abre el index
                   ],
               ]
           ]
       ]
   ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',// me trae los layout
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',// me trae los view
        ]
   ]
];