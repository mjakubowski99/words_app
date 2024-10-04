<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-auth.js"></script>
    <script>
        const firebaseConfig = {};

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
