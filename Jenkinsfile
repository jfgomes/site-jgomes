pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
               echo 'Do the checkout from the repo, and put the code i this context.'
               checkout scm
            }
        }
        stage('Build') {
            steps {
                echo 'Run composer'
                sh 'composer update'

                echo 'Copy .env file'
                sh 'cp .env.example .env'

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
                // Only deploy to prod if master
                expression {
                    return (env.BRANCH_NAME == 'master')
                }
            }
            steps {
                script {
                    sshagent(credentials: ['c44a8a0c-8686-470d-b0de-fbbb19ba86ad']) {

                        // Do the deploy - || env.BRANCH_NAME.startsWith('feature/')
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site && git stash && git pull origin master\''

                        // Do composer update (ignore the composer.lock in prod), migration, and clean the cache - php artisan migrate
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site && composer update && php artisan config:clear \''

                    }
                }
            }
        }
    }
}
