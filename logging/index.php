<html>
    <head>
        <title>Logging for the GeoSchnitzeljagd WebService</title>
        <link rel="stylesheet" type="text/css" href="logging.css">
    </head>
    <body>

    <table>
      <tr>
        <th>ID</th>
        <th>Method</th>
        <th>Route</th>
        <th>Parameter</th>
        <th>Body</th>
        <th>User</th>
        <th>Timestamp</th>
      </tr>

      <?php

        require_once(__DIR__ . '/global.php');

        $data = Logging::all($db);

        foreach ($data as $value) {
          echo "<tr>" .
                   "<td>" . $value->ID . "</td>" .
                   "<td>" . $value->Method . "</td>" .
                   "<td>" . $value->Route . "</td>" .
                   "<td>" . $value->Parameter . "</td>" .
                   "<td>" . $value->Body . "</td>" .
                   "<td>" . $value->User . "</td>" .
                   "<td>" . $value->Timestamp . "</td>" .
               "</tr>";
        }

      ?>

    </table>

    </body>
</html>