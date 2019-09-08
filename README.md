<p>
  <a href="https://github.com/videosambo/SankoForum/blob/master/LICENSE"><img alt="GitHub" src="https://img.shields.io/github/license/videosambo/SankoForum"></a>
  <a href="https://github.com/videosambo/SankoForum/releases/tag/1.0"><img alt="GitHub release (latest by date)" src="https://img.shields.io/github/v/release/videosambo/SankoForum"></a>
  <a href="https://github.com/videosambo/SankoForum/releases/tag/1.1"><img alt="GitHub tag (latest SemVer pre-release)" src="https://img.shields.io/github/v/tag/videosambo/sankoforum?include_prereleases"></a>
  <a href"https://www.php.net/"><img alt="php version" src="https://img.shields.io/badge/php-%5E7.3.2-green"></a>
</p>

# Sanko Forum

SankoForum is silly forum engine that is fully open source. 

### On work

- [x] Forum engine essentials
- [x] Basic adminstrator functions
- [x] Email verification
- [x] Recaptcha
- [x] Create sections, categoeies, topics and posts
- [ ] Manage user information
- [ ] Profile picture
- [ ] Basic text formating
- [x] Support for languages
- [ ] Support for themes
- [x] Basic configuration

### SQL

- Database
  - users
    - user_id
    - user_name   
    - user_pass
    - user_date
    - user_email
    - email_token
    - email_verified
    - user_level
  - sections
    - section_id
    - section_name
    - section_description
  - categories
    - category_id
    - category_section
    - category_name
    - category_description
  - topics
    - topic_id
    - topic_subject
    - topic_date
    - topic_category
    - topic_by
  - posts
    - post_id
    - post_content
    - post_date
    - post_topic
    - post_by
  
  [How to create database?](https://github.com/videosambo/SankoForum/blob/master/sankoforum.sql)
