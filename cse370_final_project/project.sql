
CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password CHAR(255) NOT NULL
);

CREATE TABLE admin (
    user_id INT PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE student (
    user_id INT PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);


CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seen TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES user(user_id) ON DELETE CASCADE,
    INDEX idx_sender_receiver (sender_id, receiver_id),
    INDEX idx_receiver_seen (receiver_id, seen),
    INDEX idx_sent_at (sent_at)
);


CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50),
    date_time DATETIME,
    title VARCHAR(150),
    description TEXT,
    capacity INT,
    creator_userID INT,
    location VARCHAR(150),
    FOREIGN KEY (creator_userID) REFERENCES user(user_id) ON DELETE SET NULL
);


CREATE TABLE registrations (
    reg_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    rating INT,
    comments TEXT,
    status ENUM('registered', 'waitlisted', 'cancelled') DEFAULT 'registered',
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);


CREATE TABLE items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    user_id INT,
    foundFlag BOOLEAN DEFAULT FALSE,
    lostFlag BOOLEAN DEFAULT FALSE,
    claimFlag VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE SET NULL
);

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    seen BOOLEAN DEFAULT FALSE,
    content TEXT NOT NULL,
    event_id INT,
    item_id INT,
    admin_id INT,
    user_id INT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE SET NULL,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admin(user_id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

