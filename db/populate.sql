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
('Alice Silva', 'alice@hireup.com', 'hashedpwd1', '1990-04-10', 34, 'Active'),
('Bruno Costa', 'bruno@hireup.com', 'hashedpwd2', '1988-07-21', 36, 'Active'),
('Carla Mendes', 'carla@hireup.com', 'hashedpwd3', '1998-02-15', 27, 'Active'),
('David Sousa', 'david@hireup.com', 'hashedpwd4', '1997-09-11', 28, 'Active'),
('Eva Rocha', 'eva@hireup.com', 'hashedpwd5', '1995-12-02', 29, 'Active'),
('Filipe Gomes', 'filipe@hireup.com', 'hashedpwd6', '1990-11-10', 34, 'Active'),
('Admin One', 'admin1@hireup.com', 'hashedpwd7', '1980-06-15', 44, 'Active'),
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
