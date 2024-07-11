<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Simple Login Form Example</title>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Rubik:400,700'>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="{{ asset('dist/loginPageDist/style.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


</head>

<body>
    <!-- partial:index.partial.html -->
    <div class="login-form">
        <form action="{{ route('post.login') }}" method="POST">
            @csrf
            <h1>Login</h1>
            <div class="content">
                <div class="input-field">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                        autocomplete="off">
                </div>
                <div class="input-field">
                    <input type="password" name="password" placeholder="Password" autocomplete="">
                </div>
                {{-- <a href="#" class="link">Forgot Your Password?</a> --}}
            </div>
            {{-- <div class="action">
      <button>Register</button>
      <button>Sign in</button>
    </div> --}}
            <div class="action">
                <button type="submit" class="bg-primary text-light">Login</button>
            </div>
            <hr>
            <div class="action mt-3">
                <a href="{{ route('google.login') }}" class="bg-danger text-light"
                    style="width: 100%;padding: 1rem;text-align: center;text-decoration:none;color:white">Login With
                    Google</a>
            </div>

            <div class="action
                    mt-3">
                <a href="{{ route('facebook.login') }}" class="bg-primary text-light"
                    style="width: 100%;padding: 1rem;text-align: center;text-decoration:none;color:white">Login With
                    Facebook</a>
            </div>

            <div class="action mt-3">
                <a href="{{ route('facebook.login') }}" class="bg-dark text-light"
                    style="width: 100%;padding: 1rem;text-align: center;text-decoration:none;color:white">Login With
                    Apple</a>
            </div>
        </form>
    </div>
    <!-- partial -->
    <script src="./script.js"></script>

</body>

</html>
