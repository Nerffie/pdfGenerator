<?php
namespace App\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;


/**
 * @MongoDB\Document
 */
class Variable
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $idContrat;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $var;

    public function setVar($var)
    {
        $this->var = $var;
        return $this; // pour chainer les setters
    }

    public function getVar()
    {
        return $this->var;
    }

    public function setIdContrat($idContrat)
    {
        $this->idContrat = $idContrat;
        return $this; // pour chainer les setters
    }

    public function getIdContrat()
    {
        return $this->idContrat;
    }

    public function getId()
    {
        return $this->id;
    }
}