<!-- View stored in resources/views/greeting.php -->
<link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
<?php $newUser = new \App\User(); ?>
<html>
<body>
<div class="">
    <form class="form-horizontal" method="post" action='<?= url("admin/user/")?>'>
        <div class="form-group" style="width:500px;position: relative">
            <?php foreach ($newUser->getFillable() as $key => $value) {
                 ?>
                    <div class="col-3 col-sm-12">
                        <label class="form-label" for="input-<?=$value?>"><?=$value?></label>
                    </div>
                    <div class="col-9 col-sm-12">
                        <input class="form-input" type="text" name="input-<?=$value?>" id="input-<?=$value?>" placeholder="<?= $value ?>" value="">
                    </div>
                <?php
            } ?>
        </div>
        <input type="submit">
    </form>
</div>
</body>
</html>