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
                echo 'Execute os comandos de teste, se aplicável'
            }
        }
        stage('Deploy') {
            steps {
                script {
                    sshagent(credentials: ['c44a8a0c-8686-470d-b0de-fbbb19ba86ad']) {
                        sh '''
                            # Do the deploy
                            ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 'cd /home/jgomes/my/jgomes/site && git pull origin master'

                            # Do composer update, migration and clean the cache - php artisan migrate
                            ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 'cd /home/jgomes/my/jgomes/site && composer update && php artisan config:clear'
                        '''
                    }
                }
            }
        }
    }
}
