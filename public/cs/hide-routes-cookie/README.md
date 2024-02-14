![Cookie Editor Logo](https://cookie-editor.com/img/cookie-filled.svg)

## Introduction

- This project has some routes that I don´t want to be available in production for users.

- Goal one is to protect some routes in production where users can't access it.

- Goal two is to keep all routes available ONLY in local env.

- For that the solution is to set a cookie in the browser to unlock to hidden routes.

## Requirements

- Firefox 

- Cookie-editor extension

## How to set up

- Install the Cookie Editor extension:

![Cookie Editor Extension](https://jgomes.site/images/cs/cookie-protection-extension.png)

- Protect the routes in the Laravel routes file and set the cookie in the env file:

![cookie-protection-routes.png](https://jgomes.site/images/cs/cookie-protection-routes.png)

- Set the cookie in the .env file:

![cookie-protection-env.png](https://jgomes.site/images/cs/cookie-protection-env.png)

## Test it

- Protected routes WITHOUT the defined cookie:

![cookie-protection-deny.png](https://jgomes.site/images/cs/cookie-protection-deny.png)

- Protected routes WITH the defined cookie:

![cookie-protection-allow.png](https://jgomes.site/images/cs/cookie-protection-allow.png)

## Git diagram

![Cookie Protection diagram](https://jgomes.site/images/diagrams/cookie.drawio.png)

## Demonstration 
#### ( Click on the image to see the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=s0f3kEI5ZGk)
