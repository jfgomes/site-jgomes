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
stage('Build and Deploy') {
    steps {
        script {
            // Executa migrações, otimizações e outras tarefas de construção

            // Use sh para copiar o projeto para o servidor remoto usando SCP
            sh """
                scp -o StrictHostKeyChecking=no -P 22 -r ./ jgomes@routineris.xyz:/home/jgomes/my/jgomes/site
            """
        }
    }
}
    }
}
