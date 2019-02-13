<!-- View stored in resources/views/greeting.php -->
<link rel="stylesheet" href="https://unpkg.com/spectre.css/dist/spectre.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


<html>
<br>
<a class="btn edit" style="display: block;float: right;margin-right: 20px;" href="<?= url("/") ?>">Logout</a>
<a class="btn edit" style="position: relative;top: -10px;" href="<?= url("/admin/sendmail") ?>">Send mail</a>

<h1 style="display: inline-block; margin: 0 20px;">Gateways</h1><a class="btn edit" style="position: relative;top: -10px;" href="<?= url("/admin/gateway") ?>">Add</a>
<div style="width: 100%;padding: 20px;">
    <table class="table"">
        <thead>
        <tr>
            <th scope="col"></th>
            <th scope="col">last message date</th>
        </tr>
        </thead>
        <tbody>

<!--    --><?php //foreach ($credData as $row) {
//        print '<tr>
//          <td><a class="btn edit" style="margin-right: 5px;" href="';
//          print url("/admin/gateway/{$row->idGatewayCred}");
//          print '">edit</a><button id="delete-gateway-'.$row->idGatewayCred.'" class="btn delete"';
//          print '">delete</a></td>
//          <td scope="row">'.$row->idGateway.'</td>
//          <td>'.$row->username.'</td>
//          <td>'.$row->host.'</td>
//          <td>'.$row->port.'</td>
//        </tr>';
//    } ?>

        <?php foreach ($gatewayStats as $gateway => $lastMsgDate) {
            print '<tr>';
            print '<td scope="row">'.$gateway.'</td>
                    <td>'.$lastMsgDate.'</td>';
            print '</tr>';
        } ?>

        </tbody>
    </table>
</div>
<br>
<h1 style="display: inline-block; margin: 0 20px;">Tokens</h1><a class="btn edit" style="position: relative;top: -10px;" href="<?= url("/admin/user") ?>">Add</a>
<div style="width: 100%;padding: 20px;">
    <table class="table">
        <thead>
        <tr>
            <th scope="col"></th>
            <th scope="col">Username</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($userData as $row) {
            print '<tr>
                <td><a class="btn edit" style="margin-right: 5px;" href="';
                print url("/admin/user/{$row->idGatewayUser}");
                print '">edit</a><button id="delete-user-'.$row->idGatewayUser.'" class="btn delete"';
                print '">delete</a></td>
                <td scope="row">'.$row->username.'</td>
            </tr>';
        } ?>

        </tbody>
    </table>
</div>
<br>
<h1 style="display: inline-block; margin: 0 20px;">Msg</h1>
<div style="width: 100%;padding: 20px;">
    <div class="container box">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <select id="search-order">
                        <option value="date" selected="selected">date</option>
                        <option value="gateway">gateway</option>
                        <option value="status">status</option>
                    </select>
                    <h3 align="center">Total Data : <span id="total_records"></span></h3>
                    <table id="search" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th><input type="text" name="search-idGateway" id="search-gateway" class="form-control search" placeholder="gateway" /></th>
                            <th><input type="text" name="search-domain" id="search-domain" class="form-control search" placeholder="domain" /></th>
                            <th><input type="text" name="search-msgAction" id="search-action" class="form-control search" placeholder="action" /></th>
                            <th>status</th>
                            <th><input type="text" name="search-msgDate" id="search-date" class="form-control search" placeholder="date" /></th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script>
    $(document).on('click', '.delete', function () {
        // your function here
        var id = this.id.split('-')[2];
        var action = this.id.split('-')[1];
        $.ajax({
            type: 'DELETE',
            url: "/admin/"+action+"/"+id,
        }).done(function (data) {
            window.location.href = "/admin";
        }).fail(function () {
            // fail
        });
    });

    $(document).ready(function(){

        fetch_customer_data();

        function fetch_customer_data(query = '',order = '')
        {
            $.ajax({
                url:"/admin/search/action",
                method:'GET',
                data:{query:query, order:order},
                dataType:'json',
                success:function(data)
                {
                    $('#search tbody').html(data.table_data);
                    $('#total_records').text(data.total_data);
                }
            })
        }

        $(document).on('keyup', '.search', function(){
            var query = {};
            $('.search').each(function(i, obj) {
                if ($(obj).val()) {
                    query[$(obj).attr('name').split('-')[1]] = $(obj).val();
                }
            });
            var order = $('#serch-order option:selected').text();
            fetch_customer_data(query, order);
        });
    });
</script>