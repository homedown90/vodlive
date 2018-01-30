<?php

namespace AppBundle\Entity;

/**
 * VodFile
 */
class VodFile
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var int
     */
    private $sequence;

    /**
     * @var string
     */
    private $jsMd5;

    /**
     * @var string
     */
    private $phpMd5;

    /**
     * @var bool
     */
    private $isUpload;

    /**
     * @var bool
     */
    private $isMerge;

    /**
     * @var bool
     */
    private $isHls;

    /**
     * @var string
     */
    private $streams;


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
     * Set name.
     *
     * @param string $name
     *
     * @return VodFile
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return VodFile
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set size.
     *
     * @param int $size
     *
     * @return VodFile
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set parentId.
     *
     * @param int $parentId
     *
     * @return VodFile
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set sequence.
     *
     * @param int $sequence
     *
     * @return VodFile
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence.
     *
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set jsMd5.
     *
     * @param string $jsMd5
     *
     * @return VodFile
     */
    public function setJsMd5($jsMd5)
    {
        $this->jsMd5 = $jsMd5;

        return $this;
    }

    /**
     * Get jsMd5.
     *
     * @return string
     */
    public function getJsMd5()
    {
        return $this->jsMd5;
    }

    /**
     * Set phpMd5.
     *
     * @param string $phpMd5
     *
     * @return VodFile
     */
    public function setPhpMd5($phpMd5)
    {
        $this->phpMd5 = $phpMd5;

        return $this;
    }

    /**
     * Get phpMd5.
     *
     * @return string
     */
    public function getPhpMd5()
    {
        return $this->phpMd5;
    }

    /**
     * Set isUpload.
     *
     * @param bool $isUpload
     *
     * @return VodFile
     */
    public function setIsUpload($isUpload)
    {
        $this->isUpload = $isUpload;

        return $this;
    }

    /**
     * Get isUpload.
     *
     * @return bool
     */
    public function getIsUpload()
    {
        return $this->isUpload;
    }

    /**
     * Set isMerge.
     *
     * @param bool $isMerge
     *
     * @return VodFile
     */
    public function setIsMerge($isMerge)
    {
        $this->isMerge = $isMerge;

        return $this;
    }

    /**
     * Get isMerge.
     *
     * @return bool
     */
    public function getIsMerge()
    {
        return $this->isMerge;
    }

    /**
     * Set isHls.
     *
     * @param bool $isHls
     *
     * @return VodFile
     */
    public function setIsHls($isHls)
    {
        $this->isHls = $isHls;

        return $this;
    }

    /**
     * Get isHls.
     *
     * @return bool
     */
    public function getIsHls()
    {
        return $this->isHls;
    }

    /**
     * Set streams.
     *
     * @param string $streams
     *
     * @return VodFile
     */
    public function setStreams($streams)
    {
        $this->streams = $streams;

        return $this;
    }

    /**
     * Get streams.
     *
     * @return string
     */
    public function getStreams()
    {
        return $this->streams;
    }
    /**
     * @var string
     */
    private $save_name;


    /**
     * Set saveName.
     *
     * @param string $saveName
     *
     * @return VodFile
     */
    public function setSaveName($saveName)
    {
        $this->save_name = $saveName;

        return $this;
    }

    /**
     * Get saveName.
     *
     * @return string
     */
    public function getSaveName()
    {
        return $this->save_name;
    }
}
