pipeline {
    agent any  
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
        stage('Deploy') {
            steps {
                script {
                    // Configuração das credenciais de SSH no Jenkins && composer install --no-interaction --prefer-dist
                    sshagent(credentials: ['c44a8a0c-8686-470d-b0de-fbbb19ba86ad']) {
                        // Comandos de deploy   cd /home/jgomes/my/jgomes/site && git pull origin master 
                        sh '''
                            ssh -o StrictHostKeyChecking=no jgomes@94.63.32.148 'cd /home/jgomes/my/jgomes/site && git pull origin master && composer install --no-interaction --prefer-dist'
                            # Outras tarefas de deploy podem ser adicionadas conforme necessário
                        '''
                    }
                }
            }
        }
    }
}
