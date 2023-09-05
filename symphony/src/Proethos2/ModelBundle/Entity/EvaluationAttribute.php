<?php

// This file is part of the ProEthos Software.
//
// Copyright 2013, PAHO. All rights reserved. You can redistribute it and/or modify
// ProEthos under the terms of the ProEthos License as published by PAHO, which
// restricts commercial use of the Software.
//
// ProEthos is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the ProEthos License for more details.
//
// You should have received a copy of the ProEthos License along with the ProEthos
// Software. If not, see
// https://github.com/bireme/proethos2/blob/master/LICENSE.txt


namespace Proethos2\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Cocur\Slugify\Slugify;

/**
 * EvaluationAttribute
 *
 * @ORM\Table(name="list_evaluation_attribute")
 * @ORM\Entity
 */
class EvaluationAttribute extends Base
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Call
     *
     * @ORM\ManyToOne(targetEntity="Call")
     * @ORM\JoinColumn(name="call_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $call;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $attribute_help_text;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $description_help_text;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoring1;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoring2;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoring3;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoring4;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $help_text_scoring1;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $help_text_scoring2;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $help_text_scoring3;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    private $help_text_scoring4;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status = true;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale(){
        return $this->locale;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return EvaluationAttribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return EvaluationAttribute
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set scoring1
     *
     * @param integer $scoring1
     *
     * @return EvaluationAttribute
     */
    public function setScoring1($scoring1)
    {
        $this->scoring1 = $scoring1;

        return $this;
    }

    /**
     * Get scoring1
     *
     * @return integer
     */
    public function getScoring1()
    {
        return $this->scoring1;
    }

    /**
     * Set scoring2
     *
     * @param integer $scoring2
     *
     * @return EvaluationAttribute
     */
    public function setScoring2($scoring2)
    {
        $this->scoring2 = $scoring2;

        return $this;
    }

    /**
     * Get scoring2
     *
     * @return integer
     */
    public function getScoring2()
    {
        return $this->scoring2;
    }

    /**
     * Set scoring3
     *
     * @param integer $scoring3
     *
     * @return EvaluationAttribute
     */
    public function setScoring3($scoring3)
    {
        $this->scoring3 = $scoring3;

        return $this;
    }

    /**
     * Get scoring3
     *
     * @return integer
     */
    public function getScoring3()
    {
        return $this->scoring3;
    }

    /**
     * Set scoring4
     *
     * @param integer $scoring4
     *
     * @return EvaluationAttribute
     */
    public function setScoring4($scoring4)
    {
        $this->scoring4 = $scoring4;

        return $this;
    }

    /**
     * Get scoring4
     *
     * @return integer
     */
    public function getScoring4()
    {
        return $this->scoring4;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return EvaluationAttribute
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set call
     *
     * @param \Proethos2\ModelBundle\Entity\Call $call
     *
     * @return EvaluationAttribute
     */
    public function setCall(\Proethos2\ModelBundle\Entity\Call $call = null)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * Get call
     *
     * @return \Proethos2\ModelBundle\Entity\Call
     */
    public function getCall()
    {
        return $this->call;
    }


    /**
     * Set helpTextScoring1
     *
     * @param string $helpTextScoring1
     *
     * @return EvaluationAttribute
     */
    public function setHelpTextScoring1($helpTextScoring1)
    {
        $this->help_text_scoring1 = $helpTextScoring1;

        return $this;
    }

    /**
     * Get helpTextScoring1
     *
     * @return string
     */
    public function getHelpTextScoring1()
    {
        return $this->help_text_scoring1;
    }

    /**
     * Set helpTextScoring2
     *
     * @param string $helpTextScoring2
     *
     * @return EvaluationAttribute
     */
    public function setHelpTextScoring2($helpTextScoring2)
    {
        $this->help_text_scoring2 = $helpTextScoring2;

        return $this;
    }

    /**
     * Get helpTextScoring2
     *
     * @return string
     */
    public function getHelpTextScoring2()
    {
        return $this->help_text_scoring2;
    }

    /**
     * Set helpTextScoring3
     *
     * @param string $helpTextScoring3
     *
     * @return EvaluationAttribute
     */
    public function setHelpTextScoring3($helpTextScoring3)
    {
        $this->help_text_scoring3 = $helpTextScoring3;

        return $this;
    }

    /**
     * Get helpTextScoring3
     *
     * @return string
     */
    public function getHelpTextScoring3()
    {
        return $this->help_text_scoring3;
    }

    /**
     * Set helpTextScoring4
     *
     * @param string $helpTextScoring4
     *
     * @return EvaluationAttribute
     */
    public function setHelpTextScoring4($helpTextScoring4)
    {
        $this->help_text_scoring4 = $helpTextScoring4;

        return $this;
    }

    /**
     * Get helpTextScoring4
     *
     * @return string
     */
    public function getHelpTextScoring4()
    {
        return $this->help_text_scoring4;
    }

    /**
     * Set attributeHelpText
     *
     * @param string $attributeHelpText
     *
     * @return EvaluationAttribute
     */
    public function setAttributeHelpText($attributeHelpText)
    {
        $this->attribute_help_text = $attributeHelpText;

        return $this;
    }

    /**
     * Get attributeHelpText
     *
     * @return string
     */
    public function getAttributeHelpText()
    {
        return $this->attribute_help_text;
    }

    /**
     * Set descriptionHelpText
     *
     * @param string $descriptionHelpText
     *
     * @return EvaluationAttribute
     */
    public function setDescriptionHelpText($descriptionHelpText)
    {
        $this->description_help_text = $descriptionHelpText;

        return $this;
    }

    /**
     * Get descriptionHelpText
     *
     * @return string
     */
    public function getDescriptionHelpText()
    {
        return $this->description_help_text;
    }
}
