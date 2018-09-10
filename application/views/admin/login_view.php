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

  <body class="bg-login">
          <div class="login">
              <div class="portrait">
                <img src="<?=($img)?$img:'assets/admin/img/admin-logo.png'?>">
              </div>
              <form class="m-t" role="form" id="form">
                  <div class="form-group">
                      <input type="text" class="form-control input-login input-lg" name="Username" placeholder="帳號" required>
                  </div>
                  <div class="form-group">
                      <input type="password" class="form-control input-login input-lg" name="Password" placeholder="密碼" required>
                  </div>
                  <button type="submit" class="btn btn-primary btn-lg block full-width m-b btn-login">登入</button>
              </form>
              <p class="text-center copyright">Powered by <a href="#">CREATOP</a></p>
          </div>
  </body>
</html>

<!-- Mainly scripts -->
<script src="assets/admin/js/jquery-2.1.1.min.js"></script>
<script src="assets/admin/js/jquery-ui-1.10.4.min.js"></script>
<script src="assets/admin/js/datatables.min.js"></script>
<script src="assets/admin/js/all.min.js"></script>
<script>window.addEventListener("beforeunload",function(a){$.blockUI({css:{border:"none",padding:"15px",backgroundColor:"#000","-webkit-border-radius":"10px","-moz-border-radius":"10px",opacity:0.5,color:"#fff"}})});</script>
<script>
 $(document).ready(function(){
     $("#form").validate({
         rules: {
             Username: {
                 required: true,
             },
             Password: {
                 required: true,
             },
         },
         messages: {
           Username: {
             required: "欄位必填",
           },
           Password: {
             required: "欄位必填",
           },
          },
         submitHandler: function(form) {

          $.post("<?php echo site_url("admin/login/check"); ?>", $('#form').serialize(), function(data){
            $('form')[0].reset();
            if(data.error) alert(data.error);
            if(data.success) top.location.href = data.success;
          });
        }
     });
});
</script>
