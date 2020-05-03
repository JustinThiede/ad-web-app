<?php require_once("pages/view/_partials/header.php"); ?>
    <div class="col-xl-10 col-12">
            <div class="content-wrapper">
                <div class="content">
                    <div class="button-field d-flex">
                        <?php if ((isset($data) && gettype($data) == 'array') || ($data != 'Der Benutzer wurde erfolgreich hinzugefügt.')): ?>
                            <button class="back-button alt-ex-buttons mb-4 mr-2"><i class="fas fa-reply mr-2 "></i>Zurück</button>
                            <button class="ex-buttons mr-2" type="submit" form="usereditor" value="Submit">Speichern</button>
                        <?php endif; ?>
                        <a class="ex-buttons mr-2" href="/user/index">Benutzer-Übersicht</a>
                    </div>

                    <h1>Benutzer bearbeiten</h1>
                    <?php if (isset($data) && gettype($data) != 'array'): echo $data; else:?>
                        <form action="/user/add" method="POST" enctype="multipart/form-data" id="usereditor">
                            <input type="hidden" name="edit" value="<?php if (isset($data['dn'])): echo $data['dn']; endif; ?>">

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
                            Mitglied von:
                            <select class="groups" name="memberOf[]" multiple>
                                <?php
                                    $ldap     = new LDAP();
                                    $memberOf = explode(';', $data['memberOf']);

                                    print_r($memberOf);

                                    foreach ($ldap->searchGroups() as $group) {
                                ?>
                                <option value="<?php echo $group['dn']; ?>" <?php if (in_array($group['dn'], $memberOf)): echo 'selected'; endif; ?>><?php echo $group['cn'] ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                            <?php if (isset($data['dn'])): ?>
                                Passwort ändern:<br>
                                <input type="checkbox" name="changePw" value="true">
                                <br>
                            <?php endif; ?>

                            <div class="user-pw" <?php if (isset($data['dn'])): echo 'style="display:none"'; endif;?>>
                                Passwort:<br>
                                <input class="form-control form-inputs" type="password" name="pw" <?php if (!isset($data['dn'])): echo 'required'; endif; ?>>
                                <br>
                                Passwort bestätigen:<br>
                                <input class="form-control form-inputs" type="password" name="pwConfirm" value="" <?php if (!isset($data['dn'])): echo 'required'; endif; ?>>
                                <br>
                            </div>

                            <button class="ex-buttons mt-2" type="submit" value="Submit">Speichern</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once("pages/view/_partials/footer.php"); ?>
