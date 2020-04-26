<?php require_once("pages/view/_partials/header.php"); ?>
    <div class="col-xl-10 col-12">
        <div class="content-wrapper">
            <div class="content">
                <h1>Anlagen-Übersicht</h1>
                <table class="data-grid table-responsive">
                    <tr>
                        <th>EVU</th>
                        <th>Projektname</th>
                        <th>Email / Benutzername</th>
                        <th>Preis m2</th>
                        <th>Max m2</th>
                        <th>Public Link</th>
                    </tr>
                    <?php foreach ($data as $value): ?>
                        <form action="/user/update" method="post">
                            <input type="hidden" name="userId" value="<?php echo $value['user_id'] ?>">
                            <input type="hidden" name="evuId" value="<?php echo $value['evu_id'] ?>">
                            <tr>
                                <td><?php echo $value['evu_name'] ?></td>
                                <td><?php echo $value['project_name'] ?></td>
                                <td><?php echo $value['email'] ?></td>
                                <td><?php echo $value['price_m2'] ?></td>
                                <td><?php echo $value['max_m2'] ?></td>
                                <td><input class="evu-link form-control form-inputs" type="text" value="<?php echo $value['link'] ?>"></td>
                                <td><button class="icon-buttons" type="submit" name="edit" value="Edit"><i class="far fa-edit"></i></button></td>
                                <td><button class="icon-buttons" type="submit" name="delete" value="Delete"><i class="far fa-trash-alt"></i></button></td>
                            </tr>
                        </form>
                    <?php endforeach; ?>
                </table>

                <a class="ex-buttons" href="/user/add">Anlage hinzufügen</a>
            </div>
        </div>
    </div>
<?php require_once("pages/view/_partials/footer.php"); ?>
