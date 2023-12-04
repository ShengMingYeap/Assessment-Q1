<html>
    <head>
        <title>Photo Sharing</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container mt-5" style="max-width:800px;">
            <form action="action.php" method="POST">
                <div class="mb-3">
                    <label for="InputEmail1" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="InputEmail1">
                </div>
                <div class="mb-3">
                    <label for="InputPassword" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="InputPassword">
                </div>
                <button type="submit" name="login" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </body>
</html>