<?php

namespace user;

use JsonSerializable;

class User implements JsonSerializable
{

    private $id = 1;
    private $username = "admin";
    private $password = "admin";
    private $sex = "Herr";
    private $name = "Admin";
    private $lastname = "LeBoss";
    private $address = "47th floor";
    private $postal_code = "47001";
    private $city = "Rooftop Penthouse";
    private $email = "chef.admin@mailmail.com";
    private $isAdmin = true;
    private $isInactive = false;

    public function setAllValues($username, $password, $sex, $name, $lastname, $address, $postal_code, $city, $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->sex = $sex;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->address = $address;
        $this->postal_code = $postal_code;
        $this->city = $city;
        $this->email = $email;
        $this->isAdmin = false;
        $this->isInactive = false;
    }

    public static function of($id, $username, $password, $sex, $name, $lastname, $address, $postal_code, $city, $email, $isAdmin, $isInactive): User
    {
        $user = new User();
        $user->id = $id;
        $user->username = $username;
        $user->password = $password;
        $user->sex = $sex;
        $user->name = $name;
        $user->lastname = $lastname;
        $user->address = $address;
        $user->postal_code = $postal_code;
        $user->city = $city;
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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
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

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'sex' => $this->sex,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'email' => $this->email,
            'isAdmin' => $this->isAdmin,
            'isInactive' => $this->isInactive
        ];
    }
}