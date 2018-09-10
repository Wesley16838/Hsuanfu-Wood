<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css?family=Amiri" rel="stylesheet">
    <?php if (current_url()==base_url()): ?>
    <title><?=$this->seotitle?></title>
    <?php else: ?>
    <title><?=$this->typetitle.$this->pagetitle?>範例</title>
    <?php endif; ?>
    <base href="<?=base_url()?>">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/img/apple-icon-144x144.png">
    <link rel="canonical" href="<?=$this->canonical?>" />
    <link rel="alternate" hreflang="zh-TW" href="<?=base_url().'tw'?>" />
    <meta name="title" content="<?=$this->seotitle?>">
    <meta name="description" content="<?=$this->seodesc?>">
    <meta name="keywords" content="<?=$this->seokeyword?>">
    <meta name="author" content="範例">
    <meta name="copyright" content="範例">
    <meta name="apple-itunes-app" content="app-id=編號">
    <?php if ($this->showog): ?>
    <meta property="og:title" content="<?=$this->ogtitle?>">
    <meta property="og:description" content="<?=$this->ogdesc?>">
    <meta property="og:locale" content="zh_TW">
    <meta property="og:rich_attachment" content="true">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?=current_url()?>">
    <meta property="og:image" content="<?=$this->img?>">
    <meta property="og:image:type" content="<?=$this->imgType?>">
    <meta property="og:image:width" content="<?=$this->imgWidth?>">
    <meta property="og:image:height" content="<?=$this->imgHeight?>">
    <meta property="fb:app_id" content="">
    <meta property="fb:profile_id" content="">
    <?php endif; ?>
    <link rel="stylesheet" href="assets/css/style.min.css">
    <!--[if lt IE 9]>
     <script src="assets/js/html5shiv.min.js"></script>
    <![endif]-->
</head>
