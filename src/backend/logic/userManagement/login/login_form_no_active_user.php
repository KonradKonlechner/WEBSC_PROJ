<form method="POST" class="mt-2 col-3">
    <div class="mb-3">
        <label class="form-label" for="username">Benutzername</label>
        <input class="form-control" type="text" name="username" id="username" required/>
        <!--name attribut legt Name des Parameters beim Absenden des Requests fest-->
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Passwort</label>
        <input class="form-control" type="password" name="password" id="password" required/>
    </div>

    <div class="mt-1">
        <button class="btn btn-danger" type="reset" name="reset">Reset</button>
        <button class="btn btn-success" type="submit" name="submit" id="submit" value="login">Login</button>
    </div>
</form>

<div class="mb-3">
    <p>Noch kein Profil angelegt? Hier können Sie sich
        <a href="../registration/registration_form.php">registrieren.</a>
    </p>
</div>