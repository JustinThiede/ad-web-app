<?php require_once("pages/view/_partials/header.php"); ?>
<div class="col-xl-10 col-12">
    <div class="content-wrapper">
        <div class="content">
            <h1>Benutzer löschen</h1>
            <?php if (isset($data['dn'])): ?>
                <form action="/user/delete" method="POST">
                    <input type="hidden" name="dn" value="<?php echo $data['dn'] ?>">

                    <input class="form-control form-inputs" type="text" name="cn" value="<?php if (isset($data['cn'])): echo $data['cn']; endif; ?>" required readonly>
                    <br>

                    <p>Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?</p>
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
