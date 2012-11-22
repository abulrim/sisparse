sisparse
========

sisparse is used to parse the html courses table to a nomalized sql table.

Steps:
1. Import the **siscode.sql** file to your database
2. Download the courses by saving the html file and replacing the **courses.html** file
3. Change the **database.php** credentials if needed
4. Run **index.php**
5. Run **normalize.php**
6. Run **fix_subjects.php**

**Note**
* Make sure you read and understand the code before using it (it's not very complicated)
* Be careful: the html table organization might change (it already happened)