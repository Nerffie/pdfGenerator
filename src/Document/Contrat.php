<?php
namespace App\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;


/**
 * @MongoDB\Document
 */
class Contrat
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $ops;
    /**
     * @MongoDB\Field(type="string")
     */
    protected $nom;

    /*public function setNom($nom)
    {
        $this->nom = $nom;
        return $this; // pour chainer les setters
    }

    public function getNom()
    {
        return $this->nom;
    }*/

    public function setOps($ops)
    {
        $this->ops = $ops;
        return $this; // pour chainer les setters
    }

    public function getOps()
    {
        return $this->ops;
    }

    public function getId()
    {
        return $this->id;
    }
}