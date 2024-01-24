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
                    sshagent(credentials: ['5f9bd247-5605-4b42-9bb9-c8da86395696']) {

                        // do deploy
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && git pull origin master \''

                        // do composer update, migration, and clean all caches
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && composer update && php artisan route:clear && php artisan config:clear && php artisan cache:clear \''

                        // restore composer.lock from repo as the composer update done before generates the same file as is on repo, creating also a modified file that will block the next pull on the next pipeline
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && git restore composer.lock \''
                    }
                }
            }
        }
    }
}
