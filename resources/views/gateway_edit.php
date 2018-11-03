<!-- View stored in resources/views/greeting.php -->
<link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">

<html>
<body>
<div class="">
    <form class="form-horizontal" method="post" action='<?= url("admin/gateway/".$gatewayData['idGatewayCred'])?>'>
        <input name="_method" type="hidden" value="PUT">
        <div class="form-group" style="width:500px;position: relative">
            <?php foreach ($gatewayData as $key => $value) {
                 ?>
                    <div class="col-3 col-sm-12">
                        <label class="form-label" for="<?=$key?>"><?=$key?></label>
                    </div>
                    <div class="col-9 col-sm-12">
                        <input class="form-input" type="text" name="<?=$key?>" id="<?=$key?>" placeholder="Name" value="<?= $value ?>">
                    </div>
                <?php
            } ?>
        </div>
        <input type="submit">
    </form>
</div>
</body>
</html>