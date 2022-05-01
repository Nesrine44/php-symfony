<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Canvas
 *
 * @ORM\Table(name="canvas")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CanvasRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Canvas
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Innovation", inversedBy="canvas")
     * @ORM\JoinColumn(name="innovation_id", referencedColumnName="id")
     */
    protected $innovation;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;


    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updated_at;


    /**
     * @var string
     *
     * @ORM\Column(name="block_1_a", type="text", length=65535, nullable=true)
     */
    private $block_1_a;


    /**
     * @var string
     *
     * @ORM\Column(name="block_1_b", type="text", length=65535, nullable=true)
     */
    private $block_1_b;


    /**
     * @var string
     *
     * @ORM\Column(name="block_1_c", type="text", length=65535, nullable=true)
     */
    private $block_1_c;


    /**
     * @var string
     *
     * @ORM\Column(name="block_2_a", type="text", length=65535, nullable=true)
     */
    private $block_2_a;


    /**
     * @var string
     *
     * @ORM\Column(name="block_2_b", type="text", length=65535, nullable=true)
     */
    private $block_2_b;


    /**
     * @var string
     *
     * @ORM\Column(name="block_2_c", type="text", length=65535, nullable=true)
     */
    private $block_2_c;


    /**
     * @var string
     *
     * @ORM\Column(name="block_3_a", type="text", length=65535, nullable=true)
     */
    private $block_3_a;


    /**
     * @var string
     *
     * @ORM\Column(name="block_3_b", type="text", length=65535, nullable=true)
     */
    private $block_3_b;


    /**
     * @var string
     *
     * @ORM\Column(name="block_3_c", type="text", length=65535, nullable=true)
     */
    private $block_3_c;


    /**
     * @var string
     *
     * @ORM\Column(name="block_4_a", type="text", length=65535, nullable=true)
     */
    private $block_4_a;


    /**
     * @var string
     *
     * @ORM\Column(name="block_4_b", type="text", length=65535, nullable=true)
     */
    private $block_4_b;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created_at
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updated_at = new \DateTime();
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Canvas
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Canvas
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set block1A.
     *
     * @param string|null $block1A
     *
     * @return Canvas
     */
    public function setBlock1A($block1A = null)
    {
        $this->block_1_a = $block1A;

        return $this;
    }

    /**
     * Get block1A.
     *
     * @return string|null
     */
    public function getBlock1A()
    {
        return $this->block_1_a;
    }

    /**
     * Set block1B.
     *
     * @param string|null $block1B
     *
     * @return Canvas
     */
    public function setBlock1B($block1B = null)
    {
        $this->block_1_b = $block1B;

        return $this;
    }

    /**
     * Get block1B.
     *
     * @return string|null
     */
    public function getBlock1B()
    {
        return $this->block_1_b;
    }

    /**
     * Set block1C.
     *
     * @param string|null $block1C
     *
     * @return Canvas
     */
    public function setBlock1C($block1C = null)
    {
        $this->block_1_c = $block1C;

        return $this;
    }

    /**
     * Get block1C.
     *
     * @return string|null
     */
    public function getBlock1C()
    {
        return $this->block_1_c;
    }

    /**
     * Set block2A.
     *
     * @param string|null $block2A
     *
     * @return Canvas
     */
    public function setBlock2A($block2A = null)
    {
        $this->block_2_a = $block2A;

        return $this;
    }

    /**
     * Get block2A.
     *
     * @return string|null
     */
    public function getBlock2A()
    {
        return $this->block_2_a;
    }

    /**
     * Set block2B.
     *
     * @param string|null $block2B
     *
     * @return Canvas
     */
    public function setBlock2B($block2B = null)
    {
        $this->block_2_b = $block2B;

        return $this;
    }

    /**
     * Get block2B.
     *
     * @return string|null
     */
    public function getBlock2B()
    {
        return $this->block_2_b;
    }

    /**
     * Set block2C.
     *
     * @param string|null $block2C
     *
     * @return Canvas
     */
    public function setBlock2C($block2C = null)
    {
        $this->block_2_c = $block2C;

        return $this;
    }

    /**
     * Get block2C.
     *
     * @return string|null
     */
    public function getBlock2C()
    {
        return $this->block_2_c;
    }

    /**
     * Set block3A.
     *
     * @param string|null $block3A
     *
     * @return Canvas
     */
    public function setBlock3A($block3A = null)
    {
        $this->block_3_a = $block3A;

        return $this;
    }

    /**
     * Get block3A.
     *
     * @return string|null
     */
    public function getBlock3A()
    {
        return $this->block_3_a;
    }

    /**
     * Set block3B.
     *
     * @param string|null $block3B
     *
     * @return Canvas
     */
    public function setBlock3B($block3B = null)
    {
        $this->block_3_b = $block3B;

        return $this;
    }

    /**
     * Get block3B.
     *
     * @return string|null
     */
    public function getBlock3B()
    {
        return $this->block_3_b;
    }

    /**
     * Set block3C.
     *
     * @param string|null $block3C
     *
     * @return Canvas
     */
    public function setBlock3C($block3C = null)
    {
        $this->block_3_c = $block3C;

        return $this;
    }

    /**
     * Get block3C.
     *
     * @return string|null
     */
    public function getBlock3C()
    {
        return $this->block_3_c;
    }

    /**
     * Set block4A.
     *
     * @param string|null $block4A
     *
     * @return Canvas
     */
    public function setBlock4A($block4A = null)
    {
        $this->block_4_a = $block4A;

        return $this;
    }

    /**
     * Get block4A.
     *
     * @return string|null
     */
    public function getBlock4A()
    {
        return $this->block_4_a;
    }

    /**
     * Set block4B.
     *
     * @param string|null $block4B
     *
     * @return Canvas
     */
    public function setBlock4B($block4B = null)
    {
        $this->block_4_b = $block4B;

        return $this;
    }

    /**
     * Get block4B.
     *
     * @return string|null
     */
    public function getBlock4B()
    {
        return $this->block_4_b;
    }

    /**
     * Set innovation.
     *
     * @param \AppBundle\Entity\Innovation|null $innovation
     *
     * @return Canvas
     */
    public function setInnovation(\AppBundle\Entity\Innovation $innovation = null)
    {
        $this->innovation = $innovation;

        return $this;
    }

    /**
     * Get innovation.
     *
     * @return \AppBundle\Entity\Innovation|null
     */
    public function getInnovation()
    {
        return $this->innovation;
    }

    /**
     * Get active block.
     *
     * @return string
     */
    public function getActiveBlock()
    {
        $active_block = '0';
        $blocks_to_complete = $this->getBlocksToComplete();
        if ($blocks_to_complete['4-2']) {
            $active_block = '4-2';
        }
        if ($blocks_to_complete['4-1']) {
            $active_block = '4-1';
        }
        if ($blocks_to_complete['3']) {
            $active_block = '3';
        }
        if ($blocks_to_complete['2']) {
            $active_block = '2';
        }
        if ($blocks_to_complete['1']) {
            $active_block = '1';
        }
        return $active_block;
    }


    /**
     * Get blocks to complete.
     *
     * @return array
     */
    public function getBlocksToComplete()
    {
        return array(
            '1' => (!$this->getBlock1A() && !$this->getBlock1B() && !$this->getBlock1C()),
            '2' => (!$this->getBlock2A() && !$this->getBlock2B() && !$this->getBlock2C()),
            '3' => (!$this->getBlock3A() && !$this->getBlock3B() && !$this->getBlock3C()),
            '4-1' => (!$this->getBlock4A()),
            '4-2' => (!$this->getBlock4B())
        );
    }


    /**
     * Update with datas_array.
     *
     * @param array $datas_array
     */
    public function updateWithDatas($datas_array)
    {
        foreach ($datas_array as $data) {
            if (!array_key_exists('name', $data) || !array_key_exists('value', $data)) {
                continue;
            }
            $value = Settings::getXssCleanString($data['value']);
            switch ($data['name']) {
                case 'title':
                    $this->setTitle($value);
                    break;
                case 'description':
                    $this->setDescription($value);
                    break;
                case 'block_1_a':
                    $this->setBlock1A($value);
                    break;
                case 'block_1_b':
                    $this->setBlock1B($value);
                    break;
                case 'block_1_c':
                    $this->setBlock1C($value);
                    break;
                case 'block_2_a':
                    $this->setBlock2A($value);
                    break;
                case 'block_2_b':
                    $this->setBlock2B($value);
                    break;
                case 'block_2_c':
                    $this->setBlock2C($value);
                    break;
                case 'block_3_a':
                    $this->setBlock3A($value);
                    break;
                case 'block_3_b':
                    $this->setBlock3B($value);
                    break;
                case 'block_3_c':
                    $this->setBlock3C($value);
                    break;
                case 'block_4_a':
                    $this->setBlock4A($value);
                    break;
                case 'block_4_b':
                    $this->setBlock4B($value);
                    break;
            }
        }
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray()
    {
        $ret = array();
        $ret['id'] = $this->getId();
        $ret['active_block'] = $this->getActiveBlock();
        $ret['to_complete'] = $this->getBlocksToComplete();
        $ret['created_at'] = ($this->getCreatedAt()) ? $this->getCreatedAt()->getTimestamp() : null;
        $ret['innovation_id'] = ($this->getInnovation()) ? $this->getInnovation()->getId() : null;

        $ret['title'] = ($this->getTitle()) ? $this->getTitle() : '';
        $ret['description'] = ($this->getDescription()) ? $this->getDescription() : '';

        $ret['block_1_a'] = ($this->getBlock1A()) ? $this->getBlock1A() : '';
        $ret['block_1_b'] = ($this->getBlock1B()) ? $this->getBlock1B() : '';
        $ret['block_1_c'] = ($this->getBlock1C()) ? $this->getBlock1C() : '';

        $ret['block_2_a'] = ($this->getBlock2A()) ? $this->getBlock2A() : '';
        $ret['block_2_b'] = ($this->getBlock2B()) ? $this->getBlock2B() : '';
        $ret['block_2_c'] = ($this->getBlock2C()) ? $this->getBlock2C() : '';

        $ret['block_3_a'] = ($this->getBlock3A()) ? $this->getBlock3A() : '';
        $ret['block_3_b'] = ($this->getBlock3B()) ? $this->getBlock3B() : '';
        $ret['block_3_c'] = ($this->getBlock3C()) ? $this->getBlock3C() : '';

        $ret['block_4_a'] = ($this->getBlock4A()) ? $this->getBlock4A() : '';
        $ret['block_4_b'] = ($this->getBlock4B()) ? $this->getBlock4B() : '';

        return $ret;
    }
}
