<!-- View stored in resources/views/greeting.php -->

<html>
<body>

<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">First</th>
        <th scope="col">Last</th>
        <th scope="col">Handle</th>
    </tr>
    </thead>
    <tbody>

<?php foreach ($data as $row) {
    print '<tr>
      <th scope="row">'.$row->idGateway.'</th>
      <td>'.$row->username.'</td>
      <td>'.$row->host.'</td>
      <td>'.$row->port.'</td>
    </tr>';


} ?>

    </tbody>
</table>
</body>
</html>