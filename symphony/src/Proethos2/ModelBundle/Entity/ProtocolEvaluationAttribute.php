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
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="protocol_evaluation_attribute")
 * @ORM\Entity
 */
class ProtocolEvaluationAttribute extends Base
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="members") 
     */ 
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="Protocol", inversedBy="revision")
     * @ORM\JoinColumn(name="protocol_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $protocol;

    /**
     * @ORM\ManyToOne(targetEntity="EvaluationAttribute")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $attribute;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $scoring;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $feedback;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $revisions;

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
     * Set scoring
     *
     * @param integer $scoring
     *
     * @return ProtocolEvaluationAttribute
     */
    public function setScoring($scoring)
    {
        $this->scoring = $scoring;

        return $this;
    }

    /**
     * Get scoring
     *
     * @return integer
     */
    public function getScoring()
    {
        return $this->scoring;
    }

    /**
     * Set feedback
     *
     * @param string $feedback
     *
     * @return ProtocolEvaluationAttribute
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback
     *
     * @return string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set revisions
     *
     * @param string $revisions
     *
     * @return ProtocolEvaluationAttribute
     */
    public function setRevisions($revisions)
    {
        $this->revisions = $revisions;

        return $this;
    }

    /**
     * Get revisions
     *
     * @return string
     */
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * Set member
     *
     * @param \Proethos2\ModelBundle\Entity\User $member
     *
     * @return ProtocolEvaluationAttribute
     */
    public function setMember(\Proethos2\ModelBundle\Entity\User $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \Proethos2\ModelBundle\Entity\User
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set protocol
     *
     * @param \Proethos2\ModelBundle\Entity\Protocol $protocol
     *
     * @return ProtocolEvaluationAttribute
     */
    public function setProtocol(\Proethos2\ModelBundle\Entity\Protocol $protocol = null)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return \Proethos2\ModelBundle\Entity\Protocol
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set attribute
     *
     * @param \Proethos2\ModelBundle\Entity\EvaluationAttribute $attribute
     *
     * @return ProtocolEvaluationAttribute
     */
    public function setAttribute(\Proethos2\ModelBundle\Entity\EvaluationAttribute $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return \Proethos2\ModelBundle\Entity\EvaluationAttribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}
