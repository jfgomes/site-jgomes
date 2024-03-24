## CI / CD with Jenkins

![Jenkins logo](http://127.0.0.1:8000/images/cs/jenkins/jenkins_logo.png)

## Introduction

- The objective of this case study is to create um processo de automatização na entrega do código
- A ferramenta escolhida é o jenkins
- O Jenkins é uma ferramenta de código aberto utilizada para automação de pipelines de integração contínua (CI) e entrega contínua (CD). 
- é usado no desenvolvimento de software para automatizar tarefas repetitivas, como compilação, teste e implantação de código em produção.
- Fornece uma integração Contínua (CI) onde automatiza o processo de integração de código de vários desenvolvedores em um repositório compartilhado. Ele verifica constantemente se há alterações no repositório e, quando detecta uma alteração, realiza a compilação, teste e notifica os desenvolvedores sobre o status do build.
- Entrega Contínua (CD): Além da integração contínua, o Jenkins pode ser configurado para automatizar a entrega de software em diferentes ambientes (desenvolvimento, produção, etc.). Ele pode implantar automaticamente o código em um servidor de teste ou produção após a conclusão bem-sucedida de um pipeline.
- Pipelines: No Jenkins, as automações são definidas por meio de pipelines, que são uma série de etapas (também conhecidas como jobs) que o código passa desde a integração até a entrega. Esses pipelines podem ser configurados visualmente ou definidos como código usando o Jenkinsfile.
- Plugins: O Jenkins oferece vários plugins que estendem sua funcionalidade. Esses plugins podem ser usados para integração com várias ferramentas e tecnologias, como controle de versão (Git).

- No geral, o Jenkins é uma ferramenta poderosa para automatizar o ciclo de vida do desenvolvimento de software, permitindo que as equipes entreguem software de alta qualidade de forma rápida e eficiente.


## Workflow diagram overview
## Setup server side
#### Jenkins orchestration
###### No docker-compose, este é o serviço do Jenkins:
```
    jenkins:
        container_name: jgomes_jenkins
        user: "1000:1002"
        restart: always
        build:
            context: './prod-services/jenkins'
        ports:
            - "8891:8080"
            - "50001:50001"
        volumes:
            - jenkins-data:/var/jenkins_home
        depends_on:
            - phpmyadmin
        networks:
            - jgomes-site_prod-docker
 ```
###### No docker-compose, o Dockerfile está em /prod-services/jenkins
 ```
FROM jenkins/jenkins

USER root

# Adicionar o usuário Jenkins ao grupo sudo
RUN usermod -aG sudo jenkins

RUN echo 'jenkins:jenkins' | chpasswd && \
    mkdir -p /etc/sudoers.d/ && \
    echo 'jenkins ALL=(ALL) NOPASSWD:ALL' > /etc/sudoers.d/jenkins

# Adicional tool and extensions to PHP
RUN apt-get update \
    && apt-get install -y sudo vim curl iputils-ping nano php-cli php-curl php-xml php-json php-mbstring php-tokenizer php-xmlwriter libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN sudo apt-get install ca-certificates gnupg
RUN sudo install -m 0755 -d /etc/apt/keyrings
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
RUN sudo chmod a+r /etc/apt/keyrings/docker.gpg

# Add the repository to Apt sources:
RUN echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

RUN sudo apt-get update

# Add composer
RUN curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```
#### Jenkins VHosts details:

- Este serviço está numa rede local, parém através de um ProxyPass o mesmo é exposto para fora.
- É necessário configurar um dominio independente para o serviço a resolver para o IP da maquina. Neste caso o dominio é: jjenkins.xyz
- É também necessário ter mod_ssl activo bem como o proxy_module, proxy_http_module e o headers_module

###### Jenkins service vhost

 ```
 <IfModule mod_ssl.c>
        
        LoadModule proxy_module modules/mod_proxy.so
        LoadModule proxy_http_module modules/mod_proxy_http.so
	    LoadModule headers_module modules/mod_headers.so

        <VirtualHost *:443>
        
                ServerAdmin zx.gomes@gmail.com
                ServerName jjenkins.xyz
                ErrorLog /var/log/apache2/jenkins_error.log
                CustomLog ${APACHE_LOG_DIR}/jenkins_access.log combined
                SSLEngine on
                SSLCertificateFile /var/www/html/site-jgomes-prod-infra/certs/jenkins.crt
                SSLCertificateKeyFile /var/www/html/site-jgomes-prod-infra/certs/jenkins.key
                SSLCertificateChainFile /var/www/html/site-jgomes-prod-infra/certs/jenkins.ca-bundle

                ProxyRequests Off
                ProxyPass / http://localhost:8891/ nocanon
                ProxyPassReverse / http://localhost:8891/
        
                RequestHeader set X-Forwarded-Proto "https"
                RequestHeader set X-Forwarded-Port "443"

	        <Location />
	           Order allow,deny
		       Allow from all
		       AllowOverride all
	        </Location>
	        
        </VirtualHost>
        
</IfModule>
 ```
###### Jenkins redirect port 80->443 vhost

 ```
 
 <VirtualHost *:80>
      ServerName jjenkins.xyz
      ServerAlias www.jjenkins.xyz
      Redirect permanent / https://jjenkins.xyz/
 </VirtualHost>
 
 ```
#### Jenkins SSL

 ```
SSLEngine on
SSLCertificateFile  {path_to_file}/jenkins.crt
SSLCertificateKeyFile {path_to_file}/jenkins.key
SSLCertificateChainFile {path_to_file}/jenkins.ca-bundle
 ```

## Jenkinsfile Laravel side

 ```
import groovy.json.JsonBuilder

def sshCredentials         = null
def remoteUser             = null
def remoteHost             = null
def remoteProjectDir       = null
def lastRemoteCommandError = null
def remoteCommandPrefix    = null

def executeRemoteCommand(command, remoteCommandPrefix)
{
    // Prepare full cmd
    def remoteCommandComplete = "${remoteCommandPrefix} ${command}'"

    // Tmp Jenkins to save thr logs
    def outputFile = "/tmp/${command.hashCode()}_output.txt"

    // Execute the remote command and redirect standard output and error to the file
    def result = sh(script: "${remoteCommandComplete} > ${outputFile} 2>&1", returnStatus: true)

    // Read standard output and error from the file
    def outputContent = readFile(file: outputFile).trim()

    // Return both output and exit code
    return [output: outputContent, exitCode: result]
}

pipeline
{
    agent any
    stages
    {
        stage('Get ENV vars')
        {
            steps
            {
                script
                {
                    sshCredentials      = env.SSH_CREDENTIALS
                    remoteUser          = env.REMOTE_USER
                    remoteHost          = env.REMOTE_HOST
                    remoteProjectDir    = env.REMOTE_PROJECT_DIR
                    remoteCommandPrefix = "ssh -o StrictHostKeyChecking=no ${remoteUser}@${remoteHost} 'cd ${remoteProjectDir} &&"
                }
            }
        }
        stage('Checkout')
        {
            steps
            {
               echo 'Do the checkout from the repo and put the code i this context.'
               checkout scm
            }
        }
        stage('Build')
        {
            steps
            {
                // Mandatory as I want to run unit tests using the phpunit from vendor
                echo 'Run composer'
                sh 'composer update'

                // .env file is mandatory to generate app key
                echo 'Copy dev .env file'
                sh 'cp .env.test .env'

            }
        }
        stage('Tests')
        {
            steps
            {
                echo 'Run tests'
                sh 'vendor/bin/phpunit'
            }
        }
        stage('Deploy')
        {
            when
            {
                // Only deploy to prod if master
                expression
                {
                    return (env.BRANCH_NAME == 'master')
                }
            }
            steps
            {
                script
                {
                    sshagent(credentials: [sshCredentials])
                    {
                        def commands = [

                             // Do deploy
                            'git reset --hard HEAD && git pull origin master',

                             // Do composer update, migration, and clean all backend caches
                            'APP_ENV=prod RABBIT_HOST=0.0.0.0 composer update && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan migrate && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan route:clear && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan config:clear && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan cache:clear',

                             // Do client files versioning
                            'npm cache clean --force && npm install && npm run production',

                             // Do phpunit report
                            'vendor/bin/phpunit --coverage-html storage/coverage-report && sed -i "s|<head>|<head><title>Coverage</title>|" "storage/coverage-report/index.html" && sed -i "s|<head>|<head><title>Dashboard</title>|" "storage/coverage-report/dashboard.html" && find "storage/coverage-report" -type f -exec sed -i "s#/var/www/html/site-jgomes-prod-infra/site-jgomes/app#(Coverage)#g" {} +'
                        ]

                        for (command in commands)
                        {
                            def commandResult = executeRemoteCommand(command, remoteCommandPrefix)
                            if (commandResult.exitCode != 0)
                            {
                                lastRemoteCommandError = commandResult.output
                                currentBuild.result    = 'FAILURE'
                                echo commandResult.output
                                error("The pipeline was interrupted during deployment while executing the command: ${command}")
                                return
                            }
                        }
                    }
                }
            }
        }
    }
    post
    {
        failure
        {
            script
            {
                // Check of the branch is master
                if (env.BRANCH_NAME == 'master')
                {
                    sshagent(credentials: [sshCredentials])
                    {
                        echo "Send pipeline failure notification with the error.."

                        // Prepare error message
                        def jsonError = new JsonBuilder(lastRemoteCommandError).toPrettyString().replaceAll('"', '\\"')

                        // Prepare command
                        command = "APP_ENV=prod php artisan pipeline:result --result=nok --msg=${jsonError}"

                        // Execute command
                        executeRemoteCommand(command, remoteCommandPrefix)
                    }
                }
            }
        }
        success
        {
            script
            {
                // Check of the branch is master
                if (env.BRANCH_NAME == 'master')
                {
                    sshagent(credentials: [sshCredentials])
                    {
                        echo "Send pipeline success notification.."

                        // Prepare command
                        command = 'APP_ENV=prod php artisan pipeline:result --result=ok --msg=ok'

                        // Execute command
                        executeRemoteCommand(command, remoteCommandPrefix)
                    }
                }
            }
        }
    }
}

 ```
## Explanation of the Jenkinsfile
## Jenkins interface configuration
#### Jenkins plugins
#### Jenkins project configurations
#### Jenkins gitHub configurations
## Extra information:

env vars at Jenkins

## Demonstration
#### ( Click on the image to watch the demo video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=6bGltddfJIM)
