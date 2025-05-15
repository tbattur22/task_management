## Task Management Demo Application (Laravel 12 + MySQL + InertiaJS + React/TypeScript)

### Description:
Simple Task Management Application with abilities to manage projects and their tasks.
Guest users should be able to sign up and sign in. Once signed in the authenticated users should be able to create, edit and delete projects as well as create, edit and delete tasks under the selected project.
Project has just name and task has name and priority. The task priorities are numbers and should be unique within the same project. Users should be able to drag and drop the tasks in order to set their priorities. The top task will have the highest priority starting with number 1, the second in the list will have the priority number 2 and so on.


### Tech Stack:

MySQL + Laravel 12 + InertiaJS + ReactJS + TypeScript

### What I did
- Use Laravel 12 Startup Kit and chose Breeze Authentication, and React frontend with TypeScript.
- Using migrations created db tables for Project and Task.
- Implemented the relevant models, controllers and routes for Projects and Tasks.
- Implemented the relavent React components in TypeScript.
- Switched db from default sqlite to MySQL and changed the configuration in .env file.

### How to test locally
- Get working MySQL server locally with database task_management, the user laravel_user with password laravel_password who has full privileges to task_management database.
- Configure the database connection in .env file as follows:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password

- Run the migration and seed the db (seed will add 3 projects and a fews tasks under each project):

php artisan migrate:fresh --seed

- Run composer run dev command to start up the dev servers
- Test the application locally

### Deploy to Production Server

Here we assume that there is a hosting provider that meets the Laravel 12 deployment server requirements and where all neccessary software like PHP, Composer, Node.js, Npm, Web Server (Apache) and MySQL have been installed and setup and configured properly.

- Clone the repo or upload zipped project and extract on the server
- Configure environmental variables in .env file for production environment (including db settings for production MySQL server)
- Run composer to install the application dependencies
- Run npm install and npm run build for frontend assets
- Appply caching to optimize the application
- Configure virtual host for the application, set https and permissions, and enable the site
- Make sure the app is accessible and running fine.
