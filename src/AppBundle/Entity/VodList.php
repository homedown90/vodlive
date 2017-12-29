<?php

namespace AppBundle\Entity;

/**
 * VodList
 */
class VodList
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $onlineNum;

    /**
     * @var int
     */
    private $reserveNum;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $pictureUrl;

    /**
     * @var string
     */
    private $streams;

    /**
     * @var int
     */
    private $creator;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $modifiedTime;


    /**
     * Get id
     *
     * @return int
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
     * @return VodList
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
     * Set description
     *
     * @param string $description
     *
     * @return VodList
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
     * Set onlineNum
     *
     * @param integer $onlineNum
     *
     * @return VodList
     */
    public function setOnlineNum($onlineNum)
    {
        $this->onlineNum = $onlineNum;

        return $this;
    }

    /**
     * Get onlineNum
     *
     * @return int
     */
    public function getOnlineNum()
    {
        return $this->onlineNum;
    }

    /**
     * Set reserveNum
     *
     * @param integer $reserveNum
     *
     * @return VodList
     */
    public function setReserveNum($reserveNum)
    {
        $this->reserveNum = $reserveNum;

        return $this;
    }

    /**
     * Get reserveNum
     *
     * @return int
     */
    public function getReserveNum()
    {
        return $this->reserveNum;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return VodList
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set pictureUrl
     *
     * @param string $pictureUrl
     *
     * @return VodList
     */
    public function setPictureUrl($pictureUrl)
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    /**
     * Get pictureUrl
     *
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }

    /**
     * Set streams
     *
     * @param string $streams
     *
     * @return VodList
     */
    public function setStreams($streams)
    {
        $this->streams = $streams;

        return $this;
    }

    /**
     * Get streams
     *
     * @return string
     */
    public function getStreams()
    {
        return $this->streams;
    }

    /**
     * Set creator
     *
     * @param integer $creator
     *
     * @return VodList
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return int
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return VodList
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set modifiedTime
     *
     * @param \DateTime $modifiedTime
     *
     * @return VodList
     */
    public function setModifiedTime($modifiedTime)
    {
        $this->modifiedTime = $modifiedTime;

        return $this;
    }

    /**
     * Get modifiedTime
     *
     * @return \DateTime
     */
    public function getModifiedTime()
    {
        return $this->modifiedTime;
    }
}

