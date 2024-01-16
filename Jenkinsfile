pipeline {
    agent any  
    environment {
        SERVER_HOST = 'jgomes.site'
        SERVER_PORT = '443'
        SERVER_USER = 'jgomes'
        SERVER_PASSWORD = 'jgomes'
        REMOTE_PATH = '/home/jgomes/my/jgomes/site'
    }
    stages {
        stage('Checkout') {
            steps {
               echo 'Faz o checkout do código do repositório'
               checkout scm    
            }
        }

        stage('Build') {
            steps {
                echo 'Execute os comandos de construção necessários'
            }
        }

        stage('Test') {
            steps {
                echo 'Execute os comandos de teste, se aplicável'
            }
        }

        stage('Build and Deploy') {
            steps {
                script {
                    // Executa migrações, otimizações e outras tarefas de construção
                    sh 'php artisan migrate --force'
                    sh 'php artisan optimize'
                    
                    // Copia o projeto para o servidor remoto usando SCP
                    sshCommand remote: [
                        host: env.SERVER_HOST,
                        port: env.SERVER_PORT,
                        user: env.SERVER_USER,
                        password: env.SERVER_PASSWORD
                    ], command: "scp -o StrictHostKeyChecking=no -P ${env.SERVER_PORT} -r ./ ${env.SERVER_USER}@${env.SERVER_HOST}:${env.REMOTE_PATH}"
                }
            }
        }
    }
}
