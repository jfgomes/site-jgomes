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
                    // Executa migrações, otimizações e outras tarefas de construçã
                    
                    // Copia o projeto para o servidor remoto usando SCP
                    sshCommand remote: [
                        host: 'routineris.xyz',
                        port: '443',
                        user: 'jgomes',
                        password: 'jgomes'
                    ], command: "scp -o StrictHostKeyChecking=no -P 443 -r ./ jgomes@routineris.xyz:/home/jgomes/my/jgomes/site"
                }
            }
        }
    }
}
