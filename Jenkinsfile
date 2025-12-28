pipeline {
    agent any

    environment {
        APP_NAME       = "101"
        DEPLOY_USER    = "ubuntu"
        SERVER_IP      = "182.252.71.189"
        DEPLOY_PATH    = "/var/www/html/101"
        RELEASE_DIR    = "${DEPLOY_PATH}/releases/${BUILD_NUMBER}"
        CURRENT_DIR    = "${DEPLOY_PATH}/current"
        PHP_BIN        = "/usr/bin/php"
        COMPOSER_BIN   = "/usr/bin/composer"
    }

    stages {

        stage('Checkout Code') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/Ikbal01856226840/101.git'
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                composer install \
                --no-dev \
                --optimize-autoloader \
                --no-interaction
                '''
            }
        }

        stage('Prepare Release') {
            steps {
                sh '''
                ssh -i ~/.ssh/id_rsa ${DEPLOY_USER}@${SERVER_IP} "
                mkdir -p ${RELEASE_DIR}
                mkdir -p ${DEPLOY_PATH}/shared
                "
                '''
            }
        }

        stage('Upload Code') {
            steps {
                sh '''
                rsync -avz --delete \
                --exclude=.git \
                --exclude=node_modules \
                ./ ${DEPLOY_USER}@${SERVER_IP}:${RELEASE_DIR}
                '''
            }
        }

        stage('Laravel Setup') {
            steps {
                sh '''
                ssh ${DEPLOY_USER}@${SERVER_IP} "
                ln -sfn ${DEPLOY_PATH}/shared/.env ${RELEASE_DIR}/.env
                ln -sfn ${DEPLOY_PATH}/shared/storage ${RELEASE_DIR}/storage

                cd ${RELEASE_DIR}
                ${PHP_BIN} artisan key:generate --force
                ${PHP_BIN} artisan route:clear
                ${PHP_BIN} artisan config:clear
                ${PHP_BIN} artisan view:clear
                "
                '''
            }
        }

        stage('Switch Symlink') {
            steps {
                sh '''
                ssh ${DEPLOY_USER}@${SERVER_IP} "
                ln -sfn ${RELEASE_DIR} ${CURRENT_DIR}
                chown -R www-data:www-data ${DEPLOY_PATH}
                "
                '''
            }
        }

        stage('Restart Services') {
            steps {
                sh '''
                ssh ${DEPLOY_USER}@${SERVER_IP} "
                sudo systemctl reload php8.3-fpm
                sudo systemctl reload nginx
                "
                '''
            }
        }
    }

    post {
        success {
            echo "✅ Laravel Deployment Successful"
        }
        failure {
            echo "❌ Deployment Failed"
        }
    }
}
