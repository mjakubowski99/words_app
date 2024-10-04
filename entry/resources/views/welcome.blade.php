<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-auth.js"></script>
    <script>
        const firebaseConfig = {

            apiKey: "AIzaSyDXViUhI-4C4jE3KeutGG4N7hKKmvi8q3c",

            authDomain: "vocasmart-49389.firebaseapp.com",

            projectId: "vocasmart-49389",

            storageBucket: "vocasmart-49389.appspot.com",

            messagingSenderId: "588544931926",

            appId: "1:588544931926:web:cdcfdb4bab6c10a6dba3f7",

            measurementId: "G-GE4HPMFLZW"

        };


        firebase.initializeApp(firebaseConfig);

        function googleLogin() {
            var provider = new firebase.auth.GoogleAuthProvider();
            firebase.auth().signInWithPopup(provider).then(function(result) {
                result.user.getIdToken().then(function(token) {
                    window.location.href = '/auth/google/callback?token=' + token;
                });
            }).catch(function(error) {
                console.log(error);
            });
        }
    </script>
</head>
<body>
<button onclick="googleLogin()">Login with Google</button>
</body>
</html>
