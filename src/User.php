<?php
// src model User
/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */

 class User
 {
     /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    public function getId(){
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail(){
        return $this->email;
    }
 }
