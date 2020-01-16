<?php

class DB {
    var $result;
    var $count;
    private $mysqli;

    function __construct($db_host, $db_user, $db_pass, $db_name) {
        $this->mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if (!$this->mysqli) {
           echo 'Connect failed';
           exit(0);
        } else {
            $this->mysqli->set_charset('utf-8');
        }
    }

    public function addUser($email, $username, $name, $password, $date) {
        if (!$this->checkEmail($email)) {
            return FALSE;
        }
        $sql = "INSERT INTO `users` (`id`, `username`, `password`, `avatar`, `isAdmin`, `name`, `email`, `created_at`, `updated_at`)
                 VALUES (NULL, ? , MD5(?), '/img/avatar/default.jpg', '0', ?, ?, ?, '')";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('sssss', $username, $password, $name, $email, $date)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkEmail($email) {
        $sql = "SELECT `username` FROM `users` WHERE `email`=?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('s', $email)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                $stmt->free_result();
                return TRUE;
            }
        }
        return FALSE;
    }

    public function checkUser($email, $password) {
        $sql = "SELECT `id`,`username`,`name`,`isAdmin` FROM `users` WHERE `email`=? AND `password`=MD5(?)";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('ss', $email, $password)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $isAdmin);
            $stmt->store_result();
            while($stmt->fetch());
            if ($stmt->num_rows > 0) {
                return ['id'=>$id, 'username'=>$username, 'name'=>$name, 'isAdmin'=>$isAdmin];
            }
            return FALSE;
        }
        return FALSE;
    }

    public function checkUsername($username) {
        $sql = "SELECT `id`,`username`,`name` FROM `users` WHERE `username`=?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('s', $username)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($id,$username,$name);
            if ($stmt->num_rows > 0) {
                while($stmt->fetch());
                $stmt->free_result();
                return ['id'=>$id, 'username'=>$username, 'name'=>$name];
            }
        }
        return FALSE;
    }

    public function getApproveFollow($id) {
        $sql = "SELECT `name`, `username` FROM `users` INNER JOIN `user_friends` ON `users`.`id`=`user_friends`.`user_friend_id` WHERE `user_id`=? AND `status`=0";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('i', $id)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            $stmt->bind_result($name, $username);
            $data = [];
            while($stmt->fetch()) {
                array_push($data, ['name'=>$name, 'username'=>$username]);
            }
            return $data;
        }
        return FALSE;
    }

    public function getStatusFollow($user_id, $user_follow) {
        $sql = "SELECT `status` FROM `user_friends` WHERE `user_id`=$user_id AND `user_friend_id`=$user_follow";
        $result = $this->mysqli->query($sql);
        if ($result) {
            if ($result->num_rows > 0) {
                return (int)$result->fetch_assoc()['status'];
            }
            return -1;
        }
        return FALSE;
    }

    public function addFollow($user_id, $user_follow, $date) {
        $sql = "INSERT INTO `user_friends` (`user_id`, `user_friend_id`, `status`, `created_at`) VALUES ($user_id, $user_follow, 0, '$date')";
        $result = $this->mysqli->query($sql);
        if ($result) {
            return TRUE;
        }
        return FALSE;
    }

    public function updateFollow($user_id, $user_follow, $status, $date) {
        $sql = "UPDATE `user_friends` SET `status`=$status, `updated_at`='$date' WHERE `user_id`=$user_id AND `user_friend_id`=$user_follow";
        $result = $this->mysqli->query($sql);
        if ($result) {
            return TRUE;
        }
        return FALSE;
    }

    public function getPosts($userID, $isPrivate) {
        if ($isPrivate == 1) {
            $sql = "SELECT `posts`.`id` as 'post_id',`username`,`name`,`content`,count_like(`posts`.`id`) as 'likes' 
                    FROM `posts`
                    INNER JOIN `users` ON `users`.`id`=`posts`.`user_id` 
                    WHERE `posts`.`user_id`=? 
                    ORDER BY `posts`.`id` DESC";
        } else {
            $sql = "SELECT `posts`.`id` as 'post_id',`username`,`name`,`content`,count_like(`posts`.`id`) as 'likes' 
                    FROM `posts`
                    INNER JOIN `users` ON `users`.`id`=`posts`.`user_id` 
                    WHERE `posts`.`user_id`=? AND `isPrivate`=0 
                    ORDER BY `posts`.`id` DESC";
        }
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('i', $userID)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            $stmt->bind_result($post_id, $username, $name, $content, $likes);
            $data = [];
            while ($stmt->fetch()) {
                array_push($data, ['post_id'=>$post_id, 'username'=>$username, 'name'=>$name, 'content'=>$content, 'likes'=>$likes]);
            }
            return $data;
        }
        return FALSE;
    }

    public function getPublicPosts() {
        $sql = "SELECT `posts`.`id` as 'post_id', `content`, `name`, `username`,`avatar`, count_like(`posts`.`id`) as 'likes'
                FROM `posts` INNER JOIN `users` ON `posts`.`user_id`=`users`.`id`
                WHERE `isPrivate`=0
                ORDER BY `posts`.`id` DESC";
        $result = $this->mysqli->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($data, $row);
            }
        }
        echo $this->mysqli->error;
        return $data;
    }

    public function getPrivatePosts($userID) {
        $sql = "SELECT `posts`.`id` as 'post_id', `content`, `name`, `username`,`avatar`, count_like(`posts`.`id`) as 'likes'
                FROM `posts` INNER JOIN `users` ON `posts`.`user_id`=`users`.`id`
                WHERE `user_id`=$userID AND `isPrivate`=1
                ORDER BY `posts`.`id` DESC";
        $result = $this->mysqli->query($sql);
        $data = [];
        if ($result) {
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    array_push($data, $row);
                }
            }
            return $data;
        }
        return FALSE;
    }

    public function addPost($user_id, $content, $isPrivate, $date) {
        $sql = "INSERT INTO `posts` (`id`, `user_id`, `content`, `isPrivate`, `created_at`) VALUES (NULL, ?, ?, ? , ?)";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('ssss', $user_id, $content, $isPrivate, $date)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    public function getStatusPost($id) {
        $sql = "SELECT `isPrivate` FROM `posts` WHERE `id`=$id";
        $result = $this->mysqli->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return (int)$row['isPrivate'];
        }
        echo $this->mysqli->error;
        return FALSE;
    }

    public function addLike($post_id, $user_id, $date) {
        $sql = "INSERT INTO `likes` (`post_id`,`user_id`,`status`,`created_at`) VALUES (?, ?, 1, ?)";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('iis', $post_id, $user_id, $date)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkLike($post_id, $user_id) {
        $sql = "SELECT `status` FROM `likes` WHERE `post_id`=$post_id AND `user_id`=$user_id";
        $result = $this->mysqli->query($sql);
        if ($result) {
            if ($result->num_rows > 0) {
                $row=$result->fetch_assoc();
                return (int)$row['status'];
            } else {
                return -1;
            }
        }
        return FALSE;
    }

    public function updateLike($post_id, $user_id, $status, $date) {
        $sql = "UPDATE `likes` SET `status`=$status,`updated_at`='$date' WHERE `post_id`=$post_id AND `user_id`=$user_id";
        if ($this->mysqli->query($sql)) {
            return TRUE;
        }
        return FALSE;
    }
    
    public function countLike($id) {
        $sql = "SELECT COUNT(`likes`.`user_id`) as 'likes' FROM `likes` WHERE `status`=1 AND `post_id`=$id";
        $result = $this->mysqli->query($sql);
        if ($result) {
            return (int)$result->fetch_assoc()['likes'];
        }
        return FALSE;
    }

    public function getUserIDByPostID($id) {
        $sql = "SELECT `user_id` FROM `posts` WHERE `id`=$id";
        $result = $this->mysqli->query($sql);
        if ($result) {
            return (int)$result->fetch_assoc()['user_id'];
        }
        return FALSE;
    }

    public function getUserIDByUsername($username) {
        $sql = "SELECT `id` FROM `users` WHERE `username`=?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('s', $username)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            $stmt->bind_result($id);
            $stmt->fetch();
            return $id;
        }
        return FALSE;
    }

    public function getInfoUser($id) {
        $sql = "SELECT * FROM `users` WHERE `id`=$id";
        $result = $this->mysqli->query($sql);
        if (!$result || $result->num_rows < 1) {
            return FALSE;
        }
        return $result->fetch_assoc();
    }


    /**
     * Report:
     *  - status:
     *      + 0: report đang chờ xác nhận
     *      + 1: report đã xác nhận
     *      + 2: report bị từ chối
     *  - type:
     *      + 1: broken link
     *      + 2: inappropriate
     *      + 3: spam
     */
    public function addReport($user_id, $post_id, $type, $date) {
        $sql = "INSERT INTO `report` (`user_id`, `post_id`, `status`, `type`, `created_at`) VALUES ($user_id, $post_id, 0, $type,'$date')";
        $result = $this->mysqli->query($sql);

        if (!$result) {
            echo $this->mysqli->error;
            return FALSE;
        }
        return TRUE;
    }

    public function updateReport($user_id, $post_id, $status, $type, $date) {
        $sql = "UPDATE `report` SET `type`=$type, `status`=$status, `updated_at`='$date' WHERE `user_post`=$user_id AND `post_id`=$post_id";
        $result = $this->mysqli->query($sql);
        
        if (!$result) {
            return FALSE;
        }
        return TRUE;
    }

    public function updateReportById($id, $status, $date) {
        $sql = "UPDATE `report` SET `status`=$status, `updated_at`='$date' WHERE id=?";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt || !$stmt->bind_param('i', $id)) {
            return FALSE;
        }
        if ($stmt->execute()) {
            return TRUE;
        }
        return FALSE;
    }

    public function checkReport($user_id, $post_id) {
        $sql = "SELECT id FROM `report` WHERE `user_id`=$user_id AND `post_id`=$post_id";
        $result = $this->mysqli->query($sql);

        if (!$result) {
            return FALSE;
        }
        return (int)$result->num_rows;
    }

    public function getReports() {
        $sql = "SELECT `report`.`id`,`content`,`name` as 'reporter',`type`,getUserNameFromPostId(`posts`.`id`) as `poster` 
                FROM `report`
                INNER JOIN `posts` ON `posts`.`id`=`report`.`post_id`
                INNER JOIN `users` ON `users`.`id`=`report`.`user_id`
                WHERE `report`.`status`=0";
        $result = $this->mysqli->query($sql);

        if (!$result || $result->num_rows == 0) {
            return [];
        }
        $data = [];
        while(($row=$result->fetch_assoc())) {
            array_push($data,$row);
        }
        return $data;
    }
}
?>