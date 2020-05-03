<?php require_once("pages/view/_partials/header.php"); ?>
    <div class="col-xl-10 col-12">
            <div class="content-wrapper">
                <div class="content">
                    <div class="button-field d-flex">
                        <?php if ((isset($data) && gettype($data) == 'array') || ($data != 'Der Benutzer wurde erfolgreich hinzugefügt.')): ?>
                            <button class="back-button alt-ex-buttons mb-4 mr-2"><i class="fas fa-reply mr-2 "></i>Zurück</button>
                            <button class="ex-buttons mr-2" type="submit" form="usereditor" value="Submit">Speichern</button>
                        <?php endif; ?>
                        <a class="ex-buttons mr-2" href="/group/index">Gruppen-Übersicht</a>
                    </div>

                    <h1>Gruppe bearbeiten</h1>
                    <?php if (isset($data) && gettype($data) != 'array'): echo $data; else:?>
                        <form action="/group/add" method="POST" enctype="multipart/form-data" id="usereditor">
                            <input type="hidden" name="edit" value="<?php if (isset($data['dn'])): echo $data['dn']; endif; ?>">
                            CN:<br>
                            <input class="form-control form-inputs" type="text" name="cn" value="<?php if (isset($data['cn'])): echo $data['cn']; endif; ?>" required>
                            <br>
                            Gruppenart:<br>
                            <input type="radio" name="groupType" value="1" required <?php if (isset($data['groupType']) && $data['groupType'] == 1): echo 'checked'; endif; ?>>
                            <label for="1">Security</label><br>
                            <input type="radio" name="groupType" value="2" required <?php if (isset($data['groupType']) && $data['groupType'] == 2): echo 'checked'; endif; ?>>
                            <label for="2">Distribution</label><br>
                            <br>
                            <select class="groups" name="memberOf[]" multiple>
                                <?php
                                    $ldap     = new LDAP();
                                    $memberOf = explode(';<br>', $data['memberOf']);

                                    foreach ($ldap->searchGroups() as $group) {
                                ?>
                                <option value="<?php echo $group['cn']; ?>" <?php if (in_array($group['cn'], $memberOf)): echo 'selected'; endif; ?>><?php echo $group['cn'] ?></option>
                                <?php
                                    }
                                ?>
                            </select>
                            <button class="ex-buttons mt-2" type="submit" value="Submit">Speichern</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once("pages/view/_partials/footer.php"); ?>
