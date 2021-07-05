# Simplify Auth API

This microservice is responsible for JWT generation, login, and user account registration that is used to authorize API requests on the other services on the Simplify platform.

This microservice is developed using Symfony Framework version 5.1 and PHP version 7.4 over a Docker container as a services which must communicate with other services.

## Table of Contents ##

* Installation
    * Summary of Installation
    * Configuration
    * Deployment instructions
* Usage
* Tests

### Installation ###

#### Summary of Installation
For the installation you need to install the following:

* [Docker Engine](https://docs.docker.com/engine/install/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PHP v7.4]()
* [Composer](https://getcomposer.org/doc/00-intro.md)

####Configuration

```
git clone https://bitbucket.org/simplitechdev/com.simplify.api.v1.auth.git
cd com.simplify.api.v1.auth
```

* Create a new file with the following name:
  `.env`
- Open the files `.env.dist` and `env.test` and copy all information of both files and paste in the new file `.env` created.

* Change the environment to deploy in the file `.env` as a following in the next:
    - `APP_ENV = dev` for develop environment
    - `APP_ENV = prod` for production mode
    - Fill the database url parameter for the mysql/mariadb connection
    - Fill the JWT_SECRET_KEY and JWT_PUBLIC_KEY for the token generation

#### Deployment instructions

* For build the container with all dependencies is necessary execute this following command in console:
    - `make build` download and configure all dependencies required for service.


* For deploy the service is necessary execute this following command in console:
    - `make up` up services built on previously step.


* For down the service is necessary execute this following command in console:
    - `make down` this is executed if the services is up.

## Usage

- For register a new user a request must be sent to the following endpoint:   ```POST: /api/v1/user/register```
#### The POST body request must contain
```
{
  "username": "Name of teh user",
  "email": "user@example.com",
  "password": "string",
  "companyId": "3fa85f64-5717-4562-b3fc-2c963f66afa6"
}
```

- For login user and receive the JWT to be used as Bearer authorization in the request to the **Simplify** services, make a request to endpoint:   ```POST: /api/v1/account/login```
#### The POST body request must contain
```
{
  "username": "user@example.com",
  "password": "string",
}
```

- For refresh the JWT, refresh token must be added to header authorization and make a request to endpoint:   ```POST: /api/v1/user/token_refresh```

### Response

- ##### Successful

`{
"success": true,
"status": 201
}`

`{
"success": true,
"status": 200
}`

- #### Errors

`{
"success"; false,
"status": 400
}`


`{
"success"; false,
"status": 404
}`

`{
"success"; false,
"status": 500
}`

#### Status Codes

##### Success

| HTTP Status Code | Description |
|:-----------------|:------------|
|`200 OK` | the request has been processed |
|`201 Created` | the resource has been created |

#### Errors

| HTTP Status Code | Description Error |
|:-----------------|-------------|
|`400 Bad Request` | Bad formation of data to request endpoint|
|`404 Not Found` | API endpoint doesn't exist |
|`500 Internal Server Error` | Internal error in the Simplify platform  |