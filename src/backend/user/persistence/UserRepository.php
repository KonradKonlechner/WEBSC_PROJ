<?php

namespace user;
use db;

include ('../../config/dbaccess.php');

class UserRepository
{

    public function getUserByUsername($usernameToCheck): User
    {
        $connection = db\DBConnection::getConnection();
        $sqlSelect = "SELECT * FROM users WHERE username = ?";

        $statement = $connection->prepare($sqlSelect);
        $statement->bind_param("s", $usernameToCheck); # character "s" is used due to placeholders of type String

        $fetchedUser = $this->fetchAndBindResult($statement);
        $connection->close();
        return $fetchedUser;
    }

    private function fetchAndBindResult($statement): User
    {
        $statement->execute();
        $statement->bind_result($id, $uName, $password, $sex, $fname, $lname, $email, $isAdmin, $isInactive);
        $statement->fetch();

        $statement->close();

        return User::of( # use constructor-like method to create new instance of user
            $id,    
            $uName,
            $password,
            $sex,
            $fname,
            $lname,
            $email,
            $isAdmin,
            $isInactive
        );
    }

    public function getHashedPasswordForUsernameAndActiveUser($usernameToCheck)
    {
        $connection = db\DBConnection::getConnection();
        # Prepared statement
        $sqlInsert = "SELECT password FROM users WHERE username = ? and is_user_inactive = 0";

        $statement = $connection->prepare($sqlInsert);
        $statement->bind_param("s", $usernameToCheck);

        $statement->execute();
        $statement->bind_result($password);
        $statement->fetch();

        $statement->close();
        $connection->close();

        return $password;
    }

    public function addUserToDatabase(User $user)
    {
        $connection = db\DBConnection::getConnection();

        $userName = $user->getUsername();
        $password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        $sex = $user->getSex();
        $firstname = $user->getName();
        $lastname = $user->getLastname();
        $email = $user->getEmail();

        $sqlInsert = "INSERT INTO users (username, password, sex, firstname, lastname, email) VALUES (?,?,?,?,?,?)";
        
        $statement = $connection->prepare($sqlInsert);
        $statement->bind_param(
            "ssssss", 
            $userName,
            $password,
            $sex,
            $firstname,
            $lastname,
            $email
        );

        $statement->execute();
        $statement->close();
        $connection->close();
    }

    public function updateUserPasswordByUsername(String $username, String $newPassword)
    {
        $connection = db\DBConnection::getConnection();

        $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $sqlInsert = "UPDATE users SET 
                 password = ?
                 WHERE username = ?";

        $statement = $connection->prepare($sqlInsert);
        $statement->bind_param(
            "ss",
            $password_hash,
            $username
        );

        $statement->execute();
        $statement->close();
        $connection->close();
    }

    public function updateUserProfileData(User $user)
    {
        $connection = db\DBConnection::getConnection();

        $userName = $user->getUsername();
        $password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        $sex = $user->getSex();
        $firstname = $user->getName();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $email = $user->getEmail();
        $userInactive = (integer) $user->isInactive();

        $sqlInsert = "UPDATE users SET 
                 sex = ?, firstname = ?, lastname = ?, email = ?, is_user_inactive = ?
                 WHERE username = ?";

        $statement = $connection->prepare($sqlInsert);
        $statement->bind_param(
            "ssssis",
            $sex,
            $firstname,
            $lastname,
            $email,
            $userInactive,
            $userName
        );

        $statement->execute();
        $statement->close();
        $connection->close();
    }

    public function countUsersByUsername($usernameToCheck)
    {
        $sqlInsert = "SELECT COUNT(*) FROM users WHERE username = ?";

        return $this->prepareAndExecuteCountForSqlStatementWithBoundStringParameter($sqlInsert, $usernameToCheck);
    }

    public function countUsersByEmail($emailToCheck)
    {
        $sqlInsert = "SELECT COUNT(*) FROM users WHERE email = ?";

        return $this->prepareAndExecuteCountForSqlStatementWithBoundStringParameter($sqlInsert, $emailToCheck);
    }

    private function prepareAndExecuteCountForSqlStatementWithBoundStringParameter(
        string $sqlInsert,
        string $stringValueToCheck
    ) {
        $connection = db\DBConnection::getConnection();

        $statement = $connection->prepare($sqlInsert);
        $statement->bind_param("s", $stringValueToCheck);

        $statement->execute();
        $statement->bind_result($count);
        $statement->fetch();

        $statement->close();
        $connection->close();
        return $count;
    }

    public function getAllUsers()
    {
        $connection = db\DBConnection::getConnection();

        $sqlInsert = "SELECT * FROM users";

        $statement = $connection->prepare($sqlInsert);

        $statement->execute();
        $allUsers = $this->fetchAllAndBindResult($statement);


        $statement->close();
        $connection->close();
        return $allUsers;
    }

    private function fetchAllAndBindResult(mixed $statement)
    {
        $statement->execute();
        $statement->bind_result($id, $uName, $password, $sex, $fname, $lname, $email, $isAdmin, $isInactive);

        $allUsers = [];
        while($statement->fetch()) {
            $fetched = User::of( # use constructor-like method to create new instance of user
                $id,
                $uName,
                $password,
                $sex,
                $fname,
                $lname,
                $email,
                $isAdmin,
                $isInactive
            );
            $allUsers[] = $fetched;
        };

        return $allUsers;
    }


}