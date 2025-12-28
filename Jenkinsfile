pipeline {
    agent any

    environment {
        SERVER_IP   = "182.252.71.189"
        DEPLOY_USER = "ubuntu"
        APP_DIR     = "/var/www/html/101"
        PHP_BIN     = "/usr/bin/php8.3"
    }

    stages {

        stage('Checkout') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/Ikbal01856226840/101.git'
            }
        }

        stage('Build') {
            steps {
                sh '''
                rm -f build.zip
                zip -r build.zip 101 \
                -x "*.git*" "node_modules/*"
                '''
            }
        }

        stage('Upload') {
            steps {
                sh '''
                scp -o StrictHostKeyChecking=no build.zip \
                ${DEPLOY_USER}@${SERVER_IP}:/tmp/
                '''
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${SERVER_IP} "
                sudo mkdir -p ${APP_DIR}

                sudo unzip -o /tmp/build.zip -d /var/www/html/101

                sudo chown -R www-data:www-data ${APP_DIR}
                sudo chmod -R 775 ${APP_DIR}/storage ${APP_DIR}/bootstrap/cache

                cd ${APP_DIR}

                ${PHP_BIN} artisan key:generate --force || true        
                ${PHP_BIN} artisan optimize
                ${PHP_BIN} artisan config:clear
                ${PHP_BIN} artisan config:cache
                "
                '''
            }
        }

        stage('Restart Apache') {
            steps {
                sh '''
                ssh ${DEPLOY_USER}@${SERVER_IP} "
                sudo systemctl reload apache2
                "
                '''
            }
        }
    }

    post {
        success {
            echo '✅ Laravel Deployment Successful'
        }
        failure {
            echo '❌ Deployment Failed'
        }
    }
}
