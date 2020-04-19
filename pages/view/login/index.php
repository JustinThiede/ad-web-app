<?php require_once("pages/view/_partials/login_header.php"); ?>
<div class="container site-wrapper">
    <div class="d-flex justify-content-center h-100 content-wrapper">
        <div class="content">
            <h1 class="mb-3">Login</h1>
            <form action="/" method="POST">
                <input type="hidden" name="action" value="login">
                Email:<br>
                <input class="form-control form-inputs" type="email" name="email" required>
                <br>
                Passwort:<br>
                <input class="form-control form-inputs" type="password" name="pw" required>
                <br>
                <p><?php if (isset($data)): echo $data; endif; ?></p>
                <input class="ex-buttons" type="submit" value="Submit">
            </form>
        </div>
    </div>
</div>
<?php require_once("pages/view/_partials/login_footer.php"); ?>
