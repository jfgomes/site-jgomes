pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
               echo 'Do the checkout from the repo and put the code i this context.'
               checkout scm
            }
        }
        stage('Build') {
            steps {
                // mandatory as I want to run unit tests using the phpunit from vendor
                echo 'Run composer'
                sh 'composer update'
                // .env file is mandatory to generate app key
                echo 'Copy .env file'
                sh 'cp .env.example .env'
                // app key is mandatory to run tests
                echo 'Generate application key'
                sh 'php artisan key:generate'
            }
        }
        stage('Tests') {
            steps {
                echo 'Run tests'
                sh 'vendor/bin/phpunit'
            }
        }
        stage('Deploy') {
            when {
                // only deploy to prod if master - || env.BRANCH_NAME.startsWith('feature/')
                expression {
                    return (env.BRANCH_NAME == 'master')
                }
            }
            steps {
                script {
                    sshagent(credentials: ['c44a8a0c-8686-470d-b0de-fbbb19ba86ad']) {

                        // do deployx
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site && git checkout HEAD^ -- composer.lock && git pull origin master\''

                        // Do composer update (ignore the composer.lock in prod), migration, and clean the cache
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site && composer update && php artisan config:clear \''

                    }
                }
            }
        }
    }
}
