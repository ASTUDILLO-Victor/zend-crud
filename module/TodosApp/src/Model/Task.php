<?php

namespace TodosApp\Model;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;


class Task 
{
    public $id;
    public $title;
    public $description;
    public $creationDate;
    public $finishDate;
    public $finished;
    private $inputFilter;


    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->description = !empty($data['description']) ? $data['description'] : null;
        $this->creationDate = !empty($data['creation_date']) ? $data['creation_date'] : null;
        $this->finishDate = !empty($data['finish_date']) ? $data['finish_date'] : null;
        $this->finished = !empty($data['finished']) ? $data['finished'] : null;
        // se guardan todos los datos de la tabla task ya sea los datos ya registrados,por registrar ,eliminar o actualizar 
    }
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__ // para los filtros
        ));
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class]
            ]
        ]);//filtro de validacion para que los id sean numericos

        $inputFilter->add([
            'name' => 'title',
            'required' => true,// el titulo es requerido 
            'filters' => [
                ['name' => StripTags::class],// evitar la inyeccion de sql  
                ['name' => StringTrim::class],// evitar los espacios inecesarios 
            ],
            'validators' => [
                [
                    'name' => StringLength::class,// longitud
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,// minimo uno 
                        'max' => 145// maximo 145
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'description',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 5,
                        'max' => 500
                    ],
                ],
            ],
        ]);

        
        return $inputFilter;
    }
    public function getArrayCopy(): array
{
   return [
       'id'     => $this->id,
       'title' => $this->title,
       'description'  => $this->description,
   ];
}
}
