<p align="center">
  <img src="https://t3.ftcdn.net/jpg/01/34/12/58/360_F_134125856_WrmPVAkbCxLFDt5t1TexzfcnlZWq4o0Q.jpg" width="400" alt="Descrição da imagem" />
</p>

<h1 align="center">Adília! Project</h1>

<p align="center">This project is aimed at providing a functional base with everything ready for the automatic implementation of new projects, thereby avoiding manual configurations.</p>

## Overview

When we say "everything ready", it means that various concepts such as local/prod environment setup, testing, deployment, automation, repository management, code reviewing, etc.. etc... all this topics are managed to minimize manual intervention during project setup.

## Local Environment Setup

For local development, this project provides a script named `Ready_to_Dev!` that sets up the entire development environment to mimic the production environment's behavior.

## Production Environment Setup

The project also includes a script called `Ready_to_Prod!` for creating the entire production environment and infrastructure. This script also sets up a CI/CD system, where changes pushed to the master branch trigger automatic assembly, testing, and deployment to production.

## Sensitive Data Handling

Sensitive data such as credentials and passwords are not versioned in plain text. Instead, they are managed through encrypted environment variables using a tool named `Hide_my_Ass!`.

## Versioning and Repository Workflow

- The main branch is `master`, which is read-only. Direct changes are not allowed.
- Changes are made through Pull Requests, which must be approved by designated reviewers.
- The code owners file manages code reviewers.
- Branch names must follow predefined prefixes, or they will be ignored by CI.

## Development Automation

Jenkins is used for development automation. Whenever there's a new change in the repository, Jenkins sets up the project for testing. The pipeline defined in `Jenkinsfile` governs this process.

## Development Flow

Developers push their code to the repository, triggering Jenkins to execute the CI/CD process. The developer is then notified of the process outcome.

## Services

The project operates through microservices orchestrated by Docker Compose. Current services include:

- apache2 / php
- mysql
- phpmyadmin
- redis
- redis-commander
- rabbitmq
- rabbitmq interface

## Requirements for Local Environment Setup

Ensure the following tools are installed:
- docker
- docker-compose
- nodejs
- npm
- xdebug
- redis-tools (for Ubuntu / Debian) or redis (for macOS)

## Installation

1. Checkout the project.
2. Ensure the encrypted environment variables file is present.
3. Run the installation file: `APP_ENV=local ./serve.sh load_env_vars`.
4. After the initial installation, run `./serve.sh`.

## Requirements for Production Environment Setup

1. Create a new email account (Gmail) for the project.
2. Activate and configure Google Cloud Platform buckets service.
3. Create a GitHub account and define rules for protecting the master branch.
4. Create the VPS that will host the project.
5. Create the project's domain and associate it with the host's IP.
6. Create a domain for Jenkins.
7. Create SSL certificates for both the project and Jenkins.
8. Associate the project's domain and Jenkins' domain with the SSL certificates.
9. Generate the `.env` file with the `Set_env_vars!` tool.
10. Checkout the `Ready_to_Prod!` script inside the server and execute it.
11. Place the extracted SSL certificates on the server and associate them with the respective vhosts.
12. Configure Jenkins and set up the webhook in Git for Jenkins.

## Case Studies and Additional Links

I'm using this project in my personal CV to show more information about me and about my implementations. Here are some additional links for more details:

- Site: [here](https://jgomes.site)
- Trello board: [here](https://trello.com/b/zOuG1loa/j-gomes-site)
- Swagger: [here](https://jgomes.site/api/documentation#/Message)
- Phpunit: [here](https://jgomes.site/coverage-report/index.html)
- Features & case studies: [here](https://jgomes.site/case-studies)
- Service list: [here](https://jgomes.site/details)
- Jenkins: [here](https://jjenkins.xyz/)

