<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>GoDelivery</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link href="{{ asset('mdb/css/bootstrap.min.css') }}" rel="stylesheet">
  <style type="text/css">

  </style>
</head>

<body>

  <h1>Mis Viajes</h1>

  <table class="responsive-table">
    <thead>
      <tr>
        <th data-field="id">Name</th>
        <th data-field="name">Item Name</th>
        <th data-field="price">Item Price</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Alvin</td>
        <td>Eclair</td>
        <td>.87</td>
      </tr>
      <tr>
        <td>Alan</td>
        <td>Jellybean</td>
        <td>.76</td>
      </tr>
      <tr>
        <td>Jonathan</td>
        <td>Lollipop</td>
        <td>.00</td>
      </tr>
    </tbody>
  </table>
  <!-- JQuery -->
  <script type="text/javascript" src="{{ asset('mdb/js/jquery-3.4.1.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('mdb/js/popper.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('mdb/js/bootstrap.min.js') }}"></script>

  <script>
    //Animation init
    new WOW().init();

    //Modal
    $('#myModal').on('shown.bs.modal', function () {
      $('#myInput').focus()
    })

    // Material Select Initialization
    $(document).ready(function () {
      $('.mdb-select').material_select();
    });

  </script>

</body>

</html>
