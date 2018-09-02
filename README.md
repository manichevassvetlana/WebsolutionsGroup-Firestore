<b>Installation Steps</b>

1. Require the Package

You can create your new Scaffold app with the following command:
composer create-project --stability=dev websolutions/scaffold test

2. Add Google Credentials

Next make sure to create a Firestore project and add path to your credentials file to your .env file:
GOOGLE_APPLICATION_CREDENTIALS = "GCFSCredentials.json"
To create a new Firestore project follow this quide: https://firebase.google.com/docs/firestore/quickstart

You will also need to add Firebase credentials to your app.
Add Firebase API key to your .env file:
FIREBASE_API_KEY=<i>YOUR_API_KEY</i>
Add Firebase credentials to your resource/assests/js/app.js file:
let config = {
    apiKey: "<API_KEY>",
    authDomain: "<PROJECT_ID>.firebaseapp.com",
    databaseURL: "https://<DATABASE_NAME>.firebaseio.com",
    projectId: "<PROJECT_ID>",
    storageBucket: "<BUCKET>.appspot.com",
    messagingSenderId: "<SENDER_ID>",
  };
How to add Firebase to your app: https://firebase.google.com/docs/web/setup

3. Run Seeders

Lastly, you can seed your Firestore project.
To do this simply run:

php artisan db:seed

Start up a local development server with php artisan serve. Visit http://localhost:8000/login and log into system as admin with credentials:
email: admin@admin.com
password: 123456
