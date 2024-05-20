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

                // Start MySQL with skip-grant-tables
                sh 'sudo mysqld_safe --skip-grant-tables &'
                
                // .env file is mandatory to generate app key
                echo 'Copy dev .env file'
                sh 'cp .env.test .env'

                // App key is mandatory to run tests
                // echo 'Generate application key'
                // sh 'php artisan key:generate'

                sh 'php artisan migrate'
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
                             // DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=nome_do_banco_de_testes DB_USERNAME=usuario_de_teste DB_PASSWORD=senha_de_teste
                             //'vendor/bin/phpunit --coverage-html storage/coverage-report && sed -i "s|<head>|<head><title>Coverage</title>|" "storage/coverage-report/index.html" && sed -i "s|<head>|<head><title>Dashboard</title>|" "storage/coverage-report/dashboard.html" && find "storage/coverage-report" -type f -exec sed -i "s#/var/www/html/site-jgomes-prod-infra/site-jgomes/app#(Coverage)#g" {} +'
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
