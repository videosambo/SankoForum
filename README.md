<p>
  <a href="https://github.com/videosambo/SankoForum/blob/master/LICENSE"><img alt="GitHub" src="https://img.shields.io/github/license/videosambo/SankoForum"></a>
  <a href="https://github.com/videosambo/SankoForum/releases/tag/1.0"><img alt="GitHub release (latest by date)" src="https://img.shields.io/github/v/release/videosambo/SankoForum"></a>
  <a href="https://github.com/videosambo/SankoForum/releases/tag/1.1"><img alt="GitHub tag (latest SemVer pre-release)" src="https://img.shields.io/github/v/tag/videosambo/sankoforum?include_prereleases"></a>
  <a href"https://packagist.org/packages/videosambo/sankoforum"><img alt="PHP from Packagist" src="https://img.shields.io/packagist/php-v/videosambo/sankoforum"></a>
  <a href="https://libraries.io/github/videosambo/SankoForum"><img alt="Libraries.io dependency status for GitHub repo" src="https://img.shields.io/librariesio/github/videosambo/sankoforum"></a>
  <a href="https://github.com/videosambo/SankoForum/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/videosambo/sankoforum"></a>
</p>

# Sanko Forum

SankoForum is silly forum engine that is fully open source. 

Demo [https://forum.simpelecraft.tk](https://forum.simpelecraft.tk)

### On work

- [x] Forum engine essentials
- [x] Basic adminstrator functions
- [x] Email verification
- [x] Recaptcha
- [x] Create sections, categoeies, topics and posts
- [ ] Manage user information
- [ ] Profile picture
- [x] Basic text formating
- [x] Support for languages
- [ ] Support for themes
- [x] Basic configuration
- [ ] Automatic SQL Database and table creation
- [ ] Automatic update system
- [ ] Instalation file with configuration
- [ ] Automatic webserver configuration and support
- [ ] Private messages
- [ ] Bootstrap style
- [ ] Custom error pages

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
