<?php

namespace userManagement;

class User
{

    private $id = 1;
    private $username = "admin";
    private $password = "admin";
    private $sex = "Herr";
    private $name = "Admin";
    private $lastname = "LeBoss";
    private $email = "chef.admin@mailmail.com";
    private $isAdmin = true;
    private $isInactive = false;

    public function setAllValues($username, $password, $sex, $name, $lastname, $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->sex = $sex;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->isAdmin = false;
    }

    public static function of($id, $username, $password, $sex, $name, $lastname, $email, $isAdmin, $isInactive): User
    {
        $user = new User();
        $user->id = $id;
        $user->username = $username;
        $user->password = $password;
        $user->sex = $sex;
        $user->name = $name;
        $user->lastname = $lastname;
        $user->email = $email;
        $user->isAdmin = $isAdmin;
        $user->isInactive = $isInactive;

        return $user;
    }

    public function hasUsernameAndPassword($usernameToCompare, $passwordToCompare)
    {
        return $this->username === $usernameToCompare
            && $this->password === $passwordToCompare;
    }


    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    public function isInactive()
    {
        return $this->isInactive;
    }
}