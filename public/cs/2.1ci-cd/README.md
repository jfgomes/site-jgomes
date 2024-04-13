## CI / CD with Jenkins

![Jenkins logo](https://jgomes.site/images/cs/jenkins/jenkins_logo.png)

## Introduction

- The objective of this case study is to create an automation process in code delivery.
- The chosen tool is Jenkins.
- Jenkins is an open-source tool used for automating continuous integration (CI) and continuous delivery (CD) pipelines.
- It is used in software development to automate repetitive tasks such as code compilation, testing, and deployment to production.
- It provides Continuous Integration (CI) where it automates the process of integrating code from multiple developers into a shared repository. It constantly checks for changes in the repository and, upon detecting a change, performs compilation, testing, and notifies developers about the build status.
- Continuous Delivery (CD): In addition to continuous integration, Jenkins can be configured to automate software delivery to different environments (development, production, etc.). It can automatically deploy the code to a test or production server after a successful completion of a pipeline.
- Pipelines: In Jenkins, automations are defined through pipelines, which are a series of steps (also known as jobs) that the code goes through from integration to delivery. These pipelines can be configured visually or defined as code using a Jenkinsfile.
- Plugins: Jenkins offers various plugins that extend its functionality. These plugins can be used for integration with various tools and technologies, such as version control (Git).
- Overall, Jenkins is a powerful tool for automating the software development lifecycle, enabling teams to deliver high-quality software quickly and efficiently.

## Workflow diagram overview
![git-branch-protection.png](https://jgomes.site/images/diagrams/schema.drawio.png)

## Jenkins orchestration
#### No docker-compose, este é o serviço do Jenkins:
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
#### No docker-compose, o Dockerfile está em /prod-services/jenkins
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

- This service is on a local network, but it is exposed outside through a ProxyPass.
- It is necessary to configure an independent domain for the service to resolve to the machine's IP. In this case, the domain is: jjenkins.xyz.
- It is also necessary to have mod_ssl active as well as the proxy_module, proxy_http_module, and headers_module.

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

The Jenkins file above is a declarative script that defines a workflow for continuous integration and continuous delivery (CI/CD) of a PHP application.

Here's a summary of what each stage does:

- Get Environment Variables (ENV vars): In this stage, the necessary environment variables for SSH connection to the remote server where the application will be deployed are configured.
- Checkout: A checkout operation is performed from the source code repository.
- Build: In this stage, actions related to building the application are executed, such as updating project dependencies using Composer and copying an environment file (.env) needed for generating an application key.
- Tests: Unit tests are executed using PHPUnit.
- Deploy: This stage is crucial as it involves deploying the application to a production environment. Various actions are performed here, such as resetting the repository, updating dependencies with Composer, running database migrations, clearing caches, and generating client version files, among other things. The execution of these commands is done via SSH on the specified remote server in the environment variables. If any of the implementation commands fail (i.e., if their exit code is not 0), the workflow is interrupted, and an error is logged.
- Post: This section defines actions after the main workflow:
    Failure: If the implementation fails, a script is executed to notify the error, passing an error message to the remote server via an SSH command.
    Success: If the implementation is successful, a script is executed to notify the success to the remote server.

In summary, this Jenkinsfile automates the process of continuous integration and delivery of a PHP application, from building to deployment in a production environment, using Jenkins and SSH for automation and status notification.

## Jenkins interface configuration
 
![jenkins setup](https://jgomes.site/images/cs/jenkins/f1.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f2.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f3.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f4.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f9.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f10.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f11.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f12.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f13.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f14.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f15.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f17.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f16.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f18.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f21.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f22.png)

#### Jenkins plugins
![jenkins setup](https://jgomes.site/images/cs/jenkins/f5.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f6.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f7.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f8.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f23.png)

#### Jenkins gitHub configuration
![jenkins setup](https://jgomes.site/images/cs/jenkins/f19.png)

![jenkins setup](https://jgomes.site/images/cs/jenkins/f20.png)

#### Jenkins env vars configuration
![jenkins setup](https://jgomes.site/images/cs/jenkins/f24.png)

## Extra notes

- Need to ensure the plugin "Pipeline: Stage View Plugin" is installed
- Need to ensure no pending approvals at Dashboard -> Manage Jenkins -> ScriptApproval
- The authorized_keys is a file the has the pub key
- Need to add the iptables:
  ###### sudo iptables -A INPUT -p tcp --dport 22 -s ---server ip--- -j ACCEPT
  ###### sudo iptables -A INPUT -p tcp --dport 22 -s 172.0.0.0/8 -j ACCEPT
  ###### sudo iptables -A INPUT -p tcp --dport 22 -j DROP
  ###### sudo iptables-save


## Demonstration
#### ( Click on the image to watch the demo video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=zNz07YnnJSA)
