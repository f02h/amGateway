<!-- View stored in resources/views/greeting.php -->
<link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


<html>
<br>
<a class="btn edit" style="display: block;float: right;margin-right: 20px;" href="<?= url("/") ?>">Logout</a>

<h1 style="display: inline-block; margin: 0 20px;">Gateways</h1><a class="btn edit" style="position: relative;top: -10px;" href="<?= url("/admin/cred") ?>">Add</a>
<table class="table" style="margin: 0 20px;">
    <thead>
    <tr>
        <th scope="col"></th>
        <th scope="col">#</th>
        <th scope="col">Username</th>
        <th scope="col">host</th>
        <th scope="col">port</th>
    </tr>
    </thead>
    <tbody>

<?php foreach ($credData as $row) {
    print '<tr>
      
      <td><a class="btn edit" href="';
      print url("/admin/cred/{$row->idGatewayCred}");
      print '">edit</a>

        <button id="delete-'.$row->idGatewayCred.'" class="btn delete"';
      print '">delete</a></td>
      <td scope="row">'.$row->idGateway.'</td>
      <td>'.$row->username.'</td>
      <td>'.$row->host.'</td>
      <td>'.$row->port.'</td>
    </tr>';


} ?>

    </tbody>
</table>
<br>
<h1 style="display: inline-block; margin: 0 20px;">Tokens</h1><a class="btn edit" style="position: relative;top: -10px;" href="<?= url("/admin/token") ?>">Add</a>
<table class="table" style="margin: 0 20px;">
    <thead>
    <tr>
        <th scope="col"></th>
        <th scope="col">#</th>
        <th scope="col">Username</th>
        <th scope="col">token</th>
        <th scope="col">valid</th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($tokenData as $row) {
        print '<tr>
      <td><a class="btn edit" href="';
        print url("/admin/token/{$row->idGatewayToken}");
        print '">edit</a>

        <button id="delete-'.$row->idGatewayToken.'" class="btn delete"';
        print '">delete</a></td>
      <td scope="row">'.$row->tokenName.'</td>
      <td>'.$row->token.'</td>
      <td>'.$row->valid.'</td>
    </tr>';


    } ?>

    </tbody>
</table>
</body>
</html>


<script>
    $(document).on('click', '.delete', function () {
        // your function here
        var id = this.id.split('-')[1];
        $.ajax({
            type: 'DELETE',
            url: '/admin/cred/'+id,
        }).done(function (data) {
            window.location.href = "/admin";
        }).fail(function () {
            // fail
        });
    });
</script>