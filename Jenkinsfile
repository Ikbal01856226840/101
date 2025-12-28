pipeline {
    agent any

    environment {
        SERVER_IP   = "182.252.71.189"
        DEPLOY_USER = "ubuntu"
        DEPLOY_PATH = "/var/www/html/101"
    }

    stages {

        stage('Checkout Code') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/Ikbal01856226840/101.git'
            }
        }

        stage('Upload to Server') {
            steps {
                sh '''
                rsync -avz --delete \
                --exclude=.git \
                ./ ${DEPLOY_USER}@${SERVER_IP}:${DEPLOY_PATH}
                '''
            }
        }

        stage('Remote Commands') {
            steps {
                sh '''
                ssh ${DEPLOY_USER}@${SERVER_IP} "
                cd ${DEPLOY_PATH}              
                "
                '''
            }
        }

        stage('Restart Services') {
            steps {
                sh '''
                ssh ${DEPLOY_USER}@${SERVER_IP} "
                sudo systemctl reload php8.3-fpm
                sudo systemctl reload apache2
                "
                '''
            }
        }
    }

    post {
        success {
            echo "✅ Deploy Successful"
        }
        failure {
            echo "❌ Deploy Failed"
        }
    }
}
