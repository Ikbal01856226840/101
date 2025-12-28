pipeline {
    agent any
	
	  stages {
			stage('checkout') {
				  steps {
					  git branch: 'main', url: 'https://github.com/Ikbal01856226840/101.git'
				  }
			}
		
		
			//Build stage
			  stage('Build') {
				steps {
					sh 'composer install'
					// if we need any other commands to compile
				}
			 }
		 
			//Test stage
			stage('Test') {
				steps {
					sh 'php artisan test'
				}
			}
		
			 // deploy to production server 
			stage('Deploy to production') {

				steps {
					sh 'ssh ubuntu@182.252.71.189 -o StrictHostKeyChecking=no "bash /var/www/html/101/scripts/deploy.sh" '
				}
			}  
		
	}
}