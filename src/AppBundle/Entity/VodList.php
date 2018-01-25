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
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string
     */
    private $status = 'closed';

    /**
     * @var string
     */
    private $pictureUrl;

    /**
     * @var string
     */
    private $streams;

    /**
     * @var int|null
     */
    private $creator;

    /**
     * @var int|null
     */
    private $videoId;

    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var \DateTime
     */
    private $modifiedTime;

    /**
     * @var int
     */
    private $classId;

    /**
     * @var int|null
     */
    private $playNum = 0;

    /**
     * @var string
     */
    private $originUrl;

    /**
     * @var string
     */
    private $mediaName;

    /**
     * @var string
     */
    private $mediaUrl;

    /**
     * @var bool
     */
    private $toHls = 0;


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
     * Set title.
     *
     * @param string|null $title
     *
     * @return VodList
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
     * @return VodList
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
     * Set status.
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
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set pictureUrl.
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
     * Get pictureUrl.
     *
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }

    /**
     * Set streams.
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
     * Get streams.
     *
     * @return string
     */
    public function getStreams()
    {
        return $this->streams;
    }

    /**
     * Set creator.
     *
     * @param int|null $creator
     *
     * @return VodList
     */
    public function setCreator($creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return int|null
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set videoId.
     *
     * @param int|null $videoId
     *
     * @return VodList
     */
    public function setVideoId($videoId = null)
    {
        if (empty($videoId))
        {
           return;
        }
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Get videoId.
     *
     * @return int|null
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * Set createTime.
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
     * Get createTime.
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set modifiedTime.
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
     * Get modifiedTime.
     *
     * @return \DateTime
     */
    public function getModifiedTime()
    {
        return $this->modifiedTime;
    }

    /**
     * Set classId.
     *
     * @param int $classId
     *
     * @return VodList
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;

        return $this;
    }

    /**
     * Get classId.
     *
     * @return int
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * Set playNum.
     *
     * @param int|null $playNum
     *
     * @return VodList
     */
    public function setPlayNum($playNum = null)
    {
        if (empty($videoId))
        {
            return;
        }

        $this->playNum = $playNum;

        return $this;
    }

    /**
     * Get playNum.
     *
     * @return int|null
     */
    public function getPlayNum()
    {
        return $this->playNum;
    }

    /**
     * Set originUrl.
     *
     * @param string $originUrl
     *
     * @return VodList
     */
    public function setOriginUrl($originUrl)
    {
        $this->originUrl = $originUrl;

        return $this;
    }

    /**
     * Get originUrl.
     *
     * @return string
     */
    public function getOriginUrl()
    {
        return $this->originUrl;
    }

    /**
     * Set mediaName.
     *
     * @param string $mediaName
     *
     * @return VodList
     */
    public function setMediaName($mediaName)
    {
        $this->mediaName = $mediaName;

        return $this;
    }

    /**
     * Get mediaName.
     *
     * @return string
     */
    public function getMediaName()
    {
        return $this->mediaName;
    }

    /**
     * Set mediaUrl.
     *
     * @param string $mediaUrl
     *
     * @return VodList
     */
    public function setMediaUrl($mediaUrl)
    {
        $this->mediaUrl = $mediaUrl;

        return $this;
    }

    /**
     * Get mediaUrl.
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->mediaUrl;
    }

    /**
     * Set toHls.
     *
     * @param bool $toHls
     *
     * @return VodList
     */
    public function setToHls($toHls)
    {
        if (empty($videoId))
        {
            return;
        }
        $this->toHls = $toHls;

        return $this;
    }

    /**
     * Get toHls.
     *
     * @return bool
     */
    public function getToHls()
    {
        return $this->toHls;
    }
}
