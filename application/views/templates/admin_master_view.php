<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <base href=<?=base_url(); ?>>

    <title>Admin</title>

    <link href="assets/admin/css/all/font-awesome/css/font-awesome.css" rel="stylesheet"> <!--固定-->
    <link href="assets/admin/css/all/style.css" rel="stylesheet"> <!--固定-->

  </head>

  <body>
    <div id="wrapper">

      <!--左邊選單-->
      <?php $this->load->view($this->admin_sidebar_view);?>

      <div id="page-wrapper" class="gray-bg">

        <!--上面選單-->
        <?php $this->load->view($this->admin_nav_view);?>

        <!--內頁-->
        <?= $content ?>

        <!--footer-->
        <?php $this->load->view($this->admin_footer_view);?>

      </div>

    </div>

  </body>
</html>
    <?php $this->load->view('templates/_parts/admin_js_min');?>
