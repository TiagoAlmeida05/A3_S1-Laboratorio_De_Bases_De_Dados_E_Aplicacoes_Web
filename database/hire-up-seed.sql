--
-- Schema selection
--
-- This script can be executed directly in psql/pgAdmin, or from PHP/Laravel.
-- It reads the session setting `app.schema` to decide which schema to target.
--  * If `app.schema` is set (e.g. in Laravel with DB::statement('SET app.schema TO ?')),
--    that schema will be used.
--  * If not set, it falls back to the default schema name "thingy".
--

--
-- Schema (re)creation
-- The DO block is needed because identifiers (schema names) cannot be parameterized.
--
DO $do$
DECLARE
  s text := COALESCE(current_setting('app.schema', true), 'lbaw2552');
BEGIN
  -- identifiers require dynamic SQL
  EXECUTE format('DROP SCHEMA IF EXISTS %I CASCADE', s);
  EXECUTE format('CREATE SCHEMA IF NOT EXISTS %I', s);

  -- set search_path for the rest of the script
  PERFORM set_config('search_path', format('%I, public', s), false);
END
$do$ LANGUAGE plpgsql;

--
-- Create tables.
--
CREATE TYPE notification_type_name AS ENUM ('PlatformAlert', 'Message', 'BookmarkDeadline', 'ApplicationStatus', 'NewJobPosting');
CREATE TYPE job_posting_status AS ENUM('Pending', 'Active', 'Expired', 'Closed');
CREATE TYPE account_status AS ENUM('Active', 'Suspended', 'Deleted');

CREATE TABLE notification_type (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name notification_type_name NOT NULL UNIQUE,
    send_to_registered_user BOOLEAN NOT NULL,
    send_to_job_seeker BOOLEAN NOT NULL,
    send_to_recruiter BOOLEAN NOT NULL,
    send_to_company_manager BOOLEAN NOT NULL,
    send_to_admin BOOLEAN NOT NULL,
    default_enabled BOOLEAN NOT NULL
);

CREATE TABLE registered_user (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    birthday DATE NOT NULL,
    age INT NOT NULL CHECK (age >= 18),
    sign_up_date DATE NOT NULL DEFAULT CURRENT_DATE,
    status account_status NOT NULL
);

CREATE TABLE administrator (
    registered_user_id INT PRIMARY KEY REFERENCES registered_user(id) ON UPDATE CASCADE
);

CREATE TABLE social_media_type (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    default_url VARCHAR(255) UNIQUE NOT NULL,
    illustration TEXT
);

CREATE TABLE tag (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    job_posting_exclusive BOOLEAN NOT NULL
);

CREATE TABLE website_content (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    last_edited_by INT NOT NULL,
    FOREIGN KEY (last_edited_by) REFERENCES administrator(registered_user_id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE country (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE city (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country_id INT NOT NULL,
    FOREIGN KEY (country_id) REFERENCES country(id) ON UPDATE CASCADE
);

CREATE TABLE company (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date_added DATE NOT NULL DEFAULT CURRENT_DATE,
    website VARCHAR(255),
    logo TEXT,
    about_us TEXT,
    city_id INT, 
    FOREIGN KEY (city_id) REFERENCES city(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE department (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    company_id INT,
    FOREIGN KEY (company_id) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE job_seeker (
    registered_user_id INT PRIMARY KEY,
    profile_photo TEXT,
    about_me TEXT,
    website VARCHAR(255),
    cv TEXT,
    show_cv BOOLEAN NOT NULL DEFAULT TRUE,
    city_id INT, 
    FOREIGN KEY (registered_user_id) REFERENCES registered_user(id) ON UPDATE CASCADE,
    FOREIGN KEY (city_id) REFERENCES city(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE recruiter (
    registered_user_id INT PRIMARY KEY,
    is_company_manager BOOLEAN NOT NULL DEFAULT FALSE,
    department_id INT, 
    FOREIGN KEY (registered_user_id) REFERENCES registered_user(id) ON UPDATE CASCADE,
    FOREIGN KEY (department_id) REFERENCES department(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE job_posting (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    creation_date DATE NOT NULL DEFAULT CURRENT_DATE,
    deadline DATE NOT NULL CHECK (deadline >= creation_date + INTERVAL '3 days'),
    min_wage INT,
    max_wage INT CHECK (max_wage IS NULL OR min_wage IS NULL OR max_wage >= min_wage),
    requirements TEXT,
    status job_posting_status NOT NULL,
    recruiter_id INT,
    city_id INT,
    FOREIGN KEY (recruiter_id) REFERENCES recruiter(registered_user_id) ON DELETE SET NULL ON UPDATE CASCADE, 
    FOREIGN KEY (city_id) REFERENCES city(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE application (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    date DATE NOT NULL DEFAULT CURRENT_DATE,
    cover_letter TEXT,
    recommendation_letter TEXT,
    evaluated BOOLEAN NOT NULL DEFAULT FALSE,
    accepted BOOLEAN NOT NULL DEFAULT FALSE,
    job_seeker_id INT,
    job_posting_id INT,
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE,
    FOREIGN KEY (job_posting_id) REFERENCES job_posting(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE (job_seeker_id, job_posting_id),
    CHECK (NOT (evaluated = FALSE AND accepted = TRUE))
);

CREATE TABLE bookmark (
    job_seeker_id INT NOT NULL,
    job_posting_id INT NOT NULL,
    date_added TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (job_seeker_id, job_posting_id),
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE,
    FOREIGN KEY (job_posting_id) REFERENCES job_posting(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE job_posting_tag (
    job_posting_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (job_posting_id, tag_id),
    FOREIGN KEY (job_posting_id) REFERENCES job_posting(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE job_seeker_tag (
    job_seeker_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (job_seeker_id, tag_id),
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE company_tag (
    company_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (company_id, tag_id),
    FOREIGN KEY (company_id) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE notification (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    issue_date TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_date TIMESTAMP WITH TIME ZONE,
    content TEXT NOT NULL,
    notification_type_id INT NOT NULL,
    registered_user_id INT NOT NULL,
    FOREIGN KEY (notification_type_id) REFERENCES notification_type(id) ON UPDATE CASCADE,
    FOREIGN KEY (registered_user_id) REFERENCES registered_user(id) ON UPDATE CASCADE,
    CHECK (read_date IS NULL OR issue_date <= read_date)
);

CREATE TABLE message (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    date_sent TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_read TIMESTAMP WITH TIME ZONE,
    content TEXT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES registered_user(id) ON UPDATE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES registered_user(id) ON UPDATE CASCADE,
    CHECK (sender_id <> receiver_id),
    CHECK (date_read IS NULL OR date_sent <= date_read)
);

CREATE TABLE message_notification (
    message_id INT NOT NULL,
    notification_id INT NOT NULL,
    PRIMARY KEY (message_id),
    FOREIGN KEY (message_id) REFERENCES message(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (notification_id) REFERENCES notification(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE bookmark_notification (
    notification_id INT NOT NULL,
    job_seeker_id INT NOT NULL,
    job_posting_id INT NOT NULL,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (notification_id) REFERENCES notification(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (job_seeker_id, job_posting_id) REFERENCES bookmark(job_seeker_id, job_posting_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE job_posting_notification (
    notification_id INT NOT NULL,
    job_posting_id INT NOT NULL,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (notification_id) REFERENCES notification(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (job_posting_id) REFERENCES job_posting(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE application_notification (
    notification_id INT NOT NULL,
    application_id INT NOT NULL,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (notification_id) REFERENCES notification(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (application_id) REFERENCES application(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE notification_by_admin (
    notification_id INT NOT NULL,
    admin_id INT NOT NULL,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (notification_id) REFERENCES notification(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES administrator(registered_user_id) ON UPDATE CASCADE
);

CREATE TABLE notification_subscription (
    registered_user_id INT NOT NULL,
    notification_type_id INT NOT NULL,
    is_enabled BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (registered_user_id, notification_type_id),
    FOREIGN KEY (registered_user_id) REFERENCES registered_user(id) ON UPDATE CASCADE,
    FOREIGN KEY (notification_type_id) REFERENCES notification_type(id) ON UPDATE CASCADE
);

CREATE TABLE report_to_admin (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    date TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    description TEXT NOT NULL,
    solved BOOLEAN NOT NULL DEFAULT FALSE,
    reporter_id INT NOT NULL,
    handled_by_id INT,
    FOREIGN KEY (reporter_id) REFERENCES registered_user(id) ON UPDATE CASCADE,
    FOREIGN KEY (handled_by_id) REFERENCES administrator(registered_user_id) ON UPDATE CASCADE
);

CREATE TABLE certification_entry (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    issued_by VARCHAR(255),
    date DATE,
    description TEXT,
    certificate_url VARCHAR(255),
    job_seeker_id INT NOT NULL,
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE
);

CREATE TABLE experience_entry (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    position_name VARCHAR(255) NOT NULL,
    employer VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    description TEXT,
    job_seeker_id INT NOT NULL,
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE
);

CREATE TABLE education_entry (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    issued_by VARCHAR(255) NOT NULL,
    start_date DATE,
    end_date DATE NOT NULL,
    additional_info TEXT,
    job_seeker_id INT NOT NULL,
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE,
    CHECK (start_date IS NULL OR start_date <= end_date)
);

CREATE TABLE award_entry (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    issued_by VARCHAR(255),
    date DATE,
    description TEXT,
    job_seeker_id INT NOT NULL,
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE
);

CREATE TABLE social_media_profile (
    id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    social_media_type_id INT NOT NULL,
    company_id INT,
    job_seeker_id INT,
    FOREIGN KEY (social_media_type_id) REFERENCES social_media_type(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (company_id) REFERENCES company(id) ON UPDATE CASCADE,
    FOREIGN KEY (job_seeker_id) REFERENCES job_seeker(registered_user_id) ON UPDATE CASCADE,
    CHECK ((company_id IS NOT NULL) <> (job_seeker_id IS NOT NULL))
);

CREATE INDEX job_posting_city_status_deadline ON job_posting (city_id, status, deadline);
CLUSTER job_posting USING job_posting_city_status_deadline;

CREATE INDEX application_eval_accept ON application (evaluated, accepted);

CREATE INDEX notification_user_read_date_issue_desc ON notification (registered_user_id, read_date, issue_date DESC);

CREATE INDEX registered_user_search_index 
ON registered_user 
USING GIN (to_tsvector('english', name));

ALTER TABLE job_posting
ADD COLUMN tsvectors tsvector;

CREATE FUNCTION update_jp_for_searching()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (setweight(to_tsvector('english', NEW.title), 'A') ||
                        setweight(to_tsvector('english', NEW.description), 'B'));
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.title <> OLD.title OR NEW.description <> OLD.description) THEN
            NEW.tsvectors = (setweight(to_tsvector('english', NEW.title), 'A') ||
                        setweight(to_tsvector('english', NEW.description), 'B'));
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;


CREATE TRIGGER update_jp_for_searching
    BEFORE INSERT OR UPDATE ON job_posting
    FOR EACH ROW
    EXECUTE PROCEDURE update_jp_for_searching();

CREATE INDEX jp_search_index ON job_posting USING GIN (tsvectors);

ALTER TABLE company
ADD COLUMN tsvectors tsvector;

CREATE FUNCTION update_company_for_searching()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = to_tsvector('english', NEW.name);
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.name <> OLD.name) THEN
            NEW.tsvectors = to_tsvector('english', NEW.name);
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;


CREATE TRIGGER update_company_for_searching
    BEFORE INSERT OR UPDATE ON company
    FOR EACH ROW
    EXECUTE PROCEDURE update_company_for_searching();

CREATE INDEX company_search_index ON company USING GIN (tsvectors);

CREATE FUNCTION check_notification_permissions() RETURNS TRIGGER AS $BODY$
DECLARE
    v_user_type TEXT;
    v_send_to_user BOOLEAN;
    v_is_enabled BOOLEAN;
BEGIN
    IF EXISTS (SELECT 1 FROM administrator WHERE registered_user_id = NEW.registered_user_id) THEN
        v_user_type = 'admin';
    ELSIF EXISTS (SELECT 1 FROM recruiter WHERE registered_user_id = NEW.registered_user_id) THEN
        SELECT is_company_manager INTO v_user_type FROM recruiter WHERE registered_user_id = NEW.registered_user_id;
        IF v_user_type THEN
            v_user_type = 'company_manager';
        ELSE
            v_user_type = 'recruiter';
        END IF;
    ELSIF EXISTS (SELECT 1 FROM job_seeker WHERE registered_user_id = NEW.registered_user_id) THEN
        v_user_type = 'job_seeker';
    ELSE
        v_user_type = 'registered_user';
    END IF;
    
    SELECT CASE v_user_type
            WHEN 'admin' THEN send_to_admin
            WHEN 'company_manager' THEN send_to_company_manager
            WHEN 'recruiter' THEN send_to_recruiter
            WHEN 'job_seeker' THEN send_to_job_seeker
            ELSE send_to_registered_user
        END
    INTO v_send_to_user
    FROM notification_type
    WHERE id = NEW.notification_type_id;
    
    IF NOT v_send_to_user THEN
        RAISE EXCEPTION 'Notification type % does not allow sending to user type %', NEW.notification_type_id, v_user_type;
    END IF;
    
    SELECT COALESCE(is_enabled, nt.default_enabled)
    INTO v_is_enabled
    FROM notification_type nt
    LEFT JOIN notification_subscription ns ON ns.notification_type_id = nt.id AND ns.registered_user_id = NEW.registered_user_id
    WHERE nt.id = NEW.notification_type_id;
    
    IF NOT v_is_enabled THEN
        RAISE EXCEPTION 'User % has disabled notification type %', NEW.registered_user_id, NEW.notification_type_id;
    END IF;
    
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_notification_permissions
    BEFORE INSERT ON notification
    FOR EACH ROW
    EXECUTE PROCEDURE check_notification_permissions();

CREATE FUNCTION handle_user_deletion() RETURNS TRIGGER AS $BODY$
DECLARE
    v_is_company_manager BOOLEAN;
    v_company_id INTEGER;
    v_manager_count INTEGER;
BEGIN
    IF EXISTS (SELECT 1 FROM recruiter WHERE registered_user_id = OLD.id) THEN
        SELECT is_company_manager, d.company_id
        INTO v_is_company_manager, v_company_id
        FROM recruiter r
        JOIN department d ON r.department_id = d.id
        WHERE r.registered_user_id = OLD.id;
        
        IF v_is_company_manager THEN
            SELECT COUNT(*)
            INTO v_manager_count
            FROM recruiter r
            JOIN department d ON r.department_id = d.id
            WHERE d.company_id = v_company_id AND r.is_company_manager = TRUE AND r.registered_user_id != OLD.id;
            
            IF v_manager_count = 0 THEN
                DELETE FROM company WHERE id = v_company_id;
            END IF;
        END IF;
    END IF;
    
    RETURN OLD;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER handle_user_deletion
    BEFORE DELETE ON registered_user
    FOR EACH ROW
    EXECUTE PROCEDURE handle_user_deletion();

CREATE FUNCTION check_platform_alert_sender() RETURNS TRIGGER AS $BODY$
DECLARE
    v_notification_type_name notification_type_name;
BEGIN
    SELECT nt.name INTO v_notification_type_name
    FROM notification n
    JOIN notification_type nt ON n.notification_type_id = nt.id
    WHERE n.id = NEW.notification_id;
    
    IF v_notification_type_name != 'PlatformAlert' THEN
        RAISE EXCEPTION 'Only PlatformAlert notifications can be sent by administrators';
    END IF;
    
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_platform_alert_sender
    BEFORE INSERT ON notification_by_admin
    FOR EACH ROW
    EXECUTE PROCEDURE check_platform_alert_sender();

CREATE FUNCTION check_application_uniqueness() RETURNS TRIGGER AS $BODY$
BEGIN
    IF EXISTS (SELECT 1 FROM application WHERE job_seeker_id = NEW.job_seeker_id AND job_posting_id = NEW.job_posting_id AND id != COALESCE(NEW.id, -1)) THEN
        RAISE EXCEPTION 'Job seeker % has already applied to job posting %', NEW.job_seeker_id, NEW.job_posting_id;
    END IF;
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_application_uniqueness
    BEFORE INSERT OR UPDATE ON application
    FOR EACH ROW
    EXECUTE PROCEDURE check_application_uniqueness();

CREATE FUNCTION check_message_permissions() RETURNS TRIGGER AS $BODY$
DECLARE
    v_sender_type TEXT;
    v_receiver_type TEXT;
    v_sender_company INTEGER;
    v_receiver_company INTEGER;
    v_sender_is_manager BOOLEAN;
    v_receiver_is_manager BOOLEAN;
BEGIN
    IF EXISTS (SELECT 1 FROM administrator WHERE registered_user_id = NEW.sender_id) THEN
        RETURN NEW;
    END IF;
    
    IF EXISTS (SELECT 1 FROM job_seeker WHERE registered_user_id = NEW.sender_id) THEN
        v_sender_type = 'job_seeker';
    ELSIF EXISTS (SELECT 1 FROM recruiter WHERE registered_user_id = NEW.sender_id) THEN
        v_sender_type = 'recruiter';
        SELECT is_company_manager, d.company_id
        INTO v_sender_is_manager, v_sender_company
        FROM recruiter r
        JOIN department d ON r.department_id = d.id
        WHERE r.registered_user_id = NEW.sender_id;
    END IF;
    
    IF EXISTS (SELECT 1 FROM job_seeker WHERE registered_user_id = NEW.receiver_id) THEN
        v_receiver_type = 'job_seeker';
    ELSIF EXISTS (SELECT 1 FROM recruiter WHERE registered_user_id = NEW.receiver_id) THEN
        v_receiver_type = 'recruiter';
        SELECT is_company_manager, d.company_id
        INTO v_receiver_is_manager, v_receiver_company
        FROM recruiter r
        JOIN department d ON r.department_id = d.id
        WHERE r.registered_user_id = NEW.receiver_id;
    END IF;
    
    IF v_sender_type = 'job_seeker' THEN
        IF v_receiver_type != 'recruiter' THEN
            RAISE EXCEPTION 'Job seekers can only send messages to recruiters';
        END IF;
    ELSIF v_sender_type = 'recruiter' THEN
        IF v_receiver_type = 'job_seeker' THEN
            RETURN NEW;
        ELSIF v_receiver_type = 'recruiter' THEN
            IF v_sender_company != v_receiver_company THEN
                RAISE EXCEPTION 'Recruiters can only message recruiters from their company';
            END IF;
            IF v_sender_is_manager THEN
                RETURN NEW;
            ELSIF v_receiver_is_manager THEN
                RETURN NEW;
            ELSE
                RAISE EXCEPTION 'Regular recruiters can only message their company manager';
            END IF;
        ELSE
            RAISE EXCEPTION 'Invalid message recipient';
        END IF;
    END IF;
    
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_message_permissions
    BEFORE INSERT ON message
    FOR EACH ROW
    EXECUTE PROCEDURE check_message_permissions();

CREATE FUNCTION enforce_single_company_manager() RETURNS TRIGGER AS $BODY$
DECLARE
    v_company_id INTEGER;
    v_manager_count INTEGER;
BEGIN
    SELECT d.company_id INTO v_company_id
    FROM department d
    WHERE d.id = NEW.department_id;
    
    IF NEW.is_company_manager THEN
        SELECT COUNT(*) INTO v_manager_count
        FROM recruiter r
        JOIN department d ON r.department_id = d.id
        WHERE d.company_id = v_company_id AND r.is_company_manager = TRUE AND r.registered_user_id != NEW.registered_user_id;
        
        IF v_manager_count > 0 THEN
            RAISE EXCEPTION 'Company % already has a manager', v_company_id;
        END IF;
    ELSE
        IF TG_OP = 'UPDATE' THEN
            SELECT COUNT(*) INTO v_manager_count
            FROM recruiter r
            JOIN department d ON r.department_id = d.id
            WHERE d.company_id = v_company_id AND r.is_company_manager = TRUE AND r.registered_user_id != NEW.registered_user_id;
            
            IF v_manager_count = 0 THEN
                RAISE EXCEPTION 'Cannot remove last company manager for company %', v_company_id;
            END IF;
        END IF;
    END IF;
    
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER enforce_single_company_manager
    BEFORE INSERT OR UPDATE ON recruiter
    FOR EACH ROW
    EXECUTE PROCEDURE enforce_single_company_manager();

CREATE FUNCTION check_social_media_ownership() RETURNS TRIGGER AS $BODY$
BEGIN
    IF (NEW.company_id IS NOT NULL AND NEW.job_seeker_id IS NOT NULL) THEN
        RAISE EXCEPTION 'Social media profile cannot belong to both company and job seeker';
    END IF;
    IF (NEW.company_id IS NULL AND NEW.job_seeker_id IS NULL) THEN
        RAISE EXCEPTION 'Social media profile must belong to either a company or job seeker';
    END IF;
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_social_media_ownership
    BEFORE INSERT OR UPDATE ON social_media_profile
    FOR EACH ROW
    EXECUTE PROCEDURE check_social_media_ownership();

CREATE FUNCTION check_tag_exclusivity_job_seeker() RETURNS TRIGGER AS $BODY$
DECLARE
    v_is_exclusive BOOLEAN;
BEGIN
    SELECT job_posting_exclusive INTO v_is_exclusive FROM tag WHERE id = NEW.tag_id;
    IF v_is_exclusive THEN
        RAISE EXCEPTION 'Tag % is exclusive to job postings and cannot be associated with job seekers', NEW.tag_id;
    END IF;
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_tag_exclusivity_job_seeker
    BEFORE INSERT OR UPDATE ON job_seeker_tag
    FOR EACH ROW
    EXECUTE PROCEDURE check_tag_exclusivity_job_seeker();

CREATE FUNCTION check_tag_exclusivity_company() RETURNS TRIGGER AS $BODY$
DECLARE
    v_is_exclusive BOOLEAN;
BEGIN
    SELECT job_posting_exclusive INTO v_is_exclusive FROM tag WHERE id = NEW.tag_id;
    IF v_is_exclusive THEN
        RAISE EXCEPTION 'Tag % is exclusive to job postings and cannot be associated with companies', NEW.tag_id;
    END IF;
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_tag_exclusivity_company
    BEFORE INSERT OR UPDATE ON company_tag
    FOR EACH ROW
    EXECUTE PROCEDURE check_tag_exclusivity_company();

CREATE FUNCTION check_bookmark_status() RETURNS TRIGGER AS $BODY$
DECLARE
    v_status job_posting_status;
BEGIN
    SELECT status INTO v_status FROM job_posting WHERE id = NEW.job_posting_id;
    IF v_status != 'Active' THEN
        RAISE EXCEPTION 'Cannot bookmark job posting % with status %', NEW.job_posting_id, v_status;
    END IF;
    RETURN NEW;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER check_bookmark_status
    BEFORE INSERT ON bookmark
    FOR EACH ROW
    EXECUTE PROCEDURE check_bookmark_status();


INSERT INTO notification_type (name, send_to_registered_user, send_to_job_seeker, send_to_recruiter, send_to_company_manager, send_to_admin, default_enabled)
VALUES
('PlatformAlert', TRUE, TRUE, TRUE, TRUE, TRUE, TRUE),
('Message', TRUE, TRUE, TRUE, TRUE, TRUE, TRUE),
('BookmarkDeadline', FALSE, TRUE, FALSE, FALSE, FALSE, TRUE),
('ApplicationStatus', FALSE, TRUE, TRUE, FALSE, FALSE, TRUE),
('NewJobPosting', FALSE, TRUE, FALSE, FALSE, FALSE, TRUE);

INSERT INTO social_media_type (name, default_url, illustration)
VALUES
('Facebook', 'https://facebook.com/', 'facebook_icon.svg'),
('X', 'https://x.com/', 'x_icon.svg'),
('GitHub', 'https://github.com/', 'github_icon.svg'),
('Instagram', 'https://instagram.com/', 'instagram_icon.svg'),
('YouTube', 'https://youtube.com/', 'youtube_icon.svg');

INSERT INTO tag (name, job_posting_exclusive)
VALUES
('Remote', TRUE),
('Internship', TRUE),
('Full-time', TRUE),
('Teamwork', FALSE),
('JavaScript', FALSE);

INSERT INTO country (name)
VALUES ('Portugal'), ('Spain'), ('France'), ('Germany'), ('Italy');

INSERT INTO city (name, country_id)
VALUES
('Lisbon', 1),
('Porto', 1),
('Madrid', 2),
('Paris', 3),
('Berlin', 4);

INSERT INTO registered_user (name, email, password, birthday, age, status)
VALUES
('Alice Silva', 'alice@hireup.com', '$2y$12$s.HHpvgpxU5ZHLpR5d5Hdu6nHDVZdwQMnyfJevRJzrjqGuPBQOt0e', '1990-04-10', 34, 'Active'),
('Bruno Costa', 'bruno@hireup.com', 'hashedpwd2', '1988-07-21', 36, 'Active'),
('Carla Mendes', 'carla@hireup.com', '$2y$12$ZbsqvV62fd1EmYUUud.5cuC5d6JIcVtcz6.qeakXFKdFLl2LweG2C', '1998-02-15', 27, 'Active'),
('David Sousa', 'david@hireup.com', 'hashedpwd4', '1997-09-11', 28, 'Active'),
('Eva Rocha', 'eva@hireup.com', 'hashedpwd5', '1995-12-02', 29, 'Active'),
('Filipe Gomes', 'filipe@hireup.com', 'hashedpwd6', '1990-11-10', 34, 'Active'),
('Admin One', 'admin1@hireup.com', '$2y$12$s.HHpvgpxU5ZHLpR5d5Hdu6nHDVZdwQMnyfJevRJzrjqGuPBQOt0e', '1980-06-15', 44, 'Active'),
('Admin Two', 'admin2@hireup.com', 'hashedpwd8', '1982-05-10', 42, 'Active'),
('Gabriela Lima', 'gabriela@hireup.com', 'hashedpwd7', '1993-05-17', 32, 'Active'),
('Henrique Duarte', 'henrique@hireup.com', 'hashedpwd8', '1989-03-28', 36, 'Active');

INSERT INTO administrator (registered_user_id)
VALUES (7), (8);

INSERT INTO company (name, website, logo, about_us, city_id)
VALUES
('TechNova', 'https://technova.com', 'technova_logo.png', 'Innovative software solutions for businesses.', 1),
('HealthPlus', 'https://healthplus.com', 'healthplus_logo.png', 'Digital healthcare services and analytics.', 2);

INSERT INTO department (name, company_id)
VALUES
('Engineering', 1),
('Human Resources', 1),
('Development', 2),
('Data Science', 2),
('Recruitment', 1);

INSERT INTO recruiter (registered_user_id, is_company_manager, department_id)
VALUES
(1, TRUE, 1),
(2, FALSE, 5),
(9, TRUE, 4),
(10, FALSE, 3);

INSERT INTO job_seeker (registered_user_id, profile_photo, about_me, website, cv, show_cv, city_id)
VALUES
(3, 'carla_photo.png', 'Software developer passionate about backend systems.', 'https://carlam.dev', 'carlam_cv.pdf', TRUE, 1),
(4, 'david_photo.png', 'Frontend developer who loves design.', 'https://davidsousa.dev', 'davidsousa_cv.pdf', TRUE, 2),
(5, 'eva_photo.png', 'Data analyst with experience in Python.', 'https://evarocha.dev', 'evarocha_cv.pdf', FALSE, 3),
(6, NULL, 'Marketing enthusiast with strong communication skills.', NULL, NULL, TRUE, 4);

INSERT INTO job_posting (title, description, creation_date, deadline, min_wage, max_wage, requirements, status, recruiter_id, city_id)
VALUES
('Frontend Developer', 'Develop and maintain UI components.','2025-03-27',  '2025-04-06', 2000, 3000, 'React, CSS, HTML', 'Closed', 2, 1),
('Data Analyst', 'Analyze company data for insights.', '2025-08-19', '2025-08-29', 1800, 2800, 'SQL, Python, Excel', 'Closed', 1, 2),
('Project Manager', 'Coordinate multiple software projects.', '2025-11-04', '2025-11-18', 2500, 4000, 'Agile, Scrum, Jira', 'Active', 9, 3),
('Backend Developer', 'Design RESTful APIs and databases.', '2025-11-05', '2025-11-10', 2200, 3500, 'Node.js, PostgreSQL, Docker', 'Pending', 1, 1),
('Marketing Assistant', 'Support campaign management.', '2025-06-28', '2025-07-03', 1500, 2200, 'SEO, content creation', 'Closed', 2, 3);

INSERT INTO application (cover_letter, date, recommendation_letter, evaluated, accepted, job_seeker_id, job_posting_id)
VALUES
('I am very interested in this position.', '2025-03-27', NULL, TRUE, TRUE, 3, 1),
('Looking forward to joining your team.', '2025-04-03',  NULL, TRUE, FALSE, 4, 1),
('Excited to contribute my data analysis skills.', '2025-11-04', NULL, FALSE, FALSE, 5, 3),
('Passionate about HR operations.', '2025-08-25', NULL, TRUE, TRUE, 6, 2),
('Marketing enthusiast with campaign experience.', '2025-07-01', NULL, TRUE, TRUE, 6, 5);

INSERT INTO bookmark (job_seeker_id, job_posting_id)
VALUES
(5, 3),
(4, 3);

INSERT INTO notification_subscription (registered_user_id, notification_type_id, is_enabled)
SELECT 
    ru.id,
    nt.id,
    nt.default_enabled
FROM registered_user ru
CROSS JOIN notification_type nt;

INSERT INTO notification (content, notification_type_id, registered_user_id)
VALUES
('You have a new message from Alice.', 2, 3),
('Your application for Junior Backend Developer was accepted!', 4, 3),
('New job posting available: Data Analyst.', 5, 5),
('Your bookmark deadline is approaching.', 3, 4),
('Platform maintenance scheduled tomorrow.', 1, 10);

INSERT INTO message (content, sender_id, receiver_id)
VALUES
('Hello, are you still looking for a position?', 1, 3),
('Yes, I am available and interested.', 3, 1),
('Hi, we have new positions at TechNova!', 2, 4),
('Thanks, I will check them out.', 4, 2),
('Your data analyst application was received.', 2, 5);

INSERT INTO report_to_admin (description, reporter_id, handled_by_id)
VALUES
('Issue with job posting visibility.', 3, NULL),
('Spam message received.', 4, 8),
('Application form not submitting properly.', 5, 7),
('Recruiter account suspended wrongly.', 6, 8),
('Website slow on mobile devices.', 5, 7);

INSERT INTO certification_entry (name, issued_by, job_seeker_id)
VALUES
('AWS Certified Developer', 'Amazon', 3),
('Google Data Analytics', 'Google', 5),
('Scrum Master', 'Scrum.org', 4),
('Advanced SQL', 'Udemy', 3),
('Python for Data Science', 'Coursera', 5);

INSERT INTO experience_entry (position_name, employer, start_date, end_date, job_seeker_id)
VALUES
('Backend Intern', 'TechNova', '2022-02-01', '2023-01-31', 3),
('Frontend Developer', 'HealthPlus', '2021-05-01', '2022-05-01', 4),
('Data Analyst', 'DataCorp', '2020-03-01', '2021-03-01', 5),
('Marketing Assistant', 'MediaHub', '2022-01-01', '2023-01-01', 6),
('IT Support', 'SysHelp', '2021-02-01', '2022-02-01', 3);

INSERT INTO education_entry (name, issued_by, start_date, end_date, job_seeker_id)
VALUES
('BSc Computer Science', 'University of Lisbon', '2016-09-01', '2019-06-30', 3),
('BSc Design', 'University of Porto', '2016-09-01', '2019-06-30', 4),
('MSc Data Analytics', 'University of Madrid', '2017-09-01', '2019-06-30', 5),
('BBA Marketing', 'University of Paris', '2016-09-01', '2019-06-30', 6),
('BSc IT', 'University of Berlin', '2015-09-01', '2018-06-30', 5);

INSERT INTO award_entry (name, issued_by, date, job_seeker_id)
VALUES
('Employee of the Month', 'Comp1', '2023-03-01', 3),
('Best Data Project', 'Comp2', '2022-10-01', 5),
('Top Innovator', 'Comp3', '2023-01-01', 4),
('Marketing Excellence', 'Comp2', '2023-05-01', 6),
('Support Star', 'Comp4', '2022-11-01', 5);

INSERT INTO social_media_profile (url, social_media_type_id, job_seeker_id)
VALUES
('https://facebook.com/carla.dev', 1, 3),
('https://github.com/davidsousa', 3, 4),
('https://x.com/evarocha', 2, 5),
('https://instagram.com/filipemarketing', 4, 6);

INSERT INTO social_media_profile (url, social_media_type_id, company_id)
VALUES
('https://facebook.com/technova', 1, 1),
('https://x.com/technova', 2, 1),
('https://github.com/healthplus', 3, 2),
('https://instagram.com/healthplus', 4, 2),
('https://youtube.com/technova', 5, 1);

INSERT INTO website_content (name, content, last_edited_by)
VALUES
('About Us', 'HireUp! connects job seekers and recruiters through a transparent hiring platform.', 7),
('Terms of Service', 'All users must comply with HireUp! usage terms and conditions.', 7),
('Privacy Policy', 'We protect user data under GDPR and relevant data protection laws.', 8);

INSERT INTO job_posting_tag (job_posting_id, tag_id)
VALUES
(1, 1),
(1, 3),
(2, 3),
(3, 4),
(4, 2);

INSERT INTO job_seeker_tag (job_seeker_id, tag_id)
VALUES
(3, 5), 
(4, 4), 
(5, 4),
(6, 4);

INSERT INTO company_tag (company_id, tag_id)
VALUES
(1, 4),
(1, 5),
(2, 4);

INSERT INTO message_notification (message_id, notification_id)
VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

INSERT INTO bookmark_notification (notification_id, job_seeker_id, job_posting_id)
VALUES
(4, 4, 3);

INSERT INTO job_posting_notification (notification_id, job_posting_id)
VALUES
(3, 3); 

INSERT INTO application_notification (notification_id, application_id)
VALUES
(2, 1);

INSERT INTO notification_by_admin (notification_id, admin_id)
VALUES
(5, 7);
