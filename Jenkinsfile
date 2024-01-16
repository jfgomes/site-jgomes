pipeline {
    agent any
    triggers {
      pollSCM('')
    }
    
    stages {
        stage('Checkout') {
            steps {
               echo 'Faz o checkout do código do repositório'
                
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
                echo 'Execute os comandos de deploy, se aplicável'
            }
        }
    }
}
