CREATE TABLE users (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(50) NOT NULL,
    email varchar(50) NOT NULL,
    username varchar(50) NOT NULL,
    password varchar(255) NOT NULL,
    avatar varchar(255) NOT NULL DEFAULT '/img/avatar/default.jpg',
    isAdmin tinyint(1) NOT NULL DEFAULT 0,
    created_at datetime NOT NULL,
    updated_at datetime,
    CONSTRAINT PK_users PRIMARY KEY (id)
);

CREATE TABLE user_friends (
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    user_friend_id int NOT NULL,
    status tinyint(1) NOT NULL,
    created_at datetime NOT NULL,
    updated_at datetime,
    CONSTRAINT PK_user_friends PRIMARY KEY (id),
    CONSTRAINT FK_user_friends_0 FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT FK_user_friends_1 FOREIGN KEY (user_friend_id) REFERENCES users(id),
    CONSTRAINT UQ_user_friends UNIQUE (user_id, user_friend_id)
);

CREATE TABLE posts (
    id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    content text,
    isPrivate tinyint(1) NOT NULL,
    created_at datetime NOT NULL,
    updated_at datetime,
    CONSTRAINT PK_posts PRIMARY KEY (id),
    CONSTRAINT FK_posts_users FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE likes (
    id int NOT NULL AUTO_INCREMENT,
    post_id int NOT NULL,
    user_id int NOT NULL,
    status tinyint(1) NOT NULL,
    created_at datetime NOT NULL,
    updated_at datetime,
    CONSTRAINT PK_likes PRIMARY KEY (id),
    CONSTRAINT FK_likes_posts FOREIGN KEY (post_id) REFERENCES posts(id),
    CONSTRAINT FK_likes_users FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE report (
    id int NOT NULL AUTO_INCREMENT,
    post_id int NOT NULL,
    user_id int NOT NULL,
    status tinyint(1) NOT NULL,
    type tinyint(1) NOT NULL,
    created_at datetime NOT NULL,
    updated_at datetime,
    CONSTRAINT PK_report PRIMARY KEY (id),
    CONSTRAINT FK_report_posts FOREIGN KEY (post_id) REFERENCES posts(id),
    CONSTRAINT FK_report_users FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE content_report (
    id int NOT  NULL AUTO_INCREMENT,
    report_id int NOT NULL,
    title text NOT NULL,
    content text NOT NULL,
    created_at datetime NOT NULL,
    updated_at datetime,
    CONSTRAINT PK_content_report PRIMARY KEY (id),
    CONSTRAINT FK_content_report FOREIGN KEY (report_id) REFERENCES report(id)
);

DELIMITER //
CREATE FUNCTION count_like(in_post_id int)
RETURNS int
BEGIN
    DECLARE result int;
    SELECT COUNT(`likes`.`user_id`) INTO result
    FROM `likes`
    WHERE `post_id`=in_post_id AND `status`=1;
RETURN result;
END;

//

DELIMITER ;


DELIMITER //
CREATE FUNCTION getUserNameFromPostId(in_post_id int)
RETURNS varchar(255)
BEGIN
    DECLARE result varchar(255);
    SELECT `users`.`name` INTO result
    FROM `users` INNER JOIN `posts` ON `posts`.`user_id`=`users`.`id`
    WHERE `posts`.`id`=in_post_id;
RETURN result;
END;

//

DELIMITER ;
