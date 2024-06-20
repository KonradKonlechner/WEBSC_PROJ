<?php

namespace user;
require_once '../model/User.php';
require_once '../persistence/UserRepository.php';

class UserManagementSystem
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }
 
    public function saveUserAsRegistered(User $user)
    {
        if (!$this->isRegisteredUser($user->getUsername())) {
            $this->repository->addUserToDatabase($user);
        }
    }

    public function updateUser(User $updatedUser)
    {
        if ($this->isRegisteredUser($updatedUser->getUsername())) {
            $this->repository->updateUserProfileData($updatedUser);
        }
    }

    public function isRegisteredUserWithCorrectPassword($usernameToCheck, $passwordToCheck)
    {
        $hashedPasswordFromDb = $this->repository->getHashedPasswordForUsernameAndActiveUser($usernameToCheck);

        if (!is_null($passwordToCheck) && password_verify($passwordToCheck, $hashedPasswordFromDb)) {
            return true;
        }

        return false;
    }

    public function getUserByUsername($usernameToCheck): User
    {
        return $this->repository->getUserByUsername($usernameToCheck);
    }

    public function getAllUsers()
    {
        return$this->repository->getAllUsers();
    }

    public function isRegisteredUser($usernameToCheck): bool
    {
        $count = $this->repository->countUsersByUsername($usernameToCheck);

        return $count > 0;
    }

    public function isRegisteredEmail($emailToCheck): bool
    {
        $count = $this->repository->countUsersByEmail($emailToCheck);

        return $count > 0;
    }

    public function updateUserPassword(string $username, string $newPassword)
    {
        if ($this->isRegisteredUser($username)) {
            $this->repository->updateUserPasswordByUsername($username, $newPassword);
        } else {
            echo "<script>console.log(' User with Username $username does not exist ' );</script>";
        }
    }

    public function isUserInactive(string $username) {
        if ($this->isRegisteredUser($username)) {
            User: $user = $this->getUserByUsername($username);
            if ($user != null) {
                return $user->isInactive();
            }
        }
        return false;
    }

    public function getUserById(string $id): User
    {
        return $this->repository->getUserById($id);
    }
}