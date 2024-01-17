pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
               echo 'Do the checkout from the repo, and put the code i this context.'
               checkout scm
            }
        }

        stage('Tests') {
            steps {
                echo 'Execute tests'
                sh 'composer install'
                sh 'ls -l'
                //sshagent(credentials: ['c44a8a0c-8686-470d-b0de-fbbb19ba86ad']) {
                 //   sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site && composer update && git checkout HEAD^ -- composer.lock && vendor/bin/phpunit tests \''
                // }
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
