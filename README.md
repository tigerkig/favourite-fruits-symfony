1. Database settings (username, password, port)

   - DATABASE_URL="mysql://root:@127.0.0.1:3306/fruit_manage?serverVersion=5.7"

2. API URL settings

    - API_URL='https://fruityvice.com/api/fruit/'

3. Email info settins

    - MAILER_DSN=sendgrid://KEY@default
    - FROM_EMAIL=''
    - TO_EMAIL=''

4. run the "composer install" in termial of project's folder

5. Create the database with termial

    - php bin/console doctrine:database:create

6. make the migration file (in the case not have migrate file in migrations folder)

    - php bin/console make:migration

7. Run the server 

    - symfony server:start


===========================================================================
Description for test project
===========================================================================

1.  You can get all data from API with termial command.
    it will be saved to mysql database.

    - php bin/console app:get-fruits


