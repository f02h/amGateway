<!-- View stored in resources/views/greeting.php -->
<link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">


<html>
<body>

<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">#</th>
        <th scope="col">Username</th>
        <th scope="col">host</th>
        <th scope="col">port</th>
    </tr>
    </thead>
    <tbody>

<?php foreach ($data as $row) {
    print '<tr>
      <th scope="row">'.$row->idGateway.'</th>
      <td><a class="btn" href="';
      print url("/admin/{$row->idGateway}");
      print '">edit</a></td>
      <td>'.$row->username.'</td>
      <td>'.$row->host.'</td>
      <td>'.$row->port.'</td>
    </tr>';


} ?>

    </tbody>
</table>
</body>
</html>