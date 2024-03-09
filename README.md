## Objetivo
## The Library
A library is asking you to build a simple application using PHP Web Application or any combination of those two you prefer to manage their books. Here are the specifications:

## Book
- **name*
- **author*
- **category*
- **published date*
- **user (person that borrowed a book)
## Category
- **name*
- **description*
## User
- **name*
- **email*

### Mandatory fields
- **Be able to change the status from available to not available*
- **Be able to know if a user borrowed a book or if it still available*

### Conditions/validations ● Book
- **Should be at least in 1 category*
- **Name, author: Valid values without numbers*
### Category
- **Name: Valid values without numbers*
### User
- **Name: Valid values without numbers*
- **Email: Valid email*

### Build a MessageSender class
The goal with this Class is to simulate an integration with Whatsapp, Telegram or Facebook messenger to send a message when the book that the user wants is able to rent it. This class should be able to:
- **Send message on status change from not available to available*
- **The rest is up to you*
- **The integration doesn’t require to be implemented, but you need to provide your side of the feature.*

### What we need from you:
All the functionality should be done in the backend using PHP 7 and CodeIgniter 3.
In the end we will need you to share a git repository (Github or Bitbucket) with us, the repository should contain the finished code and instructions about how to run it. A Markdown readme or any TXT file is fine, just make sure to indicate clear instructions on how to deploy your code (the simpler the better).

You can use any visual theme you like and feel comfortable with (bootstrap, foundation, material design, etc). Use Postgres or MySQL. About the views Should be as simple as possible and expressive of what the pages are showing.
- **CRUD views for Model (book, category and user)*
- **The index should use pagination (5 per page) About the Styles and build process*

We need you to use any CSS preprocessor you feel comfortable with, and structure the styles in an ordered manner.
###Bonus
- **Host on Heroku*
- **Any frontend framework*
- **Test(unit, integration)*

### Hint, make it useful before than pretty.
## Good luck!

## Instalación
> composer install

- **Ajustar el archivo .env con la configuración de la base de datos a utilizar**

- **Iniciar servidor**
> php spark serve

- **Ir la navegador e ingresar la siguiente url*
> http://localhost:8080 || > https://localhost
