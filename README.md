##Description
The project uses MVC Architecture. 

##Dependicies
The front end uses Twig as the templating engine. For routing Altourouter is used and for autoloader, psr-4 is used. They need to be installed using composer.

##Instructions
In order for customer to access site, the home route is '/';
In order for an agent to access agent administation page the route is '/agent'.

##Website Interface
A login and registration system is in place for both client and agent.
A customer can register products, see all the registered products and submit a ticket if product is still under warranty.
An agent can create products to the database and view the tickets allocated to him and reply to them.


##Apache configuration on localhost
The virtual host apache configuration for the project is as follows:
![image](https://github.com/heyrya/kahuna-client/assets/3865985/f0123864-2d2c-42e6-a5a7-63f538b4f748)

MySql is used as DBMS system. 
