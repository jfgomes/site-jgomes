pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
               echo 'Do the checkout from the repo, and put the code im this context.'
               checkout scm
            }
        }

        stage('Test') {
            steps {
                echo 'Execute tests'
            }
        }
        stage('Deploy') {
            steps {
                script {
                    sshagent(credentials: ['c44a8a0c-8686-470d-b0de-fbbb19ba86ad']) {

                        // Do the deploy
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site && git stash && git pull origin master\''

                        // Do composer update, migration, and clean the cache - php artisan migrate
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site && composer update --no-update && php artisan config:clear\''

                    }
                }
            }
        }
    }
}
