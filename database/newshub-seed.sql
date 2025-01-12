DROP SCHEMA IF EXISTS lbaw24142 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw24142;
SET search_path TO lbaw24142;

-----------------------------------------
-- Drop previous schema
-----------------------------------------

DROP TRIGGER IF EXISTS news_content_user_update ON news;
DROP TRIGGER IF EXISTS comment_content_user_update ON comment;
DROP TRIGGER IF EXISTS manage_influencers ON users;
DROP TRIGGER IF EXISTS manage_like_notification ON likes;
DROP TRIGGER IF EXISTS manage_comment_notification ON comment;
DROP TRIGGER IF EXISTS manage_follow_notification ON follow;
DROP TRIGGER IF EXISTS delete_notification_like ON notifications_like;
DROP TRIGGER IF EXISTS delete_notification_comment ON notifications_comment;
DROP TRIGGER IF EXISTS delete_notification_follow ON follow;
DROP TRIGGER IF EXISTS like_news_once ON likes;
DROP TRIGGER IF EXISTS like_comment_once ON likes;
DROP TRIGGER IF EXISTS manage_self_follow ON follow;
DROP TRIGGER IF EXISTS delete_news ON news;
DROP TRIGGER IF EXISTS delete_comment ON comment;
DROP TRIGGER IF EXISTS update_reputation ON likes;
DROP TRIGGER IF EXISTS check_comment_date ON comment;

DROP INDEX IF EXISTS idx_news_date;
DROP INDEX IF EXISTS idx_like_news;
DROP INDEX IF EXISTS idx_notification;
DROP INDEX IF EXISTS idx_news_content;
DROP INDEX IF EXISTS idx_comment_content;

DROP MATERIALIZED VIEW IF EXISTS news_like_counts CASCADE;
DROP MATERIALIZED VIEW IF EXISTS comments_like_counts CASCADE;

DROP FUNCTION IF EXISTS manage_influencers CASCADE;
DROP FUNCTION IF EXISTS manage_like_notification CASCADE;
DROP FUNCTION IF EXISTS manage_comment_notification CASCADE;
DROP FUNCTION IF EXISTS manage_follow_notification CASCADE;
DROP FUNCTION IF EXISTS delete_notification CASCADE;
DROP FUNCTION IF EXISTS delete_notification_follow CASCADE;
DROP FUNCTION IF EXISTS like_news_once CASCADE;
DROP FUNCTION IF EXISTS like_comment_once CASCADE;
DROP FUNCTION IF EXISTS manage_self_follow CASCADE;
DROP FUNCTION IF EXISTS delete_news CASCADE;
DROP FUNCTION IF EXISTS delete_comment CASCADE;
DROP FUNCTION IF EXISTS update_reputation CASCADE;
DROP FUNCTION IF EXISTS check_comment_date CASCADE;

DROP TABLE IF EXISTS notifications_like CASCADE;
DROP TABLE IF EXISTS notifications_comment CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS likes CASCADE;
DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS favorite CASCADE;
DROP TABLE IF EXISTS tag_news CASCADE;
DROP TABLE IF EXISTS follow_tag CASCADE;
DROP TABLE IF EXISTS follow CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS news CASCADE;
DROP TABLE IF EXISTS blocked CASCADE;
DROP TABLE IF EXISTS influencer CASCADE;
DROP TABLE IF EXISTS administrator CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS tag CASCADE;
DROP TABLE IF EXISTS images CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS ask_tag CASCADE;
DROP TYPE IF EXISTS  Notification_types CASCADE;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE Notification_types AS ENUM(
    'Like_notification',
    'Comment_notification',  
    'Follow_notification'
);

-----------------------------------------
-- Tables
-----------------------------------------
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY, 
    token VARCHAR(255) NOT NULL,  
    created_at TIMESTAMP NULL 
);

CREATE TABLE images (
    id SERIAL PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE tag (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    user_name VARCHAR(30) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    reputation INTEGER NOT NULL DEFAULT 0 CHECK (reputation >= 0),
    image_id INTEGER REFERENCES images(id) ON DELETE SET NULL
);

CREATE TABLE administrator (
    id SERIAL PRIMARY KEY,
    adm_name VARCHAR(30) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    adm_password VARCHAR(255) NOT NULL,
    image_id INTEGER REFERENCES images(id) ON DELETE SET NULL
);

CREATE TABLE influencer (
    user_id INTEGER PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    started_date TIMESTAMP DEFAULT now() NOT NULL,
    has_privilege BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE blocked (
    user_id INTEGER PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    blocked_date TIMESTAMP DEFAULT now() NOT NULL,
    appeal BOOLEAN NOT NULL DEFAULT FALSE,
    appeal_description TEXT

    CHECK (
        (appeal = TRUE AND appeal_description IS NOT NULL) OR
        (appeal = FALSE AND appeal_description IS NULL)
    )
);

CREATE TABLE news (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    published_date TIMESTAMP DEFAULT now() NOT NULL,
    author_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    image_id INTEGER REFERENCES images(id) ON DELETE SET NULL,
    movie_id INTEGER
);

CREATE TABLE comment (
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    published_date TIMESTAMP DEFAULT now() NOT NULL,
    news_id INTEGER NOT NULL REFERENCES news(id) ON DELETE CASCADE,
    author_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    img_id INTEGER REFERENCES images(id) ON DELETE SET NULL
);

CREATE TABLE follow (
    follower_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    followed_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (follower_id, followed_id)
);

CREATE TABLE follow_tag (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES  tag(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, tag_id)
);

CREATE TABLE tag_news (
    news_id INTEGER NOT NULL REFERENCES news(id) ON DELETE CASCADE,
    tag_id INTEGER  NOT NULL REFERENCES tag(id) ON DELETE CASCADE,
    PRIMARY KEY (news_id, tag_id)
);

CREATE TABLE ask_tag (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    tag_name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT now() NOT NULL
);

CREATE TABLE favorite (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    news_id INTEGER REFERENCES news(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, news_id)
);

CREATE TABLE report (
    id SERIAL PRIMARY KEY,
    report_description TEXT NOT NULL,
    created_date TIMESTAMP DEFAULT now() NOT NULL,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comment(id) ON DELETE CASCADE,
    news_id INTEGER REFERENCES news(id) ON DELETE CASCADE,

    CHECK (
        (user_id IS NOT NULL AND comment_id IS NULL AND news_id IS NULL) OR
        (user_id IS NULL AND comment_id IS NOT NULL AND news_id IS NULL) OR
        (user_id IS NULL AND comment_id IS NULL AND news_id IS NOT NULL)
    )
);

CREATE TABLE likes (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    news_id INTEGER REFERENCES news(id) ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comment(id) ON DELETE CASCADE,

    CHECK (
        (news_id IS NOT NULL AND comment_id IS NULL) OR
        (news_id IS NULL AND comment_id IS NOT NULL)
    )
);

CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    receiver_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    notification_date TIMESTAMP DEFAULT now() NOT NULL,
    viewed BOOLEAN NOT NULL DEFAULT FALSE,
    notification_type Notification_types NOT NULL
);

CREATE TABLE notifications_comment (
    notification_id INTEGER PRIMARY KEY REFERENCES notifications(id) ON DELETE CASCADE,
    comment_id INTEGER NOT NULL UNIQUE REFERENCES comment(id) ON DELETE CASCADE
);

CREATE TABLE notifications_like (
    notification_id INTEGER PRIMARY KEY REFERENCES notifications(id) ON DELETE CASCADE,
    like_id INTEGER NOT NULL UNIQUE REFERENCES likes(id) ON DELETE CASCADE
);


-----------------------------------------
-- Views
-----------------------------------------

CREATE MATERIALIZED VIEW news_like_counts AS
SELECT 
    news_id,
    COUNT(*) AS like_count
FROM likes
WHERE news_id IS NOT NULL
GROUP BY news_id;

CREATE MATERIALIZED VIEW comments_like_counts AS
SELECT 
    comment_id,
    COUNT(*) AS like_count
FROM likes
WHERE comment_id IS NOT NULL
GROUP BY comment_id;

-----------------------------------------
-- Indexes
-----------------------------------------

CREATE INDEX idx_news_date ON news USING btree (published_date);

CREATE INDEX idx_like_news ON likes USING btree (news_id, sender_id) WHERE news_id IS NOT NULL;

CREATE INDEX idx_notification ON notifications USING btree (receiver_id, viewed);

--- FTS Indexes

-- FTS01

ALTER TABLE news ADD COLUMN ts_content_user TSVECTOR;

CREATE OR REPLACE FUNCTION news_content_user_update() RETURNS TRIGGER AS $$
DECLARE
    user_name_value TEXT;
BEGIN
 SELECT COALESCE(user_name, 'Anonymous') INTO user_name_value FROM users WHERE users.id = NEW.author_id;
 
 IF TG_OP = 'INSERT' THEN
        NEW.ts_content_user = (
            setweight(to_tsvector('english', NEW.content), 'A') ||
            setweight(to_tsvector('english', user_name_value), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.content <> OLD.content OR NEW.author_id <> OLD.author_id) THEN
           NEW.ts_content_user = (
            setweight(to_tsvector('english', NEW.content), 'A') ||
            setweight(to_tsvector('english', user_name_value), 'B')
        );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER news_content_user_update
    BEFORE INSERT OR UPDATE ON news
    FOR EACH ROW 
    EXECUTE PROCEDURE news_content_user_update();

CREATE INDEX idx_news_content ON news USING GIN (ts_content_user);

-- FTS02

ALTER TABLE comment ADD COLUMN ts_content_user TSVECTOR;

CREATE OR REPLACE FUNCTION comment_content_user_update() RETURNS TRIGGER AS $$
DECLARE
    user_name_value TEXT;
BEGIN
    SELECT COALESCE(user_name, 'Anonymous') INTO user_name_value FROM users WHERE users.id = NEW.author_id;

 IF TG_OP = 'INSERT' THEN            
        NEW.ts_content_user = (
            setweight(to_tsvector('english', NEW.content), 'A') ||
            setweight(to_tsvector('english', user_name_value), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.content <> OLD.content OR NEW.author_id <> OLD.author_id) THEN
           NEW.ts_content_user = (
            setweight(to_tsvector('english', NEW.content), 'A') ||
            setweight(to_tsvector('english', user_name_value), 'B')
        );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER comment_content_user_update
    BEFORE INSERT OR UPDATE ON comment
    FOR EACH ROW 
    EXECUTE PROCEDURE comment_content_user_update();

CREATE INDEX idx_comment_content ON comment USING GIN (ts_content_user);

-----------------------------------------
-- Triggers and UDFs
-----------------------------------------

-- TRIGGER01

CREATE FUNCTION manage_influencers()
RETURNS TRIGGER AS $$
DECLARE
    total_users INTEGER;
    total_influencers INTEGER;
    min_influencer RECORD;
    max_user RECORD;
BEGIN
    SELECT COUNT(*) INTO total_users FROM users;
    SELECT COUNT(*) INTO total_influencers FROM influencer;

    SELECT id, reputation
    INTO min_influencer
    FROM users
    WHERE id IN (SELECT user_id FROM influencer)
    ORDER BY reputation ASC
    LIMIT 1;

    IF TG_OP = 'INSERT' THEN
        IF total_influencers < CEIL(0.1 * total_users) THEN
            IF NEW.reputation >= 100 THEN
                INSERT INTO influencer (user_id, started_date, has_privilege)
                VALUES (NEW.id, now(), FALSE);
            END IF;
        ELSIF NEW.reputation > min_influencer.reputation THEN
            INSERT INTO influencer (user_id, started_date, has_privilege)
            VALUES (NEW.id, now(), FALSE);

            DELETE FROM influencer
            WHERE user_id = min_influencer.id;
        END IF; 
    END IF;

    IF TG_OP = 'UPDATE' THEN
        IF total_influencers < CEIL(0.1 * total_users) THEN
                IF NEW.reputation >= 100 AND NEW.id NOT IN (SELECT user_id FROM influencer) THEN
                    INSERT INTO influencer (user_id, started_date, has_privilege)
                    VALUES (NEW.id, now(), FALSE);
                END IF;
        ELSIF NEW.reputation > OLD.reputation AND NEW.id NOT IN (SELECT user_id FROM influencer) THEN
                IF NEW.reputation > min_influencer.reputation THEN
                    INSERT INTO influencer (user_id, started_date, has_privilege)
                    VALUES (NEW.id, now(), FALSE);

                    DELETE FROM influencer
                    WHERE user_id = min_influencer.id;
                END IF;
        ELSIF NEW.reputation < OLD.reputation AND NEW.id IN (SELECT user_id FROM influencer) THEN
            IF NEW.reputation < 100 THEN
                DELETE FROM influencer
                WHERE user_id = NEW.id;

                SELECT id, reputation INTO max_user 
                FROM users 
                WHERE id NOT IN (SELECT user_id FROM influencer) 
                ORDER BY reputation DESC 
                LIMIT 1;
                
                IF max_user IS NOT NULL AND max_user.reputation >= 100 THEN
                    INSERT INTO influencer (user_id, started_date, has_privilege) 
                    VALUES (max_user.id, now(), FALSE);
                END IF;
            ELSIF NEW.reputation < min_influencer.reputation THEN
                    SELECT id, reputation INTO max_user 
                    FROM users 
                    WHERE id NOT IN (SELECT user_id FROM influencer) 
                    ORDER BY reputation DESC 
                    LIMIT 1;
            
                    IF max_user IS NOT NULL AND NEW.reputation < max_user.reputation AND max_user.reputation >= 100 THEN
                            DELETE FROM influencer
                            WHERE user_id = NEW.id;

                            INSERT INTO influencer (user_id, started_date, has_privilege) 
                            VALUES (max_user.id, now(), FALSE);
                    END IF;
            END IF;
        END IF;
    END IF;
    
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER manage_influencers
    AFTER INSERT OR UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION manage_influencers();


-- TRIGGER02

CREATE FUNCTION manage_like_notification() 
RETURNS TRIGGER AS $$
DECLARE
    notification_id INTEGER;
    receiver_id INTEGER;
BEGIN
    IF NEW.news_id IS NOT NULL THEN
        SELECT author_id INTO receiver_id
            FROM news
            WHERE id = NEW.news_id;

        IF receiver_id IS NULL THEN
            RETURN NULL;
        END IF;
    
        INSERT INTO notifications (sender_id, receiver_id, notification_date, viewed, notification_type)
        VALUES (NEW.sender_id, receiver_id, now(), FALSE, 'Like_notification')
        RETURNING id INTO notification_id;

        INSERT INTO notifications_like (notification_id, like_id)
        VALUES (notification_id, NEW.id);
    
    ELSIF NEW.comment_id IS NOT NULL THEN
        SELECT author_id INTO receiver_id
            FROM comment
            WHERE id = NEW.comment_id;

        IF receiver_id IS NULL THEN
            RETURN NULL;
        END IF;

        INSERT INTO notifications (sender_id, receiver_id, notification_date, viewed, notification_type)
        VALUES (NEW.sender_id, receiver_id, now(), FALSE, 'Like_notification')
        RETURNING id INTO notification_id;

        INSERT INTO notifications_like (notification_id, like_id)
        VALUES (notification_id, NEW.id);
    END IF;

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER manage_like_notification
    AFTER INSERT ON likes
    FOR EACH ROW
    EXECUTE FUNCTION manage_like_notification();


-- TRIGGER03

CREATE FUNCTION manage_comment_notification() 
RETURNS TRIGGER AS $$
DECLARE
    notification_id INTEGER;
    receiver_id INTEGER;
BEGIN
    SELECT author_id INTO receiver_id
        FROM news
        WHERE id = NEW.news_id;
    
    INSERT INTO notifications (sender_id, receiver_id, notification_date, viewed, notification_type)
    VALUES (NEW.author_id, receiver_id, now(), FALSE, 'Comment_notification')
    RETURNING id INTO notification_id;

    INSERT INTO notifications_comment (notification_id, comment_id)
    VALUES (notification_id, NEW.id);

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER manage_comment_notification
    AFTER INSERT ON comment
    FOR EACH ROW
    EXECUTE FUNCTION manage_comment_notification();


-- TRIGGER04

CREATE FUNCTION manage_follow_notification() 
RETURNS TRIGGER AS $$
DECLARE
    notification_id INTEGER;
BEGIN
    INSERT INTO notifications (sender_id, receiver_id, notification_date, viewed, notification_type)
    VALUES (NEW.follower_id, NEW.followed_id, now(), FALSE, 'Follow_notification')
    RETURNING id INTO notification_id;

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER manage_follow_notification
    AFTER INSERT ON follow
    FOR EACH ROW
    EXECUTE FUNCTION manage_follow_notification();


-- TRIGGER05

CREATE FUNCTION delete_notification()
RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM notifications
    WHERE id = OLD.notification_id;

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delete_notification_like
    AFTER DELETE ON notifications_like
    FOR EACH ROW
    EXECUTE FUNCTION delete_notification();


-- TRIGGER06    

CREATE TRIGGER delete_notification_comment
    AFTER DELETE ON notifications_comment
    FOR EACH ROW
    EXECUTE FUNCTION delete_notification();

-- TRIGGER07 

CREATE FUNCTION delete_notification_follow()
RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM notifications
    WHERE sender_id = OLD.follower_id AND receiver_id = OLD.followed_id AND notification_type = 'Follow_notification';

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delete_notification_follow
    AFTER DELETE ON follow
    FOR EACH ROW
    EXECUTE FUNCTION delete_notification_follow();


-- TRIGGER08

CREATE FUNCTION like_news_once()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'UPDATE' AND (OLD.sender_id = NEW.sender_id AND OLD.news_id = NEW.news_id) THEN
        RETURN NEW;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM likes
        WHERE sender_id = NEW.sender_id AND news_id = NEW.news_id
    ) THEN
        RAISE EXCEPTION 'User already liked this news';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER like_news_once
    BEFORE INSERT OR UPDATE ON likes
    FOR EACH ROW
    EXECUTE FUNCTION like_news_once();

-- TRIGGER09

CREATE FUNCTION like_comment_once()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'UPDATE' AND (OLD.sender_id = NEW.sender_id AND OLD.comment_id = NEW.comment_id) THEN
        RETURN NEW;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM likes
        WHERE sender_id = NEW.sender_id AND comment_id = NEW.comment_id
    ) THEN
        RAISE EXCEPTION 'User already liked this comment';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER like_comment_once
    BEFORE INSERT OR UPDATE ON likes
    FOR EACH ROW
    EXECUTE FUNCTION like_comment_once();


-- TRIGGER10

CREATE FUNCTION manage_self_follow()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.follower_id = NEW.followed_id THEN
        RAISE EXCEPTION 'User cannot follow themselves';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER manage_self_follow
    BEFORE INSERT OR UPDATE ON follow
    FOR EACH ROW
    EXECUTE FUNCTION manage_self_follow();


-- TRIGGER11

CREATE FUNCTION delete_news()
RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (
        SELECT 1 
        FROM likes 
        WHERE news_id = OLD.id
    ) THEN 
        RAISE EXCEPTION 'News has likes';

    ELSIF EXISTS (
        SELECT 1
        FROM comment
        WHERE news_id = OLD.id
    ) THEN
        RAISE EXCEPTION 'News has comments';
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delete_news
    BEFORE DELETE ON news
    FOR EACH ROW
    EXECUTE FUNCTION delete_news();


-- TRIGGER012

CREATE FUNCTION delete_comment()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM likes
        WHERE comment_id = OLD.id
    ) THEN
        RAISE EXCEPTION 'Comment has likes';
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delete_comment
    BEFORE DELETE ON comment
    FOR EACH ROW
    EXECUTE FUNCTION delete_comment();


-- TRIGGER13

CREATE FUNCTION update_reputation()
RETURNS TRIGGER AS $$
BEGIN 
    IF TG_OP = 'INSERT' THEN
        IF NEW.news_id IS NOT NULL THEN
            UPDATE users u
            SET reputation = u.reputation + 1
            FROM news n
            WHERE n.id = NEW.news_id AND u.id = n.author_id;
        ELSIF NEW.comment_id IS NOT NULL THEN
            UPDATE users u
            SET reputation = u.reputation + 1
            FROM comment c
            WHERE c.id = NEW.comment_id AND u.id = c.author_id; 
        END IF;
    ELSIF TG_OP = 'DELETE' THEN
        IF OLD.news_id IS NOT NULL THEN
            UPDATE users u
            SET reputation = u.reputation - 1
            FROM news n
            WHERE n.id = OLD.news_id AND u.id = n.author_id;
        ELSIF OLD.comment_id IS NOT NULL THEN
            UPDATE users u 
            SET reputation = u.reputation - 1
            FROM comment c
            WHERE c.id = OLD.comment_id AND u.id = c.author_id;
        END IF;
    END IF;

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_reputation
    AFTER INSERT OR DELETE ON likes
    FOR EACH ROW
    EXECUTE FUNCTION update_reputation();


-- TRIGGER14

CREATE FUNCTION check_comment_date()
RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (
        SELECT 1
        FROM news n
        WHERE n.id = NEW.news_id AND n.published_date > NEW.published_date
    ) THEN
        RAISE EXCEPTION 'Comment publication date must be later than the news publication date';
    END IF;
 
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_comment_date
    BEFORE INSERT OR UPDATE ON comment
    FOR EACH ROW
    EXECUTE FUNCTION check_comment_date();

-----------------------------------------
-- Populate the database
-----------------------------------------

INSERT INTO images (image_path) VALUES
('images/avatars/1733267601_avatar1.jpg'),
('images/avatars/1733268690_avatar2.jpg'),
('images/avatars/1734625255_adm-avatar.jpg');

INSERT INTO tag (name) VALUES
('Action'),
('Comedy'),
('Drama'),
('Horror'),
('Thriller'),
('Sci-Fi'),
('Romance'),
('Documentary'),
('Animation'),
('Fantasy'),
('Superhero'),
('True Crime'),
('Movies'), 
('TV Shows');

INSERT INTO users (user_name, email, user_password, reputation, image_id) VALUES -- Password is 123456. Generated using Hash::make('123456')
('movie_buff91', 'movie_buff91@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 50, 1),
('tv_series_queen', 'tv_series_queen@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 160, 2),
('action_fanatic', 'action_fanatic@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 75, NULL),
('horror_lover', 'horror_lover@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 120, NULL),
('comedy_guru', 'comedy_guru@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 40, NULL),
('fantasy_enthusiast', 'fantasy_enthusiast@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 90, NULL),
('film_critic_01', 'film_critic_01@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 80, NULL),
('tvfanatic_89', 'tvfanatic_89@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 60, NULL),
('blockbuster_boy', 'blockbuster_boy@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 100, NULL),
('binge_watcher', 'binge_watcher@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 150, NULL),
('geek_guru', 'geek_guru@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 90, NULL),
('cinema_lover', 'cinema_lover@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 70, NULL),
('series_addict', 'series_addict@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 85, NULL),
('classic_movie_fan', 'classic_movie_fan@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 110, NULL),
('showtime_queen', 'showtime_queen@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 130, NULL),
('movie_enthusiast', 'movie_enthusiast@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 200, NULL),
('film_buff_99', 'film_buff_99@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 55, NULL),
('tv_series_addict', 'tv_series_addict@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 65, NULL),
('popcorn_guy', 'popcorn_guy@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 50, NULL),
('action_movie_fan', 'action_movie_fan@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 120, NULL),
('movie_watcher_77', 'movie_watcher_77@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 75, NULL),
('comedy_freak', 'comedy_freak@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 95, NULL),
('cinemaddict_42', 'cinemaddict_42@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 115, NULL),
('series_buff', 'series_buff@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 80, NULL),
('entertainment_lover', 'entertainment_lover@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 105, NULL),
('tv_show_addict', 'tv_show_addict@example.com', '$2y$10$7W1/udKoJ77y9pDGLwGg4uzhnJiUvA2zpHSJrCbUysqaNyvUW76Fe', 140, NULL);

INSERT INTO administrator (adm_name, email, adm_password, image_id) VALUES -- Password is adm123. Generated using Hash::make('adm123')
('João', 'joao@example.com', '$2y$10$wkkqWV78/ews2L7nXOK/hexC9GSH.ESNePxWFaXeowkjrh4lZwaYm', NULL),
('Carlos', 'carlos@example.com', '$2y$10$wkkqWV78/ews2L7nXOK/hexC9GSH.ESNePxWFaXeowkjrh4lZwaYm', 3),
('Pedro', 'pedro@example.com', '$2y$10$wkkqWV78/ews2L7nXOK/hexC9GSH.ESNePxWFaXeowkjrh4lZwaYm', NULL),
('Manuel', 'manuel@example.com', '$2y$10$wkkqWV78/ews2L7nXOK/hexC9GSH.ESNePxWFaXeowkjrh4lZwaYm', NULL);

INSERT INTO blocked (user_id, blocked_date, appeal, appeal_description) VALUES
(3, '2024-01-01 12:00:00', FALSE, NULL),
(4, '2024-01-02 14:00:00', TRUE, 'I was trying to defend myself from an offensive comment about my family.'),
(10, '2024-01-03 16:00:00', FALSE, NULL);

INSERT INTO news (content, author_id, image_id) VALUES
('The latest Spider-Man movie breaks box office records!', 1, 1),
('Breaking News: The new Game of Thrones prequel is confirmed for 2025!', 2, 2),
('Top 10 Comedy Movies of 2024 that you must watch!', 3, NULL),
('How to watch all the Star Wars movies in chronological order', 4, NULL),
('The scariest horror movies on Netflix right now', 5, NULL),
('New trailer for the upcoming "Dune" movie drops today!', 1, 1),
('Is "The Witcher" Season 3 the best yet? Here’s everything we know', 2, 2),
('Marvel announces new "Fantastic Four" casting lineup', 3, NULL),
('The most anticipated movies of 2025', 4, NULL),
('5 hidden gems on Netflix you may have missed in 2024', 5, NULL),
('Avengers: Secret Wars will introduce new multiverse characters', 1, NULL),
('Why “Breaking Bad” still dominates TV conversations', 2, 2),
('The Oscars 2024: Who will take home Best Picture?', 3, NULL),
('Top 10 best TV shows for binge-watching in 2024', 4, NULL),
('The Best Action Movies to watch on Amazon Prime Video', 5, NULL),
('The future of horror films in 2025: What’s next for the genre?', 1, 1),
('The rise of superhero TV shows and why we can’t get enough of them', 2, 2),
('Will “Stranger Things” end with a movie? Latest rumors inside', 3, NULL),
('The most underrated animated films of all time', 4, NULL),
('10 classic horror movies that will keep you awake all night', 5, NULL),
('How the "John Wick" franchise became a global phenomenon', 1, 1),
('New “Harry Potter” series in the works: What we know so far', 2, 2),
('Star Wars fandom: How the franchise has evolved over the years', 3, NULL),
('The biggest movie flops of 2024: What went wrong?', 4, NULL),
('2024’s best true crime documentaries to stream', 5, NULL),
('“The Mandalorian” season 4: What fans are hoping to see', 1, 1),
('Netflix’s new original series “The Night Agent” breaks records', 2, 2),
('Why the 90s are making a comeback in TV and film', 3, NULL),
('Is it the end of “The Walking Dead” franchise?', 4, NULL),
('Exploring the cultural impact of the “Fast & Furious” saga', 5, NULL),
('Upcoming blockbusters: What to expect from the 2024 summer movie season', 1, 1),
('The rise of K-Dramas: Why more Americans are watching Korean series', 2, 2),
('The secret behind the success of "Stranger Things" and its fan base', 3, NULL),
('Everything we know about the new "Blade Runner" movie', 4, NULL),
('The importance of diverse representation in Hollywood movies', 5, NULL),
('TV shows we’re most excited about in 2024', 1, 1),
('The “James Bond” franchise: What to expect in the next 007 film', 2, 2),
('Best fantasy series to watch if you loved “Game of Thrones”', 3, NULL),
('The transformation of horror movies: How it’s changing in the 21st century', 4, NULL),
('What makes "Friends" still one of the most popular TV shows', 5, NULL);

INSERT INTO comment (content, news_id, author_id, img_id) VALUES
('I’m so excited for this new Spider-Man movie!', 1, 2, 1),
('Can’t believe they’re bringing back Game of Thrones, but with a twist!', 2, 3, NULL),
('Great list! I’ve seen most of them, but some are still on my watchlist.', 3, 4, NULL),
('Star Wars will always be my favorite, love this list!', 4, 5, NULL),
('I’ll never be able to watch horror movies like I used to after seeing that list!', 5, 1, NULL),
('I can’t wait for "Dune"! The visuals in the first movie were stunning.', 1, 2, 1),
('The new trailer for Dune looks even better than the first one. So excited!', 1, 3, 2),
('The Witcher Season 3 is shaping up to be amazing. Can’t wait to see more of Geralt!', 2, 4, NULL),
('I’m hoping for more character development in Season 3. The first two were amazing!', 2, 5, NULL),
('The Marvel "Fantastic Four" casting news has me excited! I wonder who will be playing Reed Richards.', 3, 1, NULL),
('This new "Fantastic Four" movie is going to be huge. Can’t wait to see who’s in it!', 3, 2, 1),
('I’m already counting down the days until the Oscars. So much good content this year!', 4, 3, 2),
('Looking forward to the Oscars, especially to see who wins Best Picture. It’s a tough year!', 4, 4, NULL),
('I love binge-watching TV shows, and 2024 seems to have some great options. Top of the list for me is "The Crown"! ', 5, 5, NULL),
('If you haven’t seen "The Crown," do it! It’s a masterpiece. Binge-worthy for sure.', 5, 1, NULL),
('I’m not sure if I’m ready for the new "Avengers" movie, but I’m curious about the multiverse aspect.', 6, 2, 1),
('I’ve been hearing so much about the Avengers multiverse story. What do you guys think will happen?', 6, 3, 2),
('Breaking Bad still has the best writing of any show I’ve seen. One of the greatest TV shows of all time.', 7, 4, NULL),
('Can’t get enough of Breaking Bad! It never gets old no matter how many times I watch it.', 7, 5, NULL),
('I’m so excited for the Oscars, especially for the performances this year. Can’t wait!', 8, 1, NULL),
('I think this year’s Best Picture race is going to be incredible. There’s so much competition!', 8, 2, 1),
('If you haven’t seen "The Bear" on Hulu, you’re missing out. So good for a binge-watch!', 9, 3, 2),
('I’ll be watching those hidden gems this weekend. Thanks for the recommendations!', 9, 4, NULL),
('It’s crazy how successful Marvel has been. The new "Secret Wars" will be epic!', 10, 5, NULL),
('I’ve been a Marvel fan forever. "Secret Wars" is the next big thing!', 10, 1, NULL),
('I don’t think anything will ever beat "Stranger Things." I hope they do something amazing for the last season!', 11, 2, 1),
('I feel like Stranger Things is getting better and better every season. Let’s see if they can top it!', 11, 3, 2),
('Game of Thrones prequels are going to be a hit! The story is so rich and deep.', 12, 4, NULL),
('Excited for the Game of Thrones prequel! I just hope it lives up to the original.', 12, 5, NULL),
('The top 10 best comedy movies list was spot on! So many good recommendations.', 13, 1, NULL),
('Love this list! Every movie here is a classic. Comedy never gets old.', 13, 2, 1),
('Star Wars has always been iconic. I’m curious how they’ll continue the legacy!', 14, 3, 2),
('Star Wars will forever be my favorite franchise. Hope they do the new movies justice!', 14, 4, NULL);

INSERT INTO follow (follower_id, followed_id) VALUES
(1, 2),
(2, 1),
(2, 3),
(3, 4),
(4, 5),
(5, 1),
(1, 3),
(2, 4),
(2, 5),
(3, 6),
(5, 6),
(6, 7),
(6, 8),
(8, 9);

INSERT INTO follow_tag (user_id, tag_id) VALUES
(1, 11), 
(1, 4),   
(3, 1),  
(4, 3),  
(5, 2),  
(5, 10),
(1, 1),  
(2, 3),
(3, 2),  
(4, 4),
(5, 5),
(6, 1),
(7, 6),  
(8, 7),  
(9, 2),
(10, 3);  

INSERT INTO tag_news (news_id, tag_id) VALUES
(1, 11), 
(2, 2),
(2, 1), 
(3, 2),  
(4, 5),  
(1, 13),  
(2, 14),  
(4, 13),  
(5, 4),  
(6, 13),
(7, 14),
(13, 13),
(14, 14),
(10, 13);

INSERT INTO favorite (user_id, news_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(10, 15),
(11, 14),
(12, 13),
(13, 12),
(14, 11),
(15, 10);

INSERT INTO report (report_description, user_id, comment_id, news_id) VALUES
('Offensive comment about a TV series', 1, NULL, NULL),
('False information about a movie release', NULL, 1, NULL),
('Clickbait headline in the news post', NULL, NULL, 3),
('Inaccurate review of the movie plot', NULL, NULL, 2),
('Offensive language in the comment section', 3, NULL, NULL),
('Clickbait title misleading readers', NULL, NULL, 4),
('Hate speech in a comment about a TV show', NULL, 3, NULL),
('Fake information about the movie plot', NULL, NULL, 5),
('Racist comment in the discussion', NULL, 4, NULL),
('Personal attack in a comment on the news post', NULL, 5, NULL),
('Promotion of illegal content', 9, NULL, NULL),
('Spamming irrelevant comments in the section', 10, NULL, NULL),
('There is a wrong fact used in the news', NULL, NULL, 11);

INSERT INTO likes (sender_id, news_id, comment_id) VALUES
(1, 1, NULL),
(2, 2, NULL),
(3, NULL, 1),
(4, NULL, 2),
(5, 5, NULL),
(10, 10, NULL),
(1, 6, NULL),  
(2, 7, NULL),  
(3, 8, NULL),  
(4, 9, NULL), 
(5, 1, NULL),
(8, NULL, 10),
(10, NULL, 11), 
(1, NULL, 12),  
(2, NULL, 12),  
(3, NULL, 16);

-----------------------------------------
-- end
-----------------------------------------
