<b>Installation Steps</b>

1. Require the Package

You can create your new Scaffold app with the following command:</br>
composer create-project --stability=dev websolutions/scaffold test

2. Add Google Credentials

Next make sure to create a Firestore project and add path to your credentials file to your .env file:</br>
GOOGLE_APPLICATION_CREDENTIALS = "GCFSCredentials.json"</br>
To create a new Firestore project follow this quide: https://firebase.google.com/docs/firestore/quickstart

You will also need to add Firebase credentials to your app.</br>
Add Firebase API key to your .env file:</br>
FIREBASE_API_KEY=<i>YOUR_API_KEY</i></br>
Add Firebase credentials to your resource/assests/js/app.js file:</br>
let config = {</br>
    apiKey: "<API_KEY>",</br>
    authDomain: "<PROJECT_ID>.firebaseapp.com",</br>
    databaseURL: "https://<DATABASE_NAME>.firebaseio.com",</br>
    projectId: "<PROJECT_ID>",</br>
    storageBucket: "<BUCKET>.appspot.com",</br>
    messagingSenderId: "<SENDER_ID>",</br>
  };</br>    
How to add Firebase to your app: https://firebase.google.com/docs/web/setup

3. Run Seeders

Lastly, you can seed your Firestore project.</br>
To do this simply run:</br>
php artisan db:seed</br>

Start up a local development server with php artisan serve. Visit http://localhost:8000/login and log into system as admin with credentials:</br>
email: admin@admin.com</br>
password: 123456</br>
