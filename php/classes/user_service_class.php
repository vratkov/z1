<?php

chdir(dirname(__FILE__));
require_once('../config.php');

class UserService {

    protected $email;
    protected $password;
    protected $db;
    protected $user;

    public function __construct(mysqli $db, $email, $password) {
        $this->db = $db;
        $this->email = $email;
        $this->password = $password;
    }

    protected function checkCredentials() {

        $sql_query = "SELECT * FROM users WHERE email=?";

        $stmt = $this->db->prepare($sql_query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if ($user_data) {
            $submitted_pass = sha1($this->password . SALT);

            if ($submitted_pass == $user_data['password_hash']) {
                return $user_data;
            }
        }
        return False;
    }

    public function login() {
        $user = $this->checkCredentials();
        if ($user) {
            $this->user = $user;
            $_SESSION['user_id'] = $user['id'];
            return $user['id'];
        }
        return False;
    }
}
