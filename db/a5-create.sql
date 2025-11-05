DROP SCHEMA IF EXISTS hire_up CASCADE;
CREATE SCHEMA hire_up;
SET search_path TO hire_up;

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
