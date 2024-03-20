<?php

namespace userManagement;

class UserRepository
{

    public function getUserByUsername($usernameToCheck): User
    {
        require '../../database/dbaccess.php';
        $sqlSelect = "SELECT * FROM users WHERE username = ?";
        # ? --> placeholder in prepared statement !!! Avoid SQL injection !!!

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
        require '../../database/dbaccess.php';
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
        $userName = $user->getUsername();
        $password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        $sex = $user->getSex();
        $firstname = $user->getName();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        
        require '../../database/dbaccess.php';
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
        $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

        require '../../database/dbaccess.php';
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
        $userName = $user->getUsername();
        $password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        $sex = $user->getSex();
        $firstname = $user->getName();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $email = $user->getEmail();
        $userInactive = (integer) $user->isInactive();

        require '../../database/dbaccess.php';
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
        require '../../database/dbaccess.php';
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
        require '../../database/dbaccess.php';
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