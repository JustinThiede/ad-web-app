<?php require_once("pages/view/_partials/header.php"); ?>
    <div class="col-xl-10 col-12">
            <div class="content-wrapper">
                <div class="content">
                    <div class="button-field d-flex">
                        <button class="back-button alt-ex-buttons mb-4 mr-2"><i class="fas fa-reply mr-2 "></i>Zurück</button>
                        <?php if ((isset($data) && gettype($data) == 'array') || (empty($data))): ?>
                            <button class="ex-buttons mr-2" type="submit" form="usereditor" value="Submit">Speichern</button>
                        <?php endif; ?>
                        <a class="ex-buttons mr-2" href="/user/index">Benutzer-Übersicht</a>
                    </div>

                    <h1>Benutzer bearbeiten</h1>
                    <?php if (isset($data) && gettype($data) != 'array'): echo $data; else:?>
                        <form action="/user/add" method="POST" enctype="multipart/form-data" id="usereditor">
                            Vorname:<br>
                            <input class="form-control form-inputs" type="text" name="firstName" value="<?php if (isset($data['firstName'])): echo $data['firstName']; endif; ?>" required>
                            <br>
                            Nachname:<br>
                            <input class="form-control form-inputs" type="text" name="lastName" value="<?php if (isset($data['lastName'])): echo $data['lastName']; endif; ?>" required>
                            <br>
                            Loginname<br>
                            <input class="form-control inline form-inputs" type="text" name="loginName" value="<?php if (isset($data['loginName'])): echo $data['loginName']; endif; ?>" required>
                            <input class="form-control inline form-inputs" type="text" name="domain" value="@smirnyag.ch" disabled>
                            <br><br>
                            Passwort:<br>
                            <input class="form-control form-inputs" type="password" name="pw" value="<?php if (isset($data['pw'])): echo $data['pw']; endif; ?>" required>
                            <br>
                            Passwort bestätigen:<br>
                            <input class="form-control form-inputs" type="password" name="pwConfirm" value="" required>
                            <br>
                            <button class="ex-buttons mt-2" type="submit" value="Submit">Speichern</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once("pages/view/_partials/footer.php"); ?>
