<?php require_once("pages/view/_partials/header.php"); ?>
        <div class="col-xl-10 col-12">
            <div class="content-wrapper">
                <div class="content">
                    <h1>Ad-Gruppen</h1>
                    <table class="data-grid table-responsive">
                        <tr>
                            <th>CN</th>
                            <th>Mitglied von</th>
                            <th>Mitglieder</th>
                        </tr>

                        <?php foreach ($data as $value): ?>
                            <form action="/group/update" method="post">
                                <input type="hidden" name="dn" value="<?php echo $value['dn'] ?>">
                                <input type="hidden" name="cn" value="<?php echo $value['cn'] ?>">

                                <tr>
                                    <td><?php echo $value['cn'] ?></td>
                                    <td><?php echo $value['memberOf'] ?></td>
                                    <td><?php echo $value['member'] ?></td>
                                    <td><button class="icon-buttons" type="submit" name="edit" value="Edit"><i class="far fa-edit"></i></button></td>
                                    <td><button class="icon-buttons" type="submit" name="delete" value="Delete"><i class="far fa-trash-alt"></i></button></td>
                                </tr>
                            </form>
                        <?php endforeach; ?>
                    </table>

                    <a class="ex-buttons" href="/group/add">Gruppe hinzufügen</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once("pages/view/_partials/footer.php"); ?>
