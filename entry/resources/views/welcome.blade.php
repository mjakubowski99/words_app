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
            apiKey: "AIzaSyAtEmKQohjg3Tcui-RCbGV4TnMtVK2-0V8",
            authDomain: "gallery-4fc0c.firebaseapp.com",
            projectId: "gallery-4fc0c",
            storageBucket: "gallery-4fc0c.appspot.com",
            messagingSenderId: "188372467209",
            appId: "1:188372467209:web:aaa289c77256aaabf042d8",
            measurementId: "G-WL9J47CX5R"
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
