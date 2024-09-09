<?php

namespace TodosApp\Controller;

// Aquí le estamos diciendo al programa dónde vivimos (el espacio de nombres).
use Laminas\View\Model\ViewModel; // Herramienta para mostrar cosas (como vistas).
use TodosApp\Model\TaskTable; // Herramienta para trabajar con las tareas.
use TodosApp\Form\TaskForm; // Formulario para crear/editar tareas.
use TodosApp\Model\Task; // Clase que representa una tarea.

class ToDoController extends \Laminas\Mvc\Controller\AbstractActionController
{
    /*
    Guardamos una mesa (table) donde pondremos nuestras tareas.
    */
    private $table;

    // Cuando nos llamen, recibimos una mesa y la guardamos para usarla después.
    public function __construct(TaskTable $table)
    {
        $this->table = $table;
    }

    // Función para recoger todas las tareas y mostrarlas.
    public function indexAction(): ViewModel
    {
        $tasks = $this->table->fetchAll(); // Recogemos todas las tareas.
        return new ViewModel(['tasks' => $tasks]); // Las enviamos para mostrarlas en una tabla.
    }

    // Función para crear una nueva tarea.
    public function createAction() 
    {
        $form = new TaskForm(); // Creamos un formulario para la tarea.
        $form->get('submit')->setValue('Nuevaaa'); // Cambiamos el botón para que diga "Nuevaaa".

        $request = $this->getRequest(); // Pedimos la solicitud de la página.

        // Si nadie ha enviado nada aún, mostramos el formulario.
        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $task = new Task(); // Creamos una tarea nueva.
        $form->setInputFilter($task->getInputFilter()); // Le decimos al formulario qué reglas seguir.
        $form->setData($request->getPost()); // Tomamos los datos que fueron enviados.

        // Si los datos no son válidos, mostramos el formulario de nuevo.
        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $task->exchangeArray($form->getData()); // Pasamos los datos de la tarea al objeto tarea.
        $this->table->saveTask($task); // Guardamos la tarea.
        return $this->redirect()->toRoute('todo-app'); // Volvemos a la página principal.
    }

    // Función para editar una tarea existente.
    public function editAction() 
    {
        // Tomamos el número de la tarea que queremos editar.
        $id = (int) $this->params()->fromRoute('id', 0);

        // Si el ID es 0, es decir, no existe, redirigimos a la página de crear.
        if (0 === $id) {
            return $this->redirect()->toRoute('todo-app-create', ['action' => 'create']);
        }

        // Intentamos obtener la tarea que queremos editar.
        try {
            $task = $this->table->getTask($id); // Buscamos la tarea.
        } catch (\Exception $e) {
            // Si no encontramos la tarea, volvemos a la página principal.
            return $this->redirect()->toRoute('todo-app', ['action' => 'index']);
        }

        $form = new TaskForm(); // Creamos el formulario para editar la tarea.
        $form->bind($task); // Ponemos los datos de la tarea en el formulario.
        $form->get('submit')->setAttribute('value', 'Editar'); // Cambiamos el botón para que diga "Editar".

        $request = $this->getRequest(); // Pedimos la solicitud de la página.
        $viewData = ['id' => $id, 'form' => $form]; // Datos que enviamos a la vista.

        // Si nadie ha enviado aún la tarea editada, mostramos lo que llevamos.
        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($task->getInputFilter()); // Aplicamos las reglas de validación.
        $form->setData($request->getPost()); // Tomamos los datos enviados por el formulario.

        // Si los datos no son válidos, mostramos lo que llevamos hasta ahora.
        if (!$form->isValid()) {
            return $viewData;
        }

        // Intentamos guardar los cambios en la tarea.
        try {
            $this->table->saveTask($task); // Guardamos los cambios.
        } catch (\Exception $e) {
            \error_log("error updating", $e->getMessage()); // Mostramos si hay un error.
        }

        // Volvemos a la página principal después de guardar.
        return $this->redirect()->toRoute('todo-app', ['action' => 'index']);
    }

    // Función para borrar una tarea.
    public function deleteAction()
    {
        // Tomamos el número de la tarea que queremos borrar.
        $id = (int) $this->params()->fromRoute('id', 0);

        // Intentamos encontrar la tarea.
        try {
            $task = $this->table->getTask($id); // Buscamos la tarea.
        } catch (\Exception $e) {
            // Si no encontramos la tarea, volvemos a la página principal.
            return $this->redirect()->toRoute('todo-app', ['action' => 'index']);
        }

        // Intentamos borrar la tarea.
        try {
            $this->table->deleteTask($task->id); // Borramos la tarea.
        } catch (\Exception $e){
            \error_log("error updating", $e->getMessage()); // Mostramos si hay un error.
        }

        // Volvemos a la página principal después de borrar la tarea.
        return $this->redirect()->toRoute('todo-app', ['action' => 'index']);
    }
}
