<!-- View stored in resources/views/greeting.php -->
<link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">

<html>
<body>
<div class="">
    <form class="form-horizontal" method="post" action='<?= url("admin/cred/".$credData['idGatewayCred'])?>'>
        <input name="_method" type="hidden" value="PUT">
        <div class="form-group" style="width:500px;position: relative">
            <?php foreach ($credData as $key => $value) {
                if ($key != 'idGatewayCred') { ?>
                    <div class="col-3 col-sm-12">
                        <label class="form-label" for="input-<?=$key?>"><?=$key?></label>
                    </div>
                    <div class="col-9 col-sm-12">
                        <input class="form-input" type="text" name="<?=$key?>" id="input-<?=$key?>" placeholder="Name" value="<?= $value ?>">
                    </div>
                <?php }
            } ?>
        </div>
        <input type="submit">
    </form>
</div>
</body>
</html>