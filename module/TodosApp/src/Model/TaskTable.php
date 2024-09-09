<?php

namespace TodosApp\Model; // Decimos en qué espacio de nombres estamos.

use Laminas\Db\TableGateway\TableGatewayInterface; // Usamos una herramienta para trabajar con tablas.
use RuntimeException; // Nos sirve para mostrar errores si algo sale mal.

class TaskTable
{
    /** @var TableGatewayInterface */
    private $tableGateway; // Aquí guardamos nuestra puerta de enlace a la tabla (como la entrada a nuestra base de datos).

    // Cuando creamos una nueva tabla de tareas, le damos una puerta de enlace (tableGateway).
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway; // Guardamos la puerta de enlace para usarla después.
    }

    // Función para obtener todas las tareas de la tabla.
    public function fetchAll()
    {
        return $this->tableGateway->select(); // Tomamos todas las filas de la tabla.
    }

    // Función para obtener una tarea por su ID.
    public function getTask($id)
    {
        $id = (int)$id; // Aseguramos que la ID sea un número entero (si es texto vacío, la convierte a 0).
        $rowset = $this->tableGateway->select(['id' => $id]); // Buscamos la fila en la tabla que tenga esa ID.
        $row = $rowset->current(); // Tomamos la primera fila que encontramos.

        // Si no encontramos ninguna fila con esa ID...
        if (!$row) {
            // Lanzamos un error que dice que no encontramos la tarea con esa ID.
            throw new RuntimeException(sprintf(
                'No se pudo encontrar la fila con el identificador %d',
                $id
            ));
        }

        return $row; // Si todo está bien, devolvemos la tarea encontrada.
    }

    // Función para guardar una tarea en la base de datos.
    public function saveTask(Task $task)
    {
        $now = new \DateTime(); // Creamos una variable con la fecha y hora actual.

        // Creamos un array con los datos que queremos guardar en la tabla.
        $data = [
            'title' => $task->title, // Guardamos el título de la tarea.
            'description' => $task->description, // Guardamos la descripción.
            'creation_date' => $now->format('Y-m-d H:i:s'), // Guardamos la fecha y hora actual como la fecha de creación.
            'finish_date' => $task->finishDate, // Guardamos la fecha en que debería terminar.
            'finished' => 0 // La tarea no está terminada todavía, así que ponemos 0.
        ];

        $id = (int) $task->id; // Aseguramos que la ID de la tarea sea un número entero.

        // Si la ID es 0, significa que es una tarea nueva.
        if ($id === 0) {
            $this->tableGateway->insert($data); // Insertamos los datos en la tabla.
            return; // Terminamos la función.
        }

        // Intentamos obtener la tarea con esa ID.
        try {
            $this->getTask($id); // Si encontramos la tarea, seguimos adelante.
        } catch (\Exception $e) {
            // Si no encontramos la tarea, lanzamos un error diciendo que no existe.
            throw new RuntimeException(sprintf(
                'Cannot update task with identifier %d; does not exist', // La tarea no existe para ser actualizada.
                $id
            ));
        }

        // Si la tarea existe, actualizamos los datos en la tabla.
        $this->tableGateway->update($data, ['id' => $id]);
    }

    // Función para borrar una tarea por su ID.
    public function deleteTask($id)
    {
        // Borramos la fila de la tabla que tiene la ID especificada.
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
