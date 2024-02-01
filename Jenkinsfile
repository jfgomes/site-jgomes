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
                echo 'Copy dev .env file'
                sh 'cp .env.dev .env'
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

                        // ( do before ) restore composer.lock en package-lock.json from repo as this are the same files in the repo, creating also a modified file that will block the next pull on the next pipeline
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && git restore composer.lock && git restore package-lock.json  \''

                        // do deploy
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && git pull origin master \''

                        // do composer update, migration, and clean all caches
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && APP_ENV=prod RABBIT_HOST=0.0.0.0 composer update && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan migrate && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan route:clear && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan config:clear && APP_ENV=prod RABBIT_HOST=0.0.0.0 php artisan cache:clear \''

                        // do public files versioning
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && npm cache clean --force && npm install && npm run production \''

                        // do phpunit report
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && vendor/bin/phpunit --coverage-html storage/coverage-report && sed -i "s|<head>|<head><title>Coverage</title>|" "storage/coverage-report/index.html"  && sed -i "s|<head>|<head><title>Dashboard</title>|" "storage/coverage-report/dashboard.html" && find "storage/coverage-report" -type f -exec sed -i "s#/home/jgomes/my/jgomes/site-jgomes/app#(Coverage)#g" {} + \''

                        // ( do after ) restore composer.lock en package-lock.json from repo as this are the same files in the repo, creating also a modified file that will block the next pull on the next pipeline
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && git restore composer.lock && git restore package-lock.json  \''
                    }
                }
            }
        }
    }
    post {
        failure {
            script {
                // Verifica se a branch é master
                if (env.BRANCH_NAME == 'master') {
                    sshagent(credentials: ['5f9bd247-5605-4b42-9bb9-c8da86395696']) {
                        def buildUrl = env.BUILD_URL
                        echo "Send pipeline failure notification"
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && APP_ENV=prod php artisan pipeline:result --result="nok" --url="test" \''
                    }
                }
            }
        }
        success {
            script {
                // Verifica se a branch é master
                if (env.BRANCH_NAME == 'master') {
                    sshagent(credentials: ['5f9bd247-5605-4b42-9bb9-c8da86395696']) {
                        def buildUrl = env.BUILD_URL
                        echo "Send pipeline success notification"
                        sh 'ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 \'cd /home/jgomes/my/jgomes/site-jgomes && APP_ENV=prod php artisan pipeline:result --result="ok" --url="$buildUrl" \''
                    }
                }
            }
        }
    }
}
