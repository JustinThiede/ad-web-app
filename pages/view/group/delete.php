<?php require_once("pages/view/_partials/header.php"); ?>
<div class="col-xl-10 col-12">
    <div class="content-wrapper">
        <div class="content">
            <div class="button-field d-flex">
                <?php if (!isset($data['success'])):?>
                    <button class="back-button alt-ex-buttons mb-4 mr-2"><i class="fas fa-reply mr-2 "></i>Zurück</button>
                <?php endif; ?>
                <a class="ex-buttons mr-2" href="/user/index">Gruppen-Übersicht</a>
            </div>
            <h1>Gruppe löschen</h1>
            <?php if (isset($data['dn'])): ?>
                <form action="/group/delete" method="POST">
                    <input type="hidden" name="dn" value="<?php echo $data['dn'] ?>">

                    <input class="form-control form-inputs" type="text" name="cn" value="<?php if (isset($data['cn'])): echo $data['cn']; endif; ?>" required readonly>
                    <br>

                    <p>Sind Sie sicher, dass Sie diese Gruppe löschen möchten?</p>
                    <div class="button-wrapper">
                        <input class="ex-buttons delete-buttons" type="submit" value="Ja">
                        <a class="ex-buttons delete-buttons" href="/user/index">Nein</a>
                    </div>

                </form>
            <?php else: ?>
                <p><?php if (isset($data['success'])): echo $data['success']; else: echo $data['error']; endif;?></p>
            <?php endif; ?>
            <?php require_once("pages/view/_partials/footer.php"); ?>
        </div>
    </div>
</div>
</div>
</div>
