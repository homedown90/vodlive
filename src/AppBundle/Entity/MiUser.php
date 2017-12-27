<?php

namespace AppBundle\Entity;

/**
 * User
 */
class MiUser
{
    /**
     * @var int
     */
    private $id=1;

    /**
     * @var string
     */
    private $account;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var integer
     */
    private $type = 2;

    /**
     * @var integer
     */
    private $mi_account_id;

    /**
     * @var \DateTime
     */
    private $create_time;

    /**
     * @var \DateTime
     */
    private $modified_time;

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
     * Set account
     *
     * @param string $account
     *
     * @return MiUser
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return MiUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return MiUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return MiUser
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return MiUser
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set miAccountId
     *
     * @param integer $miAccountId
     *
     * @return MiUser
     */
    public function setMiAccountId($miAccountId)
    {
        $this->mi_account_id = $miAccountId;

        return $this;
    }

    /**
     * Get miAccountId
     *
     * @return integer
     */
    public function getMiAccountId()
    {
        return $this->mi_account_id;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return MiUser
     */
    public function setCreateTime($createTime)
    {
        $this->create_time = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * Set modifiedTime
     *
     * @param \DateTime $modifiedTime
     *
     * @return MiUser
     */
    public function setModifiedTime($modifiedTime)
    {
        $this->modified_time = $modifiedTime;

        return $this;
    }

    /**
     * Get modifiedTime
     *
     * @return \DateTime
     */
    public function getModifiedTime()
    {
        return $this->modified_time;
    }
}
