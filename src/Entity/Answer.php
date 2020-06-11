<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $proposition;

    /**
     * @ORM\Column(type="string")
     */
    private $correction;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $questions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProposition(): ?string
    {
        return $this->proposition;
    }

    public function setProposition(string $proposition): self
    {
        $this->proposition = $proposition;

        return $this;
    }

    public function getCorrection(): ?string
    {
        return $this->correction;
    }

    public function setCorrection(string $correction): self
    {
        $this->correction = $correction;

        return $this;
    }

    public function getQuestions(): ?Question
    {
        return $this->questions;
    }

    public function setQuestions(?Question $questions): self
    {
        $this->questions = $questions;

        return $this;
    }
}
