<?php

namespace TodosApp\Form;

use Laminas\Form\Form;

class TaskForm extends Form
{
   /** TaskForm constructor. */
   public function __construct()// en este armamos los formulario con los campos que necesitemos agregar
   {
       parent::__construct('task');

       $this->add([
           'name' => 'id',
           'type' => 'hidden'
       ]);
       $this->add([
           'name' => 'title',
           'type' => 'text',
           'options' => [
               'label' => 'Titulo'
           ]
       ]);
       $this->add([
           'name' => 'description',
           'type' => 'text',
           'options' => [
               'label' => 'Descripción'
           ]
       ]);
       $this->add([
          'name' => 'submit',
          'type' => 'submit',
          'attributes' => [
              'value' => 'Go',
              'id' => 'submitbutton'
          ]
       ]);
   }
}