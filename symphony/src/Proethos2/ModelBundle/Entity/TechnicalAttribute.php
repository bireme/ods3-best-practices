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
 * TechnicalAttribute
 *
 * @ORM\Table(name="list_technical_attribute")
 * @ORM\Entity
 */
class TechnicalAttribute extends Base
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
     * @ORM\Column(type="string", length=1020)
     */
    private $title;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=1020)
     */
    private $subtitle;

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
     * Set title
     *
     * @param string $title
     *
     * @return TechnicalAttribute
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     *
     * @return TechnicalAttribute
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return TechnicalAttribute
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
     * @return TechnicalAttribute
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
}
