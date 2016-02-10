# EverythingWidget
A content management system for developers

EW helps you to build your website faster. It does not limit developer.

It is a app based CMS and you can implement your bussiness logic by creating an app. It provides you a good framework to develop an app fast and easy.

It also provides very flexible template & layout system. You can create your template and manage your layouts with the EW Layout Editor.
You can also create pre-built layouts for your customers

For more information head to : www.ewcms.org

**EW CMS is still in the development process and can't be used in production**


# How to install
**XAMPP installation guide**
  1. Download and install composer.
  2. Download master branch repo as zip.
  3. Extract the zip content inside the htdocs directory.
  4. Change the name of the extracted directory `EverythingWidget-master` to whatever you like. (optinal)
  5. Go to htdocs/**[EverythingWidget-master]**/core and run `composer install`
  6. Open htdocs/**[EverythingWidget-master]**/core/config/config.php with your text editor:
    - `EW_DIR` should be the root directory where EW CMS is installed. For example: `/EverythingWidget-master/` or `/` if EW CMS is already in the root folder
    - `EW_DIR_URL` should be the url path to EW CMS root directory. In the case of XAMPP, this is equal to the value of `EW_DIR`.
  7. Open htdocs/**[EverythingWidget-master]**/core/config/database_config.php with your text editor and specify your database host, user name and password. EW CMS creates a database with name that you specified for `database` property.
  8. Open your browser and go to http://localhost/**[EverythingWidget-master]**/ and click on install. Done!
  
  **As default you have no permission to see the webpage content as a geust. You can manage permission in administration panel**
  
  You can go to http://localhost/**[EverythingWidget-master]**/~admin/ to access administration panel:

    **User:** admin
    
    **Password:** admin
  

# Requirments 
PHP 5.5 and above
MySQL 5.6.26

# Have a question or you wanna help

Join to [EW CMS Slack](https://ewcms.slack.com/ EW CMS Slack) 

Any help is more than welcome. 

# You can contribute by
 - Finding bugs and reporting them.
 - Suggesting new and useful features.
 - Helping to create tutorials for end users.
