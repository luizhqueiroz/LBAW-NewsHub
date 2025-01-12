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
    image_id INTEGER REFERENCES images(id) ON DELETE SET NULL
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
    
        INSERT INTO notifications (sender_id, receiver_id, notification_date, viewed, notification_type)
        VALUES (NEW.sender_id, receiver_id, now(), FALSE, 'Like_notification')
        RETURNING id INTO notification_id;

        INSERT INTO notifications_like (notification_id, like_id)
        VALUES (notification_id, NEW.id);
    
    ELSIF NEW.comment_id IS NOT NULL THEN
        SELECT author_id INTO receiver_id
            FROM comment
            WHERE id = NEW.comment_id;

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
-- end
-----------------------------------------
