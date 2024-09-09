<?php

namespace TodosApp; // Decimos en qué parte de la aplicación estamos.

use Laminas\Db\Adapter\AdapterInterface; // Usamos algo que nos ayuda a conectarnos a la base de datos.
use Laminas\Db\ResultSet\ResultSet; // Esto sirve para manejar los resultados de una consulta en la base de datos.
use Laminas\Db\TableGateway\TableGateway; // Nos ayuda a hacer operaciones con una tabla en la base de datos.
use Laminas\ModuleManager\Feature\ConfigProviderInterface; // Nos ayuda a manejar la configuración del módulo.
use TodosApp\Model\Task; // Traemos la clase Task desde el modelo, donde está definida la tarea.
use TodosApp\Model\TaskTable; // Traemos la clase TaskTable desde el modelo, que maneja la tabla de tareas.

class Module
{
    // Función que obtiene la configuración del módulo.
    public function getConfig()
    {
        // Incluimos y devolvemos el archivo de configuración.
        return include __DIR__ . '/../config/module.config.php'; // Traemos lo que hay en el archivo 'module.config.php'.
    }

    // Función que configura los servicios (las partes que ayudan a la aplicación a funcionar).
    public function getServiceConfig(): array
    {
        return [
            'factories' => [ // Aquí definimos las fábricas, que crean objetos cuando los necesitamos.
                // Creamos una fábrica para manejar la tabla 'task'.
                'TaskTableGateway' => function ($sm) { // $sm es el ServiceManager, que nos ayuda a crear servicios.
                    $dbAdapter = $sm->get(AdapterInterface::class); // Obtenemos el adaptador de la base de datos.
                    $resultSetPrototype = new ResultSet(); // Creamos un prototipo (modelo) para los resultados de la base de datos.
                    $resultSetPrototype->setArrayObjectPrototype(new Task()); // Le decimos que use el modelo 'Task' para cada fila de la tabla.
                    return new TableGateway('task', $dbAdapter, null, $resultSetPrototype); // Creamos una puerta de enlace (gateway) para la tabla 'task'.
                },
                // Creamos una fábrica para el TaskTable, que maneja las operaciones de la tabla de tareas.
                'TodosApp\Model\TaskTable' => function ($sm) {
                    $tableGateWay = $sm->get('TaskTableGateway'); // Obtenemos la puerta de enlace para la tabla 'task'.
                    return new TaskTable($tableGateWay); // Usamos esa puerta de enlace para crear un TaskTable, que manejará la tabla.
                }
            ]
        ];
    }

    // Función que configura los controladores (las partes que manejan lo que hace la aplicación).
    public function getControllerConfig(): array
    {
        return [
            'factories' => [ // Aquí también usamos fábricas para crear los controladores.
                // Creamos una fábrica para el controlador de tareas (ToDoController).
                Controller\ToDoController::class => function ($container) {
                    return new Controller\ToDoController(
                        $container->get(Model\TaskTable::class) // Obtenemos el TaskTable y lo pasamos al controlador.
                    );
                }
            ]
        ];
    }
}
